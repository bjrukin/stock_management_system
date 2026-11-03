<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\FiscalYear;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmaPurchasingWebTest extends TestCase
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
            'start_date_ad' => '2024-07-16',
            'start_date_bs' => '2081-04-01',
            'end_date_ad' => '2025-07-15',
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

    public function test_purchase_order_flow(): void
    {
        // 1. Create form renders
        $res = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/purchase-order/create');
        $res->assertStatus(200);
        $res->assertSee('Create Purchase Order');

        // 2. Store draft PO
        $postData = [
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'order_date_ad' => Carbon::today()->format('Y-m-d'),
            'notes' => 'Urgent procurement',
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'unit_id' => $this->unit->id,
                    'ordered_qty' => 50,
                    'unit_price' => 20.00,
                    'discount_pct' => 5,
                    'tax_rate' => 0,
                ],
            ],
            'action' => 'save_draft',
        ];

        $res = $this->actingAs($this->admin, 'admin')->post('https://localhost/admin/pharma/purchase-order/store', $postData);
        $res->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $this->supplier->id,
            'status' => 'draft',
        ]);

        $po = \App\Models\PurchaseOrder::first();
        $this->assertNotNull($po);

        // 3. View PO details
        $res = $this->actingAs($this->admin, 'admin')->get("https://localhost/admin/pharma/purchase-order/{$po->id}");
        $res->assertStatus(200);
        $res->assertSee($po->po_number);

        // 4. Approve PO
        $res = $this->actingAs($this->admin, 'admin')->post("https://localhost/admin/pharma/purchase-order/{$po->id}/approve");
        $res->assertRedirect();
        $this->assertEquals('approved', $po->fresh()->status);
    }

    public function test_goods_receipt_flow(): void
    {
        // 1. Create GRN form renders
        $res = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/goods-receipt/create');
        $res->assertStatus(200);
        $res->assertSee('Add Goods Receipt (GRN)');

        // 2. Store and post GRN directly to stock
        $expiryDate = Carbon::today()->addYears(2)->format('Y-m-d');
        $postData = [
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'receipt_date_ad' => Carbon::today()->format('Y-m-d'),
            'supplier_invoice_number' => 'INV-TEST-88',
            'freight_amount' => 100,
            'handling_charges' => 50,
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'batch_number' => 'BATCH-TEST-GRN-1',
                    'expiry_date_ad' => $expiryDate,
                    'location_id' => $this->location->id,
                    'unit_id' => $this->unit->id,
                    'received_qty' => 100,
                    'unit_cost' => 20.00,
                    'discount_pct' => 0,
                    'tax_rate' => 0,
                ],
            ],
            'action' => 'save_and_post',
        ];

        $res = $this->actingAs($this->admin, 'admin')->post('https://localhost/admin/pharma/goods-receipt/store', $postData);
        $res->assertRedirect();

        $grn = \App\Models\GoodsReceipt::first();
        $this->assertNotNull($grn);
        $this->assertEquals('posted', $grn->status);

        // Check stock was incremented
        $this->assertEquals(100, \App\Services\InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // 3. View GRN details
        $res = $this->actingAs($this->admin, 'admin')->get("https://localhost/admin/pharma/goods-receipt/{$grn->id}");
        $res->assertStatus(200);
        $res->assertSee($grn->grn_number);
    }

    public function test_purchase_return_flow(): void
    {
        // Setup initial stock via GRN
        $grn = \App\Services\PurchasingService::createGoodsReceipt([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'receipt_date_ad' => Carbon::today()->format('Y-m-d'),
        ], [
            [
                'product_id' => $this->product->id,
                'batch_number' => 'BATCH-RET-1',
                'expiry_date_ad' => Carbon::today()->addYears(2)->format('Y-m-d'),
                'location_id' => $this->location->id,
                'unit_id' => $this->unit->id,
                'received_qty' => 50,
                'unit_cost' => 20.00,
            ],
        ]);
        \App\Services\PurchasingService::postGoodsReceipt($grn, $this->admin->id);

        $this->assertEquals(50, \App\Services\InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        $batch = Batch::where('batch_number', 'BATCH-RET-1')->first();
        $this->assertNotNull($batch);

        // 1. Create return form renders
        $res = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/purchase-return/create');
        $res->assertStatus(200);
        $res->assertSee('Create Purchase Return');

        // 2. Store and post Purchase Return (deduct stock)
        $postData = [
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'goods_receipt_id' => $grn->id,
            'return_date_ad' => Carbon::today()->format('Y-m-d'),
            'reason' => 'Damaged outer packing on 10 units',
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $batch->id,
                    'location_id' => $this->location->id,
                    'unit_id' => $this->unit->id,
                    'returned_qty' => 10,
                    'unit_price' => 20.00,
                ],
            ],
            'action' => 'save_and_post',
        ];

        $res = $this->actingAs($this->admin, 'admin')->post('https://localhost/admin/pharma/purchase-return/store', $postData);
        $res->assertRedirect();

        $prtn = \App\Models\PurchaseReturn::first();
        $this->assertNotNull($prtn);
        $this->assertEquals('posted', $prtn->status);

        // Stock decreased: 50 - 10 = 40
        $this->assertEquals(40, \App\Services\InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // 3. View Return details
        $res = $this->actingAs($this->admin, 'admin')->get("https://localhost/admin/pharma/purchase-return/{$prtn->id}");
        $res->assertStatus(200);
        $res->assertSee($prtn->return_number);
    }
}
