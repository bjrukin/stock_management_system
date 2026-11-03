<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PharmaModuleSeeder extends Seeder
{
    /**
     * Run the database seeds strictly additively using firstOrCreate.
     */
    public function run(): void
    {
        $modules = [
            'Warehouse',
            'WarehouseLocation',
            'Category',
            'Manufacturer',
            'Unit',
            'TaxCategory',
            'CashBankAccount',
            'FiscalYear',
            'Setting',
            'AuditLog',
            'Product',
            'Batch',
            'Supplier',
            'Customer',
            'PurchaseOrder',
            'GoodsReceipt',
            'PurchaseReturn',
            'SaleInvoice',
            'SaleReturn',
            'CustomerPayment',
            'SupplierPayment',
            'StockTransfer',
            'StockAdjustment',
            'StockRelocation',
            'Recall',
            'Report',
        ];

        $actions = ['view', 'create', 'edit', 'delete', 'approve', 'post', 'cancel'];

        // Get Super Admin and Admin roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        foreach ($modules as $moduleName) {
            $module = Module::firstOrCreate(['name' => $moduleName]);

            // Assign full permissions to Super Admin role
            Permission::updateOrCreate(
                [
                    'role_id' => $superAdminRole->id,
                    'module_id' => $module->id,
                ],
                [
                    'name' => json_encode($actions),
                ]
            );

            // Assign operational permissions to Admin role
            Permission::firstOrCreate(
                [
                    'role_id' => $adminRole->id,
                    'module_id' => $module->id,
                ],
                [
                    'name' => json_encode(['view', 'create', 'edit']),
                ]
            );
        }
    }
}
