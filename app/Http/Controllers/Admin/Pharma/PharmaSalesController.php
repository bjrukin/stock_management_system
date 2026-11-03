<?php

namespace App\Http\Controllers\Admin\Pharma;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use App\Services\SalesService;
use Illuminate\Http\Request;

class PharmaSalesController extends Controller
{
    public function invoices(Request $request)
    {
        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_invoices';
        $data['page_title'] = 'Sales Invoices';

        $query = SaleInvoice::with(['customer', 'warehouse', 'lines.product', 'lines.batch']);

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
        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_invoices';
        $data['page_title'] = 'Create Sales Invoice';

        $data['customers'] = Customer::active()->get();
        $data['warehouses'] = \App\Models\Warehouse::with('locations')->active()->get();
        $data['products'] = \App\Models\Product::with(['baseUnit', 'batches' => function ($q) {
            $q->sellable();
        }])->active()->get();
        $data['units'] = \App\Models\Unit::all();

        return view('admin.pharma.sales.invoice_create', $data);
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
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
            'items.*.batch_id' => 'nullable|exists:batches,id',
            'items.*.location_id' => 'nullable|exists:warehouse_locations,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_pct' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $autoFefo = $request->boolean('auto_fefo', true);

        // Fallback default location if not selected
        $wh = \App\Models\Warehouse::with('locations')->find($validated['warehouse_id']);
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
            'created_by' => auth()->id() ?? 1,
            'notes' => $validated['notes'] ?? null,
        ];

        try {
            $invoice = SalesService::createInvoice($header, $items, $autoFefo);

            return redirect()->route('admin.pharma.sale-invoice.show', $invoice->id)
                ->with('success', "Sales Invoice #{$invoice->invoice_number} created successfully.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showInvoice(int $id)
    {
        $invoice = SaleInvoice::with(['customer', 'warehouse', 'lines.product', 'lines.batch', 'lines.unit', 'lines.location'])
            ->findOrFail($id);

        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_invoices';
        $data['page_title'] = "Sales Invoice #{$invoice->invoice_number}";
        $data['invoice'] = $invoice;

        return view('admin.pharma.sales.invoice_show', $data);
    }

    public function postInvoice(Request $request, int $id)
    {
        $invoice = SaleInvoice::findOrFail($id);

        try {
            SalesService::postInvoice($invoice, auth()->id() ?? 1);

            return redirect()->route('admin.pharma.sale-invoice.show', $invoice->id)
                ->with('success', "Sales Invoice #{$invoice->invoice_number} posted successfully. Stock dispatched and customer ledger updated.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function createReturn()
    {
        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_returns';
        $data['page_title'] = 'Create Sales Return';

        $data['customers'] = Customer::active()->get();
        $data['warehouses'] = \App\Models\Warehouse::with('locations')->active()->get();
        $data['invoices'] = SaleInvoice::where('status', 'posted')->orderByDesc('id')->take(50)->get();
        $data['products'] = \App\Models\Product::with(['baseUnit', 'batches'])->active()->get();
        $data['units'] = \App\Models\Unit::all();

        return view('admin.pharma.sales.return_create', $data);
    }

    public function storeReturn(Request $request)
    {
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
            'lines.*.unit_id' => 'required|exists:units,id',
            'lines.*.returned_qty' => 'required|numeric|gt:0',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.condition' => 'nullable|in:saleable,damaged,expired',
        ]);

        $wh = \App\Models\Warehouse::with('locations')->find($validated['warehouse_id']);
        $defaultLoc = $wh->locations->first()?->id;

        $lines = [];
        foreach ($validated['lines'] as $l) {
            if (empty($l['location_id'])) {
                $l['location_id'] = $defaultLoc;
            }
            $l['conversion_factor'] = 1.0;
            $lines[] = $l;
        }

        $header = [
            'customer_id' => $validated['customer_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'sale_invoice_id' => $validated['sale_invoice_id'] ?? null,
            'return_date_ad' => $validated['return_date_ad'],
            'reason' => $validated['reason'] ?? null,
            'credit_note_issued' => $request->boolean('credit_note_issued', true),
            'created_by' => auth()->id() ?? 1,
        ];

        try {
            $return = SalesService::createSaleReturn($header, $lines);

            return redirect()->route('admin.pharma.sales-return.show', $return->id)
                ->with('success', "Sales Return #{$return->return_number} created successfully.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showReturn(int $id)
    {
        $return = SaleReturn::with(['customer', 'warehouse', 'saleInvoice', 'lines.product', 'lines.batch', 'lines.unit'])
            ->findOrFail($id);

        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_returns';
        $data['page_title'] = "Sales Return #{$return->return_number}";
        $data['return'] = $return;

        return view('admin.pharma.sales.return_show', $data);
    }

    public function postReturn(Request $request, int $id)
    {
        $return = SaleReturn::findOrFail($id);

        try {
            SalesService::postSaleReturn($return, auth()->id() ?? 1);

            return redirect()->route('admin.pharma.sales-return.show', $return->id)
                ->with('success', "Sales Return #{$return->return_number} posted successfully. Stock restocked and credit note applied.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function printInvoice(int $id)
    {
        $invoice = SaleInvoice::with(['customer', 'warehouse', 'lines.product', 'lines.batch'])->findOrFail($id);
        $pdf = SalesService::generateInvoicePdf($invoice);

        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function returns(Request $request)
    {
        $data['nav'] = 'pharma_sales';
        $data['sub_nav'] = 'sales_returns';
        $data['page_title'] = 'Sales Returns';

        $query = SaleReturn::with(['customer', 'warehouse', 'saleInvoice', 'lines.product', 'lines.batch']);

        $data['returns'] = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.pharma.sales.returns', $data);
    }
}

