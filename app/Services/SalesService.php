<?php

namespace App\Services;

use App\Helpers\BsDateHelper;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\CustomerReceiptAllocation;
use App\Models\FiscalYear;
use App\Models\Product;
use App\Models\ProductPackagingLevel;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceLine;
use App\Models\SaleReturn;
use App\Models\SaleReturnLine;
use App\Models\Unit;
use App\Models\WarehouseLocation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SalesService
{
    /**
     * Create a Sales Invoice with optional automated FEFO batch allocation.
     *
     * @throws RuntimeException
     */
    public static function createInvoice(array $header, array $items, bool $autoAllocateFefo = true): SaleInvoice
    {
        return DB::transaction(function () use ($header, $items, $autoAllocateFefo) {
            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();
            $invoice = new SaleInvoice([
                'invoice_number' => DocumentSequenceService::getNextNumber('INV', $fiscalYear),
                'fiscal_year_id' => $fiscalYear->id,
                'status' => 'draft',
                'created_by' => $header['created_by'] ?? null,
            ]);

            return static::writeInvoice($invoice, $header, $items, $autoAllocateFefo);
        });
    }

    public static function updateInvoice(SaleInvoice $invoice, array $header, array $items, bool $autoAllocateFefo, int $adminId): SaleInvoice
    {
        return DB::transaction(function () use ($invoice, $header, $items, $autoAllocateFefo, $adminId) {
            $invoice = SaleInvoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            static::assertDraft($invoice);
            static::checkDraftDates($invoice, $header['invoice_date_ad']);
            $before = $invoice->load('lines')->toArray();
            $invoice->lines()->delete();
            $invoice = static::writeInvoice($invoice, $header, $items, $autoAllocateFefo);
            AuditService::log('sale_invoice_edited', $invoice, $before, $invoice->toArray(), 'admin', $adminId);

            return $invoice;
        });
    }

    private static function writeInvoice(SaleInvoice $invoice, array $header, array $items, bool $autoAllocateFefo): SaleInvoice
    {
        $customer = isset($header['customer_id']) ? Customer::findOrFail($header['customer_id']) : null;
        if ($customer && $customer->credit_stop) {
            throw new RuntimeException("Customer '{$customer->name}' is placed on Credit Stop. Invoicing blocked.");
        }
        if (empty($items)) {
            throw new RuntimeException('At least one invoice item is required.');
        }
        $date = $header['invoice_date_ad'] ?? Carbon::today()->format('Y-m-d');
        $invoice->fill([
            'customer_id' => $customer?->id,
            'customer_name' => $customer?->name ?? ($header['customer_name'] ?? 'Cash Customer'),
            'customer_pan' => $customer?->pan_vat_number ?? ($header['customer_pan'] ?? null),
            'warehouse_id' => $header['warehouse_id'],
            'invoice_date_ad' => $date,
            'invoice_date_bs' => $header['invoice_date_bs'] ?? BsDateHelper::toBs($date),
            'salesperson_id' => $header['salesperson_id'] ?? $invoice->salesperson_id,
            'payment_method' => $header['payment_method'] ?? ($customer ? 'credit' : 'cash'),
            'notes' => $header['notes'] ?? null,
        ])->save();

        $subtotal = '0.00';
        $discountTotal = '0.00';
        $taxableTotal = '0.00';
        $nonTaxableTotal = '0.00';
        $taxTotal = '0.00';
        $cogsTotal = '0.00';
        $allocated = [];

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $unit = Unit::findOrFail($item['unit_id']);

            $conversionFactor = '1.0';
            if ($unit->id !== $product->base_unit_id) {
                $pack = ProductPackagingLevel::where('product_id', $product->id)->where('unit_id', $unit->id)->first();
                if (! $pack || bccomp((string) $pack->conversion_factor, '0', 4) <= 0) {
                    throw new RuntimeException('Select a base unit or a configured packaging unit for this product.');
                }
                $conversionFactor = (string) $pack->conversion_factor;
            }

            $requestedQty = (string) $item['qty'];
            $totalBaseQty = (int) round(bcmul($requestedQty, $conversionFactor, 4));
            if ($totalBaseQty <= 0) {
                throw new RuntimeException('Quantity must represent at least one base unit.');
            }
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
                if ((int) $batch->product_id !== (int) $product->id || ! Batch::sellable()->whereKey($batch->id)->exists()) {
                    throw new RuntimeException('Choose a sellable batch belonging to the selected product.');
                }
                $location = WarehouseLocation::findOrFail($slice['location_id']);
                if ((int) $location->warehouse_id !== (int) $header['warehouse_id'] || ! $location->is_sellable || ! $location->is_available) {
                    throw new RuntimeException('Choose an available selling location in the selected warehouse.');
                }
                $key = $product->id.':'.$batch->id.':'.$location->id;
                $allocated[$key] = ($allocated[$key] ?? 0) + $slice['base_qty'];
                if ($allocated[$key] > InventoryService::getAvailableStock($product->id, (int) $header['warehouse_id'], $location->id, $batch->id)) {
                    throw new RuntimeException('Insufficient available stock for the selected batch and location.');
                }

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
            $invoice = SaleInvoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();
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
            $saleReturn = new SaleReturn([
                'return_number' => DocumentSequenceService::getNextNumber('SRTN', $fiscalYear),
                'fiscal_year_id' => $fiscalYear->id,
                'status' => 'draft',
                'created_by' => $header['created_by'] ?? null,
            ]);

            return static::writeReturn($saleReturn, $header, $lines);
        });
    }

    public static function updateSaleReturn(SaleReturn $saleReturn, array $header, array $lines, int $adminId): SaleReturn
    {
        return DB::transaction(function () use ($saleReturn, $header, $lines, $adminId) {
            $saleReturn = SaleReturn::whereKey($saleReturn->id)->lockForUpdate()->firstOrFail();
            static::assertDraft($saleReturn);
            static::checkDraftDates($saleReturn, $header['return_date_ad']);
            $before = $saleReturn->load('lines')->toArray();
            $saleReturn->lines()->delete();
            $saleReturn = static::writeReturn($saleReturn, $header, $lines);
            AuditService::log('sale_return_edited', $saleReturn, $before, $saleReturn->toArray(), 'admin', $adminId);

            return $saleReturn;
        });
    }

    private static function writeReturn(SaleReturn $saleReturn, array $header, array $lines): SaleReturn
    {
        if (empty($lines)) {
            throw new RuntimeException('At least one return item is required.');
        }
        $date = $header['return_date_ad'] ?? Carbon::today()->format('Y-m-d');
        $saleReturn->fill([
            'sale_invoice_id' => $header['sale_invoice_id'] ?? null,
            'customer_id' => $header['customer_id'] ?? null,
            'warehouse_id' => $header['warehouse_id'],
            'return_date_ad' => $date,
            'return_date_bs' => $header['return_date_bs'] ?? BsDateHelper::toBs($date),
            'reason' => $header['reason'] ?? null,
            'credit_note_issued' => $header['credit_note_issued'] ?? true,
        ])->save();
        if ($saleReturn->sale_invoice_id) {
            $original = SaleInvoice::findOrFail($saleReturn->sale_invoice_id);
            if ($original->status !== 'posted' || (int) $original->customer_id !== (int) $saleReturn->customer_id || (int) $original->warehouse_id !== (int) $saleReturn->warehouse_id) {
                throw new RuntimeException('The original invoice must be posted and match this customer and warehouse.');
            }
        }

        $subtotal = '0.00';
        $taxTotal = '0.00';
        $grandTotal = '0.00';

        foreach ($lines as $lineData) {
            $returnedQty = (string) $lineData['returned_qty'];
            $product = Product::findOrFail($lineData['product_id']);
            $batch = Batch::findOrFail($lineData['batch_id']);
            $location = WarehouseLocation::findOrFail($lineData['location_id']);
            if ((int) $batch->product_id !== (int) $product->id || (int) $location->warehouse_id !== (int) $header['warehouse_id']) {
                throw new RuntimeException('Return product, batch and warehouse location must match.');
            }
            $conversionFactor = '1.0';
            if ((int) $lineData['unit_id'] !== (int) $product->base_unit_id) {
                $pack = ProductPackagingLevel::where('product_id', $product->id)->where('unit_id', $lineData['unit_id'])->first();
                if (! $pack || bccomp((string) $pack->conversion_factor, '0', 4) <= 0) {
                    throw new RuntimeException('Select a base unit or a configured packaging unit for this product.');
                }
                $conversionFactor = (string) $pack->conversion_factor;
            }
            if (! empty($lineData['sale_invoice_line_id'])) {
                $originalLine = SaleInvoiceLine::findOrFail($lineData['sale_invoice_line_id']);
                if ((int) $originalLine->sale_invoice_id !== (int) $saleReturn->sale_invoice_id || (int) $originalLine->product_id !== (int) $product->id || (int) $originalLine->batch_id !== (int) $batch->id) {
                    throw new RuntimeException('The original invoice line must match the selected invoice, product and batch.');
                }
            }
            $returnedBaseQty = (int) round(bcmul($returnedQty, $conversionFactor, 4));
            if ($returnedBaseQty <= 0) {
                throw new RuntimeException('Return quantity must represent at least one base unit.');
            }
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
    }

    public static function deleteInvoice(SaleInvoice $invoice, int $adminId): void
    {
        DB::transaction(function () use ($invoice, $adminId) {
            $invoice = SaleInvoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            static::assertDraft($invoice);
            static::checkDraftDates($invoice);
            $before = $invoice->load('lines')->toArray();
            $invoice->delete();
            AuditService::log('sale_invoice_draft_deleted', $invoice, $before, null, 'admin', $adminId);
        });
    }

    public static function deleteSaleReturn(SaleReturn $saleReturn, int $adminId): void
    {
        DB::transaction(function () use ($saleReturn, $adminId) {
            $saleReturn = SaleReturn::whereKey($saleReturn->id)->lockForUpdate()->firstOrFail();
            static::assertDraft($saleReturn);
            static::checkDraftDates($saleReturn);
            $before = $saleReturn->load('lines')->toArray();
            $saleReturn->delete();
            AuditService::log('sale_return_draft_deleted', $saleReturn, $before, null, 'admin', $adminId);
        });
    }

    private static function assertDraft(SaleInvoice|SaleReturn $document): void
    {
        if ($document->status !== 'draft') {
            throw new RuntimeException('Only draft sales documents can be edited or deleted. Posted documents must be corrected through a return.');
        }
        if ($document instanceof SaleInvoice && (
            $document->returns()->withTrashed()->exists()
            || CustomerReceiptAllocation::where('sale_invoice_id', $document->id)->exists()
            || SaleReturnLine::whereIn('sale_invoice_line_id', $document->lines()->select('id'))->exists()
        )) {
            throw new RuntimeException('This invoice has linked returns or receipts and cannot be edited or deleted.');
        }
    }

    private static function checkDraftDates(SaleInvoice|SaleReturn $document, ?string $newDate = null): void
    {
        $oldDate = $document instanceof SaleInvoice ? $document->invoice_date_ad : $document->return_date_ad;
        WarehouseOperationService::checkPeriodLock($oldDate->format('Y-m-d'));
        if ($newDate !== null) {
            WarehouseOperationService::checkPeriodLock($newDate);
            $year = $document->fiscalYear;
            $date = Carbon::parse($newDate)->startOfDay();
            if ($year && ($date->lt($year->start_date_ad) || $date->gt($year->end_date_ad))) {
                throw new RuntimeException('The date must remain within the original fiscal year.');
            }
        }
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
            $return = SaleReturn::whereKey($return->id)->lockForUpdate()->firstOrFail();
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
