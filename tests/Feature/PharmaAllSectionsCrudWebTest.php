<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\CashBankAccount;
use App\Models\Category;
use App\Models\Customer;
use App\Models\FiscalYear;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmaAllSectionsCrudWebTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected Admin $checker;
    protected Warehouse $warehouse1;
    protected Warehouse $warehouse2;
    protected WarehouseLocation $loc1;
    protected WarehouseLocation $loc2;
    protected Product $product;
    protected Batch $batch;
    protected Unit $unit;
    protected Category $category;
    protected Manufacturer $manufacturer;
    protected Customer $customer;
    protected Supplier $supplier;
    protected CashBankAccount $account;
    protected FiscalYear $fiscalYear;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['id' => 1, 'name' => 'Super Admin', 'guard_name' => 'admin']);

        $this->admin = Admin::create([
            'id' => 1,
            'name' => 'Lead Admin',
            'username' => 'lead_admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'status' => 'active',
        ]);

        $this->checker = Admin::create([
            'id' => 2,
            'name' => 'Checker Admin',
            'username' => 'checker_admin',
            'email' => 'checker@test.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'status' => 'active',
        ]);

        $this->fiscalYear = FiscalYear::create([
            'name' => '2081/82',
            'start_date_ad' => '2024-07-16',
            'start_date_bs' => '2081-04-01',
            'end_date_ad' => '2025-07-15',
            'end_date_bs' => '2082-03-31',
            'is_current' => true,
        ]);

        $this->category = Category::create(['code' => 'ANALG', 'name' => 'Analgesics', 'is_active' => true]);
        $this->manufacturer = Manufacturer::create(['code' => 'NPL', 'name' => 'Nepal Pharma Ltd', 'is_active' => true]);
        $this->unit = Unit::create(['name' => 'Tablet', 'abbreviation' => 'Tab', 'allow_decimal' => false, 'is_active' => true]);

        $this->warehouse1 = Warehouse::create([
            'code' => 'WH-MAIN',
            'name' => 'Main Warehouse',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->warehouse2 = Warehouse::create([
            'code' => 'WH-BRANCH',
            'name' => 'Branch Warehouse',
            'is_primary' => false,
            'is_active' => true,
        ]);

        $this->loc1 = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse1->id,
            'code' => 'RACK-01',
            'name' => 'Main Rack 01',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
            'is_active' => true,
        ]);

        $this->loc2 = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse2->id,
            'code' => 'RACK-02',
            'name' => 'Branch Rack 01',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'code' => 'MED-PARA',
            'name' => 'Paracetamol 500mg',
            'generic_name' => 'Paracetamol',
            'category_id' => $this->category->id,
            'manufacturer_id' => $this->manufacturer->id,
            'base_unit_id' => $this->unit->id,
            'mrp' => 50.00,
            'purchase_price' => 30.00,
            'selling_price' => 45.00,
            'is_active' => true,
        ]);

        $this->batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-TEST-01',
            'mfg_date_ad' => '2024-01-01',
            'expiry_date_ad' => '2026-12-31',
            'cost_price' => 30.00,
            'mrp' => 50.00,
            'selling_price' => 45.00,
        ]);

        // Seed stock for testing sales and transfers
        \App\Services\InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse1->id,
            'location_id' => $this->loc1->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 500,
            'unit_cost' => 30.00,
            'created_by' => 1,
            'notes' => 'Initial stock setup',
        ]);

        $this->customer = Customer::create([
            'code' => 'CUST-001',
            'name' => 'Patan Care Pharmacy',
            'credit_limit' => 100000.00,
            'current_balance' => 0.00,
            'credit_period_days' => 30,
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-001',
            'name' => 'Kathmandu Pharma Dist.',
            'credit_limit' => 200000.00,
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        $this->account = CashBankAccount::create([
            'code' => 'ACC-01',
            'name' => 'Main Treasury A/C',
            'type' => 'bank',
            'bank_name' => 'Nabil Bank',
            'account_number' => '123456789',
            'current_balance' => 50000.00,
            'is_active' => true,
        ]);
    }

    public function test_product_crud_lifecycle(): void
    {
        $this->actingAs($this->admin, 'admin');

        // 1. Create page
        $res = $this->get('https://localhost/admin/pharma/product/create');
        $res->assertStatus(200);

        // 2. Store
        $storeRes = $this->post('https://localhost/admin/pharma/product/store', [
            'name' => 'Amoxicillin 250mg Capsule',
            'generic_name' => 'Amoxicillin',
            'code' => 'MED-AMOX',
            'category_id' => $this->category->id,
            'base_unit_id' => $this->unit->id,
            'mrp' => 80.00,
            'purchase_price' => 50.00,
            'selling_price' => 70.00,
        ]);
        $storeRes->assertRedirect(route('admin.pharma.products'));
        $this->assertDatabaseHas('products', ['code' => 'MED-AMOX', 'name' => 'Amoxicillin 250mg Capsule']);

        $newProd = Product::where('code', 'MED-AMOX')->first();

        // 3. Edit page
        $editRes = $this->get("https://localhost/admin/pharma/product/{$newProd->id}/edit");
        $editRes->assertStatus(200);

        // 4. Update
        $updateRes = $this->post("https://localhost/admin/pharma/product/{$newProd->id}/update", [
            'name' => 'Amoxicillin 250mg Capsule (Updated)',
            'generic_name' => 'Amoxicillin',
            'code' => 'MED-AMOX',
            'category_id' => $this->category->id,
            'base_unit_id' => $this->unit->id,
            'mrp' => 90.00,
            'purchase_price' => 55.00,
            'selling_price' => 75.00,
        ]);
        $updateRes->assertRedirect(route('admin.pharma.products'));
        $this->assertDatabaseHas('products', ['id' => $newProd->id, 'name' => 'Amoxicillin 250mg Capsule (Updated)']);

        // 5. Delete
        $delRes = $this->post("https://localhost/admin/pharma/product/{$newProd->id}/delete");
        $delRes->assertRedirect(route('admin.pharma.products'));
        $this->assertSoftDeleted('products', ['id' => $newProd->id]);

        // 6. Batches list & status filter
        $batchRes = $this->get('https://localhost/admin/pharma/batches?status=active');
        $batchRes->assertStatus(200);
    }

    public function test_supplier_and_customer_crud_lifecycle(): void
    {
        $this->actingAs($this->admin, 'admin');

        // Supplier Create & Store
        $supStore = $this->post('https://localhost/admin/pharma/supplier/store', [
            'name' => 'Sunlight Pharma Traders',
            'code' => 'SUP-SUN',
            'phone' => '9800000000',
            'credit_limit' => 50000,
        ]);
        $supStore->assertRedirect(route('admin.pharma.suppliers'));
        $this->assertDatabaseHas('suppliers', ['code' => 'SUP-SUN']);

        $sup = Supplier::where('code', 'SUP-SUN')->first();

        // Supplier Edit & Update
        $supEdit = $this->get("https://localhost/admin/pharma/supplier/{$sup->id}/edit");
        $supEdit->assertStatus(200);
        $this->post("https://localhost/admin/pharma/supplier/{$sup->id}/update", [
            'name' => 'Sunlight Pharma Traders Pvt Ltd',
            'code' => 'SUP-SUN',
        ])->assertRedirect(route('admin.pharma.suppliers'));
        $this->assertDatabaseHas('suppliers', ['id' => $sup->id, 'name' => 'Sunlight Pharma Traders Pvt Ltd']);

        // Customer Create & Store
        $custStore = $this->post('https://localhost/admin/pharma/customer/store', [
            'name' => 'Bhaktapur Community Polyclinic',
            'code' => 'CUST-BKT',
            'credit_limit' => 75000,
        ]);
        $custStore->assertRedirect(route('admin.pharma.customers'));
        $this->assertDatabaseHas('customers', ['code' => 'CUST-BKT']);

        $cust = Customer::where('code', 'CUST-BKT')->first();

        // Customer Edit & Update
        $custEdit = $this->get("https://localhost/admin/pharma/customer/{$cust->id}/edit");
        $custEdit->assertStatus(200);
        $this->post("https://localhost/admin/pharma/customer/{$cust->id}/update", [
            'name' => 'Bhaktapur Community Hospital & Polyclinic',
            'code' => 'CUST-BKT',
            'credit_limit' => 90000,
        ])->assertRedirect(route('admin.pharma.customers'));
        $this->assertDatabaseHas('customers', ['id' => $cust->id, 'name' => 'Bhaktapur Community Hospital & Polyclinic']);
    }

    public function test_warehouse_and_location_crud(): void
    {
        $this->actingAs($this->admin, 'admin');

        // Store Warehouse
        $whRes = $this->post('https://localhost/admin/pharma/warehouse/store', [
            'name' => 'Pokhara Distribution Depot',
            'code' => 'WH-PKR',
            'phone' => '061-555555',
        ]);
        $whRes->assertRedirect(route('admin.pharma.warehouses'));
        $this->assertDatabaseHas('warehouses', ['code' => 'WH-PKR']);

        $newWh = Warehouse::where('code', 'WH-PKR')->first();

        // Store Location inside new warehouse
        $locRes = $this->post('https://localhost/admin/pharma/warehouse/location/store', [
            'warehouse_id' => $newWh->id,
            'name' => 'Cold Storage Unit 1',
            'code' => 'COLD-01',
            'type' => 'cold_storage',
            'storage_condition' => '2-8°C',
        ]);
        $locRes->assertRedirect(route('admin.pharma.warehouses'));
        $this->assertDatabaseHas('warehouse_locations', ['warehouse_id' => $newWh->id, 'code' => 'COLD-01']);

        $newLoc = \App\Models\WarehouseLocation::where('code', 'COLD-01')->first();

        // View Warehouse Locations table
        $viewLocRes = $this->get("https://localhost/admin/pharma/warehouse/{$newWh->id}/locations");
        $viewLocRes->assertStatus(200);

        // Update Location
        $updateLocRes = $this->post("https://localhost/admin/pharma/warehouse/location/{$newLoc->id}/update", [
            'warehouse_id' => $newWh->id,
            'name' => 'Cold Storage Unit 1 (Updated)',
            'code' => 'COLD-01',
            'type' => 'cold_storage',
            'storage_condition' => '2-4°C',
            'is_sellable' => 1,
            'is_available' => 1,
            'is_active' => 1,
        ]);
        $updateLocRes->assertRedirect();
        $this->assertDatabaseHas('warehouse_locations', ['id' => $newLoc->id, 'name' => 'Cold Storage Unit 1 (Updated)', 'storage_condition' => '2-4°C']);

        // Delete Location
        $delLocRes = $this->post("https://localhost/admin/pharma/warehouse/location/{$newLoc->id}/delete");
        $delLocRes->assertRedirect();
        $this->assertSoftDeleted('warehouse_locations', ['id' => $newLoc->id]);
    }

    public function test_sales_invoice_lifecycle(): void
    {
        $this->actingAs($this->admin, 'admin');

        // 1. Create form
        $createRes = $this->get('https://localhost/admin/pharma/sale-invoice/create');
        $createRes->assertStatus(200);

        // 2. Store invoice
        $storeRes = $this->post('https://localhost/admin/pharma/sale-invoice/store', [
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse1->id,
            'invoice_date_ad' => Carbon::today()->format('Y-m-d'),
            'payment_method' => 'credit',
            'auto_fefo' => 1,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'unit_id' => $this->unit->id,
                    'qty' => 20,
                    'unit_price' => 45.00,
                    'discount_pct' => 0,
                    'tax_rate' => 0,
                ],
            ],
        ]);

        $this->assertDatabaseHas('sale_invoices', [
            'customer_id' => $this->customer->id,
            'status' => 'draft',
        ]);

        $invoice = \App\Models\SaleInvoice::where('customer_id', $this->customer->id)->first();
        $storeRes->assertRedirect(route('admin.pharma.sale-invoice.show', $invoice->id));

        // 3. Show page
        $showRes = $this->get("https://localhost/admin/pharma/sale-invoice/{$invoice->id}");
        $showRes->assertStatus(200);

        // 4. Post invoice
        $postRes = $this->post("https://localhost/admin/pharma/sale-invoice/{$invoice->id}/post");
        $postRes->assertRedirect(route('admin.pharma.sale-invoice.show', $invoice->id));

        $invoice->refresh();
        $this->assertEquals('posted', $invoice->status);
        $this->assertDatabaseHas('customer_ledger_entries', [
            'customer_id' => $this->customer->id,
            'reference_type' => 'SaleInvoice',
            'reference_id' => $invoice->id,
        ]);

        // 5. Print PDF
        $printRes = $this->get("https://localhost/admin/pharma/sale-invoice/{$invoice->id}/print");
        $printRes->assertStatus(200);
        $this->assertEquals('application/pdf', $printRes->headers->get('Content-Type'));
    }

    public function test_sales_return_lifecycle(): void
    {
        $this->actingAs($this->admin, 'admin');

        // Create form
        $createRes = $this->get('https://localhost/admin/pharma/sales-return/create');
        $createRes->assertStatus(200);

        // Store return
        $storeRes = $this->post('https://localhost/admin/pharma/sales-return/store', [
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse1->id,
            'return_date_ad' => Carbon::today()->format('Y-m-d'),
            'reason' => 'Damaged carton replacement',
            'credit_note_issued' => 1,
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $this->batch->id,
                    'unit_id' => $this->unit->id,
                    'returned_qty' => 5,
                    'unit_price' => 45.00,
                    'condition' => 'saleable',
                ],
            ],
        ]);

        $this->assertDatabaseHas('sale_returns', [
            'customer_id' => $this->customer->id,
            'status' => 'draft',
        ]);

        $saleReturn = \App\Models\SaleReturn::where('customer_id', $this->customer->id)->first();
        $storeRes->assertRedirect(route('admin.pharma.sales-return.show', $saleReturn->id));

        // Post return
        $postRes = $this->post("https://localhost/admin/pharma/sales-return/{$saleReturn->id}/post");
        $postRes->assertRedirect(route('admin.pharma.sales-return.show', $saleReturn->id));

        $saleReturn->refresh();
        $this->assertEquals('posted', $saleReturn->status);
    }

    public function test_stock_transfer_lifecycle(): void
    {
        $this->actingAs($this->admin, 'admin');

        // 1. Create form
        $this->get('https://localhost/admin/pharma/stock-transfer/create')->assertStatus(200);

        // 2. Store transfer
        $storeRes = $this->post('https://localhost/admin/pharma/stock-transfer/store', [
            'from_warehouse_id' => $this->warehouse1->id,
            'to_warehouse_id' => $this->warehouse2->id,
            'transfer_date_ad' => Carbon::today()->format('Y-m-d'),
            'notes' => 'Replenish branch',
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $this->batch->id,
                    'from_location_id' => $this->loc1->id,
                    'quantity' => 25,
                ],
            ],
        ]);

        $transfer = \App\Models\StockTransfer::where('from_warehouse_id', $this->warehouse1->id)->first();
        $this->assertNotNull($transfer);
        $this->assertEquals('draft', $transfer->status);

        // 3. Dispatch
        $this->post("https://localhost/admin/pharma/stock-transfer/{$transfer->id}/dispatch")
            ->assertRedirect(route('admin.pharma.stock-transfer.show', $transfer->id));
        $transfer->refresh();
        $this->assertEquals('dispatched', $transfer->status);

        // 4. Receive
        $this->post("https://localhost/admin/pharma/stock-transfer/{$transfer->id}/receive")
            ->assertRedirect(route('admin.pharma.stock-transfer.show', $transfer->id));
        $transfer->refresh();
        $this->assertEquals('received', $transfer->status);
    }

    public function test_stock_adjustment_maker_checker(): void
    {
        $this->actingAs($this->admin, 'admin');

        // 1. Maker creates adjustment
        $this->post('https://localhost/admin/pharma/stock-adjustment/store', [
            'warehouse_id' => $this->warehouse1->id,
            'adjustment_date_ad' => Carbon::today()->format('Y-m-d'),
            'type' => 'PHYSICAL_COUNT',
            'reason' => 'Monthly physical verification audit',
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $this->batch->id,
                    'location_id' => $this->loc1->id,
                    'book_quantity' => 100,
                    'counted_quantity' => 98,
                ],
            ],
        ]);

        $adjustment = \App\Models\StockAdjustment::where('warehouse_id', $this->warehouse1->id)->first();
        $this->assertNotNull($adjustment);
        $this->assertEquals('draft', $adjustment->status);

        // 2. Checker approves adjustment
        $this->actingAs($this->checker, 'admin');
        $this->post("https://localhost/admin/pharma/stock-adjustment/{$adjustment->id}/approve")
            ->assertRedirect(route('admin.pharma.stock-adjustment.show', $adjustment->id));

        $adjustment->refresh();
        $this->assertEquals('approved', $adjustment->status);
    }

    public function test_customer_receipt_and_supplier_payment_lifecycle(): void
    {
        $this->actingAs($this->admin, 'admin');

        // 1. Customer Receipt Store & Post
        $rcRes = $this->post('https://localhost/admin/pharma/customer-receipt/store', [
            'customer_id' => $this->customer->id,
            'cash_bank_account_id' => $this->account->id,
            'amount' => 5000.00,
            'receipt_date_ad' => Carbon::today()->format('Y-m-d'),
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'TXN-AR-99',
        ]);

        $receipt = \App\Models\CustomerReceipt::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($receipt);
        $this->assertEquals('draft', $receipt->status);

        $this->post("https://localhost/admin/pharma/customer-receipt/{$receipt->id}/post")
            ->assertRedirect(route('admin.pharma.customer-receipt.show', $receipt->id));

        $receipt->refresh();
        $this->assertEquals('posted', $receipt->status);

        // 2. Supplier Payment Store & Post
        $pmRes = $this->post('https://localhost/admin/pharma/supplier-payment/store', [
            'supplier_id' => $this->supplier->id,
            'cash_bank_account_id' => $this->account->id,
            'amount' => 7500.00,
            'payment_date_ad' => Carbon::today()->format('Y-m-d'),
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'TXN-AP-88',
        ]);

        $payment = \App\Models\SupplierPayment::where('supplier_id', $this->supplier->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('draft', $payment->status);

        $this->post("https://localhost/admin/pharma/supplier-payment/{$payment->id}/post")
            ->assertRedirect(route('admin.pharma.supplier-payment.show', $payment->id));

        $payment->refresh();
        $this->assertEquals('posted', $payment->status);

        // 3. Cash & Bank Account Store
        $this->post('https://localhost/admin/pharma/cash-bank/store', [
            'account_name' => 'Kumari Bank Current A/C',
            'account_type' => 'bank',
            'bank_name' => 'Kumari Bank Ltd',
            'opening_balance' => 25000.00,
        ])->assertRedirect(route('admin.pharma.cash-bank'));

        $this->assertDatabaseHas('cash_bank_accounts', ['name' => 'Kumari Bank Current A/C']);
    }
}
