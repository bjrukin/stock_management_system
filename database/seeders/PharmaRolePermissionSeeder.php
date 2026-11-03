<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PharmaRolePermissionSeeder extends Seeder
{
    /**
     * Seed the 10 requested roles, module permissions, and corresponding user accounts.
     */
    public function run(): void
    {
        $allModules = [
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

        // Ensure all modules exist
        $moduleModels = [];
        foreach ($allModules as $modName) {
            $moduleModels[$modName] = Module::firstOrCreate(['name' => $modName]);
        }

        $allActions = ['view', 'create', 'edit', 'delete', 'approve', 'post', 'cancel'];

        // Define the 10 Roles and their permission matrix
        $roleDefinitions = [
            'Super Admin' => [
                'user' => [
                    'name' => 'Super Administrator',
                    'username' => 'admin',
                    'email' => 'info@nhpc.gov.np',
                    'password' => 'admin@2025',
                ],
                'matrix' => array_fill_keys($allModules, $allActions),
            ],
            'Business Owner' => [
                'user' => [
                    'name' => 'Business Owner',
                    'username' => 'business_owner',
                    'email' => 'owner@pharmadistributors.com.np',
                    'password' => 'owner@2025',
                ],
                'matrix' => [
                    'Warehouse' => ['view'],
                    'WarehouseLocation' => ['view'],
                    'Category' => ['view'],
                    'Manufacturer' => ['view'],
                    'Unit' => ['view'],
                    'TaxCategory' => ['view'],
                    'CashBankAccount' => ['view'],
                    'FiscalYear' => ['view'],
                    'Setting' => ['view'],
                    'AuditLog' => ['view'],
                    'Product' => ['view'],
                    'Batch' => ['view'],
                    'Supplier' => ['view'],
                    'Customer' => ['view'],
                    'PurchaseOrder' => ['view', 'approve', 'cancel'],
                    'GoodsReceipt' => ['view'],
                    'PurchaseReturn' => ['view', 'approve'],
                    'SaleInvoice' => ['view', 'approve'],
                    'SaleReturn' => ['view', 'approve'],
                    'CustomerPayment' => ['view', 'approve'],
                    'SupplierPayment' => ['view', 'approve'],
                    'StockTransfer' => ['view', 'approve'],
                    'StockAdjustment' => ['view', 'approve', 'post'],
                    'StockRelocation' => ['view'],
                    'Recall' => ['view', 'approve'],
                    'Report' => ['view'],
                ],
            ],
            'System Administrator' => [
                'user' => [
                    'name' => 'System Administrator',
                    'username' => 'sysadmin',
                    'email' => 'sysadmin@pharmadistributors.com.np',
                    'password' => 'sysadmin@2025',
                ],
                'matrix' => [
                    'Warehouse' => ['view', 'create', 'edit', 'delete'],
                    'WarehouseLocation' => ['view', 'create', 'edit', 'delete'],
                    'Category' => ['view', 'create', 'edit', 'delete'],
                    'Manufacturer' => ['view', 'create', 'edit', 'delete'],
                    'Unit' => ['view', 'create', 'edit', 'delete'],
                    'TaxCategory' => ['view', 'create', 'edit', 'delete'],
                    'CashBankAccount' => ['view', 'create', 'edit', 'delete'],
                    'FiscalYear' => ['view', 'create', 'edit', 'delete'],
                    'Setting' => ['view', 'create', 'edit'],
                    'AuditLog' => ['view', 'delete'],
                    'Product' => ['view', 'create', 'edit'],
                    'Batch' => ['view'],
                    'Supplier' => ['view'],
                    'Customer' => ['view'],
                    'PurchaseOrder' => ['view'],
                    'GoodsReceipt' => ['view'],
                    'PurchaseReturn' => ['view'],
                    'SaleInvoice' => ['view'],
                    'SaleReturn' => ['view'],
                    'CustomerPayment' => ['view'],
                    'SupplierPayment' => ['view'],
                    'StockTransfer' => ['view'],
                    'StockAdjustment' => ['view'],
                    'StockRelocation' => ['view'],
                    'Recall' => ['view'],
                    'Report' => ['view'],
                ],
            ],
            'Inventory Manager' => [
                'user' => [
                    'name' => 'Inventory Manager',
                    'username' => 'inventory_mgr',
                    'email' => 'inventory@pharmadistributors.com.np',
                    'password' => 'inventory@2025',
                ],
                'matrix' => [
                    'Product' => ['view', 'create', 'edit'],
                    'Batch' => ['view', 'create', 'edit'],
                    'Category' => ['view', 'create', 'edit'],
                    'Manufacturer' => ['view', 'create', 'edit'],
                    'Unit' => ['view', 'create', 'edit'],
                    'Warehouse' => ['view'],
                    'WarehouseLocation' => ['view', 'create', 'edit'],
                    'GoodsReceipt' => ['view'],
                    'PurchaseReturn' => ['view'],
                    'SaleReturn' => ['view'],
                    'StockTransfer' => ['view', 'create', 'edit', 'approve', 'post', 'cancel'],
                    'StockAdjustment' => ['view', 'create', 'edit', 'approve', 'post', 'cancel'],
                    'StockRelocation' => ['view', 'create', 'edit', 'post'],
                    'Recall' => ['view', 'create', 'edit', 'approve', 'cancel'],
                    'Report' => ['view'],
                ],
            ],
            'Purchase Officer' => [
                'user' => [
                    'name' => 'Purchase Officer',
                    'username' => 'purchase_officer',
                    'email' => 'purchase@pharmadistributors.com.np',
                    'password' => 'purchase@2025',
                ],
                'matrix' => [
                    'Supplier' => ['view', 'create', 'edit'],
                    'PurchaseOrder' => ['view', 'create', 'edit', 'approve', 'cancel'],
                    'GoodsReceipt' => ['view', 'create', 'edit', 'approve'],
                    'PurchaseReturn' => ['view', 'create', 'edit', 'approve', 'cancel'],
                    'Product' => ['view'],
                    'Batch' => ['view'],
                    'Unit' => ['view'],
                    'Warehouse' => ['view'],
                    'SupplierPayment' => ['view'],
                    'Report' => ['view'],
                ],
            ],
            'Sales Officer' => [
                'user' => [
                    'name' => 'Sales Officer',
                    'username' => 'sales_officer',
                    'email' => 'sales@pharmadistributors.com.np',
                    'password' => 'sales@2025',
                ],
                'matrix' => [
                    'Customer' => ['view', 'create', 'edit'],
                    'SaleInvoice' => ['view', 'create', 'edit', 'approve', 'cancel'],
                    'SaleReturn' => ['view', 'create', 'edit', 'approve', 'cancel'],
                    'Product' => ['view'],
                    'Batch' => ['view'],
                    'Unit' => ['view'],
                    'Warehouse' => ['view'],
                    'CustomerPayment' => ['view'],
                    'Report' => ['view'],
                ],
            ],
            'Warehouse Staff' => [
                'user' => [
                    'name' => 'Warehouse Staff',
                    'username' => 'warehouse_staff',
                    'email' => 'warehouse@pharmadistributors.com.np',
                    'password' => 'warehouse@2025',
                ],
                'matrix' => [
                    'Warehouse' => ['view'],
                    'WarehouseLocation' => ['view'],
                    'Product' => ['view'],
                    'Batch' => ['view'],
                    'GoodsReceipt' => ['view', 'create'],
                    'StockTransfer' => ['view', 'create', 'edit'],
                    'StockRelocation' => ['view', 'create', 'edit', 'post'],
                    'StockAdjustment' => ['view', 'create'],
                    'Recall' => ['view'],
                ],
            ],
            'Accountant' => [
                'user' => [
                    'name' => 'Accountant',
                    'username' => 'accountant',
                    'email' => 'accountant@pharmadistributors.com.np',
                    'password' => 'accountant@2025',
                ],
                'matrix' => [
                    'CashBankAccount' => ['view', 'create', 'edit'],
                    'FiscalYear' => ['view', 'create', 'edit'],
                    'TaxCategory' => ['view', 'create', 'edit'],
                    'CustomerPayment' => ['view', 'create', 'edit', 'approve', 'post', 'cancel'],
                    'SupplierPayment' => ['view', 'create', 'edit', 'approve', 'post', 'cancel'],
                    'SaleInvoice' => ['view', 'post'],
                    'PurchaseOrder' => ['view'],
                    'GoodsReceipt' => ['view'],
                    'PurchaseReturn' => ['view', 'post'],
                    'SaleReturn' => ['view', 'post'],
                    'Report' => ['view'],
                ],
            ],
            'Cashier' => [
                'user' => [
                    'name' => 'Cashier',
                    'username' => 'cashier',
                    'email' => 'cashier@pharmadistributors.com.np',
                    'password' => 'cashier@2025',
                ],
                'matrix' => [
                    'CustomerPayment' => ['view', 'create'],
                    'SaleInvoice' => ['view'],
                    'CashBankAccount' => ['view'],
                    'Customer' => ['view'],
                ],
            ],
            'Auditor / Read-Only User' => [
                'user' => [
                    'name' => 'External Auditor',
                    'username' => 'auditor',
                    'email' => 'auditor@pharmadistributors.com.np',
                    'password' => 'auditor@2025',
                ],
                'matrix' => array_fill_keys($allModules, ['view']),
            ],
        ];

        $defaultWarehouse = Warehouse::first();

        foreach ($roleDefinitions as $roleName => $def) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Seed / update permissions
            foreach ($def['matrix'] as $modName => $actions) {
                if (isset($moduleModels[$modName])) {
                    Permission::updateOrCreate(
                        [
                            'role_id' => $role->id,
                            'module_id' => $moduleModels[$modName]->id,
                        ],
                        [
                            'name' => json_encode($actions),
                        ]
                    );
                }
            }

            // Seed user account
            $userData = $def['user'];
            $admin = Admin::updateOrCreate(
                ['username' => $userData['username']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'password_reference' => $userData['password'],
                    'role_id' => $role->id,
                    'status' => 1,
                ]
            );

            // Assign warehouse access
            if ($defaultWarehouse && !$admin->warehouses()->where('warehouses.id', $defaultWarehouse->id)->exists()) {
                $admin->warehouses()->attach($defaultWarehouse->id, ['is_default' => true]);
            }
        }
    }
}
