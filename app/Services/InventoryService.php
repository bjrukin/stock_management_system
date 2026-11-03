<?php

namespace App\Services;

use App\Exceptions\BatchExpiredException;
use App\Exceptions\BatchQuarantinedException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\NegativeStockException;
use App\Models\Batch;
use App\Models\FiscalYear;
use App\Models\InventoryBalance;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\WarehouseLocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class InventoryService
{
    /**
     * Get authoritative available stock.
     * Formula: On-Hand - Reserved - Damaged - Quarantined - In-Transit-Out.
     */
    public static function getAvailableStock(
        int $productId,
        ?int $warehouseId = null,
        ?int $locationId = null,
        ?int $batchId = null
    ): int {
        $query = InventoryBalance::query()
            ->where('product_id', $productId);

        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($locationId !== null) {
            $query->where('location_id', $locationId);
        } else {
            // By default, only sellable & available locations count toward sellable available stock
            $query->whereHas('location', fn ($q) => $q->where('is_sellable', true)->where('is_available', true));
        }

        if ($batchId !== null) {
            $query->where('batch_id', $batchId);
        } else {
            // By default, only unexpired, non-quarantined, non-recalled batches
            $query->whereHas('batch', fn ($q) => $q->sellable());
        }

        $total = $query->selectRaw(
            'COALESCE(SUM(quantity_on_hand - quantity_reserved - quantity_damaged - quantity_quarantined - quantity_in_transit_out), 0) as available_total'
        )->value('available_total');

        return max(0, (int) $total);
    }

    /**
     * Get aggregate physical on-hand stock.
     */
    public static function getOnHandStock(
        int $productId,
        ?int $warehouseId = null,
        ?int $locationId = null,
        ?int $batchId = null
    ): int {
        $query = InventoryBalance::query()->where('product_id', $productId);

        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($locationId !== null) {
            $query->where('location_id', $locationId);
        }

        if ($batchId !== null) {
            $query->where('batch_id', $batchId);
        }

        return (int) $query->sum('quantity_on_hand');
    }

    /**
     * Allocate stock using First-Expiry-First-Out (FEFO) principle.
     *
     * @param int $productId
     * @param int $requiredQuantity (in base units)
     * @param int $warehouseId
     * @param int|null $locationId
     * @return array Array of allocated slices: ['batch_id', 'batch_number', 'location_id', 'allocated_qty', 'expiry_date', 'unit_cost']
     * @throws InsufficientStockException
     */
    public static function allocateFefo(
        int $productId,
        int $requiredQuantity,
        int $warehouseId,
        ?int $locationId = null
    ): array {
        if ($requiredQuantity <= 0) {
            return [];
        }

        $query = InventoryBalance::query()
            ->with(['batch', 'location'])
            ->where('inventory_balances.warehouse_id', $warehouseId)
            ->where('inventory_balances.product_id', $productId)
            ->whereHas('location', function ($q) use ($locationId) {
                $q->where('is_sellable', true)->where('is_available', true);
                if ($locationId !== null) {
                    $q->where('id', $locationId);
                }
            })
            ->whereHas('batch', fn ($q) => $q->sellable())
            ->join('batches', 'inventory_balances.batch_id', '=', 'batches.id')
            ->select('inventory_balances.*')
            ->orderBy('batches.expiry_date_ad', 'asc') // FEFO priority
            ->orderBy('batches.id', 'asc');

        $balances = $query->lockForUpdate()->get();

        $allocations = [];
        $remainingNeeded = $requiredQuantity;

        foreach ($balances as $balance) {
            $availableInBalance = $balance->quantity_available;
            if ($availableInBalance <= 0) {
                continue;
            }

            $take = min($remainingNeeded, $availableInBalance);

            $allocations[] = [
                'batch_id' => $balance->batch_id,
                'batch_number' => $balance->batch->batch_number,
                'location_id' => $balance->location_id,
                'location_code' => $balance->location->code,
                'allocated_qty' => $take,
                'expiry_date' => $balance->batch->expiry_date_ad?->format('Y-m-d'),
                'unit_cost' => $balance->batch->cost_price,
            ];

            $remainingNeeded -= $take;

            if ($remainingNeeded <= 0) {
                break;
            }
        }

        if ($remainingNeeded > 0) {
            $availableTotal = $requiredQuantity - $remainingNeeded;
            throw new InsufficientStockException(
                "Insufficient stock for Product ID #{$productId} in Warehouse #{$warehouseId}. Required: {$requiredQuantity}, Available: {$availableTotal}, Shortfall: {$remainingNeeded}."
            );
        }

        return $allocations;
    }

    /**
     * Record an atomic stock movement and update inventory balance.
     *
     * @param array $data
     * @return StockMovement
     * @throws NegativeStockException|BatchExpiredException|BatchQuarantinedException
     */
    public static function recordStockMovement(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $warehouseId = $data['warehouse_id'];
            $locationId = $data['location_id'];
            $productId = $data['product_id'];
            $batchId = $data['batch_id'];
            $movementType = $data['movement_type'];
            $quantity = (int) $data['quantity']; // Signed integer: + for in, - for out

            $batch = Batch::findOrFail($batchId);
            $product = Product::findOrFail($productId);

            // Outbound validation for sales / dispatch
            if ($quantity < 0 && in_array($movementType, ['SALE_DISPATCH', 'TRANSFER_OUT'])) {
                if ($batch->is_quarantined) {
                    throw new BatchQuarantinedException("Batch #{$batch->batch_number} is quarantined and cannot be dispatched.");
                }
                if ($batch->is_recalled) {
                    throw new BatchQuarantinedException("Batch #{$batch->batch_number} is recalled and cannot be dispatched.");
                }
                if ($batch->is_expired) {
                    throw new BatchExpiredException("Batch #{$batch->batch_number} expired on {$batch->expiry_date_ad->format('Y-m-d')} and cannot be sold.");
                }
            }

            // Lock or create inventory balance record
            $balance = InventoryBalance::where([
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
                'product_id' => $productId,
                'batch_id' => $batchId,
            ])->lockForUpdate()->first();

            if (! $balance) {
                $balance = new InventoryBalance([
                    'warehouse_id' => $warehouseId,
                    'location_id' => $locationId,
                    'product_id' => $productId,
                    'batch_id' => $batchId,
                    'quantity_on_hand' => 0,
                    'quantity_reserved' => 0,
                    'quantity_damaged' => 0,
                    'quantity_quarantined' => 0,
                    'quantity_in_transit_out' => 0,
                ]);
            }

            $newOnHand = $balance->quantity_on_hand + $quantity;

            // Check negative stock prevention setting
            $allowNegative = (bool) SettingService::get('allow_negative_stock', false);
            if ($newOnHand < 0 && ! $allowNegative) {
                throw new NegativeStockException(
                    "Stock movement rejected: On-hand stock for Product '{$product->name}' (Batch: {$batch->batch_number}) at Location #{$locationId} would drop to {$newOnHand}."
                );
            }

            $balance->quantity_on_hand = $newOnHand;

            // If this is a dispatch fulfilling a reservation, release reserved quantity
            if (! empty($data['fulfilling_reserved']) && $quantity < 0) {
                $fulfillQty = abs($quantity);
                $balance->quantity_reserved = max(0, $balance->quantity_reserved - $fulfillQty);
            }

            $balance->save();

            // Pricing and unit cost determination
            $unitCost = isset($data['unit_cost']) ? (string) $data['unit_cost'] : (string) $batch->cost_price;
            $absQty = (string) abs($quantity);
            $totalCost = bcmul($absQty, $unitCost, 2);

            // Current fiscal year
            $fiscalYearId = $data['fiscal_year_id'] ?? FiscalYear::current()?->id;

            $userId = $data['created_by'] ?? (Auth::guard('admin')->check() ? Auth::guard('admin')->id() : null);

            // Record immutable stock movement
            $movement = StockMovement::create([
                'fiscal_year_id' => $fiscalYearId,
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
                'product_id' => $productId,
                'batch_id' => $batchId,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'balance_after' => $newOnHand,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'from_location_id' => $data['from_location_id'] ?? null,
                'to_location_id' => $data['to_location_id'] ?? null,
                'created_by' => $userId,
                'notes' => $data['notes'] ?? null,
                'created_at' => now(),
            ]);

            // Audit log the inventory transaction
            AuditService::log(
                event: 'stock_movement',
                model: $movement,
                oldValues: ['on_hand_before' => $balance->quantity_on_hand - $quantity],
                newValues: ['on_hand_after' => $newOnHand, 'movement_type' => $movementType, 'qty' => $quantity],
                userType: 'admin',
                userId: $userId
            );

            return $movement;
        });
    }

    /**
     * Reserve stock for pending order.
     */
    public static function reserveStock(
        int $productId,
        int $batchId,
        int $warehouseId,
        int $locationId,
        int $quantity
    ): bool {
        if ($quantity <= 0) {
            return false;
        }

        return DB::transaction(function () use ($productId, $batchId, $warehouseId, $locationId, $quantity) {
            $balance = InventoryBalance::where([
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
                'product_id' => $productId,
                'batch_id' => $batchId,
            ])->lockForUpdate()->first();

            if (! $balance || $balance->quantity_available < $quantity) {
                throw new InsufficientStockException("Cannot reserve {$quantity} units: Insufficient available stock.");
            }

            $balance->quantity_reserved += $quantity;
            $balance->save();

            return true;
        });
    }

    /**
     * Release previously reserved stock.
     */
    public static function releaseReservedStock(
        int $productId,
        int $batchId,
        int $warehouseId,
        int $locationId,
        int $quantity
    ): bool {
        if ($quantity <= 0) {
            return false;
        }

        return DB::transaction(function () use ($productId, $batchId, $warehouseId, $locationId, $quantity) {
            $balance = InventoryBalance::where([
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
                'product_id' => $productId,
                'batch_id' => $batchId,
            ])->lockForUpdate()->first();

            if (! $balance) {
                return false;
            }

            $balance->quantity_reserved = max(0, $balance->quantity_reserved - $quantity);
            $balance->save();

            return true;
        });
    }

    /**
     * Reconcile warehouse balances against the append-only stock_movements ledger.
     *
     * @param int|null $warehouseId
     * @param bool $fixDiscrepancies If true, resets balances to strictly match the ledger.
     * @return array List of discrepancies found.
     */
    public static function reconcileWarehouse(?int $warehouseId = null, bool $fixDiscrepancies = false): array
    {
        $movementQuery = StockMovement::query()
            ->select(
                'warehouse_id',
                'location_id',
                'product_id',
                'batch_id',
                DB::raw('SUM(quantity) as ledger_total')
            )
            ->groupBy('warehouse_id', 'location_id', 'product_id', 'batch_id');

        if ($warehouseId !== null) {
            $movementQuery->where('warehouse_id', $warehouseId);
        }

        $ledgerBalances = $movementQuery->get()
            ->keyBy(fn ($item) => "{$item->warehouse_id}_{$item->location_id}_{$item->product_id}_{$item->batch_id}");

        $balanceQuery = InventoryBalance::query();
        if ($warehouseId !== null) {
            $balanceQuery->where('warehouse_id', $warehouseId);
        }

        $currentBalances = $balanceQuery->get()
            ->keyBy(fn ($item) => "{$item->warehouse_id}_{$item->location_id}_{$item->product_id}_{$item->batch_id}");

        $allKeys = $ledgerBalances->keys()->merge($currentBalances->keys())->unique();
        $discrepancies = [];

        foreach ($allKeys as $key) {
            $ledger = $ledgerBalances->get($key);
            $cached = $currentBalances->get($key);

            $ledgerQty = (int) ($ledger?->ledger_total ?? 0);
            $cachedQty = (int) ($cached?->quantity_on_hand ?? 0);

            if ($ledgerQty !== $cachedQty) {
                [$whId, $locId, $prodId, $batchId] = explode('_', $key);

                $diff = [
                    'warehouse_id' => (int) $whId,
                    'location_id' => (int) $locId,
                    'product_id' => (int) $prodId,
                    'batch_id' => (int) $batchId,
                    'ledger_quantity' => $ledgerQty,
                    'cached_on_hand' => $cachedQty,
                    'variance' => $cachedQty - $ledgerQty,
                    'fixed' => false,
                ];

                if ($fixDiscrepancies) {
                    DB::transaction(function () use ($whId, $locId, $prodId, $batchId, $ledgerQty) {
                        InventoryBalance::updateOrCreate(
                            [
                                'warehouse_id' => $whId,
                                'location_id' => $locId,
                                'product_id' => $prodId,
                                'batch_id' => $batchId,
                            ],
                            [
                                'quantity_on_hand' => $ledgerQty,
                            ]
                        );
                    });
                    $diff['fixed'] = true;
                }

                $discrepancies[] = $diff;
            }
        }

        return $discrepancies;
    }

    /**
     * Get aggregate Stock Flow Summary strictly adhering to the standard stock flow model:
     * Inflows: Purchase (PURCHASE_RECEIPT), Sell Return (SALE_RETURN)
     * Outflows: Sell (SALE_DISPATCH), Purchase Return (PURCHASE_RETURN), Expired Item (EXPIRED_DISPOSAL), Damage (DAMAGE_WRITE_OFF)
     */
    public static function getStockFlowSummary(
        ?int $warehouseId = null,
        ?int $productId = null,
        ?string $fromDate = null,
        ?string $toDate = null
    ): array {
        $baseQuery = StockMovement::query();

        if ($warehouseId !== null) {
            $baseQuery->where('warehouse_id', $warehouseId);
        }
        if ($productId !== null) {
            $baseQuery->where('product_id', $productId);
        }
        if ($fromDate !== null) {
            $baseQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate !== null) {
            $baseQuery->whereDate('created_at', '<=', $toDate);
        }

        $aggregates = (clone $baseQuery)
            ->select(
                'movement_type',
                DB::raw('COUNT(*) as movement_count'),
                DB::raw('SUM(quantity) as net_quantity'),
                DB::raw('SUM(total_cost) as total_cost')
            )
            ->groupBy('movement_type')
            ->get()
            ->keyBy('movement_type');

        $getTypeData = function (string $type) use ($aggregates) {
            $row = $aggregates->get($type);
            $qty = (int) ($row?->net_quantity ?? 0);
            return [
                'count' => (int) ($row?->movement_count ?? 0),
                'quantity' => abs($qty),
                'signed_quantity' => $qty,
                'total_cost' => number_format((float) ($row?->total_cost ?? 0.0), 2, '.', ''),
            ];
        };

        // Inflows
        $purchase = $getTypeData(StockMovement::TYPE_PURCHASE);
        $sellReturn = $getTypeData(StockMovement::TYPE_SELL_RETURN);
        $openingStock = $getTypeData(StockMovement::TYPE_OPENING_STOCK);

        $totalInflowQty = $purchase['quantity'] + $sellReturn['quantity'] + $openingStock['quantity'];
        $totalInflowCost = bcadd(
            bcadd((string) $purchase['total_cost'], (string) $sellReturn['total_cost'], 2),
            (string) $openingStock['total_cost'],
            2
        );

        // Outflows
        $sell = $getTypeData(StockMovement::TYPE_SELL);
        $purchaseReturn = $getTypeData(StockMovement::TYPE_PURCHASE_RETURN);
        $expiredItem = $getTypeData(StockMovement::TYPE_EXPIRED);
        $damage = $getTypeData(StockMovement::TYPE_DAMAGE);

        $totalOutflowQty = $sell['quantity'] + $purchaseReturn['quantity'] + $expiredItem['quantity'] + $damage['quantity'];
        $totalOutflowCost = bcadd(
            bcadd(
                bcadd((string) $sell['total_cost'], (string) $purchaseReturn['total_cost'], 2),
                (string) $expiredItem['total_cost'],
                2
            ),
            (string) $damage['total_cost'],
            2
        );

        // Current On-hand & Valuation
        $balQuery = InventoryBalance::query();
        if ($warehouseId !== null) {
            $balQuery->where('warehouse_id', $warehouseId);
        }
        if ($productId !== null) {
            $balQuery->where('product_id', $productId);
        }

        $totalOnHand = (int) $balQuery->sum('quantity_on_hand');
        $totalReserved = (int) $balQuery->sum('quantity_reserved');
        $totalDamaged = (int) $balQuery->sum('quantity_damaged');
        $totalQuarantined = (int) $balQuery->sum('quantity_quarantined');
        $totalAvailable = max(0, $totalOnHand - $totalReserved - $totalDamaged - $totalQuarantined);

        return [
            'inflows' => [
                'purchase' => $purchase,
                'sell_return' => $sellReturn,
                'opening_stock' => $openingStock,
                'total_quantity' => $totalInflowQty,
                'total_cost' => $totalInflowCost,
            ],
            'outflows' => [
                'sell' => $sell,
                'purchase_return' => $purchaseReturn,
                'expired_item' => $expiredItem,
                'damage' => $damage,
                'total_quantity' => $totalOutflowQty,
                'total_cost' => $totalOutflowCost,
            ],
            'stock' => [
                'on_hand_quantity' => $totalOnHand,
                'available_quantity' => $totalAvailable,
                'reserved_quantity' => $totalReserved,
                'net_flow_quantity' => $totalInflowQty - $totalOutflowQty,
            ],
        ];
    }
}
