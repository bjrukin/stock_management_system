<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PharmaDatabaseSeeder extends Seeder
{
    /**
     * Run all zero-truncate Pharmaceutical Dealer seeders.
     */
    public function run(): void
    {
        $this->call([
            PharmaModuleSeeder::class,
            PharmaRolePermissionSeeder::class,
            PharmaMasterSeeder::class,
            PharmaProductSeeder::class,
            PharmaPurchasingSeeder::class,
            PharmaSalesSeeder::class,
            PharmaPaymentSeeder::class,
            PharmaWarehouseOperationSeeder::class,
        ]);
    }
}
