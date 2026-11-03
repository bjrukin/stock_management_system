<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\SalesService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PharmaSalesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Pharmacy Customers
        $c1 = Customer::firstOrCreate(
            ['code' => 'CUST-ALKA-PHARM'],
            [
                'name' => 'Alka Hospital Pharmacy & Diagnostic Center',
                'contact_person' => 'Sunil Manandhar',
                'phone' => '+977-1-5551234',
                'email' => 'pharmacy@alkahospital.com',
                'pan_vat_number' => '301223344',
                'dda_license_number' => 'DDA-RET-0912/078',
                'address' => 'Jawalakhel, Lalitpur',
                'credit_period_days' => 30,
                'credit_limit' => 500000.00,
                'credit_stop' => false,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        $c2 = Customer::firstOrCreate(
            ['code' => 'CUST-TUTH-PHARM'],
            [
                'name' => 'Tribhuvan University Teaching Hospital Pharmacy',
                'contact_person' => 'Dr. Pravin Sharma',
                'phone' => '+977-1-4412345',
                'email' => 'tuthpharm@iom.edu.np',
                'pan_vat_number' => '300112233',
                'dda_license_number' => 'DDA-RET-0415/074',
                'address' => 'Maharajgunj, Kathmandu',
                'credit_period_days' => 45,
                'credit_limit' => 1000000.00,
                'credit_stop' => false,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        $c3 = Customer::firstOrCreate(
            ['code' => 'CUST-NORVIC-PHARM'],
            [
                'name' => 'Norvic International Hospital Pharmacy',
                'contact_person' => 'Rita Maharjan',
                'phone' => '+977-1-4258555',
                'email' => 'norvicpharm@norvichospital.com',
                'pan_vat_number' => '302334455',
                'dda_license_number' => 'DDA-RET-0881/077',
                'address' => 'Thapathali, Kathmandu',
                'credit_period_days' => 30,
                'credit_limit' => 800000.00,
                'credit_stop' => false,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        $c4 = Customer::firstOrCreate(
            ['code' => 'CUST-MEDPLUS-CLINIC'],
            [
                'name' => 'MedPlus Polyclinic & Retail Chemist',
                'contact_person' => 'Kiran Bajracharya',
                'phone' => '+977-1-5534433',
                'email' => 'medplusnepal@gmail.com',
                'pan_vat_number' => '601998877',
                'dda_license_number' => 'DDA-RET-1520/079',
                'address' => 'Kumaripati, Lalitpur',
                'credit_period_days' => 15,
                'credit_limit' => 200000.00,
                'credit_stop' => false,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        $c5 = Customer::firstOrCreate(
            ['code' => 'CUST-BHAKTAPUR-CARE'],
            [
                'name' => 'Bhaktapur Care & Community Pharmacy',
                'contact_person' => 'Raju Prajapati',
                'phone' => '+977-1-6612211',
                'email' => 'bhaktapurcare@gmail.com',
                'pan_vat_number' => '303445566',
                'dda_license_number' => 'DDA-RET-0723/076',
                'address' => 'Sallaghari, Bhaktapur',
                'credit_period_days' => 30,
                'credit_limit' => 400000.00,
                'credit_stop' => false,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        // 2. Create Sample Invoices if not already present
        $wh = Warehouse::where('code', 'WH-MAIN-KTM')->first() ?? Warehouse::first();
        $admin = Admin::first();
        $adminId = $admin ? $admin->id : 1;

        $p1 = Product::where('code', 'PRD-PCM-500')->first();
        $p2 = Product::where('code', 'PRD-PAN-40')->first();

        if ($wh && $p1 && $p2) {
            $existingInvoice = SaleInvoice::where('notes', 'Pharma Seeded Demo Invoice')->first();
            if (! $existingInvoice) {
                $tabUnit = Unit::where('name', 'Tablet')->first() ?? $p1->baseUnit;

                // Create draft invoice with automatic FEFO batch allocation
                $invoice = SalesService::createInvoice(
                    [
                        'customer_id' => $c1->id,
                        'warehouse_id' => $wh->id,
                        'invoice_date_ad' => Carbon::today()->format('Y-m-d'),
                        'payment_method' => 'credit',
                        'created_by' => $adminId,
                        'notes' => 'Pharma Seeded Demo Invoice',
                    ],
                    [
                        [
                            'product_id' => $p1->id,
                            'unit_id' => $tabUnit->id,
                            'qty' => 100, // 100 tablets of Paracetamol
                            'unit_price' => $p1->selling_price,
                            'discount_pct' => 2.0,
                        ],
                        [
                            'product_id' => $p2->id,
                            'unit_id' => $tabUnit->id,
                            'qty' => 50, // 50 tablets of Pantoprazole
                            'unit_price' => $p2->selling_price,
                            'discount_pct' => 0.0,
                        ],
                    ],
                    true
                );

                // Post the invoice
                SalesService::postInvoice($invoice, $adminId);
            }
        }
    }
}
