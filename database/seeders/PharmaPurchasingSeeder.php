<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\PurchasingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PharmaPurchasingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Suppliers
        $sup1 = Supplier::firstOrCreate(
            ['code' => 'SUP-DJPL-DIST'],
            [
                'name' => 'Deurali-Janta Distribution Division',
                'contact_person' => 'Ramesh Shrestha',
                'phone' => '+977-1-4351122',
                'email' => 'distribution@deuralijanta.com',
                'pan_vat_number' => '300123456',
                'dda_license_number' => 'DDA-W-012/075',
                'address' => 'Dhapasi, Kathmandu',
                'credit_period_days' => 45,
                'credit_limit' => 2000000.00,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        $sup2 = Supplier::firstOrCreate(
            ['code' => 'SUP-NPL-SALES'],
            [
                'name' => 'NPL Pharma Logistics & Sales',
                'contact_person' => 'Suman Adhikari',
                'phone' => '+977-1-4275566',
                'email' => 'orders@npl.com.np',
                'pan_vat_number' => '300987654',
                'dda_license_number' => 'DDA-W-034/076',
                'address' => 'Kalanki, Kathmandu',
                'credit_period_days' => 30,
                'credit_limit' => 1500000.00,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        $sup3 = Supplier::firstOrCreate(
            ['code' => 'SUP-MEDISALES'],
            [
                'name' => 'Medisales International Nepal Pvt. Ltd.',
                'contact_person' => 'Bikram Thapa',
                'phone' => '+977-1-4433221',
                'email' => 'sales@medisales.com.np',
                'pan_vat_number' => '601876543',
                'dda_license_number' => 'DDA-W-056/077',
                'address' => 'Tripureshwor, Kathmandu',
                'credit_period_days' => 60,
                'credit_limit' => 3000000.00,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        // 2. Sample Purchase Order & GRN for demonstration
        $wh = Warehouse::where('code', 'WH-CENTRAL')->first();
        $locStorage = WarehouseLocation::where('warehouse_id', $wh?->id)->where('code', 'LOC-STORAGE')->first();
        $productNiko = Product::where('code', 'PRD-NIKO-500')->first();
        $unitTab = Unit::where('abbreviation', 'Tab')->first();

        $admin = \App\Models\Admin::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'System Administrator',
                'email' => 'info@nhpc.gov.np',
                'password' => \Illuminate\Support\Facades\Hash::make('admin@2025'),
                'password_reference' => 'admin@2025',
                'role_id' => 1,
                'status' => 1,
            ]
        );

        if ($wh && $locStorage && $productNiko && $unitTab && $sup1->goodsReceipts()->count() === 0) {
            // Create and approve PO
            $po = PurchasingService::createPurchaseOrder(
                [
                    'supplier_id' => $sup1->id,
                    'warehouse_id' => $wh->id,
                    'order_date_ad' => Carbon::today()->format('Y-m-d'),
                    'notes' => 'Monthly bulk replenishment order',
                    'created_by' => $admin->id,
                ],
                [
                    [
                        'product_id' => $productNiko->id,
                        'unit_id' => $unitTab->id,
                        'ordered_qty' => 5000,
                        'conversion_factor' => 1.0,
                        'unit_price' => 1.2000,
                        'discount_pct' => 5.0000,
                    ],
                ]
            );
            PurchasingService::approvePurchaseOrder($po, $admin->id);

            $poLine = $po->lines()->first();

            // Receive goods (GRN) with freight landed cost
            $grn = PurchasingService::createGoodsReceipt(
                [
                    'purchase_order_id' => $po->id,
                    'supplier_id' => $sup1->id,
                    'warehouse_id' => $wh->id,
                    'supplier_invoice_number' => 'DJ-INV-2026-089',
                    'supplier_invoice_date_ad' => Carbon::today()->format('Y-m-d'),
                    'receipt_date_ad' => Carbon::today()->format('Y-m-d'),
                    'freight_amount' => 300.00,
                    'handling_charges' => 50.00,
                    'notes' => 'Received in good condition via courier delivery',
                ],
                [
                    [
                        'purchase_order_line_id' => $poLine->id,
                        'product_id' => $productNiko->id,
                        'batch_number' => 'NK-2026-GRN1',
                        'mfg_date_ad' => Carbon::today()->subMonths(1)->format('Y-m-d'),
                        'expiry_date_ad' => Carbon::today()->addMonths(24)->format('Y-m-d'),
                        'location_id' => $locStorage->id,
                        'unit_id' => $unitTab->id,
                        'received_qty' => 5000,
                        'conversion_factor' => 1.0,
                        'unit_cost' => 1.2000,
                        'discount_pct' => 5.0000,
                    ],
                ]
            );

            // Post GRN
            PurchasingService::postGoodsReceipt($grn, $admin->id);
        }
    }
}
