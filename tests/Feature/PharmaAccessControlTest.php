<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\CashBankAccount;
use App\Models\Customer;
use App\Models\CustomerReceipt;
use App\Models\FiscalYear;
use App\Models\InventoryBalance;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Role;
use App\Models\SaleInvoice;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PharmaAccessControlTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private Warehouse $allowed;

    private Warehouse $denied;

    private WarehouseLocation $allowedLocation;

    private WarehouseLocation $deniedLocation;

    private FiscalYear $year;

    protected function setUp(): void
    {
        parent::setUp();
        Role::forceCreate(['id' => 1, 'name' => 'Super Admin']);
        $role = Role::create(['name' => 'Restricted operator']);
        $this->admin = Admin::forceCreate(['id' => 41, 'name' => 'Operator', 'username' => 'operator', 'password' => bcrypt('password'), 'role_id' => $role->id, 'status' => true]);
        $this->allowed = Warehouse::create(['code' => 'ALLOWED', 'name' => 'Assigned warehouse', 'is_active' => true]);
        $this->denied = Warehouse::create(['code' => 'DENIED', 'name' => 'Private warehouse', 'is_active' => true]);
        $this->allowedLocation = WarehouseLocation::create(['warehouse_id' => $this->allowed->id, 'code' => 'A', 'name' => 'Allowed location']);
        $this->deniedLocation = WarehouseLocation::create(['warehouse_id' => $this->denied->id, 'code' => 'B', 'name' => 'Private location']);
        $this->admin->warehouses()->attach($this->allowed->id);
        $this->year = FiscalYear::create(['name' => '2083/84', 'start_date_ad' => '2026-07-17', 'end_date_ad' => '2027-07-16', 'is_current' => true]);
        $this->actingAs($this->admin, 'admin');
        auth()->shouldUse('web');
    }

    private function permit(string $module, array $actions): void
    {
        $m = Module::firstOrCreate(['name' => $module]);
        Permission::updateOrCreate(['role_id' => $this->admin->role_id, 'module_id' => $m->id], ['name' => $actions]);
    }

    private function order(Warehouse $warehouse): PurchaseOrder
    {
        $supplier = Supplier::firstOrCreate(['code' => 'SUP'], ['name' => 'Supplier']);

        return PurchaseOrder::create(['po_number' => 'PO-'.$warehouse->id, 'fiscal_year_id' => $this->year->id, 'supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id, 'order_date_ad' => '2026-11-04', 'status' => 'draft']);
    }

    public static function deniedRoutes(): array
    {
        return [
            ['GET', 'products'], ['GET', 'product/create'], ['POST', 'product/1/delete'],
            ['GET', 'purchase-orders'], ['POST', 'purchase-order/store'], ['POST', 'purchase-order/1/approve'],
            ['GET', 'goods-receipts'], ['POST', 'goods-receipt/1/post'],
            ['GET', 'purchase-returns'], ['GET', 'sales-invoices'], ['POST', 'sale-invoice/1/post'],
            ['GET', 'sales-returns'], ['GET', 'stock-movements'], ['GET', 'stock-transfers'],
            ['POST', 'stock-transfer/1/dispatch'], ['POST', 'stock-transfer/1/receive'],
            ['GET', 'stock-adjustments'], ['POST', 'stock-adjustment/1/approve'],
            ['GET', 'customer-receipts'], ['POST', 'customer-receipt/1/post'],
            ['GET', 'supplier-payments'], ['POST', 'supplier-payment/1/post'],
            ['GET', 'cash-bank'], ['GET', 'warehouses'], ['GET', 'warehouse-locations'],
            ['GET', 'suppliers'], ['GET', 'customers'], ['GET', 'reports/stock-valuation'],
            ['GET', 'reports/sales-register'], ['GET', 'audit-logs'], ['GET', 'settings'], ['POST', 'reconcile'],
        ];
    }

    #[DataProvider('deniedRoutes')]
    public function test_no_permission_denies_direct_requests(string $method, string $path): void
    {
        $this->json($method, 'https://localhost/admin/pharma/'.$path)->assertForbidden();
    }

    public function test_view_permission_does_not_allow_create(): void
    {
        $this->permit('PurchaseOrder', ['view']);
        $this->get('https://localhost/admin/pharma/purchase-orders')->assertOk();
        $this->postJson('https://localhost/admin/pharma/purchase-order/store')->assertForbidden();
        $this->assertDatabaseCount('purchase_orders', 0);
    }

    public function test_document_list_and_detail_enforce_warehouse_assignment(): void
    {
        $this->permit('PurchaseOrder', ['view', 'approve']);
        $allowed = $this->order($this->allowed);
        $denied = $this->order($this->denied);
        $this->get('https://localhost/admin/pharma/purchase-orders')->assertOk()
            ->assertViewHas('orders', fn ($orders) => $orders->pluck('id')->all() === [$allowed->id]);
        $this->get('https://localhost/admin/pharma/purchase-order/'.$denied->id)->assertForbidden();
        $this->post('https://localhost/admin/pharma/purchase-order/'.$denied->id.'/approve')->assertForbidden();
        $this->assertSame('draft', $denied->fresh()->status);
    }

    public function test_warehouse_selectors_and_locations_are_filtered(): void
    {
        $this->permit('PurchaseOrder', ['create']);
        $this->permit('WarehouseLocation', ['view']);
        $this->get('https://localhost/admin/pharma/purchase-order/create')->assertOk()
            ->assertViewHas('warehouses', fn ($rows) => $rows->pluck('id')->all() === [$this->allowed->id]);
        $this->get('https://localhost/admin/pharma/warehouse-locations')->assertOk()
            ->assertViewHas('locations', fn ($rows) => $rows->pluck('id')->all() === [$this->allowedLocation->id]);
    }

    public function test_no_assignments_means_no_warehouse_data(): void
    {
        $this->permit('PurchaseOrder', ['view']);
        $this->order($this->allowed);
        $this->admin->warehouses()->detach();
        $this->get('https://localhost/admin/pharma/purchase-orders')->assertOk()
            ->assertViewHas('orders', fn ($rows) => $rows->isEmpty());
    }

    public function test_forged_warehouse_and_linked_order_are_rejected_before_creation(): void
    {
        $this->permit('PurchaseOrder', ['create']);
        $this->permit('GoodsReceipt', ['create']);
        $denied = $this->order($this->denied);
        $this->postJson('https://localhost/admin/pharma/purchase-order/store', ['warehouse_id' => $this->denied->id])->assertForbidden();
        $this->get('https://localhost/admin/pharma/goods-receipt/create?po_id='.$denied->id)->assertForbidden();
        $this->postJson('https://localhost/admin/pharma/goods-receipt/store', ['warehouse_id' => $this->allowed->id, 'purchase_order_id' => $denied->id])->assertForbidden();
        $this->assertDatabaseCount('goods_receipts', 0);
    }

    public function test_inline_approval_or_post_requires_separate_permission(): void
    {
        foreach ([['PurchaseOrder', 'purchase-order', 'save_and_approve'], ['GoodsReceipt', 'goods-receipt', 'save_and_post'], ['PurchaseReturn', 'purchase-return', 'save_and_post']] as [$module, $path, $action]) {
            $this->permit($module, ['create']);
            $this->postJson('https://localhost/admin/pharma/'.$path.'/store', ['action' => $action, 'warehouse_id' => $this->allowed->id])->assertForbidden();
        }
        $this->assertDatabaseCount('purchase_orders', 0);
        $this->assertDatabaseCount('goods_receipts', 0);
        $this->assertDatabaseCount('purchase_returns', 0);
    }

    public function test_location_from_a_different_warehouse_is_rejected(): void
    {
        $this->permit('GoodsReceipt', ['create']);
        $this->admin->warehouses()->attach($this->denied->id);
        $this->postJson('https://localhost/admin/pharma/goods-receipt/store', ['warehouse_id' => $this->allowed->id, 'lines' => [['location_id' => $this->deniedLocation->id]]])->assertForbidden();
    }

    public function test_report_and_dashboard_exclude_unassigned_warehouse(): void
    {
        $this->permit('Report', ['view']);
        $this->permit('SaleInvoice', ['view']);
        SaleInvoice::create(['invoice_number' => 'SECRET', 'fiscal_year_id' => $this->year->id, 'warehouse_id' => $this->denied->id, 'invoice_date_ad' => '2026-11-04', 'total_amount' => 999, 'credit_amount' => 999, 'status' => 'posted']);
        $this->get('https://localhost/admin/pharma/reports/sales-register?from=2026-11-01&to=2026-11-30')->assertOk()
            ->assertViewHas('register', fn ($r) => $r['invoices'] === [] && (float) $r['total_grand'] === 0.0);
        $this->get('https://localhost/admin')->assertOk()
            ->assertViewHas('recentInvoices', fn ($rows) => $rows->isEmpty())
            ->assertViewHas('summary', fn ($s) => (float) $s['sales_today'] === 0.0 && $s['cash_bank_liquidity'] === null);
        $this->get('https://localhost/admin/pharma/reports/stock-valuation?warehouse_id='.$this->denied->id)->assertForbidden();
    }

    public function test_reconciliation_is_not_available_to_restricted_operator(): void
    {
        $this->permit('StockAdjustment', ['post', 'approve']);
        $this->postJson('https://localhost/admin/pharma/reconcile')->assertForbidden();
    }

    public function test_legacy_double_encoded_permissions_remain_usable(): void
    {
        $this->permit('PurchaseOrder', ['view']);
        Permission::where('role_id', $this->admin->role_id)->first()->update(['name' => json_encode(['view'])]);
        $this->assertTrue($this->admin->hasPermission('PurchaseOrder', 'view'));
        $this->assertFalse($this->admin->hasPermission('PurchaseOrder', 'approve'));
    }

    public function test_permitted_operator_can_create_a_draft_in_assigned_warehouse(): void
    {
        $this->permit('PurchaseOrder', ['create', 'view']);
        $supplier = Supplier::create(['code' => 'CREATE-SUP', 'name' => 'Supplier']);
        $unit = Unit::create(['name' => 'Tablet', 'abbreviation' => 'tab']);
        $product = Product::create(['code' => 'CREATE-PROD', 'name' => 'Tablet', 'generic_name' => 'Tablet', 'base_unit_id' => $unit->id]);
        $this->postJson('https://localhost/admin/pharma/purchase-order/store', [
            'supplier_id' => $supplier->id, 'warehouse_id' => $this->allowed->id, 'order_date_ad' => '2026-11-04',
            'lines' => [['product_id' => $product->id, 'unit_id' => $unit->id, 'ordered_qty' => 2, 'unit_price' => 10]],
        ])->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', ['warehouse_id' => $this->allowed->id, 'created_by' => 41, 'status' => 'draft']);
    }

    public function test_transfer_requires_both_warehouse_assignments(): void
    {
        $this->permit('StockTransfer', ['view', 'create', 'post']);
        $transfer = StockTransfer::create([
            'transfer_number' => 'TRF-PRIVATE', 'fiscal_year_id' => $this->year->id,
            'from_warehouse_id' => $this->allowed->id, 'to_warehouse_id' => $this->denied->id,
            'transfer_date_ad' => '2026-11-04', 'status' => 'draft',
        ]);
        $this->get('https://localhost/admin/pharma/stock-transfers')->assertOk()->assertViewHas('transfers', fn ($rows) => $rows->isEmpty());
        $this->get('https://localhost/admin/pharma/stock-transfer/'.$transfer->id)->assertForbidden();
        $this->post('https://localhost/admin/pharma/stock-transfer/'.$transfer->id.'/dispatch')->assertForbidden();
        $this->postJson('https://localhost/admin/pharma/stock-transfer/store', ['from_warehouse_id' => $this->allowed->id, 'to_warehouse_id' => $this->denied->id])->assertForbidden();
        $this->assertSame('draft', $transfer->fresh()->status);
    }

    public function test_stock_quantities_and_valuation_only_include_assigned_warehouse(): void
    {
        $this->permit('Product', ['view']);
        $this->permit('Report', ['view']);
        $unit = Unit::create(['name' => 'Tablet', 'abbreviation' => 'tab']);
        $product = Product::create(['code' => 'MED', 'name' => 'Medicine', 'generic_name' => 'Medicine', 'base_unit_id' => $unit->id]);
        $batch = Batch::create(['product_id' => $product->id, 'batch_number' => 'BATCH', 'expiry_date_ad' => '2027-01-01', 'cost_price' => 10, 'mrp' => 20]);
        foreach ([[$this->allowed, $this->allowedLocation, 3], [$this->denied, $this->deniedLocation, 100]] as [$warehouse, $location, $qty]) {
            InventoryBalance::create(['warehouse_id' => $warehouse->id, 'location_id' => $location->id, 'product_id' => $product->id, 'batch_id' => $batch->id, 'quantity_on_hand' => $qty]);
        }
        $this->get('https://localhost/admin/pharma/products')->assertOk()
            ->assertViewHas('products', fn ($rows) => (int) $rows->first()->inventoryBalances->sum('quantity_on_hand') === 3);
        $this->get('https://localhost/admin/pharma/reports/stock-valuation')->assertOk()
            ->assertViewHas('report', fn ($r) => (float) $r['total_cost_value'] === 30.0);
    }

    public function test_invoice_detail_print_and_post_reject_unassigned_warehouse(): void
    {
        $this->permit('SaleInvoice', ['view', 'post']);
        $invoice = SaleInvoice::create(['invoice_number' => 'HIDDEN', 'fiscal_year_id' => $this->year->id, 'warehouse_id' => $this->denied->id, 'invoice_date_ad' => '2026-11-04', 'status' => 'draft']);
        foreach (['', '/print'] as $suffix) {
            $this->get('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.$suffix)->assertForbidden();
        }
        $this->post('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/post')->assertForbidden();
        $this->assertSame('draft', $invoice->fresh()->status);
    }

    public function test_receipts_cannot_expose_or_allocate_an_inaccessible_invoice(): void
    {
        $this->permit('CustomerPayment', ['view', 'create', 'post']);
        $invoice = SaleInvoice::create(['invoice_number' => 'ALLOC-HIDDEN', 'fiscal_year_id' => $this->year->id, 'warehouse_id' => $this->denied->id, 'invoice_date_ad' => '2026-11-04', 'status' => 'posted']);
        $customer = Customer::create(['code' => 'CUST', 'name' => 'Customer']);
        $account = CashBankAccount::create(['code' => 'CASH', 'name' => 'Cash', 'type' => 'cash']);
        $receipt = CustomerReceipt::create(['receipt_number' => 'RECEIPT', 'fiscal_year_id' => $this->year->id, 'customer_id' => $customer->id, 'cash_bank_account_id' => $account->id, 'receipt_date_ad' => '2026-11-04', 'amount' => 10, 'status' => 'draft']);
        $receipt->allocations()->create(['sale_invoice_id' => $invoice->id, 'allocated_amount' => 10]);
        $this->get('https://localhost/admin/pharma/customer-receipts')->assertOk()->assertViewHas('receipts', fn ($rows) => $rows->isEmpty());
        $this->get('https://localhost/admin/pharma/customer-receipt/'.$receipt->id)->assertForbidden();
        $this->post('https://localhost/admin/pharma/customer-receipt/'.$receipt->id.'/post')->assertForbidden();
        $this->postJson('https://localhost/admin/pharma/customer-receipt/store', ['allocations' => [['sale_invoice_id' => $invoice->id, 'allocated_amount' => 10]]])->assertForbidden();
    }

    public function test_new_unmapped_pharma_route_is_denied(): void
    {
        Route::middleware(['admin', 'auth.admin', 'pharma.access'])
            ->get('/admin/pharma/unmapped-probe', fn () => response('should not execute'))->name('admin.pharma.unmapped-probe');
        $this->get('https://localhost/admin/pharma/unmapped-probe')->assertForbidden();
    }

    public function test_restricted_warehouse_editor_cannot_change_global_primary_warehouse(): void
    {
        $this->permit('Warehouse', ['edit']);
        $this->postJson('https://localhost/admin/pharma/warehouse/'.$this->allowed->id.'/update', ['is_primary' => true])->assertForbidden();
        $this->assertFalse($this->allowed->fresh()->is_primary);
    }

    public function test_restricted_admin_cannot_grant_themselves_broader_rights(): void
    {
        foreach (['user', 'role', 'permission', 'module'] as $path) {
            $this->get('https://localhost/admin/'.$path)->assertForbidden();
            $this->postJson('https://localhost/admin/'.$path.'/store', ['id' => $this->admin->id, 'role_id' => 1])->assertForbidden();
        }
        $this->assertSame(2, (int) $this->admin->fresh()->role_id);
        $this->assertDatabaseCount('permissions', 0);
    }

    public function test_restricted_editor_cannot_clear_existing_primary_flag(): void
    {
        $this->permit('Warehouse', ['edit']);
        $this->allowed->update(['is_primary' => true]);
        foreach ([[], ['is_primary' => false]] as $extra) {
            $this->postJson('https://localhost/admin/pharma/warehouse/'.$this->allowed->id.'/update', array_merge(['name' => 'Still primary', 'code' => 'ALLOWED'], $extra))->assertForbidden();
        }
        $this->assertTrue($this->allowed->fresh()->is_primary);
    }

    public function test_super_admin_retains_security_administration_access(): void
    {
        $this->admin->update(['role_id' => 1]);
        foreach (['user', 'role', 'permission', 'module'] as $path) {
            $this->get('https://localhost/admin/'.$path)->assertOk();
        }
    }
}
