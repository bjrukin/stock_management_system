<?php

namespace Tests\Unit;

use App\Models\Batch;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\FiscalYear;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\InventoryService;
use App\Services\PharmaExportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmaExportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FiscalYear $fiscalYear;
    protected Warehouse $warehouse;
    protected WarehouseLocation $storage;
    protected Product $product;
    protected Customer $customer;

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
            'code' => 'WH-EXP-TEST',
            'name' => 'Export Test Warehouse',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->storage = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'LOC-EXP-STORE',
            'name' => 'Export Storage',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $unitTab = Unit::create(['name' => 'Tablet', 'abbreviation' => 'Tab']);

        $this->product = Product::create([
            'code' => 'PRD-MET-500',
            'name' => 'Metformin 500mg',
            'generic_name' => 'Metformin',
            'base_unit_id' => $unitTab->id,
            'purchase_price' => 3.00,
            'selling_price' => 5.00,
            'mrp' => 6.00,
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-MET-001',
            'manufacturing_date_ad' => Carbon::today()->subMonths(1)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addYears(2)->format('Y-m-d'),
            'cost_price' => 3.00,
            'selling_price' => 5.00,
            'mrp' => 6.00,
            'is_active' => true,
        ]);

        $role = \App\Models\Role::create(['name' => 'Admin']);
        $admin = \App\Models\Admin::create([
            'role_id' => $role->id,
            'name' => 'Admin',
            'username' => 'admin_export',
            'status' => 1,
        ]);

        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storage->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 100,
            'unit_cost' => 3.00,
            'created_by' => $admin->id,
        ]);

        $this->customer = Customer::create([
            'code' => 'CUST-EXP-01',
            'name' => 'Om Hospital Pharmacy',
            'pan_vat_number' => '300554433',
            'current_balance' => 2500.00,
            'is_active' => true,
        ]);
    }

    public function test_exports_stock_valuation_csv(): void
    {
        $csv = PharmaExportService::exportStockValuationCsv($this->warehouse->id);

        $this->assertNotEmpty($csv);
        $this->assertStringContainsString('Product Code', $csv);
        $this->assertStringContainsString('PRD-MET-500', $csv);
        $this->assertStringContainsString('300.00', $csv); // 100 * 3.00
    }

    public function test_exports_customer_statement_csv(): void
    {
        CustomerLedgerEntry::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'customer_id' => $this->customer->id,
            'entry_date_ad' => '2026-10-18',
            'entry_date_bs' => '2083-07-02',
            'transaction_type' => 'SALE_INVOICE',
            'reference_type' => 'SaleInvoice',
            'reference_id' => 1,
            'reference_number' => 'INV-0099',
            'debit_amount' => 2500.00,
            'credit_amount' => 0.00,
            'running_balance' => 2500.00,
        ]);

        $csv = PharmaExportService::exportCustomerStatementCsv($this->customer->id);

        $this->assertNotEmpty($csv);
        $this->assertStringContainsString('Om Hospital Pharmacy', $csv);
        $this->assertStringContainsString('INV-0099', $csv);
        $this->assertStringContainsString('2500.00', $csv);
    }

    public function test_exports_sales_register_csv(): void
    {
        SaleInvoice::create([
            'invoice_number' => 'INV-CSV-01',
            'fiscal_year_id' => $this->fiscalYear->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'invoice_date_ad' => '2026-10-18',
            'invoice_date_bs' => '2083-07-02',
            'customer_name' => $this->customer->name,
            'total_amount' => 1130.00,
            'taxable_amount' => 1000.00,
            'tax_amount' => 130.00,
            'status' => 'posted',
        ]);

        $csv = PharmaExportService::exportSalesRegisterCsv('2026-10-01', '2026-10-31');

        $this->assertNotEmpty($csv);
        $this->assertStringContainsString('Invoice #', $csv);
        $this->assertStringContainsString('INV-CSV-01', $csv);
        $this->assertStringContainsString('1130.00', $csv);
    }

    public function test_imports_products_from_csv(): void
    {
        $csvData = <<<CSV
code,name,generic_name,brand,dosage_form,strength,base_unit,purchase_price,selling_price,mrp,min_stock,reorder_level
PRD-CSV-001,Cetirizine 10mg,Cetirizine,Alerid,Tablet,10mg,Tablet,1.50,2.50,3.00,50,100
PRD-CSV-002,Omeprazole 20mg,Omeprazole,Omee,Capsule,20mg,Capsule,4.00,6.00,7.50,40,80
CSV;

        $res = PharmaExportService::importProductsFromCsv($csvData);

        $this->assertEquals(2, $res['imported_count']);
        $this->assertEquals(0, $res['updated_count']);
        $this->assertEmpty($res['errors']);

        // Verify products created in database
        $p1 = Product::where('code', 'PRD-CSV-001')->first();
        $this->assertNotNull($p1);
        $this->assertEquals('Cetirizine 10mg', $p1->name);
        $this->assertEquals('1.5000', (string) $p1->purchase_price);

        $p2 = Product::where('code', 'PRD-CSV-002')->first();
        $this->assertNotNull($p2);
        $this->assertEquals('Omeprazole 20mg', $p2->name);
    }
}
