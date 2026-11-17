<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DocumentSequence;
use App\Models\FiscalYear;
use App\Models\GoodsReceipt;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\PurchasingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderDraftCrudTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected Supplier $supplier;

    protected Warehouse $warehouse;

    protected WarehouseLocation $location;

    protected Product $product;

    protected Unit $unit;

    protected FiscalYear $fiscalYear;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['id' => 1, 'name' => 'Super Admin', 'guard_name' => 'admin']);

        $this->admin = Admin::create([
            'name' => 'Super Admin',
            'username' => 'purchasing_admin',
            'email' => 'purchasing@test.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'status' => 'active',
        ]);

        $this->fiscalYear = FiscalYear::create([
            'name' => '2081/82',
            'start_date_ad' => '2026-07-17',
            'start_date_bs' => '2081-04-01',
            'end_date_ad' => '2027-07-16',
            'end_date_bs' => '2082-03-31',
            'is_current' => true,
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-001',
            'name' => 'Apex Healthcare Pvt Ltd',
            'email' => 'sales@apex.com',
            'phone' => '9841000000',
            'is_active' => true,
        ]);

        $this->warehouse = Warehouse::create([
            'code' => 'WH-KTM',
            'name' => 'Kathmandu Central Hub',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->location = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'LOC-A1',
            'name' => 'Main Storage Rack A1',
            'is_sellable' => true,
            'is_available' => true,
            'is_active' => true,
        ]);

        $this->unit = Unit::create([
            'name' => 'Strip',
            'abbreviation' => 'Str',
        ]);

        $this->product = Product::create([
            'code' => 'PRD-CET-1',
            'name' => 'Cetirizine 10mg',
            'generic_name' => 'Cetirizine',
            'base_unit_id' => $this->unit->id,
            'purchase_price' => 20.00,
            'selling_price' => 35.00,
            'is_active' => true,
        ]);
    }

    private function payload(): array
    {
        return ['supplier_id' => $this->supplier->id, 'warehouse_id' => $this->warehouse->id,
            'order_date_ad' => '2026-11-04', 'expected_delivery_date_ad' => '2026-11-09', 'notes' => 'Saved notes',
            'lines' => [['product_id' => $this->product->id, 'unit_id' => $this->unit->id, 'ordered_qty' => 2, 'conversion_factor' => 10, 'unit_price' => 20, 'discount_pct' => 0, 'tax_rate' => 13]]];
    }

    private function draft(): PurchaseOrder
    {
        $p = $this->payload();

        return PurchasingService::createPurchaseOrder($p + ['created_by' => $this->admin->id], $p['lines']);
    }

    public function test_draft_list_and_detail_offer_edit_and_delete(): void
    {
        $this->actingAs($this->admin, 'admin');
        $order = $this->draft();
        foreach (['https://localhost/admin/pharma/purchase-orders', 'https://localhost/admin/pharma/purchase-order/'.$order->id] as $url) {
            $this->get($url)->assertOk()->assertSee(route('admin.pharma.purchase-order.edit', $order->id), false)->assertSee(route('admin.pharma.purchase-order.delete', $order->id), false);
        }
        $this->get('https://localhost/admin/pharma/purchase-order/'.$order->id.'/edit')->assertOk()->assertViewHas('initialLines', fn ($rows) => (float) $rows[0]['conversion_factor'] === 10.0 && (float) $rows[0]['ordered_qty'] === 2.0);
    }

    public function test_edit_recalculates_same_order_and_does_not_move_stock(): void
    {
        $this->actingAs($this->admin, 'admin');
        $order = $this->draft();
        $number = $order->po_number;
        $sequence = DocumentSequence::where('series', 'PO')->value('last_number');
        $p = $this->payload();
        $p['lines'][0]['ordered_qty'] = 3;
        $p['lines'][0]['unit_price'] = 50;
        $p['lines'][0]['discount_pct'] = 10;
        $p['order_date_ad'] = '2026-11-05';
        $p['expected_delivery_date_ad'] = null;
        $this->post('https://localhost/admin/pharma/purchase-order/'.$order->id.'/update', $p)->assertRedirect(route('admin.pharma.purchase-order.show', $order->id));
        $order->refresh();
        $this->assertSame($number, $order->po_number);
        $this->assertSame($this->admin->id, (int) $order->created_by);
        $this->assertSame('152.55', $order->total_amount);
        $this->assertSame('2083-07-19', $order->order_date_bs);
        $this->assertNull($order->expected_delivery_date_ad);
        $this->assertNull($order->expected_delivery_date_bs);
        $this->assertSame(30, $order->lines->first()->ordered_base_qty);
        $this->assertSame($sequence, DocumentSequence::where('series', 'PO')->value('last_number'));
        $this->assertDatabaseCount('purchase_orders', 1);
        $this->assertDatabaseCount('stock_movements', 0);
        $this->assertDatabaseCount('supplier_ledger_entries', 0);
    }

    public function test_delete_draft_preserves_line_history(): void
    {
        $this->actingAs($this->admin, 'admin');
        $order = $this->draft();
        $this->post('https://localhost/admin/pharma/purchase-order/'.$order->id.'/delete')->assertRedirect(route('admin.pharma.purchase-orders'));
        $this->assertSoftDeleted('purchase_orders', ['id' => $order->id]);
        $this->assertDatabaseCount('purchase_order_lines', 1);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_approved_order_cannot_be_changed_or_deleted(): void
    {
        $this->actingAs($this->admin, 'admin');
        $order = $this->draft();
        PurchasingService::approvePurchaseOrder($order, $this->admin->id);
        $this->get('https://localhost/admin/pharma/purchase-order/'.$order->id.'/edit')->assertStatus(409);
        $this->post('https://localhost/admin/pharma/purchase-order/'.$order->id.'/update', $this->payload())->assertSessionHas('error');
        $this->post('https://localhost/admin/pharma/purchase-order/'.$order->id.'/delete')->assertSessionHas('error');
        $this->get('https://localhost/admin/pharma/purchase-orders')->assertDontSee(route('admin.pharma.purchase-order.edit', $order->id), false)->assertDontSee(route('admin.pharma.purchase-order.delete', $order->id), false);
        $this->assertSame('approved', $order->fresh()->status);
    }

    public function test_order_linked_to_a_receipt_cannot_lose_its_lines(): void
    {
        $this->actingAs($this->admin, 'admin');
        $order = $this->draft();
        GoodsReceipt::create(['grn_number' => 'LINK', 'fiscal_year_id' => $this->fiscalYear->id, 'purchase_order_id' => $order->id, 'supplier_id' => $this->supplier->id, 'warehouse_id' => $this->warehouse->id, 'receipt_date_ad' => '2026-11-04', 'status' => 'draft']);
        $this->post('https://localhost/admin/pharma/purchase-order/'.$order->id.'/update', $this->payload())->assertSessionHas('error');
        $this->post('https://localhost/admin/pharma/purchase-order/'.$order->id.'/delete')->assertSessionHas('error');
        $this->assertNotNull($order->fresh());
        $this->assertDatabaseCount('purchase_order_lines', 1);
    }

    public function test_permission_and_warehouse_checks_protect_new_actions(): void
    {
        $order = $this->draft();
        $role = Role::create(['name' => 'Viewer']);
        $this->admin->update(['role_id' => $role->id]);
        $this->actingAs($this->admin, 'admin');
        $this->admin->warehouses()->attach($this->warehouse->id);
        foreach (['update', 'delete'] as $action) {
            $this->postJson('https://localhost/admin/pharma/purchase-order/'.$order->id.'/'.$action, $this->payload())->assertForbidden();
        }
        $module = Module::create(['name' => 'PurchaseOrder']);
        Permission::create(['role_id' => $role->id, 'module_id' => $module->id, 'name' => ['view', 'edit', 'delete']]);
        $this->admin->warehouses()->detach();
        foreach (['update', 'delete'] as $action) {
            $this->postJson('https://localhost/admin/pharma/purchase-order/'.$order->id.'/'.$action, $this->payload())->assertForbidden();
        }
    }

    public function test_invalid_lines_do_not_replace_the_saved_order(): void
    {
        $this->actingAs($this->admin, 'admin');
        $order = $this->draft();
        $lineId = $order->lines->first()->id;
        $p = $this->payload();
        $p['lines'][0]['conversion_factor'] = 0;
        $this->post('https://localhost/admin/pharma/purchase-order/'.$order->id.'/update', $p)->assertSessionHasErrors('lines.0.conversion_factor');
        $this->assertSame($lineId, $order->fresh('lines')->lines->first()->id);
        $this->assertSame('45.20', $order->fresh()->total_amount);
    }

    public function test_edit_preserves_non_primary_warehouse_selection(): void
    {
        $this->actingAs($this->admin, 'admin');
        $this->warehouse->update(['is_primary' => false]);
        Warehouse::create(['code' => 'PRIMARY', 'name' => 'Z Primary Warehouse', 'is_primary' => true, 'is_active' => true]);
        $order = $this->draft();
        $response = $this->get('https://localhost/admin/pharma/purchase-order/'.$order->id.'/edit')->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML($response->getContent());
        $options = (new \DOMXPath($dom))->query('//select[@name="warehouse_id"]/option[@selected]');
        $this->assertSame(1, $options->length);
        $this->assertSame((string) $this->warehouse->id, $options->item(0)->getAttribute('value'));
    }
}
