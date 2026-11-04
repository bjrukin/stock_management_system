<?php

namespace App\Http\Controllers\Admin\Pharma;

use App\Http\Controllers\Controller;
use App\Support\PharmaAccess;
use App\Models\BatchRecall;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class PharmaWarehouseController extends Controller
{
    public function movements(Request $request)
    {
        $data['nav'] = 'pharma_warehouse';
        $data['sub_nav'] = 'movements';
        $data['page_title'] = 'Stock Ledger (Audit Trail)';

        $query = PharmaAccess::scope(StockMovement::query())->with(['product', 'batch', 'warehouse', 'location']);

        if ($request->filled('flow_type')) {
            match ($request->flow_type) {
                'purchase' => $query->purchases(),
                'sell_return' => $query->sellReturns(),
                'sell' => $query->sales(),
                'purchase_return' => $query->purchaseReturns(),
                'expired_item' => $query->expiredItems(),
                'damage' => $query->damages(),
                'inflows' => $query->inflows(),
                'outflows' => $query->outflows(),
                default => null,
            };
        } elseif ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        $data['movements'] = $query->orderBy('id', 'desc')->paginate(30);
        $data['stockFlow'] = InventoryService::getStockFlowSummary(warehouseIds: PharmaAccess::warehouseIds());
        $data['currentFlow'] = $request->get('flow_type', 'all');

        return view('admin.pharma.warehouse.movements', $data);
    }

    public function transfers(Request $request)
    {
        $data['nav'] = 'pharma_warehouse';
        $data['sub_nav'] = 'transfers';
        $data['page_title'] = 'Inter-Warehouse Transfers';

        $data['transfers'] = PharmaAccess::scope(StockTransfer::query(), ['from_warehouse_id', 'to_warehouse_id'])->with(['fromWarehouse', 'toWarehouse', 'lines.product', 'lines.batch'])
            ->orderBy('id', 'desc')->paginate(20);

        return view('admin.pharma.warehouse.transfers', $data);
    }

    public function createTransfer()
    {
        $data['nav'] = 'pharma_warehouse';
        $data['sub_nav'] = 'transfers';
        $data['page_title'] = 'Create Inter-Warehouse Transfer';

        $data['warehouses'] = PharmaAccess::scope(\App\Models\Warehouse::query(), ['id'])->with('locations')->active()->get();
        $data['products'] = \App\Models\Product::with(['baseUnit', 'batches' => function ($q) {
            $q->active();
        }])->active()->get();

        return view('admin.pharma.warehouse.transfer_create', $data);
    }

    public function storeTransfer(Request $request)
    {
        $adminId = auth()->guard('admin')->id();
        abort_if($adminId === null, 401, 'Please log in as an admin.');

        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date_ad' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.batch_id' => 'required|exists:batches,id',
            'lines.*.from_location_id' => 'nullable|exists:warehouse_locations,id',
            'lines.*.quantity' => 'required|integer|gt:0',
        ]);

        $wh = PharmaAccess::scope(\App\Models\Warehouse::query(), ['id'])->with('locations')->find($validated['from_warehouse_id']);
        $defaultLoc = $wh->locations->first()?->id;

        $lines = [];
        foreach ($validated['lines'] as $l) {
            if (empty($l['from_location_id'])) {
                $l['from_location_id'] = $defaultLoc;
            }
            $lines[] = $l;
        }

        $header = [
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id' => $validated['to_warehouse_id'],
            'transfer_date_ad' => $validated['transfer_date_ad'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => $adminId,
        ];

        try {
            $transfer = \App\Services\WarehouseOperationService::createTransfer($header, $lines);

            return redirect()->route('admin.pharma.stock-transfer.show', $transfer->id)
                ->with('success', "Stock Transfer #{$transfer->transfer_number} created in draft status.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showTransfer(int $id)
    {
        $transfer = PharmaAccess::scope(StockTransfer::query(), ['from_warehouse_id', 'to_warehouse_id'])->with(['fromWarehouse.locations', 'toWarehouse.locations', 'lines.product', 'lines.batch', 'lines.fromLocation', 'lines.toLocation'])
            ->findOrFail($id);

        $data['nav'] = 'pharma_warehouse';
        $data['sub_nav'] = 'transfers';
        $data['page_title'] = "Stock Transfer #{$transfer->transfer_number}";
        $data['transfer'] = $transfer;

        return view('admin.pharma.warehouse.transfer_show', $data);
    }

    public function dispatchTransfer(Request $request, int $id)
    {
        $adminId = auth()->guard('admin')->id();
        abort_if($adminId === null, 401, 'Please log in as an admin.');

        $transfer = PharmaAccess::scope(StockTransfer::query(), ['from_warehouse_id', 'to_warehouse_id'])->findOrFail($id);

        try {
            \App\Services\WarehouseOperationService::dispatchTransfer($transfer, $adminId);

            return redirect()->route('admin.pharma.stock-transfer.show', $transfer->id)
                ->with('success', "Stock Transfer #{$transfer->transfer_number} dispatched from origin warehouse.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receiveTransfer(Request $request, int $id)
    {
        $adminId = auth()->guard('admin')->id();
        abort_if($adminId === null, 401, 'Please log in as an admin.');

        $transfer = PharmaAccess::scope(StockTransfer::query(), ['from_warehouse_id', 'to_warehouse_id'])->findOrFail($id);

        try {
            \App\Services\WarehouseOperationService::receiveTransfer($transfer, $request->destinations ?? [], $adminId);

            return redirect()->route('admin.pharma.stock-transfer.show', $transfer->id)
                ->with('success', "Stock Transfer #{$transfer->transfer_number} received at destination warehouse.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function adjustments(Request $request)
    {
        $data['nav'] = 'pharma_warehouse';
        $data['sub_nav'] = 'adjustments';
        $data['page_title'] = 'Stock Adjustments (Maker-Checker)';

        $data['adjustments'] = PharmaAccess::scope(StockAdjustment::query())->with(['warehouse', 'creator', 'approver', 'lines.product', 'lines.batch'])
            ->orderBy('id', 'desc')->paginate(20);

        return view('admin.pharma.warehouse.adjustments', $data);
    }

    public function createAdjustment()
    {
        $data['nav'] = 'pharma_warehouse';
        $data['sub_nav'] = 'adjustments';
        $data['page_title'] = 'Create Stock Adjustment';

        $data['warehouses'] = PharmaAccess::scope(\App\Models\Warehouse::query(), ['id'])->with('locations')->active()->get();
        $data['products'] = \App\Models\Product::with(['baseUnit', 'batches'])->active()->get();

        return view('admin.pharma.warehouse.adjustment_create', $data);
    }

    public function storeAdjustment(Request $request)
    {
        $adminId = auth()->guard('admin')->id();
        abort_if($adminId === null, 401, 'Please log in as an admin.');

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_date_ad' => 'required|date',
            'type' => 'required|in:PHYSICAL_COUNT,DAMAGE_WRITE_OFF,EXPIRY_DISPOSAL,SAMPLE_ISSUE',
            'reason' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.batch_id' => 'required|exists:batches,id',
            'lines.*.location_id' => 'nullable|exists:warehouse_locations,id',
            'lines.*.book_quantity' => 'required|integer|min:0',
            'lines.*.counted_quantity' => 'required|integer|min:0',
            'lines.*.reason' => 'nullable|string',
        ]);

        $wh = PharmaAccess::scope(\App\Models\Warehouse::query(), ['id'])->with('locations')->find($validated['warehouse_id']);
        $defaultLoc = $wh->locations->first()?->id;

        $lines = [];
        foreach ($validated['lines'] as $l) {
            if (empty($l['location_id'])) {
                $l['location_id'] = $defaultLoc;
            }
            $lines[] = $l;
        }

        $header = [
            'warehouse_id' => $validated['warehouse_id'],
            'adjustment_date_ad' => $validated['adjustment_date_ad'],
            'type' => $validated['type'],
            'reason' => $validated['reason'] ?? null,
        ];

        try {
            $adjustment = \App\Services\WarehouseOperationService::createAdjustment($header, $lines, $adminId);

            return redirect()->route('admin.pharma.stock-adjustment.show', $adjustment->id)
                ->with('success', "Stock Adjustment #{$adjustment->adjustment_number} created. Awaiting approval by independent checker.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showAdjustment(int $id)
    {
        $adjustment = PharmaAccess::scope(StockAdjustment::query())->with(['warehouse', 'creator', 'approver', 'lines.product', 'lines.batch', 'lines.location'])
            ->findOrFail($id);

        $data['nav'] = 'pharma_warehouse';
        $data['sub_nav'] = 'adjustments';
        $data['page_title'] = "Stock Adjustment #{$adjustment->adjustment_number}";
        $data['adjustment'] = $adjustment;

        return view('admin.pharma.warehouse.adjustment_show', $data);
    }

    public function approveAdjustment(Request $request, int $id)
    {
        $adjustment = PharmaAccess::scope(StockAdjustment::query())->findOrFail($id);

        $admin = auth()->guard('admin')->user();
        abort_if($admin === null, 401, 'Please log in as an admin.');
        abort_unless($admin->hasPermission('StockAdjustment', 'approve'), 403);
        $checkerId = (int) $admin->getAuthIdentifier();
        abort_if((int) $adjustment->created_by === $checkerId, 403,
            'The maker cannot approve their own stock adjustment. A different authorized admin must approve it.');

        try {
            \App\Services\WarehouseOperationService::approveAdjustment($adjustment, $checkerId);

            return redirect()->route('admin.pharma.stock-adjustment.show', $adjustment->id)
                ->with('success', "Stock Adjustment #{$adjustment->adjustment_number} approved and inventory balance updated.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function recalls(Request $request)
    {
        $data['nav'] = 'pharma_warehouse';
        $data['sub_nav'] = 'recalls';
        $data['page_title'] = 'DDA Batch Recalls & Quarantine';

        $data['recalls'] = BatchRecall::with(['batch.product', 'creator'])
            ->when(PharmaAccess::warehouseIds() !== null, fn ($q) => $q->whereHas('batch.inventoryBalances', fn ($q) => PharmaAccess::scope($q)))
            ->orderBy('id', 'desc')->paginate(20);

        return view('admin.pharma.warehouse.recalls', $data);
    }

    public function reconcile(Request $request)
    {
        $results = InventoryService::reconcileStock();

        return response()->json([
            'status' => 'success',
            'message' => 'Stock Ledger Reconciled Successfully: ' . $results['discrepancies_found'] . ' discrepancies found, ' . $results['discrepancies_fixed'] . ' auto-corrected.',
            'data' => $results,
        ]);
    }
}

