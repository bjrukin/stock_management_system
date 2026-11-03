<?php

namespace Database\Seeders;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $roles = [
            'Super Admin',
            'Business Owner',
            'System Administrator',
            'Inventory Manager',
            'Purchase Officer',
            'Sales Officer',
            'Warehouse Staff',
            'Accountant',
            'Cashier',
            'Auditor / Read-Only User',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}