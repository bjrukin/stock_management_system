<?php

namespace App\Services;

use App\Helpers\BsDateHelper;
use App\Models\AccountingPeriod;
use App\Models\Batch;
use App\Models\BatchRecall;
use App\Models\FiscalYear;
use App\Models\InventoryBalance;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentLine;
use App\Models\StockTransfer;
use App\Models\StockTransferLine;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WarehouseOperationService
{
    /**
     * Check if a given date falls within a closed accounting period.
     *
     * @param string $dateAd
     * @throws RuntimeException
     */
    public static function checkPeriodLock(string $dateAd): void
    {
        if (AccountingPeriod::isDateClosed($dateAd)) {
            throw new RuntimeException("Accounting period for date '{$dateAd}' is locked and closed. Transactions and stock movements are blocked.");
        }
    }

    /**
     * Create an Inter-Warehouse Stock Transfer in draft status.
     *
     * @param array $header
     * @param array $lines
     * @return StockTransfer
     * @throws RuntimeException
     */
    public static function createTransfer(array $header, array $lines): StockTransfer
    {
        return DB::transaction(function () use ($header, $lines) {
            $fromWarehouseId = $header['from_warehouse_id'];
            $toWarehouseId = $header['to_warehouse_id'];

            if ($fromWarehouseId === $toWarehouseId) {
                throw new RuntimeException("Source and destination warehouses must be distinct.");
            }

            $dateAd = $header['transfer_date_ad'] ?? Carbon::today()->format('Y-m-d');
            static::checkPeriodLock($dateAd);

            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            $transferNumber = DocumentSequenceService::getNextNumber('TRF', $fiscalYear);
            $transferDateBs = $header['transfer_date_bs'] ?? BsDateHelper::toBs($dateAd);

            $transfer = StockTransfer::create([
                'transfer_number' => $transferNumber,
                'fiscal_year_id' => $fiscalYear->id,
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
                'transfer_date_ad' => $dateAd,
                'transfer_date_bs' => $transferDateBs,
                'status' => 'draft',
                'total_items' => count($lines),
                'notes' => $header['notes'] ?? null,
                'created_by' => $header['created_by'] ?? null,
            ]);

            foreach ($lines as $lineData) {
                $product = Product::findOrFail($lineData['product_id']);
                $batch = Batch::findOrFail($lineData['batch_id']);
                $fromLocation = WarehouseLocation::where('id', $lineData['from_location_id'])
                    ->where('warehouse_id', $fromWarehouseId)
                    ->firstOrFail();

                // Validate available balance in source location
                $balance = InventoryBalance::where('warehouse_id', $fromWarehouseId)
                    ->where('location_id', $fromLocation->id)
                    ->where('product_id', $product->id)
                    ->where('batch_id', $batch->id)
                    ->first();

                $available = $balance ? $balance->quantity_available : 0;
                if ($available < (int) $lineData['quantity']) {
                    throw new RuntimeException("Insufficient available stock for Product '{$product->name}', Batch '{$batch->batch_number}' at location '{$fromLocation->name}'. Available: {$available}, Requested: {$lineData['quantity']}.");
                }

                StockTransferLine::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'from_location_id' => $fromLocation->id,
                    'to_location_id' => $lineData['to_location_id'] ?? null,
                    'quantity' => (int) $lineData['quantity'],
                    'unit_cost' => $lineData['unit_cost'] ?? $batch->cost_price,
                    'status' => 'pending',
                ]);
            }

            return $transfer->fresh('lines');
        });
    }

    /**
     * Dispatch an Inter-Warehouse Transfer:
     * Deducts stock from origin warehouse location via TRANSFER_OUT.
     */
    public static function dispatchTransfer(StockTransfer $transfer, int $adminId): StockTransfer
    {
        return DB::transaction(function () use ($transfer, $adminId) {
            $transfer->refresh();
            if ($transfer->status !== 'draft') {
                throw new RuntimeException("Transfer #{$transfer->transfer_number} is already {$transfer->status} and cannot be dispatched.");
            }

            static::checkPeriodLock($transfer->transfer_date_ad->format('Y-m-d'));

            foreach ($transfer->lines as $line) {
                InventoryService::recordStockMovement([
                    'fiscal_year_id' => $transfer->fiscal_year_id,
                    'warehouse_id' => $transfer->from_warehouse_id,
                    'location_id' => $line->from_location_id,
                    'product_id' => $line->product_id,
                    'batch_id' => $line->batch_id,
                    'movement_type' => 'TRANSFER_OUT',
                    'quantity' => -1 * (int) $line->quantity,
                    'unit_cost' => $line->unit_cost,
                    'reference_type' => 'StockTransfer',
                    'reference_id' => $transfer->id,
                    'created_by' => $adminId,
                    'notes' => "Dispatched Transfer #{$transfer->transfer_number} to Warehouse #{$transfer->to_warehouse_id}",
                ]);

                $line->update(['status' => 'in_transit']);
            }

            $transfer->update([
                'status' => 'dispatched',
                'dispatched_by' => $adminId,
                'dispatched_at' => now(),
            ]);

            AuditService::log('stock_transfer_dispatched', $transfer, null, ['status' => 'dispatched'], 'admin', $adminId);

            return $transfer->fresh('lines');
        });
    }

    /**
     * Receive an Inter-Warehouse Transfer at destination warehouse:
     * Adds stock into destination location via TRANSFER_IN.
     *
     * @param StockTransfer $transfer
     * @param array $destinations Map of line_id => to_location_id
     * @param int $adminId
     * @return StockTransfer
     */
    public static function receiveTransfer(StockTransfer $transfer, array $destinations, int $adminId): StockTransfer
    {
        return DB::transaction(function () use ($transfer, $destinations, $adminId) {
            $transfer->refresh();
            if ($transfer->status !== 'dispatched') {
                throw new RuntimeException("Transfer #{$transfer->transfer_number} is in '{$transfer->status}' status and cannot be received.");
            }

            static::checkPeriodLock(Carbon::today()->format('Y-m-d'));

            foreach ($transfer->lines as $line) {
                $toLocationId = $destinations[$line->id] ?? $line->to_location_id;
                if (! $toLocationId) {
                    $defaultLoc = WarehouseLocation::where('warehouse_id', $transfer->to_warehouse_id)
                        ->where('is_sellable', true)
                        ->firstOrFail();
                    $toLocationId = $defaultLoc->id;
                }

                // Verify destination location belongs to destination warehouse
                WarehouseLocation::where('id', $toLocationId)
                    ->where('warehouse_id', $transfer->to_warehouse_id)
                    ->firstOrFail();

                InventoryService::recordStockMovement([
                    'fiscal_year_id' => $transfer->fiscal_year_id,
                    'warehouse_id' => $transfer->to_warehouse_id,
                    'location_id' => $toLocationId,
                    'product_id' => $line->product_id,
                    'batch_id' => $line->batch_id,
                    'movement_type' => 'TRANSFER_IN',
                    'quantity' => (int) $line->quantity,
                    'unit_cost' => $line->unit_cost,
                    'reference_type' => 'StockTransfer',
                    'reference_id' => $transfer->id,
                    'created_by' => $adminId,
                    'notes' => "Received Transfer #{$transfer->transfer_number} from Warehouse #{$transfer->from_warehouse_id}",
                ]);

                $line->update([
                    'to_location_id' => $toLocationId,
                    'status' => 'received',
                ]);
            }

            $transfer->update([
                'status' => 'received',
                'received_by' => $adminId,
                'received_at' => now(),
            ]);

            AuditService::log('stock_transfer_received', $transfer, null, ['status' => 'received'], 'admin', $adminId);

            return $transfer->fresh('lines');
        });
    }

    /**
     * Internal Warehouse Relocation (e.g. ambient rack to cold-chain room or receiving bay to storage).
     */
    public static function relocateStock(
        int $warehouseId,
        int $productId,
        int $batchId,
        int $fromLocationId,
        int $toLocationId,
        int $quantity,
        int $adminId,
        ?string $notes = null
    ): array {
        return DB::transaction(function () use ($warehouseId, $productId, $batchId, $fromLocationId, $toLocationId, $quantity, $adminId, $notes) {
            if ($fromLocationId === $toLocationId) {
                throw new RuntimeException("Source and destination locations must be distinct.");
            }

            if ($quantity <= 0) {
                throw new RuntimeException("Relocation quantity must be strictly greater than zero.");
            }

            static::checkPeriodLock(Carbon::today()->format('Y-m-d'));

            $fromLoc = WarehouseLocation::where('id', $fromLocationId)->where('warehouse_id', $warehouseId)->firstOrFail();
            $toLoc = WarehouseLocation::where('id', $toLocationId)->where('warehouse_id', $warehouseId)->firstOrFail();
            $batch = Batch::findOrFail($batchId);
            $fiscalYear = FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            // Outflow from source location
            $outMvt = InventoryService::recordStockMovement([
                'fiscal_year_id' => $fiscalYear->id,
                'warehouse_id' => $warehouseId,
                'location_id' => $fromLoc->id,
                'product_id' => $productId,
                'batch_id' => $batchId,
                'movement_type' => 'RELOCATION',
                'quantity' => -1 * $quantity,
                'unit_cost' => $batch->cost_price,
                'reference_type' => 'InternalRelocation',
                'reference_id' => null,
                'created_by' => $adminId,
                'notes' => "Relocated to '{$toLoc->name}'. " . ($notes ?? ''),
            ]);

            // Inflow to destination location
            $inMvt = InventoryService::recordStockMovement([
                'fiscal_year_id' => $fiscalYear->id,
                'warehouse_id' => $warehouseId,
                'location_id' => $toLoc->id,
                'product_id' => $productId,
                'batch_id' => $batchId,
                'movement_type' => 'RELOCATION',
                'quantity' => $quantity,
                'unit_cost' => $batch->cost_price,
                'reference_type' => 'InternalRelocation',
                'reference_id' => null,
                'created_by' => $adminId,
                'notes' => "Relocated from '{$fromLoc->name}'. " . ($notes ?? ''),
            ]);

            return [
                'out_movement' => $outMvt,
                'in_movement' => $inMvt,
                'from_location' => $fromLoc,
                'to_location' => $toLoc,
                'quantity' => $quantity,
            ];
        });
    }

    /**
     * Create a Stock Adjustment / Physical Count document (Maker workflow).
     */
    public static function createAdjustment(array $header, array $lines, int $makerId): StockAdjustment
    {
        return DB::transaction(function () use ($header, $lines, $makerId) {
            $warehouse = Warehouse::findOrFail($header['warehouse_id']);
            $dateAd = $header['adjustment_date_ad'] ?? Carbon::today()->format('Y-m-d');
            static::checkPeriodLock($dateAd);

            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            $adjustmentNumber = DocumentSequenceService::getNextNumber('ADJ', $fiscalYear);
            $adjustmentDateBs = $header['adjustment_date_bs'] ?? BsDateHelper::toBs($dateAd);

            $adjustment = StockAdjustment::create([
                'adjustment_number' => $adjustmentNumber,
                'fiscal_year_id' => $fiscalYear->id,
                'warehouse_id' => $warehouse->id,
                'adjustment_date_ad' => $dateAd,
                'adjustment_date_bs' => $adjustmentDateBs,
                'type' => $header['type'] ?? 'PHYSICAL_COUNT',
                'status' => 'draft',
                'total_variance_value' => 0.00,
                'reason' => $header['reason'] ?? null,
                'created_by' => $makerId,
            ]);

            $totalVariance = '0.00';

            foreach ($lines as $lineData) {
                $product = Product::findOrFail($lineData['product_id']);
                $batch = Batch::findOrFail($lineData['batch_id']);
                $location = WarehouseLocation::where('id', $lineData['location_id'])
                    ->where('warehouse_id', $warehouse->id)
                    ->firstOrFail();

                $bookQty = (int) $lineData['book_quantity'];
                $countedQty = (int) $lineData['counted_quantity'];
                $varianceQty = $countedQty - $bookQty;
                $unitCost = (string) ($lineData['unit_cost'] ?? $batch->cost_price);
                $lineVarianceVal = bcmul((string) $varianceQty, $unitCost, 2);

                StockAdjustmentLine::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'location_id' => $location->id,
                    'book_quantity' => $bookQty,
                    'counted_quantity' => $countedQty,
                    'variance_quantity' => $varianceQty,
                    'unit_cost' => $unitCost,
                    'variance_value' => $lineVarianceVal,
                    'reason' => $lineData['reason'] ?? null,
                ]);

                $totalVariance = bcadd($totalVariance, $lineVarianceVal, 2);
            }

            $adjustment->update(['total_variance_value' => $totalVariance]);

            return $adjustment->fresh('lines');
        });
    }

    /**
     * Approve and Post a Stock Adjustment (Maker-Checker enforcement).
     *
     * @param StockAdjustment $adjustment
     * @param int $checkerId
     * @return StockAdjustment
     * @throws RuntimeException
     */
    public static function approveAdjustment(StockAdjustment $adjustment, int $checkerId): StockAdjustment
    {
        return DB::transaction(function () use ($adjustment, $checkerId) {
            $adjustment->refresh();
            if ($adjustment->status !== 'draft') {
                throw new RuntimeException("Stock Adjustment #{$adjustment->adjustment_number} is already {$adjustment->status} and cannot be approved.");
            }

            // Maker-Checker Dual Authorization Rule
            if ($adjustment->created_by === $checkerId) {
                throw new RuntimeException("Maker-Checker violation: The user who created this adjustment (#{$adjustment->adjustment_number}) cannot approve it. A distinct checker is strictly required.");
            }

            static::checkPeriodLock($adjustment->adjustment_date_ad->format('Y-m-d'));

            foreach ($adjustment->lines as $line) {
                if ($line->variance_quantity === 0) {
                    continue;
                }

                $movementType = match ($adjustment->type) {
                    'DAMAGE_WRITE_OFF' => 'DAMAGE_WRITE_OFF',
                    'EXPIRY_DISPOSAL' => 'EXPIRED_DISPOSAL',
                    default => ($line->variance_quantity > 0 ? 'PHYSICAL_COUNT_SURPLUS' : 'PHYSICAL_COUNT_SHORTAGE'),
                };

                $outflowQty = in_array($adjustment->type, ['DAMAGE_WRITE_OFF', 'EXPIRY_DISPOSAL'])
                    ? -abs((int) $line->variance_quantity)
                    : (int) $line->variance_quantity;

                InventoryService::recordStockMovement([
                    'fiscal_year_id' => $adjustment->fiscal_year_id,
                    'warehouse_id' => $adjustment->warehouse_id,
                    'location_id' => $line->location_id,
                    'product_id' => $line->product_id,
                    'batch_id' => $line->batch_id,
                    'movement_type' => $movementType,
                    'quantity' => $outflowQty,
                    'unit_cost' => $line->unit_cost,
                    'reference_type' => 'StockAdjustment',
                    'reference_id' => $adjustment->id,
                    'created_by' => $checkerId,
                    'notes' => "Stock Adjustment #{$adjustment->adjustment_number} ({$adjustment->type})",
                ]);
            }

            $adjustment->update([
                'status' => 'approved',
                'approved_by' => $checkerId,
                'approved_at' => now(),
            ]);

            AuditService::log('stock_adjustment_approved', $adjustment, null, ['status' => 'approved'], 'admin', $checkerId);

            return $adjustment->fresh('lines');
        });
    }

    /**
     * Quarantine and Recall a Drug Batch across all warehouses.
     */
    public static function quarantineBatch(
        int $batchId,
        string $recallRef,
        string $issuedBy,
        string $reason,
        int $adminId
    ): BatchRecall {
        return DB::transaction(function () use ($batchId, $recallRef, $issuedBy, $reason, $adminId) {
            $batch = Batch::findOrFail($batchId);
            $batch->update([
                'is_quarantined' => true,
                'is_recalled' => true,
                'notes' => trim(($batch->notes ?? '') . " | RECALL: {$recallRef} - {$reason}"),
            ]);

            $fiscalYear = FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();
            $today = Carbon::today()->format('Y-m-d');
            $todayBs = BsDateHelper::toBs($today);

            // Relocate active inventory of this batch to the quarantine location in each warehouse
            $balances = InventoryBalance::where('batch_id', $batch->id)
                ->where('quantity_on_hand', '>', 0)
                ->get();

            $totalQuarantined = 0;

            foreach ($balances as $bal) {
                $currentLoc = WarehouseLocation::findOrFail($bal->location_id);
                if ($currentLoc->type === 'quarantine') {
                    $totalQuarantined += $bal->quantity_on_hand;
                    continue; // Already in quarantine rack
                }

                $whQuarantineLoc = WarehouseLocation::where('warehouse_id', $bal->warehouse_id)
                    ->where('type', 'quarantine')
                    ->first();

                if (! $whQuarantineLoc) {
                    $whQuarantineLoc = WarehouseLocation::create([
                        'warehouse_id' => $bal->warehouse_id,
                        'code' => 'LOC-QUAR-' . $bal->warehouse_id,
                        'name' => 'Auto Quarantine Hold',
                        'type' => 'quarantine',
                        'is_sellable' => false,
                        'is_available' => false,
                    ]);
                }

                $qty = $bal->quantity_on_hand;

                // Move out of ambient/storage location
                InventoryService::recordStockMovement([
                    'fiscal_year_id' => $fiscalYear->id,
                    'warehouse_id' => $bal->warehouse_id,
                    'location_id' => $currentLoc->id,
                    'product_id' => $bal->product_id,
                    'batch_id' => $batch->id,
                    'movement_type' => 'QUARANTINE_MOVE',
                    'quantity' => -1 * $qty,
                    'unit_cost' => $batch->cost_price,
                    'reference_type' => 'BatchRecall',
                    'reference_id' => null,
                    'created_by' => $adminId,
                    'notes' => "Batch Quarantine: Recall {$recallRef}",
                ]);

                // Move into quarantine isolation rack
                InventoryService::recordStockMovement([
                    'fiscal_year_id' => $fiscalYear->id,
                    'warehouse_id' => $bal->warehouse_id,
                    'location_id' => $whQuarantineLoc->id,
                    'product_id' => $bal->product_id,
                    'batch_id' => $batch->id,
                    'movement_type' => 'QUARANTINE_MOVE',
                    'quantity' => $qty,
                    'unit_cost' => $batch->cost_price,
                    'reference_type' => 'BatchRecall',
                    'reference_id' => null,
                    'created_by' => $adminId,
                    'notes' => "Batch Quarantine: Recall {$recallRef} into isolation",
                ]);

                $totalQuarantined += $qty;
            }

            $recall = BatchRecall::create([
                'batch_id' => $batch->id,
                'recall_reference' => $recallRef,
                'issued_by' => $issuedBy,
                'recall_date_ad' => $today,
                'recall_date_bs' => $todayBs,
                'reason' => $reason,
                'quarantined_quantity' => $totalQuarantined,
                'status' => 'active',
                'created_by' => $adminId,
            ]);

            AuditService::log('batch_recall_issued', $recall, null, ['status' => 'active', 'quarantined' => $totalQuarantined], 'admin', $adminId);

            return $recall;
        });
    }
}
