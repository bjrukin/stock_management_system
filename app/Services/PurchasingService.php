<?php

namespace App\Services;

use App\Helpers\BsDateHelper;
use App\Models\Batch;
use App\Models\FiscalYear;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnLine;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class PurchasingService
{
    /**
     * Create a Purchase Order in draft status.
     */
    public static function createPurchaseOrder(array $header, array $lines): PurchaseOrder
    {
        return DB::transaction(function () use ($header, $lines) {
            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            $poNumber = DocumentSequenceService::getNextNumber('PO', $fiscalYear);

            $orderDateAd = $header['order_date_ad'] ?? Carbon::today()->format('Y-m-d');
            $orderDateBs = $header['order_date_bs'] ?? BsDateHelper::toBs($orderDateAd);

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'fiscal_year_id' => $fiscalYear->id,
                'supplier_id' => $header['supplier_id'],
                'warehouse_id' => $header['warehouse_id'],
                'order_date_ad' => $orderDateAd,
                'order_date_bs' => $orderDateBs,
                'expected_delivery_date_ad' => $header['expected_delivery_date_ad'] ?? null,
                'expected_delivery_date_bs' => $header['expected_delivery_date_bs'] ?? null,
                'status' => 'draft',
                'subtotal' => 0.00,
                'discount_amount' => 0.00,
                'taxable_amount' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => 0.00,
                'notes' => $header['notes'] ?? null,
                'created_by' => $header['created_by'] ?? null,
            ]);

            $subtotal = '0.00';
            $discountTotal = '0.00';
            $taxableTotal = '0.00';
            $taxTotal = '0.00';
            $grandTotal = '0.00';

            foreach ($lines as $lineData) {
                $orderedQty = (string) $lineData['ordered_qty'];
                $conversionFactor = (string) ($lineData['conversion_factor'] ?? 1.0);
                $orderedBaseQty = (int) round(bcmul($orderedQty, $conversionFactor, 4));
                $unitPrice = (string) $lineData['unit_price'];

                $grossLine = bcmul($orderedQty, $unitPrice, 4);
                $discountPct = (string) ($lineData['discount_pct'] ?? '0.0000');
                $lineDiscount = bcdiv(bcmul($grossLine, $discountPct, 4), '100', 2);
                $netLine = bcsub($grossLine, $lineDiscount, 2);

                $taxRate = (string) ($lineData['tax_rate'] ?? '0.0000');
                $lineTax = bcdiv(bcmul($netLine, $taxRate, 4), '100', 2);
                $lineFinalTotal = bcadd($netLine, $lineTax, 2);

                PurchaseOrderLine::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $lineData['product_id'],
                    'unit_id' => $lineData['unit_id'],
                    'ordered_qty' => $orderedQty,
                    'conversion_factor' => $conversionFactor,
                    'ordered_base_qty' => $orderedBaseQty,
                    'received_base_qty' => 0,
                    'unit_price' => $unitPrice,
                    'discount_pct' => $discountPct,
                    'discount_amount' => $lineDiscount,
                    'tax_category_id' => $lineData['tax_category_id'] ?? null,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $lineTax,
                    'line_total' => $lineFinalTotal,
                ]);

                $subtotal = bcadd($subtotal, $grossLine, 2);
                $discountTotal = bcadd($discountTotal, $lineDiscount, 2);
                $taxableTotal = bcadd($taxableTotal, $netLine, 2);
                $taxTotal = bcadd($taxTotal, $lineTax, 2);
                $grandTotal = bcadd($grandTotal, $lineFinalTotal, 2);
            }

            $po->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountTotal,
                'taxable_amount' => $taxableTotal,
                'tax_amount' => $taxTotal,
                'total_amount' => $grandTotal,
            ]);

            return $po->fresh('lines');
        });
    }

    /**
     * Approve a Purchase Order.
     */
    public static function approvePurchaseOrder(PurchaseOrder $po, int $adminId): PurchaseOrder
    {
        if ($po->status !== 'draft') {
            throw new RuntimeException("Purchase Order #{$po->po_number} cannot be approved from status '{$po->status}'.");
        }

        $po->update([
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);

        AuditService::log('po_approved', $po, null, ['status' => 'approved'], 'admin', $adminId);

        return $po;
    }

    /**
     * Create a Goods Receipt (GRN) in draft status.
     */
    public static function createGoodsReceipt(array $header, array $lines): GoodsReceipt
    {
        return DB::transaction(function () use ($header, $lines) {
            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            $grnNumber = DocumentSequenceService::getNextNumber('GRN', $fiscalYear);

            $receiptDateAd = $header['receipt_date_ad'] ?? Carbon::today()->format('Y-m-d');
            $receiptDateBs = $header['receipt_date_bs'] ?? BsDateHelper::toBs($receiptDateAd);

            $freight = (string) ($header['freight_amount'] ?? '0.00');
            $handling = (string) ($header['handling_charges'] ?? '0.00');
            $other = (string) ($header['other_charges'] ?? '0.00');

            $grn = GoodsReceipt::create([
                'grn_number' => $grnNumber,
                'fiscal_year_id' => $fiscalYear->id,
                'purchase_order_id' => $header['purchase_order_id'] ?? null,
                'supplier_id' => $header['supplier_id'],
                'warehouse_id' => $header['warehouse_id'],
                'supplier_invoice_number' => $header['supplier_invoice_number'] ?? null,
                'supplier_invoice_date_ad' => $header['supplier_invoice_date_ad'] ?? null,
                'supplier_invoice_date_bs' => $header['supplier_invoice_date_bs'] ?? null,
                'receipt_date_ad' => $receiptDateAd,
                'receipt_date_bs' => $receiptDateBs,
                'freight_amount' => $freight,
                'handling_charges' => $handling,
                'other_charges' => $other,
                'subtotal' => 0.00,
                'discount_amount' => 0.00,
                'taxable_amount' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => 0.00,
                'total_landed_cost' => 0.00,
                'status' => 'draft',
                'created_by' => $header['created_by'] ?? null,
                'notes' => $header['notes'] ?? null,
            ]);

            $subtotal = '0.00';
            $discountTotal = '0.00';
            $taxableTotal = '0.00';
            $taxTotal = '0.00';
            $grandTotal = '0.00';

            foreach ($lines as $lineData) {
                $receivedQty = (string) $lineData['received_qty'];
                $conversionFactor = (string) ($lineData['conversion_factor'] ?? 1.0);
                $receivedBaseQty = (int) round(bcmul($receivedQty, $conversionFactor, 4));
                $unitCost = (string) $lineData['unit_cost'];
                $unitCostBase = bcdiv($unitCost, $conversionFactor, 4);

                $grossLine = bcmul($receivedQty, $unitCost, 4);
                $discountPct = (string) ($lineData['discount_pct'] ?? '0.0000');
                $lineDiscount = bcdiv(bcmul($grossLine, $discountPct, 4), '100', 2);
                $netLine = bcsub($grossLine, $lineDiscount, 2);

                $taxRate = (string) ($lineData['tax_rate'] ?? '0.0000');
                $lineTax = bcdiv(bcmul($netLine, $taxRate, 4), '100', 2);
                $lineFinalTotal = bcadd($netLine, $lineTax, 2);

                $mfgAd = $lineData['mfg_date_ad'] ?? null;
                $mfgBs = $lineData['mfg_date_bs'] ?? ($mfgAd ? BsDateHelper::toBs($mfgAd) : null);
                $expiryAd = $lineData['expiry_date_ad'];
                $expiryBs = $lineData['expiry_date_bs'] ?? BsDateHelper::toBs($expiryAd);

                GoodsReceiptLine::create([
                    'goods_receipt_id' => $grn->id,
                    'purchase_order_line_id' => $lineData['purchase_order_line_id'] ?? null,
                    'product_id' => $lineData['product_id'],
                    'batch_id' => null, // Resolved on post
                    'batch_number' => $lineData['batch_number'],
                    'mfg_date_ad' => $mfgAd,
                    'mfg_date_bs' => $mfgBs,
                    'expiry_date_ad' => $expiryAd,
                    'expiry_date_bs' => $expiryBs,
                    'location_id' => $lineData['location_id'],
                    'unit_id' => $lineData['unit_id'],
                    'received_qty' => $receivedQty,
                    'conversion_factor' => $conversionFactor,
                    'received_base_qty' => $receivedBaseQty,
                    'rejected_base_qty' => $lineData['rejected_base_qty'] ?? 0,
                    'unit_cost' => $unitCost,
                    'unit_cost_base' => $unitCostBase,
                    'mrp' => $lineData['mrp'] ?? 0.00,
                    'selling_price' => $lineData['selling_price'] ?? 0.00,
                    'wholesale_price' => $lineData['wholesale_price'] ?? 0.00,
                    'discount_pct' => $discountPct,
                    'discount_amount' => $lineDiscount,
                    'tax_category_id' => $lineData['tax_category_id'] ?? null,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $lineTax,
                    'line_total' => $lineFinalTotal,
                    'landed_cost_share' => 0.00,
                    'effective_unit_cost' => $unitCostBase,
                ]);

                $subtotal = bcadd($subtotal, $grossLine, 2);
                $discountTotal = bcadd($discountTotal, $lineDiscount, 2);
                $taxableTotal = bcadd($taxableTotal, $netLine, 2);
                $taxTotal = bcadd($taxTotal, $lineTax, 2);
                $grandTotal = bcadd($grandTotal, $lineFinalTotal, 2);
            }

            $totalLandedCharges = bcadd(bcadd($freight, $handling, 2), $other, 2);
            $totalValuation = bcadd($grandTotal, $totalLandedCharges, 2);

            $grn->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountTotal,
                'taxable_amount' => $taxableTotal,
                'tax_amount' => $taxTotal,
                'total_amount' => $grandTotal,
                'total_landed_cost' => $totalValuation,
            ]);

            return $grn->fresh('lines');
        });
    }

    /**
     * Post a Goods Receipt (GRN):
     * 1. Allocates landed costs across lines.
     * 2. Ingests or links Batch records with effective landed cost.
     * 3. Appends StockMovement inflow and updates inventory_balances.
     * 4. Updates Purchase Order lines received quantities and PO status.
     * 5. Appends Accounts Payable credit entry to supplier_ledger_entries.
     * 6. Increments supplier.current_balance.
     */
    public static function postGoodsReceipt(GoodsReceipt $grn, int $adminId): GoodsReceipt
    {
        return DB::transaction(function () use ($grn, $adminId) {
            $grn->refresh();
            if ($grn->status !== 'draft') {
                throw new RuntimeException("Goods Receipt #{$grn->grn_number} is already {$grn->status} and cannot be posted.");
            }

            $lines = $grn->lines;
            if ($lines->isEmpty()) {
                throw new RuntimeException("Cannot post Goods Receipt #{$grn->grn_number}: Has no item lines.");
            }

            $subtotal = (string) $grn->subtotal;
            $totalLandedCharges = bcadd(
                bcadd((string) $grn->freight_amount, (string) $grn->handling_charges, 2),
                (string) $grn->other_charges,
                2
            );

            $supplier = Supplier::where('id', $grn->supplier_id)->lockForUpdate()->firstOrFail();

            foreach ($lines as $line) {
                // Compute Landed Cost share: (line_total / subtotal) * totalLandedCharges
                $landedShare = '0.00';
                if (bccomp($subtotal, '0.00', 2) > 0 && bccomp($totalLandedCharges, '0.00', 2) > 0) {
                    $ratio = bcdiv((string) $line->line_total, $subtotal, 6);
                    $landedShare = bcmul($ratio, $totalLandedCharges, 2);
                }

                // Total line cost including landed share
                $totalLineCost = bcadd((string) $line->line_total, $landedShare, 2);
                $effectiveUnitCost = bcdiv($totalLineCost, (string) $line->received_base_qty, 4);

                // Per Decision D-002: Distinct batch record identified by surrogate id
                $batch = Batch::firstOrCreate(
                    [
                        'product_id' => $line->product_id,
                        'batch_number' => $line->batch_number,
                        'goods_receipt_id' => $grn->id,
                    ],
                    [
                        'manufacturer_id' => $line->product?->manufacturer_id,
                        'supplier_id' => $grn->supplier_id,
                        'mfg_date_ad' => $line->mfg_date_ad,
                        'mfg_date_bs' => $line->mfg_date_bs,
                        'expiry_date_ad' => $line->expiry_date_ad,
                        'expiry_date_bs' => $line->expiry_date_bs,
                        'cost_price' => $effectiveUnitCost,
                        'mrp' => ($line->mrp && $line->mrp > 0) ? $line->mrp : ($line->product?->mrp ?? 0.0000),
                        'selling_price' => ($line->selling_price && $line->selling_price > 0) ? $line->selling_price : ($line->product?->selling_price ?? 0.0000),
                        'wholesale_price' => ($line->wholesale_price && $line->wholesale_price > 0) ? $line->wholesale_price : ($line->product?->wholesale_price ?? 0.0000),
                    ]
                );

                // Sync latest pricing back to product master if provided
                $productUpdates = [];
                if (!empty($line->mrp) && $line->mrp > 0) {
                    $productUpdates['mrp'] = $line->mrp;
                }
                if (!empty($line->unit_cost) && $line->unit_cost > 0) {
                    $productUpdates['purchase_price'] = $line->unit_cost;
                }
                if (!empty($line->selling_price) && $line->selling_price > 0) {
                    $productUpdates['selling_price'] = $line->selling_price;
                }
                if (!empty($line->wholesale_price) && $line->wholesale_price > 0) {
                    $productUpdates['wholesale_price'] = $line->wholesale_price;
                }
                if (!empty($line->tax_category_id)) {
                    $productUpdates['tax_category_id'] = $line->tax_category_id;
                }
                if (!empty($productUpdates) && $line->product) {
                    $line->product->update($productUpdates);
                }

                $line->update([
                    'batch_id' => $batch->id,
                    'landed_cost_share' => $landedShare,
                    'effective_unit_cost' => $effectiveUnitCost,
                ]);

                // Record atomic stock movement
                InventoryService::recordStockMovement([
                    'fiscal_year_id' => $grn->fiscal_year_id,
                    'warehouse_id' => $grn->warehouse_id,
                    'location_id' => $line->location_id,
                    'product_id' => $line->product_id,
                    'batch_id' => $batch->id,
                    'movement_type' => 'PURCHASE_RECEIPT',
                    'quantity' => $line->received_base_qty,
                    'unit_cost' => $effectiveUnitCost,
                    'reference_type' => 'GoodsReceipt',
                    'reference_id' => $grn->id,
                    'created_by' => $adminId,
                    'notes' => "GRN #{$grn->grn_number} (Supplier Inv: {$grn->supplier_invoice_number})",
                ]);

                // If linked to PO line, increment received_base_qty
                if ($line->purchase_order_line_id) {
                    $poLine = PurchaseOrderLine::find($line->purchase_order_line_id);
                    if ($poLine) {
                        $poLine->increment('received_base_qty', $line->received_base_qty);
                    }
                }
            }

            // Update linked PO status
            if ($grn->purchase_order_id) {
                $po = PurchaseOrder::with('lines')->find($grn->purchase_order_id);
                if ($po) {
                    $allFulfilled = $po->lines->every(fn (PurchaseOrderLine $l) => $l->is_fulfilled);
                    $po->update(['status' => $allFulfilled ? 'received' : 'partially_received']);
                }
            }

            // Accounts Payable Subledger Entry (Decision D-003)
            $newBalance = bcadd((string) $supplier->current_balance, (string) $grn->total_amount, 2);

            SupplierLedgerEntry::create([
                'fiscal_year_id' => $grn->fiscal_year_id,
                'supplier_id' => $supplier->id,
                'entry_date_ad' => $grn->receipt_date_ad,
                'entry_date_bs' => $grn->receipt_date_bs,
                'transaction_type' => 'PURCHASE_INVOICE',
                'reference_type' => 'GoodsReceipt',
                'reference_id' => $grn->id,
                'reference_number' => $grn->grn_number,
                'debit_amount' => 0.00,
                'credit_amount' => $grn->total_amount,
                'running_balance' => $newBalance,
                'notes' => "Invoice #{$grn->supplier_invoice_number} received via GRN #{$grn->grn_number}",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $supplier->update(['current_balance' => $newBalance]);

            $grn->update([
                'status' => 'posted',
                'posted_by' => $adminId,
                'posted_at' => now(),
            ]);

            AuditService::log('grn_posted', $grn, null, ['status' => 'posted'], 'admin', $adminId);

            return $grn->fresh(['lines.batch', 'supplier']);
        });
    }

    /**
     * Create a Purchase Return in draft status.
     */
    public static function createPurchaseReturn(array $header, array $lines): PurchaseReturn
    {
        return DB::transaction(function () use ($header, $lines) {
            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            $returnNumber = DocumentSequenceService::getNextNumber('PRTN', $fiscalYear);

            $returnDateAd = $header['return_date_ad'] ?? Carbon::today()->format('Y-m-d');
            $returnDateBs = $header['return_date_bs'] ?? BsDateHelper::toBs($returnDateAd);

            $prtn = PurchaseReturn::create([
                'return_number' => $returnNumber,
                'fiscal_year_id' => $fiscalYear->id,
                'goods_receipt_id' => $header['goods_receipt_id'] ?? null,
                'supplier_id' => $header['supplier_id'],
                'warehouse_id' => $header['warehouse_id'],
                'return_date_ad' => $returnDateAd,
                'return_date_bs' => $returnDateBs,
                'subtotal' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => 0.00,
                'reason' => $header['reason'] ?? null,
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

                PurchaseReturnLine::create([
                    'purchase_return_id' => $prtn->id,
                    'goods_receipt_line_id' => $lineData['goods_receipt_line_id'] ?? null,
                    'product_id' => $lineData['product_id'],
                    'batch_id' => $lineData['batch_id'],
                    'location_id' => $lineData['location_id'],
                    'unit_id' => $lineData['unit_id'],
                    'returned_qty' => $returnedQty,
                    'conversion_factor' => $conversionFactor,
                    'returned_base_qty' => $returnedBaseQty,
                    'unit_price' => $unitPrice,
                    'tax_amount' => $taxAmount,
                    'line_total' => $finalLine,
                ]);

                $subtotal = bcadd($subtotal, $lineTotal, 2);
                $taxTotal = bcadd($taxTotal, $taxAmount, 2);
                $grandTotal = bcadd($grandTotal, $finalLine, 2);
            }

            $prtn->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxTotal,
                'total_amount' => $grandTotal,
            ]);

            return $prtn->fresh('lines');
        });
    }

    /**
     * Post a Purchase Return:
     * 1. Ingests outbound stock movement.
     * 2. Appends Accounts Payable debit entry to supplier_ledger_entries.
     * 3. Decrements supplier.current_balance.
     */
    public static function postPurchaseReturn(PurchaseReturn $prtn, int $adminId): PurchaseReturn
    {
        return DB::transaction(function () use ($prtn, $adminId) {
            $prtn->refresh();
            if ($prtn->status !== 'draft') {
                throw new RuntimeException("Purchase Return #{$prtn->return_number} is already {$prtn->status} and cannot be posted.");
            }

            $lines = $prtn->lines;
            if ($lines->isEmpty()) {
                throw new RuntimeException("Cannot post Purchase Return #{$prtn->return_number}: Has no item lines.");
            }

            $supplier = Supplier::where('id', $prtn->supplier_id)->lockForUpdate()->firstOrFail();

            foreach ($lines as $line) {
                // Record outbound stock movement
                InventoryService::recordStockMovement([
                    'fiscal_year_id' => $prtn->fiscal_year_id,
                    'warehouse_id' => $prtn->warehouse_id,
                    'location_id' => $line->location_id,
                    'product_id' => $line->product_id,
                    'batch_id' => $line->batch_id,
                    'movement_type' => 'PURCHASE_RETURN',
                    'quantity' => -1 * (int) $line->returned_base_qty, // Outflow
                    'unit_cost' => $line->unit_price,
                    'reference_type' => 'PurchaseReturn',
                    'reference_id' => $prtn->id,
                    'created_by' => $adminId,
                    'notes' => "Purchase Return #{$prtn->return_number} to {$supplier->name}",
                ]);
            }

            // Accounts Payable Subledger Entry (Debit reduces liability)
            $newBalance = bcsub((string) $supplier->current_balance, (string) $prtn->total_amount, 2);

            SupplierLedgerEntry::create([
                'fiscal_year_id' => $prtn->fiscal_year_id,
                'supplier_id' => $supplier->id,
                'entry_date_ad' => $prtn->return_date_ad,
                'entry_date_bs' => $prtn->return_date_bs,
                'transaction_type' => 'PURCHASE_RETURN',
                'reference_type' => 'PurchaseReturn',
                'reference_id' => $prtn->id,
                'reference_number' => $prtn->return_number,
                'debit_amount' => $prtn->total_amount,
                'credit_amount' => 0.00,
                'running_balance' => $newBalance,
                'notes' => "Debit note for Purchase Return #{$prtn->return_number}",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $supplier->update(['current_balance' => $newBalance]);

            $prtn->update([
                'status' => 'posted',
                'posted_by' => $adminId,
                'posted_at' => now(),
            ]);

            AuditService::log('purchase_return_posted', $prtn, null, ['status' => 'posted'], 'admin', $adminId);

            return $prtn->fresh(['lines', 'supplier']);
        });
    }
}
