<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\FiscalYear;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\Role;
use App\Models\SaleInvoice;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use App\Models\TaxCategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\InventoryService;
use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FiscalYear $fiscalYear;
    protected Warehouse $warehouse;
    protected WarehouseLocation $storage;
    protected Product $product;
    protected Batch $batchExpiringSoon;
    protected Batch $batchExpiringFar;
    protected Customer $customer;
    protected Supplier $supplier;
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fiscalYear = FiscalYear::create([
            'name' => '2081/82',
            'start_date_ad' => '2024-07-16',
            'start_date_bs' => '2081-04-01',
            'end_date_ad' => '2025-07-15',
            'end_date_bs' => '2082-03-31',
            'is_current' => true,
        ]);

        $this->warehouse = Warehouse::create([
            'code' => 'WH-REP-TEST',
            'name' => 'Reporting Test Warehouse',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->storage = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'LOC-REP-STORE',
            'name' => 'Store Rack 1',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $unitTab = Unit::create(['name' => 'Tablet', 'abbreviation' => 'Tab']);

        $taxCat = TaxCategory::create([
            'code' => 'VAT13',
            'name' => 'VAT 13%',
            'rate' => 13.00,
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'code' => 'PRD-REP-AZI',
            'name' => 'Azithromycin 500mg',
            'generic_name' => 'Azithromycin',
            'base_unit_id' => $unitTab->id,
            'tax_category_id' => $taxCat->id,
            'purchase_price' => 20.00,
            'selling_price' => 30.00,
            'mrp' => 35.00,
            'min_stock' => 10,
            'reorder_level' => 25,
            'is_active' => true,
        ]);

        // Batch 1: Expiring in 20 days (< 30 days critical)
        $this->batchExpiringSoon = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-NEAR-01',
            'manufacturing_date_ad' => Carbon::today()->subMonths(10)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addDays(20)->format('Y-m-d'),
            'cost_price' => 20.00,
            'selling_price' => 30.00,
            'mrp' => 35.00,
            'is_active' => true,
        ]);

        // Batch 2: Expiring in 400 days
        $this->batchExpiringFar = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-FAR-01',
            'manufacturing_date_ad' => Carbon::today()->subMonths(2)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addDays(400)->format('Y-m-d'),
            'cost_price' => 20.00,
            'selling_price' => 30.00,
            'mrp' => 35.00,
            'is_active' => true,
        ]);

        $role = Role::create(['name' => 'Manager']);
        $this->admin = Admin::create([
            'role_id' => $role->id,
            'name' => 'Report Officer',
            'username' => 'report_officer',
            'status' => 1,
        ]);

        $this->customer = Customer::create([
            'code' => 'CUST-REP-01',
            'name' => 'KMC Hospital Pharmacy',
            'pan_vat_number' => '300889911',
            'current_balance' => 15000.00,
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-REP-01',
            'name' => 'Omnica Laboratories',
            'pan_vat_number' => '300778899',
            'current_balance' => 25000.00,
            'is_active' => true,
        ]);

        // Inflow stock: 15 of near expiry, 50 of far expiry
        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storage->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batchExpiringSoon->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 15,
            'unit_cost' => 20.00,
            'created_by' => $this->admin->id,
        ]);

        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storage->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batchExpiringFar->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 50,
            'unit_cost' => 20.00,
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_dashboard_summary_kpis(): void
    {
        $kpi = ReportingService::getDashboardSummary($this->warehouse->id);

        $this->assertEquals('1300.00', $kpi['stock_valuation_cost']); // (15+50)*20 = 1300
        $this->assertEquals('2275.00', $kpi['stock_valuation_mrp']); // (15+50)*35 = 2275
        $this->assertEquals('15000.00', $kpi['total_receivables']);
        $this->assertEquals('25000.00', $kpi['total_payables']);
        $this->assertEquals(1, $kpi['near_expiry_count']);
    }

    public function test_near_expiry_batches_brackets(): void
    {
        $near = ReportingService::getNearExpiryBatches(90, $this->warehouse->id);

        $this->assertCount(1, $near);
        $item = $near[0];
        $this->assertEquals('BATCH-NEAR-01', $item['batch_number']);
        $this->assertEquals(15, $item['quantity_on_hand']);
        $this->assertEquals('< 30 days (Critical)', $item['bracket']);
        $this->assertEquals('300.00', $item['total_cost']);
    }

    public function test_stock_valuation_report(): void
    {
        $val = ReportingService::getStockValuationReport($this->warehouse->id);

        $this->assertEquals(65, $val['total_units']);
        $this->assertEquals('1300.00', $val['total_cost_value']);
        $this->assertEquals('2275.00', $val['total_mrp_value']);
        $this->assertCount(2, $val['rows']);
    }

    public function test_ar_and_ap_aging_summary(): void
    {
        // Create an unpaid invoice 45 days ago
        SaleInvoice::create([
            'invoice_number' => 'INV-AGING-01',
            'fiscal_year_id' => $this->fiscalYear->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'invoice_date_ad' => Carbon::today()->subDays(45)->format('Y-m-d'),
            'invoice_date_bs' => '2081-05-15',
            'customer_name' => $this->customer->name,
            'payment_method' => 'credit',
            'total_amount' => 5000.00,
            'credit_amount' => 5000.00,
            'status' => 'posted',
        ]);

        $arAging = ReportingService::getArAgingSummary($this->customer->id);

        $this->assertEquals('5000.00', $arAging['days_31_60']);
        $this->assertEquals('5000.00', $arAging['total']);

        // Create a GRN 15 days ago
        GoodsReceipt::create([
            'grn_number' => 'GRN-AGING-01',
            'fiscal_year_id' => $this->fiscalYear->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'receipt_date_ad' => Carbon::today()->subDays(15)->format('Y-m-d'),
            'receipt_date_bs' => '2081-06-15',
            'total_amount' => 8000.00,
            'status' => 'posted',
        ]);

        $apAging = ReportingService::getApAgingSummary($this->supplier->id);

        $this->assertEquals('8000.00', $apAging['current']);
        $this->assertEquals('8000.00', $apAging['total']);
    }

    public function test_customer_and_supplier_statements(): void
    {
        CustomerLedgerEntry::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'customer_id' => $this->customer->id,
            'entry_date_ad' => '2026-10-18',
            'entry_date_bs' => '2083-07-02',
            'transaction_type' => 'SALE_INVOICE',
            'reference_type' => 'SaleInvoice',
            'reference_id' => 1,
            'reference_number' => 'INV-001',
            'debit_amount' => 15000.00,
            'credit_amount' => 0.00,
            'running_balance' => 15000.00,
            'notes' => 'Invoice for Azithromycin',
        ]);

        $stmt = ReportingService::getCustomerStatement($this->customer->id);

        $this->assertEquals('KMC Hospital Pharmacy', $stmt['customer']['name']);
        $this->assertEquals('15000.00', $stmt['total_debit']);
        $this->assertEquals('0.00', $stmt['total_credit']);
        $this->assertCount(1, $stmt['entries']);
    }

    public function test_sales_and_purchase_registers(): void
    {
        SaleInvoice::create([
            'invoice_number' => 'INV-REG-01',
            'fiscal_year_id' => $this->fiscalYear->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'invoice_date_ad' => '2026-10-18',
            'invoice_date_bs' => '2083-07-02',
            'customer_name' => $this->customer->name,
            'customer_pan' => $this->customer->pan_vat_number,
            'non_taxable_amount' => 1000.00,
            'taxable_amount' => 2000.00,
            'tax_amount' => 260.00,
            'total_amount' => 3260.00,
            'status' => 'posted',
        ]);

        $reg = ReportingService::getSalesRegister('2026-10-01', '2026-10-31');

        $this->assertCount(1, $reg['invoices']);
        $this->assertEquals('1000.00', $reg['total_non_taxable']);
        $this->assertEquals('2000.00', $reg['total_taxable']);
        $this->assertEquals('260.00', $reg['total_tax']);
        $this->assertEquals('3260.00', $reg['total_grand']);
    }
}
