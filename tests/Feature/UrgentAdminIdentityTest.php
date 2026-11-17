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

class UrgentAdminIdentityTest extends TestCase
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

        $this->admin = Admin::forceCreate([
            'id' => 41,
            'name' => 'Lead Admin',
            'username' => 'lead_admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'status' => 'active',
        ]);

        $this->checker = Admin::forceCreate([
            'id' => 73,
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
            'created_by' => 41,
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
        $this->assertSame(41, (int) $invoice->created_by);
        $this->assertSame(41, (int) $invoice->posted_by);
        $this->assertDatabaseHas('stock_movements', ['reference_type' => 'SaleInvoice', 'reference_id' => $invoice->id, 'created_by' => 41]);
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
        $this->assertSame(41, (int) $saleReturn->created_by);
        $this->assertSame(41, (int) $saleReturn->posted_by);
        $this->assertDatabaseHas('stock_movements', ['reference_type' => 'SaleReturn', 'reference_id' => $saleReturn->id, 'created_by' => 41]);
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
        $this->assertSame(41, (int) $transfer->created_by);
        $this->assertSame(41, (int) $transfer->dispatched_by);
        $this->assertSame(41, (int) $transfer->received_by);
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
        $this->assertSame(41, (int) $adjustment->created_by);
        $this->assertSame(73, (int) $adjustment->approved_by);
        $this->assertDatabaseHas('stock_movements', ['reference_type' => 'StockAdjustment', 'reference_id' => $adjustment->id, 'created_by' => 73]);
        $this->assertDatabaseHas('audit_logs', ['event' => 'stock_adjustment_approved', 'user_id' => 73]);
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
        $this->assertSame(41, (int) $receipt->created_by);
        $this->assertSame(41, (int) $receipt->posted_by);

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
        $this->assertSame(41, (int) $payment->created_by);
        $this->assertSame(41, (int) $payment->posted_by);

        // 3. Cash & Bank Account Store
        $this->post('https://localhost/admin/pharma/cash-bank/store', [
            'account_name' => 'Kumari Bank Current A/C',
            'account_type' => 'bank',
            'bank_name' => 'Kumari Bank Ltd',
            'opening_balance' => 25000.00,
        ])->assertRedirect(route('admin.pharma.cash-bank'));

        $this->assertDatabaseHas('cash_bank_accounts', ['name' => 'Kumari Bank Current A/C']);
    }

    public function actingAs(\Illuminate\Contracts\Auth\Authenticatable $user, $guard = null)
    {
        parent::actingAs($user, $guard);
        // Reproduce a real admin session while the app's default remains web.
        auth()->shouldUse('web');

        return $this;
    }

    private function draftAdjustment(): \App\Models\StockAdjustment
    {
        return \App\Services\WarehouseOperationService::createAdjustment([
            'warehouse_id' => $this->warehouse1->id,
            'adjustment_date_ad' => Carbon::today()->format('Y-m-d'),
            'type' => 'PHYSICAL_COUNT',
        ], [[
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'location_id' => $this->loc1->id,
            'book_quantity' => 500,
            'counted_quantity' => 498,
        ]], 41);
    }

    public function test_maker_cannot_approve_even_when_another_admin_exists(): void
    {
        $adjustment = $this->draftAdjustment();
        $this->actingAs($this->admin, 'admin');
        $before = \App\Models\StockMovement::count();

        $this->post("https://localhost/admin/pharma/stock-adjustment/{$adjustment->id}/approve")
            ->assertForbidden();

        $this->assertSame('draft', $adjustment->fresh()->status);
        $this->assertNull($adjustment->fresh()->approved_by);
        $this->assertSame($before, \App\Models\StockMovement::count());
    }

    public function test_checker_without_approval_permission_cannot_approve(): void
    {
        $adjustment = $this->draftAdjustment();
        $role = Role::create(['name' => 'View only']);
        $this->checker->update(['role_id' => $role->id]);
        $this->actingAs($this->checker, 'admin');
        $before = \App\Models\StockMovement::count();

        $this->post("https://localhost/admin/pharma/stock-adjustment/{$adjustment->id}/approve")
            ->assertForbidden();

        $this->assertSame('draft', $adjustment->fresh()->status);
        $this->assertNull($adjustment->fresh()->approved_by);
        $this->assertSame($before, \App\Models\StockMovement::count());
    }

    public function test_independent_checker_with_explicit_permission_can_approve(): void
    {
        $adjustment = $this->draftAdjustment();
        $role = Role::create(['name' => 'Stock checker']);
        $this->checker->warehouses()->attach($this->warehouse1->id);
        $module = \App\Models\Module::create(['name' => 'StockAdjustment']);
        \App\Models\Permission::create([
            'role_id' => $role->id,
            'module_id' => $module->id,
            'name' => ['view', 'approve'],
        ]);
        $this->checker->update(['role_id' => $role->id]);
        $this->actingAs($this->checker, 'admin');

        $this->post("https://localhost/admin/pharma/stock-adjustment/{$adjustment->id}/approve")
            ->assertRedirect(route('admin.pharma.stock-adjustment.show', $adjustment->id));

        $this->assertSame('approved', $adjustment->fresh()->status);
        $this->assertSame(73, (int) $adjustment->fresh()->approved_by);
    }

    public function test_default_guard_user_does_not_override_real_checker(): void
    {
        $adjustment = $this->draftAdjustment();
        $this->actingAs($this->checker, 'admin');
        auth()->guard('web')->setUser($this->admin);

        $this->post("https://localhost/admin/pharma/stock-adjustment/{$adjustment->id}/approve")
            ->assertSessionHasNoErrors();

        $this->assertSame('approved', $adjustment->fresh()->status);
        $this->assertSame(73, (int) $adjustment->fresh()->approved_by);
    }

    public static function unauthenticatedSessions(): array
    {
        return ['guest' => [false], 'default guard only' => [true]];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('unauthenticatedSessions')]
    public function test_mutations_require_an_admin_session(bool $webOnly): void
    {
        if ($webOnly) {
            auth()->guard('web')->setUser($this->admin);
        }
        $before = \App\Models\StockMovement::count();
        foreach (['sale-invoice/store', 'sale-invoice/1/post', 'sales-return/store', 'sales-return/1/post',
            'customer-receipt/store', 'customer-receipt/1/post', 'supplier-payment/store', 'supplier-payment/1/post',
            'stock-transfer/store', 'stock-transfer/1/dispatch', 'stock-transfer/1/receive',
            'stock-adjustment/store', 'stock-adjustment/1/approve'] as $path) {
            $this->postJson('https://localhost/admin/pharma/'.$path)->assertUnauthorized();
        }
        $this->assertSame($before, \App\Models\StockMovement::count());
        $this->assertDatabaseCount('stock_adjustments', 0);
        $this->assertDatabaseCount('customer_receipts', 0);
        $this->assertDatabaseCount('supplier_payments', 0);
    }

}
