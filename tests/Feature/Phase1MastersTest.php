<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\CashBankAccount;
use App\Models\Category;
use App\Models\FiscalYear;
use App\Models\Manufacturer;
use App\Models\Role;
use App\Models\TaxCategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase1MastersTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouse_and_locations_with_scopes(): void
    {
        $wh = Warehouse::create([
            'code' => 'WH-TEST',
            'name' => 'Testing Warehouse',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $locStorage = WarehouseLocation::create([
            'warehouse_id' => $wh->id,
            'code' => 'TEST-STORAGE',
            'name' => 'Storage Bay',
            'type' => 'storage',
            'is_sellable' => true,
            'is_available' => true,
        ]);

        $locQuarantine = WarehouseLocation::create([
            'warehouse_id' => $wh->id,
            'code' => 'TEST-QUARANTINE',
            'name' => 'Quarantine Bay',
            'type' => 'quarantine',
            'is_sellable' => false,
            'is_available' => false,
        ]);

        $this->assertCount(2, $wh->locations);
        $this->assertEquals(1, $wh->locations()->sellable()->count());
        $this->assertEquals($locStorage->id, $wh->locations()->sellable()->first()->id);
    }

    public function test_admin_warehouse_pivot_and_access_permissions(): void
    {
        $roleSuper = Role::create(['name' => 'Super Admin']);
        $roleAdmin = Role::create(['name' => 'Staff Role']);

        $staff = Admin::create([
            'role_id' => $roleAdmin->id,
            'name' => 'Staff Member',
            'username' => 'staff1',
            'status' => 1,
        ]);

        $superAdmin = Admin::create([
            'role_id' => 1, // Super admin
            'name' => 'Super User',
            'username' => 'superuser',
            'status' => 1,
        ]);

        $wh1 = Warehouse::create(['code' => 'WH-1', 'name' => 'Warehouse 1', 'is_active' => true]);
        $wh2 = Warehouse::create(['code' => 'WH-2', 'name' => 'Warehouse 2', 'is_active' => true]);

        // Attach WH1 to staff
        $staff->warehouses()->attach($wh1->id, ['is_default' => true]);

        // Super Admin has access to all warehouses automatically
        $this->assertTrue($superAdmin->hasWarehouseAccess($wh1->id));
        $this->assertTrue($superAdmin->hasWarehouseAccess($wh2->id));

        // Staff only has access to assigned warehouse WH1
        $this->assertTrue($staff->hasWarehouseAccess($wh1->id));
        $this->assertFalse($staff->hasWarehouseAccess($wh2->id));
        $this->assertEquals($wh1->id, $staff->defaultWarehouse()->id);
    }

    public function test_hierarchical_category_parent_child_relationship(): void
    {
        $parent = Category::create([
            'code' => 'CAT-ANTI',
            'name' => 'Antibiotics',
            'is_active' => true,
        ]);

        $child = Category::create([
            'parent_id' => $parent->id,
            'code' => 'CAT-PENI',
            'name' => 'Penicillins',
            'is_active' => true,
        ]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertCount(1, $parent->children);
        $this->assertEquals('Penicillins', $parent->children->first()->name);
    }

    public function test_creates_manufacturer_unit_tax_and_bank_account(): void
    {
        $mfg = Manufacturer::create([
            'code' => 'MFG-DEU',
            'name' => 'Deurali-Janta Pharmaceuticals',
            'country' => 'Nepal',
            'dda_registration_no' => 'DDA-1234',
            'is_active' => true,
        ]);

        $unit = Unit::create([
            'name' => 'Strip',
            'abbreviation' => 'Str',
            'is_active' => true,
        ]);

        $tax = TaxCategory::create([
            'code' => 'TAX-EXEMPT',
            'name' => 'Exempt',
            'rate' => 0.0000,
            'is_active' => true,
        ]);

        $bank = CashBankAccount::create([
            'code' => 'ACC-TEST-BANK',
            'name' => 'Test Bank Account',
            'type' => 'bank',
            'opening_balance' => 50000.00,
            'current_balance' => 50000.00,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('manufacturers', ['code' => 'MFG-DEU']);
        $this->assertDatabaseHas('units', ['abbreviation' => 'Str']);
        $this->assertDatabaseHas('tax_categories', ['code' => 'TAX-EXEMPT', 'rate' => '0.0000']);
        $this->assertDatabaseHas('cash_bank_accounts', ['code' => 'ACC-TEST-BANK', 'opening_balance' => '50000.00']);
    }
}
