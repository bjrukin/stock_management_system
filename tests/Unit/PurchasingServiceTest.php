<?php

namespace Tests\Unit;

use App\Models\FiscalYear;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\InventoryService;
use App\Services\PurchasingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PurchasingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FiscalYear $fiscalYear;
    protected Warehouse $warehouse;
    protected WarehouseLocation $storageLocation;
    protected Supplier $supplier;
    protected Product $product;
    protected Unit $unitTab;

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
            'code' => 'WH-PUR-TEST',
            'name' => 'Purchasing Test Hub',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->storageLocation = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'LOC-STORE-1',
            'name' => 'Main Storage',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-TEST-1',
            'name' => 'Nepal Pharma Suppliers',
            'pan_vat_number' => '301234567',
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        $this->unitTab = Unit::create(['name' => 'Tablet', 'abbreviation' => 'Tab']);

        $this->product = Product::create([
            'code' => 'PRD-TEST-MED',
            'name' => 'Amoxicillin 500mg',
            'generic_name' => 'Amoxicillin',
            'base_unit_id' => $this->unitTab->id,
            'purchase_price' => 5.0000,
            'selling_price' => 7.0000,
            'is_active' => true,
        ]);

        $role = \App\Models\Role::create(['name' => 'Admin']);
        $this->admin = \App\Models\Admin::create([
            'role_id' => $role->id,
            'name' => 'Purchasing Manager',
            'username' => 'purchasing_admin',
            'status' => 1,
        ]);
    }

    public function test_creates_and_approves_purchase_order(): void
    {
        $po = PurchasingService::createPurchaseOrder(
            [
                'supplier_id' => $this->supplier->id,
                'warehouse_id' => $this->warehouse->id,
                'order_date_ad' => '2026-10-18',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'unit_id' => $this->unitTab->id,
                    'ordered_qty' => 1000,
                    'conversion_factor' => 1.0,
                    'unit_price' => 5.0000,
                    'discount_pct' => 10.0000, // 10% discount on 5000 = 500 discount -> 4500
                    'tax_rate' => 0.0000,
                ],
            ]
        );

        $this->assertEquals('draft', $po->status);
        $this->assertStringStartsWith('PO-', $po->po_number);
        $this->assertEquals('5000.00', $po->subtotal);
        $this->assertEquals('500.00', $po->discount_amount);
        $this->assertEquals('4500.00', $po->total_amount);

        // Approve PO
        $approvedPo = PurchasingService::approvePurchaseOrder($po, $this->admin->id);
        $this->assertEquals('approved', $approvedPo->status);
        $this->assertEquals($this->admin->id, $approvedPo->approved_by);
        $this->assertNotNull($approvedPo->approved_at);
    }

    public function test_posts_goods_receipt_with_landed_cost_allocation_and_subledger_updates(): void
    {
        // 1. Create and approve PO
        $po = PurchasingService::createPurchaseOrder(
            [
                'supplier_id' => $this->supplier->id,
                'warehouse_id' => $this->warehouse->id,
                'order_date_ad' => '2026-10-18',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'unit_id' => $this->unitTab->id,
                    'ordered_qty' => 1000,
                    'conversion_factor' => 1.0,
                    'unit_price' => 5.0000,
                ],
            ]
        );
        PurchasingService::approvePurchaseOrder($po, $this->admin->id);
        $poLine = $po->lines()->first();

        // 2. Create GRN receiving 1000 tablets with freight = 200.00
        $grn = PurchasingService::createGoodsReceipt(
            [
                'purchase_order_id' => $po->id,
                'supplier_id' => $this->supplier->id,
                'warehouse_id' => $this->warehouse->id,
                'supplier_invoice_number' => 'INV-SUP-9988',
                'receipt_date_ad' => '2026-10-18',
                'freight_amount' => 200.00,
            ],
            [
                [
                    'purchase_order_line_id' => $poLine->id,
                    'product_id' => $this->product->id,
                    'batch_number' => 'BT-AMOX-2024',
                    'mfg_date_ad' => '2024-05-01',
                    'expiry_date_ad' => Carbon::today()->addMonths(24)->format('Y-m-d'),
                    'location_id' => $this->storageLocation->id,
                    'unit_id' => $this->unitTab->id,
                    'received_qty' => 1000,
                    'conversion_factor' => 1.0,
                    'unit_cost' => 5.0000,
                ],
            ]
        );

        $this->assertEquals('draft', $grn->status);
        $this->assertEquals('5000.00', $grn->total_amount); // Supplier payable amount
        $this->assertEquals('5200.00', $grn->total_landed_cost); // Total inventory value

        // 3. Post GRN
        $postedGrn = PurchasingService::postGoodsReceipt($grn, $this->admin->id);

        $this->assertEquals('posted', $postedGrn->status);

        // Verify line received landed cost share: 200.00 freight allocated
        $grnLine = $postedGrn->lines()->first();
        $this->assertEquals('200.00', $grnLine->landed_cost_share);
        // Effective unit cost = (5000 + 200) / 1000 = 5.2000 per tablet
        $this->assertEquals('5.2000', $grnLine->effective_unit_cost);

        // Verify batch was created with cost_price = 5.2000
        $batch = $grnLine->batch;
        $this->assertNotNull($batch);
        $this->assertEquals('5.2000', $batch->cost_price);

        // Verify stock was ingested
        $this->assertEquals(1000, InventoryService::getOnHandStock($this->product->id, $this->warehouse->id));

        // Verify PO was updated to 'received'
        $po->refresh();
        $this->assertEquals('received', $po->status);
        $this->assertEquals(1000, $poLine->fresh()->received_base_qty);

        // Verify Supplier Accounts Payable Subledger credit entry
        $this->assertDatabaseHas('supplier_ledger_entries', [
            'supplier_id' => $this->supplier->id,
            'transaction_type' => 'PURCHASE_INVOICE',
            'reference_id' => $grn->id,
            'credit_amount' => '5000.00',
            'running_balance' => '5000.00',
        ]);

        $this->assertEquals('5000.00', $this->supplier->fresh()->current_balance);
    }

    public function test_posts_purchase_return_and_deducts_stock_and_supplier_liability(): void
    {
        // First receipt 500 units
        $grn = PurchasingService::createGoodsReceipt(
            [
                'supplier_id' => $this->supplier->id,
                'warehouse_id' => $this->warehouse->id,
                'receipt_date_ad' => '2026-10-18',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'batch_number' => 'BT-RET-TEST',
                    'expiry_date_ad' => Carbon::today()->addMonths(12)->format('Y-m-d'),
                    'location_id' => $this->storageLocation->id,
                    'unit_id' => $this->unitTab->id,
                    'received_qty' => 500,
                    'conversion_factor' => 1.0,
                    'unit_cost' => 5.0000,
                ],
            ]
        );
        PurchasingService::postGoodsReceipt($grn, $this->admin->id);
        $batch = $grn->lines()->first()->batch;

        $this->assertEquals(500, InventoryService::getOnHandStock($this->product->id, $this->warehouse->id));
        $this->assertEquals('2500.00', $this->supplier->fresh()->current_balance);

        // Now return 100 units
        $prtn = PurchasingService::createPurchaseReturn(
            [
                'goods_receipt_id' => $grn->id,
                'supplier_id' => $this->supplier->id,
                'warehouse_id' => $this->warehouse->id,
                'return_date_ad' => '2026-10-18',
                'reason' => 'Damaged during transit',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $batch->id,
                    'location_id' => $this->storageLocation->id,
                    'unit_id' => $this->unitTab->id,
                    'returned_qty' => 100,
                    'conversion_factor' => 1.0,
                    'unit_price' => 5.0000,
                ],
            ]
        );

        $this->assertEquals('draft', $prtn->status);
        $this->assertEquals('500.00', $prtn->total_amount);

        // Post return
        $postedPrtn = PurchasingService::postPurchaseReturn($prtn, $this->admin->id);
        $this->assertEquals('posted', $postedPrtn->status);

        // Stock must drop to 400
        $this->assertEquals(400, InventoryService::getOnHandStock($this->product->id, $this->warehouse->id));

        // Supplier AP liability drops to 2000.00
        $this->assertEquals('2000.00', $this->supplier->fresh()->current_balance);

        // Verify debit note in supplier ledger
        $this->assertDatabaseHas('supplier_ledger_entries', [
            'supplier_id' => $this->supplier->id,
            'transaction_type' => 'PURCHASE_RETURN',
            'reference_id' => $prtn->id,
            'debit_amount' => '500.00',
            'running_balance' => '2000.00',
        ]);
    }

    public function test_supplier_ledger_entry_is_immutable(): void
    {
        $entry = SupplierLedgerEntry::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'supplier_id' => $this->supplier->id,
            'entry_date_ad' => '2026-10-18',
            'transaction_type' => 'PURCHASE_INVOICE',
            'reference_type' => 'GoodsReceipt',
            'reference_id' => 1,
            'reference_number' => 'GRN-001',
            'credit_amount' => 1000.00,
            'running_balance' => 1000.00,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Supplier ledger entries are immutable and cannot be updated.');

        $entry->credit_amount = 999.00;
        $entry->save();
    }
}
