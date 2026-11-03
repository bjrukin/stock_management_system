<?php

namespace App\Http\Controllers\Admin\Pharma;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\TaxCategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\PurchasingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PharmaPurchasingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Purchase Orders
    |--------------------------------------------------------------------------
    */

    public function orders(Request $request)
    {
        $data['nav'] = 'pharma_purchasing';
        $data['sub_nav'] = 'purchase_orders';
        $data['page_title'] = 'Purchase Orders';

        $query = PurchaseOrder::with(['supplier', 'warehouse', 'lines.product']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data['orders'] = $query->orderBy('id', 'desc')->paginate(20);
        $data['suppliers'] = Supplier::where('is_active', true)->get();

        return view('admin.pharma.purchasing.orders', $data);
    }

    public function createOrder(Request $request)
    {
        $data['nav'] = 'pharma_purchasing';
        $data['sub_nav'] = 'purchase_orders';
        $data['page_title'] = 'Create Purchase Order';

        $data['suppliers'] = Supplier::where('is_active', true)->orderBy('name')->get();
        $data['warehouses'] = Warehouse::where('is_active', true)->orderBy('name')->get();
        $data['products'] = Product::with(['baseUnit', 'packagingLevels.unit', 'taxCategory'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $data['units'] = Unit::orderBy('name')->get();
        $data['taxCategories'] = TaxCategory::where('is_active', true)->get();
        $data['defaultDate'] = Carbon::today()->format('Y-m-d');

        return view('admin.pharma.purchasing.order_create', $data);
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date_ad' => 'required|date',
            'expected_delivery_date_ad' => 'nullable|date',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.unit_id' => 'required|exists:units,id',
            'lines.*.ordered_qty' => 'required|numeric|min:0.0001',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            $adminId = Auth::guard('admin')->id();

            $header = [
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'order_date_ad' => $request->order_date_ad,
                'expected_delivery_date_ad' => $request->expected_delivery_date_ad,
                'notes' => $request->notes,
                'created_by' => $adminId,
            ];

            $lines = [];
            foreach ($request->lines as $line) {
                if (empty($line['product_id']) || empty($line['ordered_qty'])) {
                    continue;
                }
                $lines[] = [
                    'product_id' => $line['product_id'],
                    'unit_id' => $line['unit_id'],
                    'ordered_qty' => $line['ordered_qty'],
                    'conversion_factor' => $line['conversion_factor'] ?? 1.0,
                    'unit_price' => $line['unit_price'],
                    'discount_pct' => $line['discount_pct'] ?? 0,
                    'tax_category_id' => $line['tax_category_id'] ?? null,
                    'tax_rate' => $line['tax_rate'] ?? 0,
                ];
            }

            if (empty($lines)) {
                return back()->withInput()->with('error', 'Please provide at least one valid item line.');
            }

            $po = PurchasingService::createPurchaseOrder($header, $lines);

            if ($request->input('action') === 'save_and_approve') {
                PurchasingService::approvePurchaseOrder($po, $adminId);
                return redirect()->route('admin.pharma.purchase-orders')
                    ->with('success', "Purchase Order #{$po->po_number} created and approved successfully.");
            }

            return redirect()->route('admin.pharma.purchase-order.show', $po->id)
                ->with('success', "Purchase Order #{$po->po_number} created as draft.");
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Error creating Purchase Order: ' . $e->getMessage());
        }
    }

    public function showOrder(int $id)
    {
        $data['nav'] = 'pharma_purchasing';
        $data['sub_nav'] = 'purchase_orders';
        $data['page_title'] = 'Purchase Order Details';

        $data['order'] = PurchaseOrder::with(['supplier', 'warehouse', 'lines.product.baseUnit', 'lines.unit', 'lines.taxCategory'])
            ->findOrFail($id);

        return view('admin.pharma.purchasing.order_show', $data);
    }

    public function approveOrder(int $id)
    {
        try {
            $po = PurchaseOrder::findOrFail($id);
            PurchasingService::approvePurchaseOrder($po, Auth::guard('admin')->id());

            return redirect()->route('admin.pharma.purchase-orders')
                ->with('success', "Purchase Order #{$po->po_number} has been approved successfully.");
        } catch (Throwable $e) {
            return back()->with('error', 'Error approving Purchase Order: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Goods Receipts (GRN)
    |--------------------------------------------------------------------------
    */

    public function receipts(Request $request)
    {
        $data['nav'] = 'pharma_purchasing';
        $data['sub_nav'] = 'goods_receipts';
        $data['page_title'] = 'Goods Receipts (GRN)';

        $query = GoodsReceipt::with(['supplier', 'warehouse', 'purchaseOrder', 'lines.product', 'lines.batch']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data['receipts'] = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.pharma.purchasing.receipts', $data);
    }

    public function createReceipt(Request $request)
    {
        $data['nav'] = 'pharma_purchasing';
        $data['sub_nav'] = 'goods_receipts';
        $data['page_title'] = 'Add Goods Receipt (GRN)';

        $data['suppliers'] = Supplier::where('is_active', true)->orderBy('name')->get();
        $data['warehouses'] = Warehouse::where('is_active', true)->orderBy('name')->get();
        $data['locations'] = WarehouseLocation::where('is_available', true)->orderBy('code')->get();
        $data['products'] = Product::with(['baseUnit', 'packagingLevels.unit', 'taxCategory'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $data['units'] = Unit::orderBy('name')->get();
        $data['taxCategories'] = TaxCategory::where('is_active', true)->get();
        $data['approvedOrders'] = PurchaseOrder::whereIn('status', ['approved', 'partially_received'])
            ->with(['supplier', 'warehouse', 'lines.product', 'lines.unit'])
            ->orderByDesc('id')
            ->get();
        $data['defaultDate'] = Carbon::today()->format('Y-m-d');

        // Optional prefill from PO
        $data['selectedPo'] = null;
        if ($request->filled('po_id')) {
            $data['selectedPo'] = PurchaseOrder::with(['lines.product.baseUnit', 'lines.unit'])
                ->where('id', $request->po_id)
                ->first();
        }

        return view('admin.pharma.purchasing.receipt_create', $data);
    }

    public function storeReceipt(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'receipt_date_ad' => 'required|date',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.batch_number' => 'required|string|max:100',
            'lines.*.expiry_date_ad' => 'required|date|after:today',
            'lines.*.location_id' => 'required|exists:warehouse_locations,id',
            'lines.*.unit_id' => 'required|exists:units,id',
            'lines.*.received_qty' => 'required|numeric|min:0.0001',
            'lines.*.unit_cost' => 'required|numeric|min:0',
            'lines.*.mrp' => 'nullable|numeric|min:0',
            'lines.*.selling_price' => 'nullable|numeric|min:0',
            'lines.*.wholesale_price' => 'nullable|numeric|min:0',
            'lines.*.tax_category_id' => 'nullable|exists:tax_categories,id',
            'lines.*.tax_rate' => 'nullable|numeric|min:0',
        ], [
            'lines.*.expiry_date_ad.after' => 'Pharmaceutical expiry date must be a future date.',
            'lines.*.batch_number.required' => 'Batch Number is strictly required for pharmaceutical items.',
        ]);

        try {
            $adminId = Auth::guard('admin')->id();

            $header = [
                'purchase_order_id' => $request->purchase_order_id ?: null,
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'supplier_invoice_date_ad' => $request->supplier_invoice_date_ad,
                'receipt_date_ad' => $request->receipt_date_ad,
                'freight_amount' => $request->freight_amount ?? '0.00',
                'handling_charges' => $request->handling_charges ?? '0.00',
                'other_charges' => $request->other_charges ?? '0.00',
                'notes' => $request->notes,
                'created_by' => $adminId,
            ];

            $lines = [];
            foreach ($request->lines as $line) {
                if (empty($line['product_id']) || empty($line['received_qty'])) {
                    continue;
                }
                $lines[] = [
                    'purchase_order_line_id' => $line['purchase_order_line_id'] ?? null,
                    'product_id' => $line['product_id'],
                    'batch_number' => trim($line['batch_number']),
                    'mfg_date_ad' => $line['mfg_date_ad'] ?? null,
                    'expiry_date_ad' => $line['expiry_date_ad'],
                    'location_id' => $line['location_id'],
                    'unit_id' => $line['unit_id'],
                    'received_qty' => $line['received_qty'],
                    'conversion_factor' => $line['conversion_factor'] ?? 1.0,
                    'rejected_base_qty' => $line['rejected_base_qty'] ?? 0,
                    'unit_cost' => $line['unit_cost'],
                    'mrp' => $line['mrp'] ?? 0.00,
                    'selling_price' => $line['selling_price'] ?? 0.00,
                    'wholesale_price' => $line['wholesale_price'] ?? 0.00,
                    'discount_pct' => $line['discount_pct'] ?? 0,
                    'tax_category_id' => $line['tax_category_id'] ?? null,
                    'tax_rate' => $line['tax_rate'] ?? 0,
                ];
            }

            if (empty($lines)) {
                return back()->withInput()->with('error', 'Please provide at least one valid item line.');
            }

            $grn = PurchasingService::createGoodsReceipt($header, $lines);

            if ($request->input('action') === 'save_and_post') {
                PurchasingService::postGoodsReceipt($grn, $adminId);
                return redirect()->route('admin.pharma.goods-receipts')
                    ->with('success', "Goods Receipt #{$grn->grn_number} posted! Inventory increased and batches activated.");
            }

            return redirect()->route('admin.pharma.goods-receipt.show', $grn->id)
                ->with('success', "Goods Receipt #{$grn->grn_number} created as draft.");
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Error recording Goods Receipt: ' . $e->getMessage());
        }
    }

    public function showReceipt(int $id)
    {
        $data['nav'] = 'pharma_purchasing';
        $data['sub_nav'] = 'goods_receipts';
        $data['page_title'] = 'Goods Receipt Details';

        $data['receipt'] = GoodsReceipt::with([
            'supplier',
            'warehouse',
            'purchaseOrder',
            'lines.product.baseUnit',
            'lines.unit',
            'lines.location',
            'lines.batch'
        ])->findOrFail($id);

        return view('admin.pharma.purchasing.receipt_show', $data);
    }

    public function postReceipt(int $id)
    {
        try {
            $grn = GoodsReceipt::findOrFail($id);
            PurchasingService::postGoodsReceipt($grn, Auth::guard('admin')->id());

            return redirect()->route('admin.pharma.goods-receipts')
                ->with('success', "Goods Receipt #{$grn->grn_number} successfully posted to inventory & supplier ledger.");
        } catch (Throwable $e) {
            return back()->with('error', 'Error posting Goods Receipt: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Purchase Returns
    |--------------------------------------------------------------------------
    */

    public function returns(Request $request)
    {
        $data['nav'] = 'pharma_purchasing';
        $data['sub_nav'] = 'purchase_returns';
        $data['page_title'] = 'Purchase Returns';

        $query = PurchaseReturn::with(['supplier', 'warehouse', 'goodsReceipt', 'lines.product', 'lines.batch']);

        $data['returns'] = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.pharma.purchasing.returns', $data);
    }

    public function createReturn(Request $request)
    {
        $data['nav'] = 'pharma_purchasing';
        $data['sub_nav'] = 'purchase_returns';
        $data['page_title'] = 'Create Purchase Return';

        $data['suppliers'] = Supplier::where('is_active', true)->orderBy('name')->get();
        $data['warehouses'] = Warehouse::where('is_active', true)->orderBy('name')->get();
        $data['locations'] = WarehouseLocation::where('is_available', true)->orderBy('code')->get();
        $data['products'] = Product::with(['baseUnit', 'batches'])->where('is_active', true)->orderBy('name')->get();
        $data['units'] = Unit::orderBy('name')->get();
        $data['postedReceipts'] = GoodsReceipt::where('status', 'posted')
            ->with(['supplier', 'warehouse', 'lines.product', 'lines.batch', 'lines.unit'])
            ->orderByDesc('id')
            ->get();
        $data['batches'] = Batch::with(['product.baseUnit'])->orderByDesc('id')->get();
        $data['defaultDate'] = Carbon::today()->format('Y-m-d');

        // Optional prefill from GRN
        $data['selectedGrn'] = null;
        if ($request->filled('grn_id')) {
            $data['selectedGrn'] = GoodsReceipt::with(['lines.product', 'lines.batch', 'lines.unit', 'lines.location'])
                ->where('id', $request->grn_id)
                ->first();
        }

        return view('admin.pharma.purchasing.return_create', $data);
    }

    public function storeReturn(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date_ad' => 'required|date',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.batch_id' => 'required|exists:batches,id',
            'lines.*.location_id' => 'required|exists:warehouse_locations,id',
            'lines.*.unit_id' => 'required|exists:units,id',
            'lines.*.returned_qty' => 'required|numeric|min:0.0001',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            $adminId = Auth::guard('admin')->id();

            $header = [
                'goods_receipt_id' => $request->goods_receipt_id ?: null,
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'return_date_ad' => $request->return_date_ad,
                'reason' => $request->reason,
                'created_by' => $adminId,
            ];

            $lines = [];
            foreach ($request->lines as $line) {
                if (empty($line['product_id']) || empty($line['returned_qty'])) {
                    continue;
                }
                $lines[] = [
                    'goods_receipt_line_id' => $line['goods_receipt_line_id'] ?? null,
                    'product_id' => $line['product_id'],
                    'batch_id' => $line['batch_id'],
                    'location_id' => $line['location_id'],
                    'unit_id' => $line['unit_id'],
                    'returned_qty' => $line['returned_qty'],
                    'conversion_factor' => $line['conversion_factor'] ?? 1.0,
                    'unit_price' => $line['unit_price'],
                    'tax_amount' => $line['tax_amount'] ?? '0.00',
                ];
            }

            if (empty($lines)) {
                return back()->withInput()->with('error', 'Please provide at least one valid item line to return.');
            }

            $prtn = PurchasingService::createPurchaseReturn($header, $lines);

            if ($request->input('action') === 'save_and_post') {
                PurchasingService::postPurchaseReturn($prtn, $adminId);
                return redirect()->route('admin.pharma.purchase-returns')
                    ->with('success', "Purchase Return #{$prtn->return_number} posted! Stock deducted and debit note recorded.");
            }

            return redirect()->route('admin.pharma.purchase-return.show', $prtn->id)
                ->with('success', "Purchase Return #{$prtn->return_number} created as draft.");
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Error creating Purchase Return: ' . $e->getMessage());
        }
    }

    public function showReturn(int $id)
    {
        $data['nav'] = 'pharma_purchasing';
        $data['sub_nav'] = 'purchase_returns';
        $data['page_title'] = 'Purchase Return Details';

        $data['return'] = PurchaseReturn::with([
            'supplier',
            'warehouse',
            'goodsReceipt',
            'lines.product.baseUnit',
            'lines.unit',
            'lines.batch',
            'lines.location'
        ])->findOrFail($id);

        return view('admin.pharma.purchasing.return_show', $data);
    }

    public function postReturn(int $id)
    {
        try {
            $prtn = PurchaseReturn::findOrFail($id);
            PurchasingService::postPurchaseReturn($prtn, Auth::guard('admin')->id());

            return redirect()->route('admin.pharma.purchase-returns')
                ->with('success', "Purchase Return #{$prtn->return_number} posted successfully.");
        } catch (Throwable $e) {
            return back()->with('error', 'Error posting Purchase Return: ' . $e->getMessage());
        }
    }
}
