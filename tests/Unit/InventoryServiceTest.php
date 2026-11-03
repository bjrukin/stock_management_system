<?php

namespace Tests\Unit;

use App\Exceptions\BatchExpiredException;
use App\Exceptions\BatchQuarantinedException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\NegativeStockException;
use App\Models\Batch;
use App\Models\FiscalYear;
use App\Models\InventoryBalance;
use App\Models\Product;
use App\Models\ProductPackagingLevel;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\InventoryService;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Warehouse $warehouse;
    protected WarehouseLocation $storageLocation;
    protected WarehouseLocation $quarantineLocation;
    protected Product $product;
    protected Unit $unitTab;
    protected Unit $unitStr;
    protected FiscalYear $fiscalYear;

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
            'code' => 'WH-TEST',
            'name' => 'Test Hub',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->storageLocation = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'LOC-STORE',
            'name' => 'Storage A',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $this->quarantineLocation = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'LOC-QUAR',
            'name' => 'Quarantine Area',
            'type' => 'quarantine',
            'is_sellable' => false,
            'is_available' => false,
        ]);

        $this->unitTab = Unit::create(['name' => 'Tablet', 'abbreviation' => 'Tab']);
        $this->unitStr = Unit::create(['name' => 'Strip', 'abbreviation' => 'Str']);

        $this->product = Product::create([
            'code' => 'PRD-TEST-1',
            'name' => 'Paracetamol 500mg',
            'generic_name' => 'Paracetamol',
            'base_unit_id' => $this->unitTab->id,
            'purchase_price' => 1.0000,
            'selling_price' => 1.5000,
            'is_active' => true,
        ]);

        // Default setting: do not allow negative stock
        SettingService::set('allow_negative_stock', 'false', 'inventory', 'boolean');
    }

    public function test_records_stock_inflow_and_calculates_on_hand_and_available(): void
    {
        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'B-001',
            'expiry_date_ad' => Carbon::today()->addMonths(12),
            'cost_price' => 1.0000,
        ]);

        $movement = InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'OPENING_STOCK',
            'quantity' => 1000,
        ]);

        $this->assertInstanceOf(StockMovement::class, $movement);
        $this->assertEquals(1000, $movement->quantity);
        $this->assertEquals(1000, $movement->balance_after);

        $this->assertEquals(1000, InventoryService::getOnHandStock($this->product->id, $this->warehouse->id));
        $this->assertEquals(1000, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));
    }

    public function test_fefo_allocation_strictly_prioritizes_earliest_expiry(): void
    {
        // Batch A: expires in 2 months (quantity 500)
        $batchA = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-A-EARLY',
            'expiry_date_ad' => Carbon::today()->addMonths(2),
            'cost_price' => 1.0000,
        ]);
        InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batchA->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 500,
        ]);

        // Batch B: expires in 12 months (quantity 1000)
        $batchB = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-B-LATER',
            'expiry_date_ad' => Carbon::today()->addMonths(12),
            'cost_price' => 1.0500,
        ]);
        InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batchB->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 1000,
        ]);

        // Allocate 700 units: Should take 500 from Batch A, then 200 from Batch B
        $allocation = InventoryService::allocateFefo($this->product->id, 700, $this->warehouse->id);

        $this->assertCount(2, $allocation);
        $this->assertEquals($batchA->id, $allocation[0]['batch_id']);
        $this->assertEquals(500, $allocation[0]['allocated_qty']);

        $this->assertEquals($batchB->id, $allocation[1]['batch_id']);
        $this->assertEquals(200, $allocation[1]['allocated_qty']);
    }

    public function test_fefo_excludes_expired_and_quarantined_batches(): void
    {
        // Expired batch
        $batchExpired = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-EXPIRED',
            'expiry_date_ad' => Carbon::today()->subDays(5),
            'cost_price' => 1.0000,
        ]);
        InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batchExpired->id,
            'movement_type' => 'OPENING_STOCK',
            'quantity' => 500,
        ]);

        // Quarantined batch
        $batchQuarantine = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-QUARANTINE',
            'expiry_date_ad' => Carbon::today()->addMonths(6),
            'is_quarantined' => true,
            'cost_price' => 1.0000,
        ]);
        InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batchQuarantine->id,
            'movement_type' => 'OPENING_STOCK',
            'quantity' => 500,
        ]);

        // Available stock should be 0 because both batches are ineligible for sale
        $this->assertEquals(0, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // Attempting to allocate should throw InsufficientStockException
        $this->expectException(InsufficientStockException::class);
        InventoryService::allocateFefo($this->product->id, 100, $this->warehouse->id);
    }

    public function test_reservation_reduces_available_stock_without_affecting_on_hand(): void
    {
        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'B-RES-1',
            'expiry_date_ad' => Carbon::today()->addMonths(6),
            'cost_price' => 1.0000,
        ]);
        InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'OPENING_STOCK',
            'quantity' => 1000,
        ]);

        $this->assertEquals(1000, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));
        $this->assertEquals(1000, InventoryService::getOnHandStock($this->product->id, $this->warehouse->id));

        // Reserve 300 units
        InventoryService::reserveStock($this->product->id, $batch->id, $this->warehouse->id, $this->storageLocation->id, 300);

        // Available stock drops to 700; physical on-hand remains 1000
        $this->assertEquals(700, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));
        $this->assertEquals(1000, InventoryService::getOnHandStock($this->product->id, $this->warehouse->id));

        // Release 100 units
        InventoryService::releaseReservedStock($this->product->id, $batch->id, $this->warehouse->id, $this->storageLocation->id, 100);
        $this->assertEquals(800, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));
    }

    public function test_negative_stock_exception_thrown_when_stock_insufficient(): void
    {
        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'B-NEG-1',
            'expiry_date_ad' => Carbon::today()->addMonths(6),
            'cost_price' => 1.0000,
        ]);
        InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'OPENING_STOCK',
            'quantity' => 100,
        ]);

        $this->expectException(NegativeStockException::class);
        // Attempt to deduct 150 from 100 on-hand
        InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'SALE_DISPATCH',
            'quantity' => -150,
        ]);
    }

    public function test_stock_movement_cannot_be_updated_or_deleted(): void
    {
        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'B-IMMUTABLE',
            'expiry_date_ad' => Carbon::today()->addMonths(6),
        ]);
        $movement = InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'OPENING_STOCK',
            'quantity' => 50,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stock movements are immutable and cannot be updated.');
        $movement->quantity = 100;
        $movement->save();
    }

    public function test_packaging_level_multi_tier_conversions(): void
    {
        $packLevel = ProductPackagingLevel::create([
            'product_id' => $this->product->id,
            'unit_id' => $this->unitStr->id,
            'level_name' => 'Strip of 10',
            'level_order' => 2,
            'conversion_factor' => 10.0000,
        ]);

        // 5 strips = 50 base units (tablets)
        $this->assertEquals(50, $packLevel->toBaseUnits(5));

        // 50 base units = 5 strips
        $this->assertEquals(5.0, $packLevel->fromBaseUnits(50));
    }

    public function test_reconciliation_detects_and_fixes_balance_discrepancies(): void
    {
        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'B-RECON',
            'expiry_date_ad' => Carbon::today()->addMonths(6),
        ]);
        InventoryService::recordStockMovement([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'OPENING_STOCK',
            'quantity' => 500,
        ]);

        // Artificially corrupt the balance cache to simulate external drift
        $balance = InventoryBalance::where([
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
        ])->first();
        $balance->quantity_on_hand = 999;
        $balance->save();

        // Reconcile without fix: should report 1 discrepancy
        $discrepancies = InventoryService::reconcileWarehouse($this->warehouse->id, false);
        $this->assertCount(1, $discrepancies);
        $this->assertEquals(500, $discrepancies[0]['ledger_quantity']);
        $this->assertEquals(999, $discrepancies[0]['cached_on_hand']);
        $this->assertEquals(499, $discrepancies[0]['variance']);
        $this->assertFalse($discrepancies[0]['fixed']);

        // Reconcile with fix: should correct the balance back to 500
        $fixed = InventoryService::reconcileWarehouse($this->warehouse->id, true);
        $this->assertCount(1, $fixed);
        $this->assertTrue($fixed[0]['fixed']);

        // Verify balance is now restored
        $balance->refresh();
        $this->assertEquals(500, $balance->quantity_on_hand);
    }
}
