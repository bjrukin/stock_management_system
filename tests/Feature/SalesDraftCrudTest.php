<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\DocumentSequence;
use App\Models\FiscalYear;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\InventoryService;
use App\Services\SalesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesDraftCrudTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private Warehouse $warehouse;

    private WarehouseLocation $location;

    private Customer $customer;

    private Product $product;

    private Batch $batch;

    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();
        Role::forceCreate(['id' => 1, 'name' => 'Super Admin']);
        $this->admin = Admin::forceCreate(['id' => 41, 'name' => 'Admin', 'username' => 'admin', 'password' => bcrypt('password'), 'role_id' => 1, 'status' => true]);
        FiscalYear::create(['name' => '2083/84', 'start_date_ad' => '2026-07-17', 'end_date_ad' => '2027-07-16', 'is_current' => true]);
        $this->warehouse = Warehouse::create(['code' => 'WH', 'name' => 'Warehouse', 'is_active' => true]);
        $this->location = WarehouseLocation::create(['warehouse_id' => $this->warehouse->id, 'name' => 'Shelf', 'code' => 'SHELF', 'is_sellable' => true, 'is_available' => true, 'is_active' => true]);
        $this->customer = Customer::create(['code' => 'CUSTOMER', 'name' => 'Customer', 'credit_limit' => 100000, 'is_active' => true]);
        $this->unit = Unit::create(['name' => 'Tablet', 'abbreviation' => 'tab', 'is_active' => true]);
        $category = Category::create(['code' => 'CAT', 'name' => 'Category']);
        $this->product = Product::create(['code' => 'MED', 'name' => 'Medicine', 'generic_name' => 'Medicine', 'category_id' => $category->id, 'base_unit_id' => $this->unit->id, 'selling_price' => 20, 'is_active' => true]);
        $this->batch = Batch::create(['product_id' => $this->product->id, 'batch_number' => 'BATCH', 'expiry_date_ad' => '2027-07-01', 'cost_price' => 10, 'mrp' => 25]);
        InventoryService::recordStockMovement(['fiscal_year_id' => 1, 'warehouse_id' => $this->warehouse->id, 'location_id' => $this->location->id, 'product_id' => $this->product->id, 'batch_id' => $this->batch->id, 'movement_type' => 'PURCHASE_RECEIPT', 'quantity' => 100, 'unit_cost' => 10, 'created_by' => 41]);
        $this->actingAs($this->admin, 'admin');
        auth()->shouldUse('web');
    }

    private function invoicePayload(): array
    {
        return ['customer_id' => $this->customer->id, 'warehouse_id' => $this->warehouse->id, 'invoice_date_ad' => '2026-11-04', 'payment_method' => 'credit', 'auto_fefo' => 1, 'items' => [['product_id' => $this->product->id, 'unit_id' => $this->unit->id, 'qty' => 2, 'unit_price' => 20, 'discount_pct' => 0, 'tax_rate' => 0]]];
    }

    private function invoice(): SaleInvoice
    {
        $p = $this->invoicePayload();

        return SalesService::createInvoice($p + ['created_by' => 41], $p['items']);
    }

    private function returnPayload(): array
    {
        return ['customer_id' => $this->customer->id, 'warehouse_id' => $this->warehouse->id, 'return_date_ad' => '2026-11-04', 'reason' => 'Exchange', 'credit_note_issued' => 1, 'lines' => [['product_id' => $this->product->id, 'batch_id' => $this->batch->id, 'location_id' => $this->location->id, 'unit_id' => $this->unit->id, 'returned_qty' => 2, 'unit_price' => 20, 'condition' => 'saleable']]];
    }

    private function saleReturn(): SaleReturn
    {
        $p = $this->returnPayload();

        return SalesService::createSaleReturn($p + ['created_by' => 41], $p['lines']);
    }

    public function test_edit_form_contains_existing_header_and_line_values(): void
    {
        $invoice = $this->invoice();
        $this->get('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/edit')->assertOk()->assertSee('Edit Sales Invoice')->assertViewHas('initialItems', fn ($rows) => count($rows) === 1 && (float) $rows[0]['qty'] === 2.0 && (int) $rows[0]['batch_id'] === $this->batch->id);
        $return = $this->saleReturn();
        $this->get('https://localhost/admin/pharma/sales-return/'.$return->id.'/edit')->assertOk()->assertSee('Edit Sales Return')->assertViewHas('initialLines', fn ($rows) => count($rows) === 1 && (float) $rows[0]['returned_qty'] === 2.0);
    }

    public function test_update_invoice_recalculates_without_changing_identity_or_stock(): void
    {
        $invoice = $this->invoice();
        $number = $invoice->invoice_number;
        $sequence = DocumentSequence::where('series', 'INV')->value('last_number');
        $payload = $this->invoicePayload();
        $payload['items'][0] = array_merge($payload['items'][0], ['qty' => 3, 'unit_price' => 50, 'discount_pct' => 10, 'tax_rate' => 13]);
        $payload['delivery_charge'] = 5;
        $payload['round_off'] = -0.55;
        $payload['paid_amount'] = 20;
        $payload['invoice_date_ad'] = '2026-11-05';
        $this->post('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/update', $payload)->assertRedirect(route('admin.pharma.sale-invoice.show', $invoice->id));
        $invoice->refresh();
        $this->assertSame($number, $invoice->invoice_number);
        $this->assertSame(41, (int) $invoice->created_by);
        $this->assertSame('157.00', $invoice->total_amount);
        $this->assertSame('137.00', $invoice->credit_amount);
        $this->assertSame('2083-07-19', $invoice->invoice_date_bs);
        $this->assertCount(1, $invoice->lines);
        $this->assertSame(3, $invoice->lines->first()->base_qty);
        $this->assertSame($sequence, DocumentSequence::where('series', 'INV')->value('last_number'));
        $this->assertDatabaseCount('sale_invoices', 1);
        $this->assertDatabaseCount('stock_movements', 1);
        $this->assertDatabaseCount('customer_ledger_entries', 0);
    }

    public function test_invalid_update_keeps_original_lines_and_totals(): void
    {
        $invoice = $this->invoice();
        $originalLine = $invoice->lines->first()->id;
        $payload = $this->invoicePayload();
        $payload['items'][0]['qty'] = 1000;
        $this->post('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/update', $payload)->assertSessionHas('error');
        $this->assertSame('40.00', $invoice->fresh()->total_amount);
        $this->assertSame($originalLine, $invoice->fresh('lines')->lines->first()->id);
    }

    public function test_delete_draft_preserves_lines_and_has_no_stock_or_ledger_effect(): void
    {
        $invoice = $this->invoice();
        $this->post('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/delete')->assertRedirect(route('admin.pharma.sales-invoices'));
        $this->assertSoftDeleted('sale_invoices', ['id' => $invoice->id]);
        $this->assertDatabaseCount('sale_invoice_lines', 1);
        $this->assertDatabaseCount('stock_movements', 1);
        $this->assertDatabaseCount('customer_ledger_entries', 0);
    }

    public function test_posted_documents_cannot_be_edited_updated_or_deleted(): void
    {
        $invoice = $this->invoice();
        SalesService::postInvoice($invoice, 41);
        $return = $this->saleReturn();
        SalesService::postSaleReturn($return, 41);
        foreach ([['sale-invoice', $invoice, $this->invoicePayload()], ['sales-return', $return, $this->returnPayload()]] as [$path, $model, $payload]) {
            $this->get('https://localhost/admin/pharma/'.$path.'/'.$model->id.'/edit')->assertStatus(409);
            $this->post('https://localhost/admin/pharma/'.$path.'/'.$model->id.'/update', $payload)->assertSessionHas('error');
            $this->post('https://localhost/admin/pharma/'.$path.'/'.$model->id.'/delete')->assertSessionHas('error');
            $this->assertSame('posted', $model->fresh()->status);
        }
        $this->assertDatabaseCount('stock_movements', 3);
    }

    public function test_draft_return_can_be_updated_and_deleted_without_restocking(): void
    {
        $return = $this->saleReturn();
        $payload = $this->returnPayload();
        $payload['lines'][0]['returned_qty'] = 4;
        $payload['lines'][0]['condition'] = 'damaged';
        $payload['credit_note_issued'] = 0;
        $this->post('https://localhost/admin/pharma/sales-return/'.$return->id.'/update', $payload)->assertRedirect(route('admin.pharma.sales-return.show', $return->id));
        $this->assertSame('80.00', $return->fresh()->total_amount);
        $this->assertFalse($return->fresh()->credit_note_issued);
        $this->assertSame('damaged', $return->fresh('lines')->lines->first()->condition);
        $this->assertDatabaseCount('sale_returns', 1);
        $this->post('https://localhost/admin/pharma/sales-return/'.$return->id.'/delete')->assertRedirect(route('admin.pharma.sales-returns'));
        $this->assertSoftDeleted('sale_returns', ['id' => $return->id]);
        $this->assertDatabaseCount('stock_movements', 1);
        $this->assertDatabaseCount('customer_ledger_entries', 0);
    }

    public function test_products_in_stock_or_document_history_cannot_be_deleted(): void
    {
        $this->post('https://localhost/admin/pharma/product/'.$this->product->id.'/delete')->assertSessionHas('error');
        $this->assertNotNull($this->product->fresh());
        $unused = Product::create(['code' => 'UNUSED', 'name' => 'Unused', 'generic_name' => 'Unused', 'base_unit_id' => $this->unit->id, 'category_id' => $this->product->category_id]);
        $this->post('https://localhost/admin/pharma/product/'.$unused->id.'/delete')->assertRedirect(route('admin.pharma.products'));
        $this->assertSoftDeleted('products', ['id' => $unused->id]);
    }

    public function test_draft_delete_and_update_obey_permissions_and_original_warehouse(): void
    {
        $invoice = $this->invoice();
        $role = Role::create(['name' => 'Read only']);
        $this->admin->update(['role_id' => $role->id]);
        $this->admin->warehouses()->attach($this->warehouse->id);
        foreach (['update', 'delete'] as $action) {
            $this->postJson('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/'.$action, $this->invoicePayload())->assertForbidden();
        }
        $module = Module::create(['name' => 'SaleInvoice']);
        Permission::create(['role_id' => $role->id, 'module_id' => $module->id, 'name' => ['edit', 'delete']]);
        $this->admin->warehouses()->detach();
        foreach (['update', 'delete'] as $action) {
            $this->postJson('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/'.$action, $this->invoicePayload())->assertForbidden();
        }
        $this->assertNotNull($invoice->fresh());
        $this->assertSame('40.00', $invoice->fresh()->total_amount);
    }

    public function test_update_rejects_a_batch_from_another_product(): void
    {
        $invoice = $this->invoice();
        $other = Product::create(['code' => 'OTHER', 'name' => 'Other', 'generic_name' => 'Other', 'base_unit_id' => $this->unit->id]);
        $batch = Batch::create(['product_id' => $other->id, 'batch_number' => 'WRONG', 'expiry_date_ad' => '2027-07-01', 'cost_price' => 10]);
        $p = $this->invoicePayload();
        $p['items'][0]['batch_id'] = $batch->id;
        $p['items'][0]['location_id'] = $this->location->id;
        $this->post('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/update', $p)->assertSessionHas('error');
        $this->assertSame($this->batch->id, (int) $invoice->fresh('lines')->lines->first()->batch_id);
    }

    public function test_product_base_unit_cannot_change_after_stock_exists(): void
    {
        $newUnit = Unit::create(['name' => 'Box', 'abbreviation' => 'box']);
        $p = ['name' => 'Medicine', 'generic_name' => 'Medicine', 'code' => 'MED', 'category_id' => $this->product->category_id, 'base_unit_id' => $newUnit->id];
        $this->post('https://localhost/admin/pharma/product/'.$this->product->id.'/update', $p)->assertSessionHas('error');
        $this->assertSame($this->unit->id, (int) $this->product->fresh()->base_unit_id);
    }

    public function test_invoice_with_linked_return_is_preserved_even_if_draft(): void
    {
        $invoice = $this->invoice();
        $r = $this->saleReturn();
        $r->update(['sale_invoice_id' => $invoice->id]);
        $this->post('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/delete')->assertSessionHas('error');
        $this->post('https://localhost/admin/pharma/sale-invoice/'.$invoice->id.'/update', $this->invoicePayload())->assertSessionHas('error');
        $this->assertNotNull($invoice->fresh());
        $this->assertDatabaseCount('sale_invoice_lines', 1);
    }

    public function test_return_update_preserves_tax_and_uses_the_product_packaging_conversion(): void
    {
        $return = $this->saleReturn();
        $box = Unit::create(['name' => 'Box', 'abbreviation' => 'box']);
        $this->product->packagingLevels()->create(['unit_id' => $box->id, 'level_name' => 'Box', 'level_order' => 1, 'conversion_factor' => 10]);
        $p = $this->returnPayload();
        $p['lines'][0]['unit_id'] = $box->id;
        $p['lines'][0]['tax_amount'] = 5.2;
        $this->post('https://localhost/admin/pharma/sales-return/'.$return->id.'/update', $p)->assertRedirect(route('admin.pharma.sales-return.show', $return->id));
        $return->refresh();
        $this->assertSame('45.20', $return->total_amount);
        $this->assertSame(20, $return->lines->first()->returned_base_qty);
    }

    public function test_action_buttons_follow_permissions_and_document_state(): void
    {
        $draft = $this->invoice();
        $posted = $this->invoice();
        SalesService::postInvoice($posted, 41);
        $this->get('https://localhost/admin/pharma/sales-invoices')->assertSee(route('admin.pharma.sale-invoice.edit', $draft->id), false)->assertDontSee(route('admin.pharma.sale-invoice.edit', $posted->id), false);
        $role = Role::create(['name' => 'Viewer']);
        $this->admin->update(['role_id' => $role->id]);
        $this->admin->warehouses()->attach($this->warehouse->id);
        foreach (['SaleInvoice', 'Product'] as $name) {
            $module = Module::create(['name' => $name]);
            Permission::create(['role_id' => $role->id, 'module_id' => $module->id, 'name' => ['view']]);
        }
        $this->actingAs($this->admin->fresh(), 'admin');
        auth()->shouldUse('web');
        $this->get('https://localhost/admin/pharma/sales-invoices')->assertOk()->assertDontSee(route('admin.pharma.sale-invoice.edit', $draft->id), false)->assertDontSee(route('admin.pharma.sale-invoice.delete', $draft->id), false);
        $this->get('https://localhost/admin/pharma/products')->assertOk()->assertDontSee(route('admin.pharma.product.edit', $this->product->id), false)->assertDontSee(route('admin.pharma.product.delete', $this->product->id), false);
    }

    public function test_return_edit_includes_its_original_invoice_beyond_recent_fifty(): void
    {
        $original = $this->invoice();
        SalesService::postInvoice($original, 41);
        $return = $this->saleReturn();
        $return->update(['sale_invoice_id' => $original->id]);
        for ($i = 0; $i < 51; $i++) {
            SaleInvoice::create(['invoice_number' => 'NEWER-'.$i, 'fiscal_year_id' => 1, 'warehouse_id' => $this->warehouse->id, 'customer_id' => $this->customer->id, 'invoice_date_ad' => '2026-11-04', 'status' => 'posted']);
        }
        $this->get('https://localhost/admin/pharma/sales-return/'.$return->id.'/edit')->assertOk()->assertViewHas('invoices', fn ($rows) => $rows->contains('id', $original->id));
    }
}
