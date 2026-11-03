<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\FiscalYear;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\InventoryService;
use App\Services\WarehouseOperationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockFlowProcessTest extends TestCase
{
    use RefreshDatabase;

    protected Warehouse $warehouse;
    protected WarehouseLocation $storageLocation;
    protected Product $product;
    protected Batch $batch;
    protected FiscalYear $fiscalYear;
    protected Admin $maker;
    protected Admin $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Inventory Manager', 'guard_name' => 'admin']);

        $this->maker = Admin::create([
            'role_id' => $role->id,
            'name' => 'Maker Officer',
            'email' => 'maker@test.com',
            'username' => 'maker_flow',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->checker = Admin::create([
            'role_id' => $role->id,
            'name' => 'Checker Officer',
            'email' => 'checker@test.com',
            'username' => 'checker_flow',
            'password' => bcrypt('password'),
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

        $this->warehouse = Warehouse::create([
            'code' => 'WH-MAIN',
            'name' => 'Central Pharma Warehouse',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->storageLocation = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'STORE-01',
            'name' => 'Main Shelf 1',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $unit = Unit::create(['name' => 'Box', 'abbreviation' => 'Box']);

        $this->product = Product::create([
            'code' => 'PRD-MED-1',
            'name' => 'Paracetamol 500mg',
            'generic_name' => 'Paracetamol',
            'base_unit_id' => $unit->id,
            'is_active' => true,
        ]);

        $this->batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-FLOW-01',
            'manufacturing_date_ad' => Carbon::today()->subMonths(2)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addYears(2)->format('Y-m-d'),
            'cost_price' => 50.00,
            'selling_price' => 70.00,
            'is_active' => true,
        ]);
    }

    public function test_complete_stock_flow_process(): void
    {
        // 1. Inflow: Purchase (+100 units)
        $m1 = InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'movement_type' => StockMovement::TYPE_PURCHASE,
            'quantity' => 100,
            'unit_cost' => 50.00,
            'created_by' => $this->maker->id,
        ]);
        $this->assertTrue($m1->isInflow());
        $this->assertEquals('purchase', $m1->flow_category);
        $this->assertEquals(100, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // 2. Outflow: Sell (-30 units)
        $m2 = InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'movement_type' => StockMovement::TYPE_SELL,
            'quantity' => -30,
            'unit_cost' => 50.00,
            'created_by' => $this->maker->id,
        ]);
        $this->assertTrue($m2->isOutflow());
        $this->assertEquals('sell', $m2->flow_category);
        $this->assertEquals(70, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // 3. Inflow: Sell Return (+5 units)
        $m3 = InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'movement_type' => StockMovement::TYPE_SELL_RETURN,
            'quantity' => 5,
            'unit_cost' => 50.00,
            'created_by' => $this->maker->id,
        ]);
        $this->assertTrue($m3->isInflow());
        $this->assertEquals('sell_return', $m3->flow_category);
        $this->assertEquals(75, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // 4. Outflow: Purchase Return (-10 units)
        $m4 = InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'movement_type' => StockMovement::TYPE_PURCHASE_RETURN,
            'quantity' => -10,
            'unit_cost' => 50.00,
            'created_by' => $this->maker->id,
        ]);
        $this->assertTrue($m4->isOutflow());
        $this->assertEquals('purchase_return', $m4->flow_category);
        $this->assertEquals(65, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // 5. Outflow: Expired Item (-3 units)
        $adjExpiry = WarehouseOperationService::createAdjustment(
            [
                'warehouse_id' => $this->warehouse->id,
                'type' => 'EXPIRY_DISPOSAL',
                'reason' => 'Expired items disposal write-off',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $this->batch->id,
                    'location_id' => $this->storageLocation->id,
                    'book_quantity' => 65,
                    'counted_quantity' => 62, // -3 units expired
                    'unit_cost' => 50.00,
                ],
            ],
            $this->maker->id
        );
        WarehouseOperationService::approveAdjustment($adjExpiry, $this->checker->id);
        $this->assertEquals(62, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // 6. Outflow: Damage (-2 units)
        $adjDamage = WarehouseOperationService::createAdjustment(
            [
                'warehouse_id' => $this->warehouse->id,
                'type' => 'DAMAGE_WRITE_OFF',
                'reason' => 'Broken bottles in storage',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $this->batch->id,
                    'location_id' => $this->storageLocation->id,
                    'book_quantity' => 62,
                    'counted_quantity' => 60, // -2 units damaged
                    'unit_cost' => 50.00,
                ],
            ],
            $this->maker->id
        );
        WarehouseOperationService::approveAdjustment($adjDamage, $this->checker->id);
        $this->assertEquals(60, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // Verify Authoritative Stock Formula:
        // Inflows = Purchase (100) + Sell Return (5) = 105
        // Outflows = Sell (30) + Purchase Return (10) + Expired (3) + Damage (2) = 45
        // Stock = Inflows (105) - Outflows (45) = 60
        $flow = InventoryService::getStockFlowSummary($this->warehouse->id, $this->product->id);

        $this->assertEquals(100, $flow['inflows']['purchase']['quantity']);
        $this->assertEquals(5, $flow['inflows']['sell_return']['quantity']);
        $this->assertEquals(105, $flow['inflows']['total_quantity']);

        $this->assertEquals(30, $flow['outflows']['sell']['quantity']);
        $this->assertEquals(10, $flow['outflows']['purchase_return']['quantity']);
        $this->assertEquals(3, $flow['outflows']['expired_item']['quantity']);
        $this->assertEquals(2, $flow['outflows']['damage']['quantity']);
        $this->assertEquals(45, $flow['outflows']['total_quantity']);

        $this->assertEquals(60, $flow['stock']['on_hand_quantity']);
        $this->assertEquals(60, $flow['stock']['available_quantity']);
        $this->assertEquals(60, $flow['stock']['net_flow_quantity']);
    }
}
