<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductPackagingLevel;
use App\Models\TaxCategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PharmaProductSeeder extends Seeder
{
    public function run(): void
    {
        $taxExempt = TaxCategory::where('code', 'VAT-EXEMPT')->first();
        $unitTab = Unit::where('abbreviation', 'Tab')->first() ?? Unit::firstOrCreate(['name' => 'Tablet', 'abbreviation' => 'Tab']);
        $unitCap = Unit::where('abbreviation', 'Cap')->first() ?? Unit::firstOrCreate(['name' => 'Capsule', 'abbreviation' => 'Cap']);
        $unitStr = Unit::where('abbreviation', 'Str')->first() ?? Unit::firstOrCreate(['name' => 'Strip', 'abbreviation' => 'Str']);
        $unitBox = Unit::where('abbreviation', 'Box')->first() ?? Unit::firstOrCreate(['name' => 'Box', 'abbreviation' => 'Box']);
        $unitVial = Unit::where('abbreviation', 'Vial')->first() ?? Unit::firstOrCreate(['name' => 'Vial', 'abbreviation' => 'Vial']);

        // 1. Manufacturers
        $djpl = Manufacturer::firstOrCreate(
            ['code' => 'MFG-DJPL'],
            [
                'name' => 'Deurali-Janta Pharmaceuticals Pvt. Ltd.',
                'country' => 'Nepal',
                'contact_person' => 'Supply Chain Manager',
                'phone' => '+977-1-4356789',
                'email' => 'info@deuralijanta.com',
                'dda_registration_no' => 'DDA-MFG-001/2047',
                'is_active' => true,
            ]
        );

        $npl = Manufacturer::firstOrCreate(
            ['code' => 'MFG-NPL'],
            [
                'name' => 'Nepal Pharmaceuticals Laboratory (NPL)',
                'country' => 'Nepal',
                'contact_person' => 'Distribution Officer',
                'phone' => '+977-1-4271234',
                'email' => 'sales@npl.com.np',
                'dda_registration_no' => 'DDA-MFG-002/2048',
                'is_active' => true,
            ]
        );

        // 2. Categories
        $catAnalgesic = Category::firstOrCreate(['code' => 'CAT-ANALGESIC'], ['name' => 'Analgesics & Antipyretics', 'is_active' => true]);
        $catAntibiotic = Category::firstOrCreate(['code' => 'CAT-ANTIBIOTIC'], ['name' => 'Antibiotics & Antimicrobials', 'is_active' => true]);
        $catGastro = Category::firstOrCreate(['code' => 'CAT-GASTRO'], ['name' => 'Gastrointestinal Agents', 'is_active' => true]);
        $catEndo = Category::firstOrCreate(['code' => 'CAT-ENDOCRINE'], ['name' => 'Endocrine & Diabetes Care', 'is_active' => true]);

        // 3. Products
        // Product 1: Niko 500mg
        $niko = Product::firstOrCreate(
            ['code' => 'PRD-NIKO-500'],
            [
                'name' => 'Niko 500mg Tablet',
                'generic_name' => 'Paracetamol',
                'brand' => 'Niko',
                'category_id' => $catAnalgesic->id,
                'manufacturer_id' => $djpl->id,
                'dosage_form' => 'Tablet',
                'strength' => '500mg',
                'base_unit_id' => $unitTab->id,
                'tax_category_id' => $taxExempt?->id,
                'mrp' => 2.0000,
                'purchase_price' => 1.2000,
                'selling_price' => 1.6000,
                'wholesale_price' => 1.5000,
                'min_stock' => 500,
                'reorder_level' => 1000,
                'max_level' => 10000,
                'storage_requirement' => 'room_temperature',
                'is_active' => true,
            ]
        );

        // Multi-tier packaging for Niko: 1 Strip = 10 Tabs, 1 Box = 100 Tabs
        ProductPackagingLevel::firstOrCreate(
            ['product_id' => $niko->id, 'level_order' => 1],
            ['unit_id' => $unitTab->id, 'level_name' => 'Tablet (Base)', 'conversion_factor' => 1.0000]
        );
        ProductPackagingLevel::firstOrCreate(
            ['product_id' => $niko->id, 'level_order' => 2],
            ['unit_id' => $unitStr->id, 'level_name' => 'Strip of 10', 'conversion_factor' => 10.0000]
        );
        ProductPackagingLevel::firstOrCreate(
            ['product_id' => $niko->id, 'level_order' => 3],
            ['unit_id' => $unitBox->id, 'level_name' => 'Box of 100', 'conversion_factor' => 100.0000]
        );

        ProductBarcode::firstOrCreate(['product_id' => $niko->id, 'barcode' => '8901234560011'], ['type' => 'EAN13']);

        // Product 2: Cipro-500
        $cipro = Product::firstOrCreate(
            ['code' => 'PRD-CIPRO-500'],
            [
                'name' => 'Cipro-500 Tablet',
                'generic_name' => 'Ciprofloxacin Hydrochloride',
                'brand' => 'Cipro',
                'category_id' => $catAntibiotic->id,
                'manufacturer_id' => $npl->id,
                'dosage_form' => 'Tablet',
                'strength' => '500mg',
                'base_unit_id' => $unitTab->id,
                'tax_category_id' => $taxExempt?->id,
                'mrp' => 15.0000,
                'purchase_price' => 9.5000,
                'selling_price' => 12.0000,
                'wholesale_price' => 11.2000,
                'min_stock' => 200,
                'reorder_level' => 500,
                'is_active' => true,
            ]
        );

        // Product 3: Human Insulin 40IU (Cold Chain)
        $insulin = Product::firstOrCreate(
            ['code' => 'PRD-INSULIN-40'],
            [
                'name' => 'Human Insulin 40IU/ml 10ml Vial',
                'generic_name' => 'Human Recombinant Insulin',
                'brand' => 'Insugen-R',
                'category_id' => $catEndo->id,
                'manufacturer_id' => $npl->id,
                'dosage_form' => 'Injection (Vial)',
                'strength' => '40 IU/ml, 10ml',
                'base_unit_id' => $unitVial->id,
                'tax_category_id' => $taxExempt?->id,
                'mrp' => 450.0000,
                'purchase_price' => 320.0000,
                'selling_price' => 395.0000,
                'wholesale_price' => 380.0000,
                'min_stock' => 50,
                'reorder_level' => 100,
                'storage_requirement' => 'cold_chain_2_8',
                'is_active' => true,
            ]
        );

        // 4. Batches for Niko 500mg with varied expiries (Demonstrating FEFO)
        $batchNikoNear = Batch::firstOrCreate(
            ['product_id' => $niko->id, 'batch_number' => 'NK-2024-01'],
            [
                'manufacturer_id' => $djpl->id,
                'mfg_date_ad' => '2024-01-01',
                'expiry_date_ad' => Carbon::today()->addMonths(3)->format('Y-m-d'), // 3 months away (FEFO #1)
                'mrp' => 2.0000,
                'cost_price' => 1.2000,
                'is_quarantined' => false,
                'is_recalled' => false,
            ]
        );

        $batchNikoFar = Batch::firstOrCreate(
            ['product_id' => $niko->id, 'batch_number' => 'NK-2024-02'],
            [
                'manufacturer_id' => $djpl->id,
                'mfg_date_ad' => '2024-06-01',
                'expiry_date_ad' => Carbon::today()->addMonths(18)->format('Y-m-d'), // 18 months away (FEFO #2)
                'mrp' => 2.0000,
                'cost_price' => 1.2500,
                'is_quarantined' => false,
                'is_recalled' => false,
            ]
        );

        $batchNikoExpired = Batch::firstOrCreate(
            ['product_id' => $niko->id, 'batch_number' => 'NK-2023-EX'],
            [
                'manufacturer_id' => $djpl->id,
                'mfg_date_ad' => '2023-01-01',
                'expiry_date_ad' => Carbon::today()->subMonths(1)->format('Y-m-d'), // Expired
                'mrp' => 2.0000,
                'cost_price' => 1.1000,
                'is_quarantined' => false,
                'is_recalled' => false,
            ]
        );

        $batchNikoQuarantine = Batch::firstOrCreate(
            ['product_id' => $niko->id, 'batch_number' => 'NK-2024-QR'],
            [
                'manufacturer_id' => $djpl->id,
                'mfg_date_ad' => '2024-05-01',
                'expiry_date_ad' => Carbon::today()->addMonths(12)->format('Y-m-d'),
                'mrp' => 2.0000,
                'cost_price' => 1.2000,
                'is_quarantined' => true, // Quarantined
                'is_recalled' => false,
                'notes' => 'Quality seal check pending',
            ]
        );

        // Batches for Insulin
        $batchInsulin = Batch::firstOrCreate(
            ['product_id' => $insulin->id, 'batch_number' => 'INS-2024-09'],
            [
                'manufacturer_id' => $npl->id,
                'mfg_date_ad' => '2024-09-01',
                'expiry_date_ad' => Carbon::today()->addMonths(12)->format('Y-m-d'),
                'mrp' => 450.0000,
                'cost_price' => 320.0000,
                'is_quarantined' => false,
                'is_recalled' => false,
            ]
        );

        // 5. Seed Opening Stock via InventoryService (ensuring append-only ledger math)
        $wh = Warehouse::where('code', 'WH-CENTRAL')->first();
        if ($wh) {
            $locStorage = WarehouseLocation::where('warehouse_id', $wh->id)->where('code', 'LOC-STORAGE')->first();
            $locCold = WarehouseLocation::where('warehouse_id', $wh->id)->where('code', 'LOC-COLD-CHAIN')->first();

            // Record opening stock for Batch 1 (Near expiry): 2,000 tablets
            if ($locStorage && $batchNikoNear->inventoryBalances()->count() === 0) {
                InventoryService::recordStockMovement([
                    'warehouse_id' => $wh->id,
                    'location_id' => $locStorage->id,
                    'product_id' => $niko->id,
                    'batch_id' => $batchNikoNear->id,
                    'movement_type' => 'OPENING_STOCK',
                    'quantity' => 2000,
                    'unit_cost' => 1.2000,
                    'notes' => 'Initial inventory opening balance',
                ]);
            }

            // Record opening stock for Batch 2 (Far expiry): 5,000 tablets
            if ($locStorage && $batchNikoFar->inventoryBalances()->count() === 0) {
                InventoryService::recordStockMovement([
                    'warehouse_id' => $wh->id,
                    'location_id' => $locStorage->id,
                    'product_id' => $niko->id,
                    'batch_id' => $batchNikoFar->id,
                    'movement_type' => 'OPENING_STOCK',
                    'quantity' => 5000,
                    'unit_cost' => 1.2500,
                    'notes' => 'Initial inventory opening balance',
                ]);
            }

            // Record opening stock for Cold Chain Insulin: 150 vials in cold storage location
            if ($locCold && $batchInsulin->inventoryBalances()->count() === 0) {
                InventoryService::recordStockMovement([
                    'warehouse_id' => $wh->id,
                    'location_id' => $locCold->id,
                    'product_id' => $insulin->id,
                    'batch_id' => $batchInsulin->id,
                    'movement_type' => 'OPENING_STOCK',
                    'quantity' => 150,
                    'unit_cost' => 320.0000,
                    'notes' => 'Cold chain opening stock',
                ]);
            }
        }
    }
}
