<?php

namespace App\Services;

use App\Helpers\BsDateHelper;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\FiscalYear;
use App\Models\Product;
use App\Models\ProductPackagingLevel;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceLine;
use App\Models\SaleReturn;
use App\Models\SaleReturnLine;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SalesService
{
    /**
     * Create a Sales Invoice with optional automated FEFO batch allocation.
     *
     * @param array $header
     * @param array $items
     * @param bool $autoAllocateFefo
     * @return SaleInvoice
     * @throws RuntimeException
     */
    public static function createInvoice(array $header, array $items, bool $autoAllocateFefo = true): SaleInvoice
    {
        return DB::transaction(function () use ($header, $items, $autoAllocateFefo) {
            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            $customer = isset($header['customer_id']) ? Customer::find($header['customer_id']) : null;
            if ($customer && $customer->credit_stop) {
                throw new RuntimeException("Customer '{$customer->name}' is placed on Credit Stop. Invoicing blocked.");
            }

            $invoiceNumber = DocumentSequenceService::getNextNumber('INV', $fiscalYear);

            $invoiceDateAd = $header['invoice_date_ad'] ?? Carbon::today()->format('Y-m-d');
            $invoiceDateBs = $header['invoice_date_bs'] ?? BsDateHelper::toBs($invoiceDateAd);

            $invoice = SaleInvoice::create([
                'invoice_number' => $invoiceNumber,
                'fiscal_year_id' => $fiscalYear->id,
                'customer_id' => $customer?->id,
                'customer_name' => $customer?->name ?? ($header['customer_name'] ?? 'Cash Customer'),
                'customer_pan' => $customer?->pan_vat_number ?? ($header['customer_pan'] ?? null),
                'warehouse_id' => $header['warehouse_id'],
                'invoice_date_ad' => $invoiceDateAd,
                'invoice_date_bs' => $invoiceDateBs,
                'salesperson_id' => $header['salesperson_id'] ?? null,
                'payment_method' => $header['payment_method'] ?? ($customer ? 'credit' : 'cash'),
                'subtotal' => 0.00,
                'discount_amount' => 0.00,
                'taxable_amount' => 0.00,
                'non_taxable_amount' => 0.00,
                'tax_amount' => 0.00,
                'delivery_charge' => $header['delivery_charge'] ?? 0.00,
                'round_off' => $header['round_off'] ?? 0.00,
                'total_amount' => 0.00,
                'paid_amount' => 0.00,
                'credit_amount' => 0.00,
                'cogs_amount' => 0.00,
                'status' => 'draft',
                'created_by' => $header['created_by'] ?? null,
                'notes' => $header['notes'] ?? null,
            ]);

            $subtotal = '0.00';
            $discountTotal = '0.00';
            $taxableTotal = '0.00';
            $nonTaxableTotal = '0.00';
            $taxTotal = '0.00';
            $cogsTotal = '0.00';

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unit = Unit::findOrFail($item['unit_id']);

                $conversionFactor = '1.0';
                if ($unit->id !== $product->base_unit_id) {
                    $pack = ProductPackagingLevel::where('product_id', $product->id)->where('unit_id', $unit->id)->first();
                    $conversionFactor = $pack ? (string) $pack->conversion_factor : '1.0';
                }

                $requestedQty = (string) $item['qty'];
                $totalBaseQty = (int) round(bcmul($requestedQty, $conversionFactor, 4));
                $unitPrice = (string) ($item['unit_price'] ?? $product->selling_price);

                // Batch slices determined either via FEFO allocation or explicit batch
                $slices = [];
                if ($autoAllocateFefo && empty($item['batch_id'])) {
                    $fefoSlices = InventoryService::allocateFefo($product->id, $totalBaseQty, $header['warehouse_id']);
                    foreach ($fefoSlices as $s) {
                        $sliceBaseQty = (int) $s['allocated_qty'];
                        $sliceQty = bcdiv((string) $sliceBaseQty, $conversionFactor, 4);
                        $slices[] = [
                            'batch_id' => $s['batch_id'],
                            'location_id' => $s['location_id'],
                            'qty' => $sliceQty,
                            'base_qty' => $sliceBaseQty,
                        ];
                    }
                } else {
                    $slices[] = [
                        'batch_id' => $item['batch_id'],
                        'location_id' => $item['location_id'],
                        'qty' => $requestedQty,
                        'base_qty' => $totalBaseQty,
                    ];
                }

                foreach ($slices as $slice) {
                    $batch = Batch::findOrFail($slice['batch_id']);

                    $gross = bcmul((string) $slice['qty'], $unitPrice, 4);
                    $discountPct = (string) ($item['discount_pct'] ?? '0.0000');
                    $lineDiscount = bcdiv(bcmul($gross, $discountPct, 4), '100', 2);
                    $net = bcsub($gross, $lineDiscount, 2);

                    $taxRate = (string) ($item['tax_rate'] ?? '0.0000');
                    $lineTax = bcdiv(bcmul($net, $taxRate, 4), '100', 2);
                    $lineFinal = bcadd($net, $lineTax, 2);

                    $unitCost = (string) $batch->cost_price;
                    $lineCogs = bcmul((string) $slice['base_qty'], $unitCost, 2);

                    SaleInvoiceLine::create([
                        'sale_invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'batch_id' => $batch->id,
                        'location_id' => $slice['location_id'],
                        'unit_id' => $unit->id,
                        'product_code' => $product->code,
                        'product_name' => $product->name,
                        'batch_number' => $batch->batch_number,
                        'expiry_date_ad' => $batch->expiry_date_ad,
                        'expiry_date_bs' => $batch->expiry_date_bs,
                        'unit_name' => $unit->name,
                        'qty' => $slice['qty'],
                        'conversion_factor' => $conversionFactor,
                        'base_qty' => $slice['base_qty'],
                        'unit_price' => $unitPrice,
                        'discount_pct' => $discountPct,
                        'discount_amount' => $lineDiscount,
                        'tax_category_id' => $item['tax_category_id'] ?? $product->tax_category_id,
                        'tax_rate' => $taxRate,
                        'tax_amount' => $lineTax,
                        'line_total' => $lineFinal,
                        'unit_cost' => $unitCost,
                        'cogs_amount' => $lineCogs,
                    ]);

                    $subtotal = bcadd($subtotal, $gross, 2);
                    $discountTotal = bcadd($discountTotal, $lineDiscount, 2);

                    if (bccomp($taxRate, '0.0000', 4) > 0) {
                        $taxableTotal = bcadd($taxableTotal, $net, 2);
                        $taxTotal = bcadd($taxTotal, $lineTax, 2);
                    } else {
                        $nonTaxableTotal = bcadd($nonTaxableTotal, $net, 2);
                    }

                    $cogsTotal = bcadd($cogsTotal, $lineCogs, 2);
                }
            }

            $delivery = (string) ($header['delivery_charge'] ?? '0.00');
            $roundOff = (string) ($header['round_off'] ?? '0.00');
            $netItems = bcadd(bcadd($taxableTotal, $nonTaxableTotal, 2), $taxTotal, 2);
            $grandTotal = bcadd(bcadd($netItems, $delivery, 2), $roundOff, 2);

            $paidAmount = '0.00';
            $creditAmount = '0.00';
            if ($invoice->payment_method === 'cash') {
                $paidAmount = $grandTotal;
            } else {
                $paidAmount = (string) ($header['paid_amount'] ?? '0.00');
                $creditAmount = bcsub($grandTotal, $paidAmount, 2);
            }

            // Credit limit validation for credit customers
            if ($customer && bccomp($creditAmount, '0.00', 2) > 0) {
                if (! $customer->canTakeCredit((float) $creditAmount)) {
                    throw new RuntimeException(
                        "Credit limit exceeded for customer '{$customer->name}'. Limit: NPR {$customer->credit_limit}, Current: NPR {$customer->current_balance}, Invoiced: NPR {$creditAmount}."
                    );
                }
            }

            $invoice->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountTotal,
                'taxable_amount' => $taxableTotal,
                'non_taxable_amount' => $nonTaxableTotal,
                'tax_amount' => $taxTotal,
                'delivery_charge' => $delivery,
                'round_off' => $roundOff,
                'total_amount' => $grandTotal,
                'paid_amount' => $paidAmount,
                'credit_amount' => $creditAmount,
                'cogs_amount' => $cogsTotal,
            ]);

            return $invoice->fresh('lines');
        });
    }

    /**
     * Post a Sales Invoice:
     * 1. Deducts physical stock via InventoryService SALE_DISPATCH.
     * 2. Appends Accounts Receivable debit entry to customer_ledger_entries.
     * 3. Increments customer.current_balance by credit_amount.
     */
    public static function postInvoice(SaleInvoice $invoice, int $adminId): SaleInvoice
    {
        return DB::transaction(function () use ($invoice, $adminId) {
            $invoice->refresh();
            if ($invoice->status !== 'draft') {
                throw new RuntimeException("Invoice #{$invoice->invoice_number} is already {$invoice->status} and cannot be posted.");
            }

            $lines = $invoice->lines;
            if ($lines->isEmpty()) {
                throw new RuntimeException("Invoice #{$invoice->invoice_number} has no line items.");
            }

            $customer = $invoice->customer_id ? Customer::where('id', $invoice->customer_id)->lockForUpdate()->first() : null;

            foreach ($lines as $line) {
                // Record stock outflow
                InventoryService::recordStockMovement([
                    'fiscal_year_id' => $invoice->fiscal_year_id,
                    'warehouse_id' => $invoice->warehouse_id,
                    'location_id' => $line->location_id,
                    'product_id' => $line->product_id,
                    'batch_id' => $line->batch_id,
                    'movement_type' => 'SALE_DISPATCH',
                    'quantity' => -1 * (int) $line->base_qty, // Outbound
                    'unit_cost' => $line->unit_cost,
                    'reference_type' => 'SaleInvoice',
                    'reference_id' => $invoice->id,
                    'created_by' => $adminId,
                    'notes' => "Invoice #{$invoice->invoice_number} to {$invoice->customer_name}",
                ]);
            }

            // Customer Accounts Receivable Subledger Entry (Decision D-003)
            if ($customer && bccomp((string) $invoice->credit_amount, '0.00', 2) > 0) {
                $newBalance = bcadd((string) $customer->current_balance, (string) $invoice->credit_amount, 2);

                CustomerLedgerEntry::create([
                    'fiscal_year_id' => $invoice->fiscal_year_id,
                    'customer_id' => $customer->id,
                    'entry_date_ad' => $invoice->invoice_date_ad,
                    'entry_date_bs' => $invoice->invoice_date_bs,
                    'transaction_type' => 'SALE_INVOICE',
                    'reference_type' => 'SaleInvoice',
                    'reference_id' => $invoice->id,
                    'reference_number' => $invoice->invoice_number,
                    'debit_amount' => $invoice->credit_amount, // Debit increases receivable
                    'credit_amount' => 0.00,
                    'running_balance' => $newBalance,
                    'notes' => "Sales Invoice #{$invoice->invoice_number}",
                    'created_by' => $adminId,
                    'created_at' => now(),
                ]);

                $customer->update(['current_balance' => $newBalance]);
            }

            $invoice->update([
                'status' => 'posted',
                'posted_by' => $adminId,
                'posted_at' => now(),
            ]);

            AuditService::log('sale_invoice_posted', $invoice, null, ['status' => 'posted'], 'admin', $adminId);

            return $invoice->fresh(['lines', 'customer']);
        });
    }

    /**
     * Generate printable PDF invoice using DomPDF.
     */
    public static function generateInvoicePdf(SaleInvoice $invoice)
    {
        $invoice->load([
            'lines.product',
            'lines.batch',
            'customer',
            'warehouse',
            'salesperson',
        ]);

        return Pdf::loadView('pdf.invoice', ['invoice' => $invoice])
            ->setPaper('a4', 'portrait');
    }

    /**
     * Create a Sales Return in draft status.
     */
    public static function createSaleReturn(array $header, array $lines): SaleReturn
    {
        return DB::transaction(function () use ($header, $lines) {
            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            $returnNumber = DocumentSequenceService::getNextNumber('SRTN', $fiscalYear);

            $returnDateAd = $header['return_date_ad'] ?? Carbon::today()->format('Y-m-d');
            $returnDateBs = $header['return_date_bs'] ?? BsDateHelper::toBs($returnDateAd);

            $saleReturn = SaleReturn::create([
                'return_number' => $returnNumber,
                'fiscal_year_id' => $fiscalYear->id,
                'sale_invoice_id' => $header['sale_invoice_id'] ?? null,
                'customer_id' => $header['customer_id'] ?? null,
                'warehouse_id' => $header['warehouse_id'],
                'return_date_ad' => $returnDateAd,
                'return_date_bs' => $returnDateBs,
                'subtotal' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => 0.00,
                'reason' => $header['reason'] ?? null,
                'credit_note_issued' => $header['credit_note_issued'] ?? true,
                'status' => 'draft',
                'created_by' => $header['created_by'] ?? null,
            ]);

            $subtotal = '0.00';
            $taxTotal = '0.00';
            $grandTotal = '0.00';

            foreach ($lines as $lineData) {
                $returnedQty = (string) $lineData['returned_qty'];
                $conversionFactor = (string) ($lineData['conversion_factor'] ?? 1.0);
                $returnedBaseQty = (int) round(bcmul($returnedQty, $conversionFactor, 4));
                $unitPrice = (string) $lineData['unit_price'];

                $lineTotal = bcmul($returnedQty, $unitPrice, 2);
                $taxAmount = (string) ($lineData['tax_amount'] ?? '0.00');
                $finalLine = bcadd($lineTotal, $taxAmount, 2);

                SaleReturnLine::create([
                    'sale_return_id' => $saleReturn->id,
                    'sale_invoice_line_id' => $lineData['sale_invoice_line_id'] ?? null,
                    'product_id' => $lineData['product_id'],
                    'batch_id' => $lineData['batch_id'],
                    'location_id' => $lineData['location_id'],
                    'unit_id' => $lineData['unit_id'],
                    'returned_qty' => $returnedQty,
                    'conversion_factor' => $conversionFactor,
                    'returned_base_qty' => $returnedBaseQty,
                    'condition' => $lineData['condition'] ?? 'saleable',
                    'unit_price' => $unitPrice,
                    'tax_amount' => $taxAmount,
                    'line_total' => $finalLine,
                    'restocked' => false,
                ]);

                $subtotal = bcadd($subtotal, $lineTotal, 2);
                $taxTotal = bcadd($taxTotal, $taxAmount, 2);
                $grandTotal = bcadd($grandTotal, $finalLine, 2);
            }

            $saleReturn->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxTotal,
                'total_amount' => $grandTotal,
            ]);

            return $saleReturn->fresh('lines');
        });
    }

    /**
     * Post a Sales Return:
     * 1. Restocks inventory into location according to condition.
     * 2. Appends Accounts Receivable credit entry to customer_ledger_entries.
     * 3. Decrements customer.current_balance.
     */
    public static function postSaleReturn(SaleReturn $return, int $adminId): SaleReturn
    {
        return DB::transaction(function () use ($return, $adminId) {
            $return->refresh();
            if ($return->status !== 'draft') {
                throw new RuntimeException("Sale Return #{$return->return_number} is already {$return->status} and cannot be posted.");
            }

            $customer = $return->customer_id ? Customer::where('id', $return->customer_id)->lockForUpdate()->first() : null;

            foreach ($return->lines as $line) {
                // All customer returns are classified as SALE_RETURN inflow
                $movementType = 'SALE_RETURN';

                // Restock inventory via InventoryService
                InventoryService::recordStockMovement([
                    'fiscal_year_id' => $return->fiscal_year_id,
                    'warehouse_id' => $return->warehouse_id,
                    'location_id' => $line->location_id,
                    'product_id' => $line->product_id,
                    'batch_id' => $line->batch_id,
                    'movement_type' => $movementType,
                    'quantity' => (int) $line->returned_base_qty, // Inflow
                    'unit_cost' => $line->unit_price,
                    'reference_type' => 'SaleReturn',
                    'reference_id' => $return->id,
                    'created_by' => $adminId,
                    'notes' => "Sale Return #{$return->return_number} ({$line->condition})",
                ]);

                $line->update(['restocked' => true]);
            }

            // Customer Accounts Receivable Subledger Entry (Credit reduces receivable)
            if ($customer && $return->credit_note_issued) {
                $newBalance = bcsub((string) $customer->current_balance, (string) $return->total_amount, 2);

                CustomerLedgerEntry::create([
                    'fiscal_year_id' => $return->fiscal_year_id,
                    'customer_id' => $customer->id,
                    'entry_date_ad' => $return->return_date_ad,
                    'entry_date_bs' => $return->return_date_bs,
                    'transaction_type' => 'SALE_RETURN',
                    'reference_type' => 'SaleReturn',
                    'reference_id' => $return->id,
                    'reference_number' => $return->return_number,
                    'debit_amount' => 0.00,
                    'credit_amount' => $return->total_amount,
                    'running_balance' => $newBalance,
                    'notes' => "Credit note for Sale Return #{$return->return_number}",
                    'created_by' => $adminId,
                    'created_at' => now(),
                ]);

                $customer->update(['current_balance' => $newBalance]);
            }

            $return->update([
                'status' => 'posted',
                'posted_by' => $adminId,
                'posted_at' => now(),
            ]);

            AuditService::log('sale_return_posted', $return, null, ['status' => 'posted'], 'admin', $adminId);

            return $return->fresh(['lines', 'customer']);
        });
    }
}
