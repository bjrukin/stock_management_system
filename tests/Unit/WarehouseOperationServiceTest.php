<?php

namespace Tests\Unit;

use App\Models\AccountingPeriod;
use App\Models\Admin;
use App\Models\Batch;
use App\Models\BatchRecall;
use App\Models\FiscalYear;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\TaxCategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\InventoryService;
use App\Services\WarehouseOperationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class WarehouseOperationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FiscalYear $fiscalYear;
    protected Warehouse $warehouseA;
    protected Warehouse $warehouseB;
    protected WarehouseLocation $storageA;
    protected WarehouseLocation $storageB;
    protected WarehouseLocation $coldStorageA;
    protected WarehouseLocation $quarantineA;
    protected Product $product;
    protected Batch $batch;
    protected Unit $unitBottle;
    protected Admin $makerAdmin;
    protected Admin $checkerAdmin;

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

        $this->warehouseA = Warehouse::create([
            'code' => 'WH-KTM-HUB',
            'name' => 'Kathmandu Central Hub',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->warehouseB = Warehouse::create([
            'code' => 'WH-POKHARA-HUB',
            'name' => 'Pokhara Regional Depot',
            'is_primary' => false,
            'is_active' => true,
        ]);

        $this->storageA = WarehouseLocation::create([
            'warehouse_id' => $this->warehouseA->id,
            'code' => 'LOC-A-STORE',
            'name' => 'Main Ambient Storage',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $this->coldStorageA = WarehouseLocation::create([
            'warehouse_id' => $this->warehouseA->id,
            'code' => 'LOC-A-COLD',
            'name' => 'Chiller Room 2-8C',
            'type' => 'cold_storage',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $this->quarantineA = WarehouseLocation::create([
            'warehouse_id' => $this->warehouseA->id,
            'code' => 'LOC-A-QUAR',
            'name' => 'Quarantine Bay',
            'type' => 'quarantine',
            'is_sellable' => false,
            'is_available' => false,
        ]);

        $this->storageB = WarehouseLocation::create([
            'warehouse_id' => $this->warehouseB->id,
            'code' => 'LOC-B-STORE',
            'name' => 'Pokhara Primary Rack',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $this->unitBottle = Unit::create(['name' => 'Bottle', 'abbreviation' => 'Btl']);

        $taxCat = TaxCategory::create([
            'code' => 'VAT0',
            'name' => 'VAT Exempt',
            'rate' => 0.00,
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'code' => 'PRD-INSULIN-100',
            'name' => 'Human Insulin 100IU/ml',
            'generic_name' => 'Human Insulin',
            'base_unit_id' => $this->unitBottle->id,
            'tax_category_id' => $taxCat->id,
            'purchase_price' => 350.00,
            'selling_price' => 450.00,
            'is_active' => true,
        ]);

        $this->batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-INS-001',
            'manufacturing_date_ad' => Carbon::today()->subMonths(2)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addYears(2)->format('Y-m-d'),
            'cost_price' => 350.00,
            'selling_price' => 450.00,
            'is_active' => true,
        ]);

        $role = Role::create(['name' => 'Supervisor']);
        $this->makerAdmin = Admin::create([
            'role_id' => $role->id,
            'name' => 'Warehouse Staff (Maker)',
            'username' => 'maker_staff',
            'status' => 1,
        ]);

        $this->checkerAdmin = Admin::create([
            'role_id' => $role->id,
            'name' => 'Warehouse Manager (Checker)',
            'username' => 'checker_manager',
            'status' => 1,
        ]);
    }

    public function test_creates_dispatches_and_receives_inter_warehouse_transfer(): void
    {
        // Inflow 100 bottles into Warehouse A
        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouseA->id,
            'location_id' => $this->storageA->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 100,
            'unit_cost' => 350.00,
            'created_by' => $this->makerAdmin->id,
        ]);

        $this->assertEquals(100, InventoryService::getAvailableStock($this->product->id, $this->warehouseA->id));
        $this->assertEquals(0, InventoryService::getAvailableStock($this->product->id, $this->warehouseB->id));

        // Create transfer of 40 bottles from Warehouse A to Warehouse B
        $transfer = WarehouseOperationService::createTransfer(
            [
                'from_warehouse_id' => $this->warehouseA->id,
                'to_warehouse_id' => $this->warehouseB->id,
                'created_by' => $this->makerAdmin->id,
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $this->batch->id,
                    'from_location_id' => $this->storageA->id,
                    'to_location_id' => $this->storageB->id,
                    'quantity' => 40,
                ],
            ]
        );

        $this->assertEquals('draft', $transfer->status);
        $this->assertCount(1, $transfer->lines);

        // Dispatch transfer
        $dispatched = WarehouseOperationService::dispatchTransfer($transfer, $this->makerAdmin->id);
        $this->assertEquals('dispatched', $dispatched->status);
        $this->assertEquals('in_transit', $dispatched->lines->first()->status);

        // Warehouse A available stock reduced: 100 - 40 = 60
        $this->assertEquals(60, InventoryService::getAvailableStock($this->product->id, $this->warehouseA->id));
        // Warehouse B stock still 0 (in transit)
        $this->assertEquals(0, InventoryService::getAvailableStock($this->product->id, $this->warehouseB->id));

        // Receive transfer at destination Warehouse B
        $received = WarehouseOperationService::receiveTransfer(
            $dispatched,
            [$dispatched->lines->first()->id => $this->storageB->id],
            $this->checkerAdmin->id
        );

        $this->assertEquals('received', $received->status);
        $this->assertEquals('received', $received->lines->first()->status);

        // Warehouse B available stock now 40
        $this->assertEquals(40, InventoryService::getAvailableStock($this->product->id, $this->warehouseB->id));
    }

    public function test_internal_warehouse_relocation(): void
    {
        // Inflow 50 bottles into Ambient Storage A
        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouseA->id,
            'location_id' => $this->storageA->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 50,
            'unit_cost' => 350.00,
            'created_by' => $this->makerAdmin->id,
        ]);

        // Relocate 20 bottles to Cold Storage A
        $res = WarehouseOperationService::relocateStock(
            $this->warehouseA->id,
            $this->product->id,
            $this->batch->id,
            $this->storageA->id,
            $this->coldStorageA->id,
            20,
            $this->makerAdmin->id,
            'Transferring sensitive cold chain stock'
        );

        $this->assertNotNull($res['out_movement']);
        $this->assertNotNull($res['in_movement']);

        // Ambient balance drops to 30
        $ambientBal = \App\Models\InventoryBalance::where('warehouse_id', $this->warehouseA->id)
            ->where('location_id', $this->storageA->id)
            ->where('batch_id', $this->batch->id)
            ->first();
        $this->assertEquals(30, $ambientBal->quantity_on_hand);

        // Cold storage balance is 20
        $coldBal = \App\Models\InventoryBalance::where('warehouse_id', $this->warehouseA->id)
            ->where('location_id', $this->coldStorageA->id)
            ->where('batch_id', $this->batch->id)
            ->first();
        $this->assertEquals(20, $coldBal->quantity_on_hand);

        // Overall warehouse available stock unchanged: 30 + 20 = 50
        $this->assertEquals(50, InventoryService::getAvailableStock($this->product->id, $this->warehouseA->id));
    }

    public function test_stock_adjustment_maker_checker_approval(): void
    {
        // Initial stock 50 bottles
        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouseA->id,
            'location_id' => $this->storageA->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 50,
            'unit_cost' => 350.00,
            'created_by' => $this->makerAdmin->id,
        ]);

        // Maker creates adjustment: book 50, physical count 55 (+5 surplus)
        $adjustment = WarehouseOperationService::createAdjustment(
            [
                'warehouse_id' => $this->warehouseA->id,
                'type' => 'PHYSICAL_COUNT',
                'reason' => 'Monthly cycle count surplus',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $this->batch->id,
                    'location_id' => $this->storageA->id,
                    'book_quantity' => 50,
                    'counted_quantity' => 55,
                    'unit_cost' => 350.00,
                ],
            ],
            $this->makerAdmin->id
        );

        $this->assertEquals('draft', $adjustment->status);
        $this->assertEquals('1750.00', $adjustment->total_variance_value); // 5 * 350

        // Maker attempts self-approval -> Maker-checker exception
        try {
            WarehouseOperationService::approveAdjustment($adjustment, $this->makerAdmin->id);
            $this->fail('Expected maker-checker violation exception was not thrown.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Maker-Checker violation', $e->getMessage());
        }

        // Distinct checker approves
        $approved = WarehouseOperationService::approveAdjustment($adjustment, $this->checkerAdmin->id);

        $this->assertEquals('approved', $approved->status);
        $this->assertEquals($this->checkerAdmin->id, $approved->approved_by);

        // Stock increased to 55
        $this->assertEquals(55, InventoryService::getAvailableStock($this->product->id, $this->warehouseA->id));
    }

    public function test_quarantine_and_batch_recall(): void
    {
        // Inflow 80 bottles into Ambient Storage
        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouseA->id,
            'location_id' => $this->storageA->id,
            'product_id' => $this->product->id,
            'batch_id' => $this->batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 80,
            'unit_cost' => 350.00,
            'created_by' => $this->makerAdmin->id,
        ]);

        $this->assertEquals(80, InventoryService::getAvailableStock($this->product->id, $this->warehouseA->id));

        // Issue recall
        $recall = WarehouseOperationService::quarantineBatch(
            $this->batch->id,
            'DDA-RECALL-2081-09',
            'Department of Drug Administration',
            'Substandard potency detected during random sampling',
            $this->checkerAdmin->id
        );

        $this->assertInstanceOf(BatchRecall::class, $recall);
        $this->assertEquals(80, $recall->quarantined_quantity);

        // Batch flagged quarantined & recalled
        $this->batch->refresh();
        $this->assertTrue($this->batch->is_quarantined);
        $this->assertTrue($this->batch->is_recalled);

        // Available sellable stock immediately drops to 0!
        $this->assertEquals(0, InventoryService::getAvailableStock($this->product->id, $this->warehouseA->id));

        // Quarantine location now holds 80 units
        $quarBal = \App\Models\InventoryBalance::where('warehouse_id', $this->warehouseA->id)
            ->where('location_id', $this->quarantineA->id)
            ->where('batch_id', $this->batch->id)
            ->first();
        $this->assertNotNull($quarBal);
        $this->assertEquals(80, $quarBal->quantity_on_hand);
    }

    public function test_blocks_operations_in_closed_accounting_period(): void
    {
        // Close period 1
        $period = AccountingPeriod::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'name' => 'Shrawan 2081',
            'period_number' => 1,
            'start_date_ad' => '2024-07-16',
            'start_date_bs' => '2081-04-01',
            'end_date_ad' => '2024-08-16',
            'end_date_bs' => '2081-04-32',
            'is_closed' => true,
            'closed_by' => $this->checkerAdmin->id,
            'closed_at' => now(),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('locked and closed');

        WarehouseOperationService::createTransfer(
            [
                'from_warehouse_id' => $this->warehouseA->id,
                'to_warehouse_id' => $this->warehouseB->id,
                'transfer_date_ad' => '2024-08-01',
            ],
            []
        );
    }
}
