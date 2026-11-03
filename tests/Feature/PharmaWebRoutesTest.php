<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmaWebRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['id' => 1, 'name' => 'Super Admin']);

        $this->admin = Admin::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('admin@2025'),
            'role_id' => 1,
            'status' => 1,
        ]);
    }

    public function test_dashboard_renders_successfully()
    {
        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin');
        $response->assertStatus(200);
        $response->assertSee('Pharmaceutical Stock & Distribution Hub', false);
    }

    public function test_pharma_catalog_routes_render()
    {
        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/products');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/batches');
        $response->assertStatus(200);
    }

    public function test_pharma_purchasing_routes_render()
    {
        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/purchase-orders');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/goods-receipts');
        $response->assertStatus(200);
    }

    public function test_pharma_sales_routes_render()
    {
        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/sales-invoices');
        $response->assertStatus(200);
    }

    public function test_pharma_warehouse_routes_render()
    {
        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/stock-movements');
        $response->assertStatus(200);
    }

    public function test_pharma_reports_routes_render()
    {
        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/reports/stock-valuation');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/reports/near-expiry');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin, 'admin')->get('https://localhost/admin/pharma/reports/sales-register');
        $response->assertStatus(200);
    }
}
