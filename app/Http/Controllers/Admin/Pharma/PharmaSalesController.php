<?php

namespace App\Http\Controllers\Admin\Pharma;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\SalesService;
use App\Support\PharmaAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PharmaSalesController extends Controller
{
    public function invoices(Request $request)
    {
        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_invoices';
        $data['page_title'] = 'Sales Invoices';

        $query = PharmaAccess::scope(SaleInvoice::query())->with(['customer', 'warehouse', 'lines.product', 'lines.batch']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data['invoices'] = $query->orderBy('id', 'desc')->paginate(20);
        $data['customers'] = Customer::all();

        return view('admin.pharma.sales.invoices', $data);
    }

    public function createInvoice()
    {
        return $this->invoiceForm();
    }

    public function editInvoice(int $id)
    {
        $invoice = PharmaAccess::scope(SaleInvoice::query())->with('lines')->findOrFail($id);
        abort_unless($invoice->status === 'draft', 409, 'Only draft documents can be edited.');

        return $this->invoiceForm($invoice);
    }

    private function invoiceForm(?SaleInvoice $invoice = null)
    {
        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_invoices';
        $data['page_title'] = $invoice ? 'Edit Sales Invoice' : 'Create Sales Invoice';

        $data['customers'] = Customer::where(fn ($q) => $q->where('is_active', true)->orWhere('id', $invoice?->customer_id))->get();
        $data['warehouses'] = PharmaAccess::scope(Warehouse::query(), ['id'])->with('locations')->where(fn ($q) => $q->where('is_active', true)->orWhere('id', $invoice?->warehouse_id))->get();
        $data['products'] = Product::with(['baseUnit', 'batches' => function ($q) use ($invoice) {
            $q->where(fn ($q) => $q->sellable()->orWhereIn('id', $invoice?->lines->pluck('batch_id') ?? []));
        }])->where(fn ($q) => $q->where('is_active', true)->orWhereIn('id', $invoice?->lines->pluck('product_id') ?? []))->get();
        $data['units'] = Unit::all();

        $data['invoice'] = $invoice;
        $data['initialItems'] = $invoice ? $invoice->lines->map(fn ($line) => $line->only(['product_id', 'batch_id', 'location_id', 'unit_id', 'qty', 'unit_price', 'discount_pct', 'tax_rate', 'tax_category_id']))->all() : [];

        return view('admin.pharma.sales.invoice_create', $data);
    }

    public function storeInvoice(Request $request)
    {
        return $this->saveInvoice($request);
    }

    public function updateInvoice(Request $request, int $id)
    {
        $invoice = PharmaAccess::scope(SaleInvoice::query())->findOrFail($id);

        return $this->saveInvoice($request, $invoice);
    }

    public function destroyInvoice(int $id)
    {
        $invoice = PharmaAccess::scope(SaleInvoice::query())->findOrFail($id);
        try {
            SalesService::deleteInvoice($invoice, auth()->guard('admin')->id());

            return redirect()->route('admin.pharma.sales-invoices')->with('success', 'Draft deleted successfully.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function saveInvoice(Request $request, ?SaleInvoice $invoice = null)
    {
        $adminId = auth()->guard('admin')->id();
        abort_if($adminId === null, 401, 'Please log in as an admin.');

        $validated = $request->validate([
            'auto_fefo' => 'sometimes|boolean',
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'invoice_date_ad' => 'required|date',
            'payment_method' => 'required|in:cash,credit,bank_transfer',
            'delivery_charge' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_id' => [Rule::requiredIf(! $request->boolean('auto_fefo', true)), 'nullable', 'exists:batches,id'],
            'items.*.tax_category_id' => 'nullable|exists:tax_categories,id',
            'items.*.location_id' => 'nullable|exists:warehouse_locations,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_pct' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $autoFefo = $request->boolean('auto_fefo', true);

        // Fallback default location if not selected
        $wh = PharmaAccess::scope(Warehouse::query(), ['id'])->with('locations')->find($validated['warehouse_id']);
        $defaultLoc = $wh->locations->first()?->id;

        $items = [];
        foreach ($validated['items'] as $item) {
            if (empty($item['location_id'])) {
                $item['location_id'] = $defaultLoc;
            }
            $items[] = $item;
        }

        $header = [
            'customer_id' => $validated['customer_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'invoice_date_ad' => $validated['invoice_date_ad'],
            'payment_method' => $validated['payment_method'],
            'delivery_charge' => $validated['delivery_charge'] ?? 0.00,
            'round_off' => $validated['round_off'] ?? 0.00,
            'paid_amount' => $validated['paid_amount'] ?? 0.00,
            'created_by' => $adminId,
            'notes' => $validated['notes'] ?? null,
        ];

        try {
            $invoice = $invoice
                ? SalesService::updateInvoice($invoice, $header, $items, $autoFefo, $adminId)
                : SalesService::createInvoice($header, $items, $autoFefo);

            return redirect()->route('admin.pharma.sale-invoice.show', $invoice->id)
                ->with('success', "Sales Invoice #{$invoice->invoice_number} saved successfully.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showInvoice(int $id)
    {
        $invoice = PharmaAccess::scope(SaleInvoice::query())->with(['customer', 'warehouse', 'lines.product', 'lines.batch', 'lines.unit', 'lines.location'])
            ->findOrFail($id);

        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_invoices';
        $data['page_title'] = "Sales Invoice #{$invoice->invoice_number}";
        $data['invoice'] = $invoice;

        return view('admin.pharma.sales.invoice_show', $data);
    }

    public function postInvoice(Request $request, int $id)
    {
        $adminId = auth()->guard('admin')->id();
        abort_if($adminId === null, 401, 'Please log in as an admin.');

        $invoice = PharmaAccess::scope(SaleInvoice::query())->findOrFail($id);

        try {
            SalesService::postInvoice($invoice, $adminId);

            return redirect()
                ->route('admin.pharma.sale-invoice.show', $invoice->id)
                ->with('success', "Sales Invoice #{$invoice->invoice_number} posted successfully. Stock dispatched and customer ledger updated.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function createReturn()
    {
        return $this->returnForm();
    }

    public function editReturn(int $id)
    {
        $saleReturn = PharmaAccess::scope(SaleReturn::query())->with('lines')->findOrFail($id);
        abort_unless($saleReturn->status === 'draft', 409, 'Only draft documents can be edited.');

        return $this->returnForm($saleReturn);
    }

    private function returnForm(?SaleReturn $saleReturn = null)
    {
        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_returns';
        $data['page_title'] = $saleReturn ? 'Edit Sales Return' : 'Create Sales Return';

        $data['customers'] = Customer::where(fn ($q) => $q->where('is_active', true)->orWhere('id', $saleReturn?->customer_id))->get();
        $data['warehouses'] = PharmaAccess::scope(Warehouse::query(), ['id'])->with('locations')->where(fn ($q) => $q->where('is_active', true)->orWhere('id', $saleReturn?->warehouse_id))->get();
        $data['invoices'] = PharmaAccess::scope(SaleInvoice::query())->where('status', 'posted')->orderByDesc('id')->take(50)->get();
        if ($saleReturn?->sale_invoice_id && ! $data['invoices']->contains('id', $saleReturn->sale_invoice_id)) {
            $original = PharmaAccess::scope(SaleInvoice::query())->find($saleReturn->sale_invoice_id);
            if ($original) {
                $data['invoices']->push($original);
            }
        }
        $data['products'] = Product::with(['baseUnit', 'batches'])->where(fn ($q) => $q->where('is_active', true)->orWhereIn('id', $saleReturn?->lines->pluck('product_id') ?? []))->get();
        $data['units'] = Unit::all();

        $data['return'] = $saleReturn;
        $data['initialLines'] = $saleReturn ? $saleReturn->lines->map(fn ($line) => $line->only(['product_id', 'batch_id', 'location_id', 'unit_id', 'returned_qty', 'unit_price', 'condition', 'tax_amount', 'conversion_factor', 'sale_invoice_line_id']))->all() : [];

        return view('admin.pharma.sales.return_create', $data);
    }

    public function storeReturn(Request $request)
    {
        return $this->saveReturn($request);
    }

    public function updateReturn(Request $request, int $id)
    {
        $saleReturn = PharmaAccess::scope(SaleReturn::query())->findOrFail($id);

        return $this->saveReturn($request, $saleReturn);
    }

    public function destroyReturn(int $id)
    {
        $saleReturn = PharmaAccess::scope(SaleReturn::query())->findOrFail($id);
        try {
            SalesService::deleteSaleReturn($saleReturn, auth()->guard('admin')->id());

            return redirect()->route('admin.pharma.sales-returns')->with('success', 'Draft deleted successfully.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function saveReturn(Request $request, ?SaleReturn $saleReturn = null)
    {
        $adminId = auth()->guard('admin')->id();
        abort_if($adminId === null, 401, 'Please log in as an admin.');

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'sale_invoice_id' => 'nullable|exists:sale_invoices,id',
            'return_date_ad' => 'required|date',
            'reason' => 'nullable|string',
            'credit_note_issued' => 'nullable|boolean',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.batch_id' => 'required|exists:batches,id',
            'lines.*.location_id' => 'nullable|exists:warehouse_locations,id',
            'lines.*.tax_amount' => 'nullable|numeric|min:0',
            'lines.*.sale_invoice_line_id' => 'nullable|exists:sale_invoice_lines,id',
            'lines.*.unit_id' => 'required|exists:units,id',
            'lines.*.returned_qty' => 'required|numeric|gt:0',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.condition' => 'nullable|in:saleable,damaged,expired',
        ]);

        $wh = PharmaAccess::scope(Warehouse::query(), ['id'])->with('locations')->find($validated['warehouse_id']);
        $defaultLoc = $wh->locations->first()?->id;

        $lines = [];
        foreach ($validated['lines'] as $l) {
            if (empty($l['location_id'])) {
                $l['location_id'] = $defaultLoc;
            }
            $lines[] = $l;
        }

        $header = [
            'customer_id' => $validated['customer_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'sale_invoice_id' => $validated['sale_invoice_id'] ?? null,
            'return_date_ad' => $validated['return_date_ad'],
            'reason' => $validated['reason'] ?? null,
            'credit_note_issued' => $request->boolean('credit_note_issued', true),
            'created_by' => $adminId,
        ];

        try {
            $return = $saleReturn
                ? SalesService::updateSaleReturn($saleReturn, $header, $lines, $adminId)
                : SalesService::createSaleReturn($header, $lines);

            return redirect()->route('admin.pharma.sales-return.show', $return->id)
                ->with('success', "Sales Return #{$return->return_number} saved successfully.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showReturn(int $id)
    {
        $return = PharmaAccess::scope(SaleReturn::query())->with(['customer', 'warehouse', 'saleInvoice', 'lines.product', 'lines.batch', 'lines.unit'])
            ->findOrFail($id);

        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_returns';
        $data['page_title'] = "Sales Return #{$return->return_number}";
        $data['return'] = $return;

        return view('admin.pharma.sales.return_show', $data);
    }

    public function postReturn(Request $request, int $id)
    {
        $adminId = auth()->guard('admin')->id();
        abort_if($adminId === null, 401, 'Please log in as an admin.');

        $return = PharmaAccess::scope(SaleReturn::query())->findOrFail($id);

        try {
            SalesService::postSaleReturn($return, $adminId);

            return redirect()->route('admin.pharma.sales-return.show', $return->id)
                ->with('success', "Sales Return #{$return->return_number} posted successfully. Stock restocked and credit note applied.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function printInvoice(int $id)
    {
        $invoice = PharmaAccess::scope(SaleInvoice::query())->with(['customer', 'warehouse', 'lines.product', 'lines.batch'])->findOrFail($id);
        $pdf = SalesService::generateInvoicePdf($invoice);

        return $pdf->stream('invoice-'.$invoice->invoice_number.'.pdf');
    }

    public function returns(Request $request)
    {
        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_returns';
        $data['page_title'] = 'Sales Returns';

        $query = PharmaAccess::scope(SaleReturn::query())->with(['customer', 'warehouse', 'saleInvoice', 'lines.product', 'lines.batch']);

        $data['returns'] = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.pharma.sales.returns', $data);
    }
}
