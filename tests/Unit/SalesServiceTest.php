<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\FiscalYear;
use App\Models\Product;
use App\Models\Role;
use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use App\Models\TaxCategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\InventoryService;
use App\Services\SalesService;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class SalesServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FiscalYear $fiscalYear;
    protected Warehouse $warehouse;
    protected WarehouseLocation $storageLocation;
    protected Customer $customer;
    protected Product $product;
    protected Unit $unitStrip;
    protected Admin $admin;

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
            'code' => 'WH-SALES-TEST',
            'name' => 'Sales Test Hub',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->storageLocation = WarehouseLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'LOC-SALE-1',
            'name' => 'Primary Saleable Shelf',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $this->unitStrip = Unit::create(['name' => 'Strip', 'abbreviation' => 'Stp']);

        $taxCat = TaxCategory::create([
            'code' => 'VAT13',
            'name' => 'VAT 13%',
            'rate' => 13.00,
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'code' => 'PRD-CEF-200',
            'name' => 'Cefixime 200mg',
            'generic_name' => 'Cefixime',
            'base_unit_id' => $this->unitStrip->id,
            'tax_category_id' => $taxCat->id,
            'purchase_price' => 80.00,
            'selling_price' => 120.00,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'code' => 'CUST-001',
            'name' => 'Patan Hospital Pharmacy',
            'pan_vat_number' => '301998877',
            'dda_license_number' => 'DDA-RET-9988',
            'credit_limit' => 50000.00,
            'credit_period_days' => 30,
            'credit_stop' => false,
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        $role = Role::create(['name' => 'Admin']);
        $this->admin = Admin::create([
            'role_id' => $role->id,
            'name' => 'Sales Manager',
            'username' => 'sales_admin',
            'status' => 1,
        ]);
    }

    public function test_creates_invoice_with_fefo_allocation(): void
    {
        // Batch A: Expiring earlier (2 years from now) with 50 units
        $batchA = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-EARLY',
            'manufacturing_date_ad' => Carbon::today()->subMonths(3)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addYears(1)->format('Y-m-d'),
            'cost_price' => 80.00,
            'selling_price' => 120.00,
            'mrp' => 130.00,
            'is_active' => true,
        ]);

        // Batch B: Expiring later (3 years from now) with 100 units
        $batchB = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-LATER',
            'manufacturing_date_ad' => Carbon::today()->subMonths(2)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addYears(2)->format('Y-m-d'),
            'cost_price' => 82.00,
            'selling_price' => 120.00,
            'mrp' => 130.00,
            'is_active' => true,
        ]);

        // Inflow stock: 50 of Batch A, 100 of Batch B
        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batchA->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 50,
            'unit_cost' => 80.00,
            'created_by' => $this->admin->id,
        ]);

        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batchB->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 100,
            'unit_cost' => 82.00,
            'created_by' => $this->admin->id,
        ]);

        // Order 70 units -> Should take all 50 from Batch A and 20 from Batch B
        $invoice = SalesService::createInvoice(
            [
                'customer_id' => $this->customer->id,
                'warehouse_id' => $this->warehouse->id,
                'payment_method' => 'credit',
                'created_by' => $this->admin->id,
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'unit_id' => $this->unitStrip->id,
                    'qty' => 70,
                    'unit_price' => 120.00,
                    'tax_rate' => 13.00,
                ],
            ],
            true
        );

        $this->assertEquals('draft', $invoice->status);
        $this->assertCount(2, $invoice->lines);

        $line1 = $invoice->lines->firstWhere('batch_id', $batchA->id);
        $line2 = $invoice->lines->firstWhere('batch_id', $batchB->id);

        $this->assertNotNull($line1);
        $this->assertNotNull($line2);
        $this->assertEquals(50, (int) $line1->base_qty);
        $this->assertEquals(20, (int) $line2->base_qty);

        // Subtotal = 70 * 120 = 8400
        $this->assertEquals('8400.00', $invoice->subtotal);
        // Tax 13% of 8400 = 1092.00
        $this->assertEquals('1092.00', $invoice->tax_amount);
        // Total = 9492.00
        $this->assertEquals('9492.00', $invoice->total_amount);
        $this->assertEquals('9492.00', $invoice->credit_amount);
    }

    public function test_blocks_invoice_when_customer_is_on_credit_stop(): void
    {
        $this->customer->update(['credit_stop' => true]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Credit Stop');

        SalesService::createInvoice(
            [
                'customer_id' => $this->customer->id,
                'warehouse_id' => $this->warehouse->id,
            ],
            []
        );
    }

    public function test_blocks_invoice_when_credit_limit_exceeded(): void
    {
        $this->customer->update([
            'credit_limit' => 1000.00,
            'current_balance' => 800.00,
        ]);

        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-TEST-CL',
            'manufacturing_date_ad' => Carbon::today()->subMonths(1)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addYears(2)->format('Y-m-d'),
            'cost_price' => 80.00,
            'selling_price' => 120.00,
            'is_active' => true,
        ]);

        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 10,
            'unit_cost' => 80.00,
            'created_by' => $this->admin->id,
        ]);

        // Invoicing 5 units at 120 = 600 NPR (800 + 600 = 1400 > 1000 limit)
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Credit limit exceeded');

        SalesService::createInvoice(
            [
                'customer_id' => $this->customer->id,
                'warehouse_id' => $this->warehouse->id,
                'payment_method' => 'credit',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'unit_id' => $this->unitStrip->id,
                    'qty' => 5,
                    'unit_price' => 120.00,
                ],
            ]
        );
    }

    public function test_posts_invoice_deducts_stock_and_records_ar_subledger(): void
    {
        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-POST-1',
            'manufacturing_date_ad' => Carbon::today()->subMonths(1)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addYears(2)->format('Y-m-d'),
            'cost_price' => 80.00,
            'selling_price' => 120.00,
            'is_active' => true,
        ]);

        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 50,
            'unit_cost' => 80.00,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals(50, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        $invoice = SalesService::createInvoice(
            [
                'customer_id' => $this->customer->id,
                'warehouse_id' => $this->warehouse->id,
                'payment_method' => 'credit',
                'created_by' => $this->admin->id,
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'unit_id' => $this->unitStrip->id,
                    'qty' => 10,
                    'unit_price' => 100.00,
                ],
            ]
        );

        $postedInvoice = SalesService::postInvoice($invoice, $this->admin->id);

        $this->assertEquals('posted', $postedInvoice->status);
        $this->assertEquals($this->admin->id, $postedInvoice->posted_by);

        // Stock decreased: 50 - 10 = 40
        $this->assertEquals(40, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // AR subledger ledger entry exists
        $ledgerEntry = CustomerLedgerEntry::where('reference_id', $postedInvoice->id)->first();
        $this->assertNotNull($ledgerEntry);
        $this->assertEquals('1000.00', $ledgerEntry->debit_amount);
        $this->assertEquals('0.00', $ledgerEntry->credit_amount);
        $this->assertEquals('1000.00', $ledgerEntry->running_balance);

        // Customer balance updated
        $this->customer->refresh();
        $this->assertEquals('1000.00', $this->customer->current_balance);
    }

    public function test_generates_invoice_pdf(): void
    {
        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-PDF-1',
            'manufacturing_date_ad' => Carbon::today()->subMonths(1)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addYears(2)->format('Y-m-d'),
            'cost_price' => 80.00,
            'selling_price' => 120.00,
            'is_active' => true,
        ]);

        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 10,
            'unit_cost' => 80.00,
            'created_by' => $this->admin->id,
        ]);

        $invoice = SalesService::createInvoice(
            [
                'customer_id' => $this->customer->id,
                'warehouse_id' => $this->warehouse->id,
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'unit_id' => $this->unitStrip->id,
                    'qty' => 5,
                    'unit_price' => 120.00,
                ],
            ]
        );

        $pdf = SalesService::generateInvoicePdf($invoice);
        $this->assertInstanceOf(PDF::class, $pdf);

        $output = $pdf->output();
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF', $output);
    }

    public function test_creates_and_posts_sale_return(): void
    {
        $batch = Batch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-SRTN-1',
            'manufacturing_date_ad' => Carbon::today()->subMonths(1)->format('Y-m-d'),
            'expiry_date_ad' => Carbon::today()->addYears(2)->format('Y-m-d'),
            'cost_price' => 80.00,
            'selling_price' => 100.00,
            'is_active' => true,
        ]);

        // Inflow 20, sell 10
        InventoryService::recordStockMovement([
            'fiscal_year_id' => $this->fiscalYear->id,
            'warehouse_id' => $this->warehouse->id,
            'location_id' => $this->storageLocation->id,
            'product_id' => $this->product->id,
            'batch_id' => $batch->id,
            'movement_type' => 'PURCHASE_RECEIPT',
            'quantity' => 20,
            'unit_cost' => 80.00,
            'created_by' => $this->admin->id,
        ]);

        $invoice = SalesService::createInvoice(
            [
                'customer_id' => $this->customer->id,
                'warehouse_id' => $this->warehouse->id,
                'payment_method' => 'credit',
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'unit_id' => $this->unitStrip->id,
                    'qty' => 10,
                    'unit_price' => 100.00,
                ],
            ]
        );
        SalesService::postInvoice($invoice, $this->admin->id);

        $this->customer->refresh();
        $this->assertEquals('1000.00', $this->customer->current_balance);
        $this->assertEquals(10, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // Return 3 units (saleable condition)
        $saleReturn = SalesService::createSaleReturn(
            [
                'customer_id' => $this->customer->id,
                'warehouse_id' => $this->warehouse->id,
                'sale_invoice_id' => $invoice->id,
                'reason' => 'Customer overstocked',
                'credit_note_issued' => true,
                'created_by' => $this->admin->id,
            ],
            [
                [
                    'product_id' => $this->product->id,
                    'batch_id' => $batch->id,
                    'location_id' => $this->storageLocation->id,
                    'unit_id' => $this->unitStrip->id,
                    'returned_qty' => 3,
                    'unit_price' => 100.00,
                    'condition' => 'saleable',
                ],
            ]
        );

        $this->assertEquals('draft', $saleReturn->status);
        $this->assertEquals('300.00', $saleReturn->total_amount);

        // Post return
        $postedReturn = SalesService::postSaleReturn($saleReturn, $this->admin->id);

        $this->assertEquals('posted', $postedReturn->status);

        // Available stock restored: 10 + 3 = 13
        $this->assertEquals(13, InventoryService::getAvailableStock($this->product->id, $this->warehouse->id));

        // Customer AR balance reduced by 300: 1000 - 300 = 700
        $this->customer->refresh();
        $this->assertEquals('700.00', $this->customer->current_balance);

        $returnLedger = CustomerLedgerEntry::where('reference_id', $postedReturn->id)
            ->where('transaction_type', 'SALE_RETURN')
            ->first();
        $this->assertNotNull($returnLedger);
        $this->assertEquals('300.00', $returnLedger->credit_amount);
        $this->assertEquals('700.00', $returnLedger->running_balance);
    }

    public function test_customer_ledger_entry_is_immutable(): void
    {
        $entry = CustomerLedgerEntry::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'customer_id' => $this->customer->id,
            'entry_date_ad' => '2026-10-18',
            'entry_date_bs' => '2083-07-02',
            'transaction_type' => 'SALE_INVOICE',
            'reference_type' => 'SaleInvoice',
            'reference_id' => 1,
            'reference_number' => 'INV-00001',
            'debit_amount' => 500.00,
            'credit_amount' => 0.00,
            'running_balance' => 500.00,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Customer ledger entries are immutable');

        $entry->update(['debit_amount' => 600.00]);
    }
}
