<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\WarehouseOperationService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PharmaWarehouseOperationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();
        $adminId = $admin ? $admin->id : 1;

        // 1. Seed Pokhara Regional Warehouse
        $whPokhara = Warehouse::firstOrCreate(
            ['code' => 'WH-POKHARA-DEPOT'],
            [
                'name' => 'Pokhara Regional Distribution Depot',
                'phone' => '+977-61-532100',
                'email' => 'pokharadepot@pharmadistributors.com.np',
                'address' => 'Nagdhunga, Pokhara, Kaski',
                'is_primary' => false,
                'is_active' => true,
            ]
        );

        $pLocStorage = WarehouseLocation::firstOrCreate(
            ['warehouse_id' => $whPokhara->id, 'code' => 'LOC-PKR-STORE'],
            [
                'name' => 'Main Ambient Storage Rack',
                'type' => 'storage',
                'depth' => 1,
                'is_sellable' => true,
                'is_available' => true,
                'storage_condition' => 'room_temperature',
                'is_active' => true,
            ]
        );

        $pLocQuar = WarehouseLocation::firstOrCreate(
            ['warehouse_id' => $whPokhara->id, 'code' => 'LOC-PKR-QUAR'],
            [
                'name' => 'Quarantine Bay',
                'type' => 'quarantine',
                'depth' => 1,
                'is_sellable' => false,
                'is_available' => false,
                'storage_condition' => 'room_temperature',
                'is_active' => true,
            ]
        );

        // 2. Demo Inter-Warehouse Stock Transfer (KTM to Pokhara)
        $whKtm = Warehouse::where('code', 'WH-MAIN-KTM')->first();
        $product = Product::where('code', 'PRD-PCM-500')->first();
        $batch = $product ? Batch::where('product_id', $product->id)->where('is_active', true)->first() : null;
        $ktmStorage = $whKtm ? WarehouseLocation::where('warehouse_id', $whKtm->id)->where('type', 'storage')->first() : null;

        if ($whKtm && $product && $batch && $ktmStorage) {
            $existingTransfer = StockTransfer::where('notes', 'Pharma Seeded Demo Transfer to Pokhara')->first();
            if (! $existingTransfer) {
                // Check if sufficient stock exists in KTM storage
                $bal = \App\Models\InventoryBalance::where('warehouse_id', $whKtm->id)
                    ->where('location_id', $ktmStorage->id)
                    ->where('batch_id', $batch->id)
                    ->first();

                if ($bal && $bal->quantity_available >= 50) {
                    $transfer = WarehouseOperationService::createTransfer(
                        [
                            'from_warehouse_id' => $whKtm->id,
                            'to_warehouse_id' => $whPokhara->id,
                            'transfer_date_ad' => Carbon::today()->format('Y-m-d'),
                            'notes' => 'Pharma Seeded Demo Transfer to Pokhara',
                            'created_by' => $adminId,
                        ],
                        [
                            [
                                'product_id' => $product->id,
                                'batch_id' => $batch->id,
                                'from_location_id' => $ktmStorage->id,
                                'to_location_id' => $pLocStorage->id,
                                'quantity' => 50,
                            ],
                        ]
                    );

                    // Dispatch and receive transfer
                    $dispatched = WarehouseOperationService::dispatchTransfer($transfer, $adminId);
                    WarehouseOperationService::receiveTransfer(
                        $dispatched,
                        [$dispatched->lines->first()->id => $pLocStorage->id],
                        $adminId
                    );
                }
            }
        }
    }
}
