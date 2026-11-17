<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\CashBankAccount;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\GoodsReceipt;
use App\Models\InventoryBalance;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    /**
     * Executive Overview KPIs & Dashboard metrics.
     */
    public static function getDashboardSummary(?int $warehouseId = null, ?array $warehouseIds = null): array
    {
        $today = Carbon::today()->format('Y-m-d');

        // 1. Stock Valuation
        $stockVal = static::getStockValuationReport($warehouseId, $warehouseIds);

        // 2. Receivables & Payables
        $totalReceivables = $warehouseIds === null ? Customer::sum('current_balance') : static::getArAgingSummary(null, $warehouseIds)['total'];
        $totalPayables = $warehouseIds === null ? Supplier::sum('current_balance') : static::getApAgingSummary(null, $warehouseIds)['total'];

        // 3. Cash & Bank Balances
        $cashBankTotal = $warehouseIds === null ? CashBankAccount::where('is_active', true)->sum('current_balance') : null;

        // 4. Sales Today
        $salesToday = SaleInvoice::where('invoice_date_ad', $today)
            ->when($warehouseIds !== null, fn ($q) => $q->whereIn('warehouse_id', $warehouseIds))
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // 5. Expiry & Reorder alerts count
        $nearExpiry = static::getNearExpiryBatches(90, $warehouseId, $warehouseIds);
        $lowStock = static::getLowStockAlerts($warehouseId, $warehouseIds);

        return [
            'stock_valuation_cost' => $stockVal['total_cost_value'],
            'stock_valuation_mrp' => $stockVal['total_mrp_value'],
            'total_receivables' => number_format((float) $totalReceivables, 2, '.', ''),
            'total_payables' => number_format((float) $totalPayables, 2, '.', ''),
            'cash_bank_liquidity' => $cashBankTotal === null ? null : number_format((float) $cashBankTotal, 2, '.', ''),
            'sales_today' => number_format((float) $salesToday, 2, '.', ''),
            'near_expiry_count' => count($nearExpiry),
            'low_stock_count' => count($lowStock),
            'as_of_date_ad' => $today,
        ];
    }

    /**
     * Near-Expiry Batches Report (< 90 days, < 60 days, < 30 days).
     */
    public static function getNearExpiryBatches(int $days = 90, ?int $warehouseId = null, ?array $warehouseIds = null): array
    {
        $today = Carbon::today();
        $cutoff = Carbon::today()->addDays($days);

        $query = InventoryBalance::query()
            ->with(['product', 'batch', 'warehouse', 'location'])
            ->where('quantity_on_hand', '>', 0)
            ->whereHas('batch', function ($q) use ($today, $cutoff) {
                $q->where('expiry_date_ad', '>', $today)
                    ->where('expiry_date_ad', '<=', $cutoff)
                    ->where('is_recalled', false);
            });

        if ($warehouseIds !== null) {
            $query->whereIn('warehouse_id', $warehouseIds);
        }

        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        $balances = $query->get();
        $results = [];

        foreach ($balances as $bal) {
            $expiryDate = Carbon::parse($bal->batch->expiry_date_ad);
            $daysLeft = (int) $today->diffInDays($expiryDate, false);

            $bracket = match (true) {
                $daysLeft <= 30 => '< 30 days (Critical)',
                $daysLeft <= 60 => '31 - 60 days (Warning)',
                default => '61 - 90 days (Advisory)',
            };

            $results[] = [
                'product_id' => $bal->product_id,
                'product_code' => $bal->product->code,
                'product_name' => $bal->product->name,
                'generic_name' => $bal->product->generic_name,
                'batch_id' => $bal->batch_id,
                'batch_number' => $bal->batch->batch_number,
                'warehouse_id' => $bal->warehouse_id,
                'warehouse_name' => $bal->warehouse->name,
                'location_name' => $bal->location->name,
                'quantity_on_hand' => $bal->quantity_on_hand,
                'expiry_date_ad' => $bal->batch->expiry_date_ad?->format('Y-m-d'),
                'expiry_date_bs' => $bal->batch->expiry_date_bs,
                'days_remaining' => $daysLeft,
                'bracket' => $bracket,
                'unit_cost' => (string) $bal->batch->cost_price,
                'total_cost' => bcmul((string) $bal->quantity_on_hand, (string) $bal->batch->cost_price, 2),
            ];
        }

        usort($results, fn ($a, $b) => $a['days_remaining'] <=> $b['days_remaining']);

        return $results;
    }

    /**
     * Low-Stock & Out-of-Stock Reorder Alerts.
     */
    public static function getLowStockAlerts(?int $warehouseId = null, ?array $warehouseIds = null): array
    {
        $products = Product::where('is_active', true)->get();
        $alerts = [];

        foreach ($products as $prod) {
            $query = InventoryBalance::where('product_id', $prod->id);
            if ($warehouseIds !== null) {
                $query->whereIn('warehouse_id', $warehouseIds);
            }
            if ($warehouseId !== null) {
                $query->where('warehouse_id', $warehouseId);
            }

            $onHand = (int) $query->sum('quantity_on_hand');
            $reserved = (int) $query->sum('quantity_reserved');
            $available = max(0, $onHand - $reserved);

            $reorderLevel = (int) $prod->reorder_level;
            $minStock = (int) $prod->min_stock;

            if ($available <= $reorderLevel || $available <= $minStock || $available === 0) {
                $status = match (true) {
                    $available === 0 => 'Out of Stock',
                    $available <= $minStock => 'Critical Below Minimum',
                    default => 'Reorder Recommended',
                };

                $alerts[] = [
                    'product_id' => $prod->id,
                    'product_code' => $prod->code,
                    'product_name' => $prod->name,
                    'generic_name' => $prod->generic_name,
                    'available_stock' => $available,
                    'on_hand_stock' => $onHand,
                    'min_stock' => $minStock,
                    'reorder_level' => $reorderLevel,
                    'status' => $status,
                ];
            }
        }

        return $alerts;
    }

    /**
     * Stock Valuation Report (FIFO / Landed cost basis vs MRP).
     */
    public static function getStockValuationReport(?int $warehouseId = null, ?array $warehouseIds = null): array
    {
        $query = InventoryBalance::query()
            ->with(['product', 'batch', 'warehouse', 'location'])
            ->where('quantity_on_hand', '>', 0);

        if ($warehouseIds !== null) {
            $query->whereIn('warehouse_id', $warehouseIds);
        }

        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        $balances = $query->get();

        $rows = [];
        $totalCostVal = '0.00';
        $totalMrpVal = '0.00';
        $totalUnits = 0;

        foreach ($balances as $bal) {
            $qty = (string) $bal->quantity_on_hand;
            $cost = (string) $bal->batch->cost_price;
            $mrp = (string) $bal->batch->mrp;

            $costVal = bcmul($qty, $cost, 2);
            $mrpVal = bcmul($qty, $mrp, 2);

            $rows[] = [
                'product_code' => $bal->product->code,
                'product_name' => $bal->product->name,
                'generic_name' => $bal->product->generic_name,
                'batch_number' => $bal->batch->batch_number,
                'expiry_date_ad' => $bal->batch->expiry_date_ad?->format('Y-m-d'),
                'expiry_date_bs' => $bal->batch->expiry_date_bs,
                'warehouse' => $bal->warehouse->name,
                'location' => $bal->location->name,
                'quantity_on_hand' => (int) $qty,
                'unit_cost' => $cost,
                'cost_value' => $costVal,
                'mrp' => $mrp,
                'mrp_value' => $mrpVal,
            ];

            $totalCostVal = bcadd($totalCostVal, $costVal, 2);
            $totalMrpVal = bcadd($totalMrpVal, $mrpVal, 2);
            $totalUnits += (int) $qty;
        }

        return [
            'rows' => $rows,
            'total_units' => $totalUnits,
            'total_cost_value' => $totalCostVal,
            'total_mrp_value' => $totalMrpVal,
        ];
    }

    /**
     * Accounts Receivable (AR) Aging Summary.
     */
    public static function getArAgingSummary(?int $customerId = null, ?array $warehouseIds = null): array
    {
        $today = Carbon::today();
        $query = SaleInvoice::where('status', 'posted')
            ->where('credit_amount', '>', 0);

        if ($warehouseIds !== null) {
            $query->whereIn('warehouse_id', $warehouseIds);
        }

        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        $invoices = $query->with('customer')->get();

        $aging = [
            'current' => '0.00',     // 0 - 30 days
            'days_31_60' => '0.00',  // 31 - 60 days
            'days_61_90' => '0.00',  // 61 - 90 days
            'over_90' => '0.00',     // > 90 days
            'total' => '0.00',
            'customers' => [],
        ];

        foreach ($invoices as $inv) {
            $invoiceDate = Carbon::parse($inv->invoice_date_ad);
            $ageDays = $invoiceDate->diffInDays($today);
            $unpaid = (string) $inv->credit_amount;

            $custName = $inv->customer ? $inv->customer->name : $inv->customer_name;
            if (! isset($aging['customers'][$custName])) {
                $aging['customers'][$custName] = [
                    'current' => '0.00',
                    'days_31_60' => '0.00',
                    'days_61_90' => '0.00',
                    'over_90' => '0.00',
                    'total' => '0.00',
                ];
            }

            if ($ageDays <= 30) {
                $aging['current'] = bcadd($aging['current'], $unpaid, 2);
                $aging['customers'][$custName]['current'] = bcadd($aging['customers'][$custName]['current'], $unpaid, 2);
            } elseif ($ageDays <= 60) {
                $aging['days_31_60'] = bcadd($aging['days_31_60'], $unpaid, 2);
                $aging['customers'][$custName]['days_31_60'] = bcadd($aging['customers'][$custName]['days_31_60'], $unpaid, 2);
            } elseif ($ageDays <= 90) {
                $aging['days_61_90'] = bcadd($aging['days_61_90'], $unpaid, 2);
                $aging['customers'][$custName]['days_61_90'] = bcadd($aging['customers'][$custName]['days_61_90'], $unpaid, 2);
            } else {
                $aging['over_90'] = bcadd($aging['over_90'], $unpaid, 2);
                $aging['customers'][$custName]['over_90'] = bcadd($aging['customers'][$custName]['over_90'], $unpaid, 2);
            }

            $aging['total'] = bcadd($aging['total'], $unpaid, 2);
            $aging['customers'][$custName]['total'] = bcadd($aging['customers'][$custName]['total'], $unpaid, 2);
        }

        return $aging;
    }

    /**
     * Accounts Payable (AP) Aging Summary.
     */
    public static function getApAgingSummary(?int $supplierId = null, ?array $warehouseIds = null): array
    {
        $today = Carbon::today();
        $query = GoodsReceipt::where('status', 'posted');

        if ($warehouseIds !== null) {
            $query->whereIn('warehouse_id', $warehouseIds);
        }

        if ($supplierId !== null) {
            $query->where('supplier_id', $supplierId);
        }

        $grns = $query->with('supplier')->get();

        $aging = [
            'current' => '0.00',
            'days_31_60' => '0.00',
            'days_61_90' => '0.00',
            'over_90' => '0.00',
            'total' => '0.00',
            'suppliers' => [],
        ];

        foreach ($grns as $grn) {
            $receiptDate = Carbon::parse($grn->receipt_date_ad);
            $ageDays = $receiptDate->diffInDays($today);
            $amount = (string) $grn->total_amount;

            $supName = $grn->supplier ? $grn->supplier->name : "Supplier #{$grn->supplier_id}";
            if (! isset($aging['suppliers'][$supName])) {
                $aging['suppliers'][$supName] = [
                    'current' => '0.00',
                    'days_31_60' => '0.00',
                    'days_61_90' => '0.00',
                    'over_90' => '0.00',
                    'total' => '0.00',
                ];
            }

            if ($ageDays <= 30) {
                $aging['current'] = bcadd($aging['current'], $amount, 2);
                $aging['suppliers'][$supName]['current'] = bcadd($aging['suppliers'][$supName]['current'], $amount, 2);
            } elseif ($ageDays <= 60) {
                $aging['days_31_60'] = bcadd($aging['days_31_60'], $amount, 2);
                $aging['suppliers'][$supName]['days_31_60'] = bcadd($aging['suppliers'][$supName]['days_31_60'], $amount, 2);
            } elseif ($ageDays <= 90) {
                $aging['days_61_90'] = bcadd($aging['days_61_90'], $amount, 2);
                $aging['suppliers'][$supName]['days_61_90'] = bcadd($aging['suppliers'][$supName]['days_61_90'], $amount, 2);
            } else {
                $aging['over_90'] = bcadd($aging['over_90'], $amount, 2);
                $aging['suppliers'][$supName]['over_90'] = bcadd($aging['suppliers'][$supName]['over_90'], $amount, 2);
            }

            $aging['total'] = bcadd($aging['total'], $amount, 2);
            $aging['suppliers'][$supName]['total'] = bcadd($aging['suppliers'][$supName]['total'], $amount, 2);
        }

        return $aging;
    }

    /**
     * Customer Statement of Account with chronological ledger transactions.
     */
    public static function getCustomerStatement(int $customerId, ?string $fromDateAd = null, ?string $toDateAd = null): array
    {
        $customer = Customer::findOrFail($customerId);

        $query = CustomerLedgerEntry::where('customer_id', $customer->id)
            ->orderBy('entry_date_ad')
            ->orderBy('id');

        if ($fromDateAd) {
            $query->where('entry_date_ad', '>=', $fromDateAd);
        }
        if ($toDateAd) {
            $query->where('entry_date_ad', '<=', $toDateAd);
        }

        $entries = $query->get();

        $rows = [];
        $totalDebit = '0.00';
        $totalCredit = '0.00';

        foreach ($entries as $e) {
            $debit = (string) $e->debit_amount;
            $credit = (string) $e->credit_amount;

            $rows[] = [
                'id' => $e->id,
                'date_ad' => $e->entry_date_ad?->format('Y-m-d'),
                'date_bs' => $e->entry_date_bs,
                'transaction_type' => $e->transaction_type,
                'reference_number' => $e->reference_number,
                'debit_amount' => $debit,
                'credit_amount' => $credit,
                'running_balance' => (string) $e->running_balance,
                'notes' => $e->notes,
            ];

            $totalDebit = bcadd($totalDebit, $debit, 2);
            $totalCredit = bcadd($totalCredit, $credit, 2);
        }

        return [
            'customer' => [
                'id' => $customer->id,
                'code' => $customer->code,
                'name' => $customer->name,
                'pan_vat' => $customer->pan_vat_number,
                'address' => $customer->address,
                'current_balance' => (string) $customer->current_balance,
            ],
            'period' => [
                'from_date_ad' => $fromDateAd,
                'to_date_ad' => $toDateAd,
            ],
            'entries' => $rows,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'closing_balance' => (string) $customer->current_balance,
        ];
    }

    /**
     * Supplier Statement of Account with chronological ledger transactions.
     */
    public static function getSupplierStatement(int $supplierId, ?string $fromDateAd = null, ?string $toDateAd = null): array
    {
        $supplier = Supplier::findOrFail($supplierId);

        $query = SupplierLedgerEntry::where('supplier_id', $supplier->id)
            ->orderBy('entry_date_ad')
            ->orderBy('id');

        if ($fromDateAd) {
            $query->where('entry_date_ad', '>=', $fromDateAd);
        }
        if ($toDateAd) {
            $query->where('entry_date_ad', '<=', $toDateAd);
        }

        $entries = $query->get();

        $rows = [];
        $totalDebit = '0.00';
        $totalCredit = '0.00';

        foreach ($entries as $e) {
            $debit = (string) $e->debit_amount;
            $credit = (string) $e->credit_amount;

            $rows[] = [
                'id' => $e->id,
                'date_ad' => $e->entry_date_ad?->format('Y-m-d'),
                'date_bs' => $e->entry_date_bs,
                'transaction_type' => $e->transaction_type,
                'reference_number' => $e->reference_number,
                'debit_amount' => $debit,
                'credit_amount' => $credit,
                'running_balance' => (string) $e->running_balance,
                'notes' => $e->notes,
            ];

            $totalDebit = bcadd($totalDebit, $debit, 2);
            $totalCredit = bcadd($totalCredit, $credit, 2);
        }

        return [
            'supplier' => [
                'id' => $supplier->id,
                'code' => $supplier->code,
                'name' => $supplier->name,
                'pan_vat' => $supplier->pan_vat_number,
                'address' => $supplier->address,
                'current_balance' => (string) $supplier->current_balance,
            ],
            'period' => [
                'from_date_ad' => $fromDateAd,
                'to_date_ad' => $toDateAd,
            ],
            'entries' => $rows,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'closing_balance' => (string) $supplier->current_balance,
        ];
    }

    /**
     * Sales Register (Compliant with Nepal Inland Revenue Department VAT rules).
     */
    public static function getSalesRegister(?string $fromDateAd = null, ?string $toDateAd = null, ?int $fiscalYearId = null, ?array $warehouseIds = null): array
    {
        $query = SaleInvoice::where('status', '!=', 'cancelled')
            ->orderBy('invoice_date_ad')
            ->orderBy('id');

        if ($warehouseIds !== null) {
            $query->whereIn('warehouse_id', $warehouseIds);
        }

        if ($fromDateAd) {
            $query->where('invoice_date_ad', '>=', $fromDateAd);
        }
        if ($toDateAd) {
            $query->where('invoice_date_ad', '<=', $toDateAd);
        }
        if ($fiscalYearId) {
            $query->where('fiscal_year_id', $fiscalYearId);
        }

        $invoices = $query->get();

        $rows = [];
        $totalNonTaxable = '0.00';
        $totalTaxable = '0.00';
        $totalTax = '0.00';
        $totalGrand = '0.00';

        foreach ($invoices as $inv) {
            $nonTaxable = (string) $inv->non_taxable_amount;
            $taxable = (string) $inv->taxable_amount;
            $tax = (string) $inv->tax_amount;
            $total = (string) $inv->total_amount;

            $rows[] = [
                'invoice_number' => $inv->invoice_number,
                'invoice_date_ad' => $inv->invoice_date_ad?->format('Y-m-d'),
                'invoice_date_bs' => $inv->invoice_date_bs,
                'customer_name' => $inv->customer_name,
                'customer_pan' => $inv->customer_pan ?? '-',
                'non_taxable_amount' => $nonTaxable,
                'taxable_amount' => $taxable,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'status' => $inv->status,
            ];

            $totalNonTaxable = bcadd($totalNonTaxable, $nonTaxable, 2);
            $totalTaxable = bcadd($totalTaxable, $taxable, 2);
            $totalTax = bcadd($totalTax, $tax, 2);
            $totalGrand = bcadd($totalGrand, $total, 2);
        }

        return [
            'invoices' => $rows,
            'total_non_taxable' => $totalNonTaxable,
            'total_taxable' => $totalTaxable,
            'total_tax' => $totalTax,
            'total_grand' => $totalGrand,
        ];
    }

    /**
     * Purchase Register (Compliant with Nepal Inland Revenue Department VAT rules).
     */
    public static function getPurchaseRegister(?string $fromDateAd = null, ?string $toDateAd = null, ?int $fiscalYearId = null, ?array $warehouseIds = null): array
    {
        $query = GoodsReceipt::with('supplier')
            ->where('status', '!=', 'cancelled')
            ->orderBy('receipt_date_ad')
            ->orderBy('id');

        if ($warehouseIds !== null) {
            $query->whereIn('warehouse_id', $warehouseIds);
        }

        if ($fromDateAd) {
            $query->where('receipt_date_ad', '>=', $fromDateAd);
        }
        if ($toDateAd) {
            $query->where('receipt_date_ad', '<=', $toDateAd);
        }
        if ($fiscalYearId) {
            $query->where('fiscal_year_id', $fiscalYearId);
        }

        $receipts = $query->get();

        $rows = [];
        $totalTaxable = '0.00';
        $totalTax = '0.00';
        $totalGrand = '0.00';

        foreach ($receipts as $r) {
            $taxable = (string) $r->taxable_amount;
            $tax = (string) $r->tax_amount;
            $total = (string) $r->total_amount;

            $rows[] = [
                'grn_number' => $r->grn_number,
                'receipt_date_ad' => $r->receipt_date_ad?->format('Y-m-d'),
                'receipt_date_bs' => $r->receipt_date_bs,
                'supplier_invoice_number' => $r->supplier_invoice_number ?? '-',
                'supplier_name' => $r->supplier ? $r->supplier->name : "Supplier #{$r->supplier_id}",
                'supplier_pan' => $r->supplier?->pan_vat_number ?? '-',
                'taxable_amount' => $taxable,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'status' => $r->status,
            ];

            $totalTaxable = bcadd($totalTaxable, $taxable, 2);
            $totalTax = bcadd($totalTax, $tax, 2);
            $totalGrand = bcadd($totalGrand, $total, 2);
        }

        return [
            'receipts' => $rows,
            'total_taxable' => $totalTaxable,
            'total_tax' => $totalTax,
            'total_grand' => $totalGrand,
        ];
    }
}
