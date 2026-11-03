<?php

namespace Database\Seeders;

use App\Models\AccountingPeriod;
use App\Models\CashBankAccount;
use App\Models\DocumentSequence;
use App\Models\FiscalYear;
use App\Models\TaxCategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Services\SettingService;
use Illuminate\Database\Seeder;

class PharmaMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings (Key-Value Store)
        $settings = [
            ['key' => 'company_name', 'value' => 'PharmaDistributors Nepal Pvt. Ltd.', 'group' => 'company', 'type' => 'string', 'desc' => 'Registered Entity Name'],
            ['key' => 'company_pan_vat', 'value' => '601234567', 'group' => 'company', 'type' => 'string', 'desc' => 'PAN / VAT Registration Number'],
            ['key' => 'company_dda_reg_no', 'value' => 'DDA-D-10294/080', 'group' => 'company', 'type' => 'string', 'desc' => 'Department of Drug Administration License No.'],
            ['key' => 'company_phone', 'value' => '+977-1-4412345', 'group' => 'company', 'type' => 'string', 'desc' => 'Head Office Phone'],
            ['key' => 'company_email', 'value' => 'info@pharmadistributors.com.np', 'group' => 'company', 'type' => 'string', 'desc' => 'Official Email'],
            ['key' => 'company_address', 'value' => 'Tripureshwor, Kathmandu, Nepal', 'group' => 'company', 'type' => 'string', 'desc' => 'Physical Address'],
            ['key' => 'currency_symbol', 'value' => 'NPR', 'group' => 'financial', 'type' => 'string', 'desc' => 'Currency code/symbol'],
            ['key' => 'enforce_fefo', 'value' => 'true', 'group' => 'inventory', 'type' => 'boolean', 'desc' => 'Enforce First-Expiry-First-Out sales allocation'],
            ['key' => 'allow_negative_stock', 'value' => 'false', 'group' => 'inventory', 'type' => 'boolean', 'desc' => 'Strictly prevent negative stock balances'],
            ['key' => 'near_expiry_alert_days', 'value' => '90', 'group' => 'inventory', 'type' => 'integer', 'desc' => 'Days before expiry to trigger alert'],
            ['key' => 'require_adjustment_maker_checker', 'value' => 'true', 'group' => 'inventory', 'type' => 'boolean', 'desc' => 'Stock adjustments require two-person approval'],
            ['key' => 'maker_checker_threshold_amount', 'value' => '10000', 'group' => 'inventory', 'type' => 'integer', 'desc' => 'Value above which 2nd approver is mandatory'],
        ];

        foreach ($settings as $s) {
            SettingService::set($s['key'], $s['value'], $s['group'], $s['type'], $s['desc']);
        }

        // 2. Fiscal Year (Current Nepal FY 2081/82)
        $fy = FiscalYear::firstOrCreate(
            ['name' => '2081/82'],
            [
                'start_date_ad' => '2024-07-16',
                'start_date_bs' => '2081-04-01',
                'end_date_ad' => '2025-07-15',
                'end_date_bs' => '2082-03-31',
                'is_current' => true,
                'is_closed' => false,
            ]
        );

        // 12 Nepali Accounting Periods for FY 2081/82
        $periods = [
            ['name' => 'Shrawan 2081', 'period_number' => 1, 'start_ad' => '2024-07-16', 'end_ad' => '2024-08-16', 'start_bs' => '2081-04-01', 'end_bs' => '2081-04-32'],
            ['name' => 'Bhadra 2081', 'period_number' => 2, 'start_ad' => '2024-08-17', 'end_ad' => '2024-09-16', 'start_bs' => '2081-05-01', 'end_bs' => '2081-05-31'],
            ['name' => 'Ashwin 2081', 'period_number' => 3, 'start_ad' => '2024-09-17', 'end_ad' => '2024-10-16', 'start_bs' => '2081-06-01', 'end_bs' => '2081-06-30'],
            ['name' => 'Kartik 2081', 'period_number' => 4, 'start_ad' => '2024-10-17', 'end_ad' => '2024-11-15', 'start_bs' => '2081-07-01', 'end_bs' => '2081-07-30'],
            ['name' => 'Mangsir 2081', 'period_number' => 5, 'start_ad' => '2024-11-16', 'end_ad' => '2024-12-15', 'start_bs' => '2081-08-01', 'end_bs' => '2081-08-29'],
            ['name' => 'Poush 2081', 'period_number' => 6, 'start_ad' => '2024-12-16', 'end_ad' => '2025-01-14', 'start_bs' => '2081-09-01', 'end_bs' => '2081-09-30'],
            ['name' => 'Magh 2081', 'period_number' => 7, 'start_ad' => '2025-01-15', 'end_ad' => '2025-02-12', 'start_bs' => '2081-10-01', 'end_bs' => '2081-10-29'],
            ['name' => 'Falgun 2081', 'period_number' => 8, 'start_ad' => '2025-02-13', 'end_ad' => '2025-03-14', 'start_bs' => '2081-11-01', 'end_bs' => '2081-11-30'],
            ['name' => 'Chaitra 2081', 'period_number' => 9, 'start_ad' => '2025-03-15', 'end_ad' => '2025-04-13', 'start_bs' => '2081-12-01', 'end_bs' => '2081-12-30'],
            ['name' => 'Baisakh 2082', 'period_number' => 10, 'start_ad' => '2025-04-14', 'end_ad' => '2025-05-14', 'start_bs' => '2082-01-01', 'end_bs' => '2082-01-31'],
            ['name' => 'Jestha 2082', 'period_number' => 11, 'start_ad' => '2025-05-15', 'end_ad' => '2025-06-14', 'start_bs' => '2082-02-01', 'end_bs' => '2082-02-31'],
            ['name' => 'Ashadh 2082', 'period_number' => 12, 'start_ad' => '2025-06-15', 'end_ad' => '2025-07-15', 'start_bs' => '2082-03-01', 'end_bs' => '2082-03-31'],
        ];

        foreach ($periods as $p) {
            AccountingPeriod::firstOrCreate(
                [
                    'fiscal_year_id' => $fy->id,
                    'period_number' => $p['period_number'],
                ],
                [
                    'name' => $p['name'],
                    'start_date_ad' => $p['start_ad'],
                    'start_date_bs' => $p['start_bs'],
                    'end_date_ad' => $p['end_ad'],
                    'end_date_bs' => $p['end_bs'],
                    'is_closed' => false,
                ]
            );
        }

        // 3. Document Sequences for FY 2081/82
        $sequences = [
            ['series' => 'PO', 'prefix' => 'PO-8182-', 'pad' => 5],
            ['series' => 'GRN', 'prefix' => 'GRN-8182-', 'pad' => 5],
            ['series' => 'INV', 'prefix' => 'INV-8182-', 'pad' => 5],
            ['series' => 'PINV', 'prefix' => 'PINV-8182-', 'pad' => 5],
            ['series' => 'SRTN', 'prefix' => 'SRTN-8182-', 'pad' => 5],
            ['series' => 'PRTN', 'prefix' => 'PRTN-8182-', 'pad' => 5],
            ['series' => 'ADJ', 'prefix' => 'ADJ-8182-', 'pad' => 5],
            ['series' => 'TRF', 'prefix' => 'TRF-8182-', 'pad' => 5],
            ['series' => 'CRCPT', 'prefix' => 'CRCPT-8182-', 'pad' => 5],
            ['series' => 'SPMT', 'prefix' => 'SPMT-8182-', 'pad' => 5],
        ];

        foreach ($sequences as $seq) {
            DocumentSequence::firstOrCreate(
                [
                    'fiscal_year_id' => $fy->id,
                    'series' => $seq['series'],
                ],
                [
                    'prefix' => $seq['prefix'],
                    'suffix' => null,
                    'last_number' => 0,
                    'pad_length' => $seq['pad'],
                ]
            );
        }

        // 4. Primary Warehouse & Standard Locations
        $wh = Warehouse::firstOrCreate(
            ['code' => 'WH-CENTRAL'],
            [
                'name' => 'Central Pharma Distribution Hub',
                'phone' => '+977-1-4498765',
                'email' => 'centralwh@pharmadistributors.com.np',
                'address' => 'Balaju Industrial Area, Kathmandu',
                'is_primary' => true,
                'is_active' => true,
            ]
        );

        $locations = [
            ['code' => 'LOC-STORAGE', 'name' => 'General Ambient Storage (Rack A1-D10)', 'type' => 'storage', 'is_sellable' => true, 'is_available' => true, 'condition' => 'room_temperature'],
            ['code' => 'LOC-RECEIVING', 'name' => 'Inbound Goods Staging Dock', 'type' => 'receiving', 'is_sellable' => false, 'is_available' => false, 'condition' => 'room_temperature'],
            ['code' => 'LOC-DISPATCH', 'name' => 'Outbound Pack & Dispatch Bay', 'type' => 'dispatch', 'is_sellable' => false, 'is_available' => false, 'condition' => 'room_temperature'],
            ['code' => 'LOC-COLD-CHAIN', 'name' => 'Cold Chain Walk-in Chiller (2°C - 8°C)', 'type' => 'cold_storage', 'is_sellable' => true, 'is_available' => true, 'condition' => 'cold_chain_2_8'],
            ['code' => 'LOC-QUARANTINE', 'name' => 'Quality Quarantine & Hold Area', 'type' => 'quarantine', 'is_sellable' => false, 'is_available' => false, 'condition' => 'room_temperature'],
            ['code' => 'LOC-DAMAGED', 'name' => 'Damaged Goods Isolation Rack', 'type' => 'damaged', 'is_sellable' => false, 'is_available' => false, 'condition' => 'room_temperature'],
            ['code' => 'LOC-EXPIRED', 'name' => 'Expired Stock / DDA Disposal Staging', 'type' => 'expired_disposal', 'is_sellable' => false, 'is_available' => false, 'condition' => 'room_temperature'],
            ['code' => 'LOC-RETURNS', 'name' => 'Customer Returns Inspection Area', 'type' => 'returns', 'is_sellable' => false, 'is_available' => false, 'condition' => 'room_temperature'],
        ];

        foreach ($locations as $loc) {
            WarehouseLocation::firstOrCreate(
                [
                    'warehouse_id' => $wh->id,
                    'code' => $loc['code'],
                ],
                [
                    'name' => $loc['name'],
                    'type' => $loc['type'],
                    'depth' => 1,
                    'is_sellable' => $loc['is_sellable'],
                    'is_available' => $loc['is_available'],
                    'storage_condition' => $loc['condition'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Units of Measurement
        $units = [
            ['name' => 'Piece', 'abbreviation' => 'Pcs'],
            ['name' => 'Strip', 'abbreviation' => 'Str'],
            ['name' => 'Box', 'abbreviation' => 'Box'],
            ['name' => 'Bottle', 'abbreviation' => 'Btl'],
            ['name' => 'Vial', 'abbreviation' => 'Vial'],
            ['name' => 'Ampoule', 'abbreviation' => 'Amp'],
            ['name' => 'Tube', 'abbreviation' => 'Tube'],
            ['name' => 'Sachet', 'abbreviation' => 'Sachet'],
            ['name' => 'Tablet', 'abbreviation' => 'Tab'],
            ['name' => 'Capsule', 'abbreviation' => 'Cap'],
        ];

        foreach ($units as $u) {
            Unit::firstOrCreate(['abbreviation' => $u['abbreviation']], ['name' => $u['name'], 'is_active' => true]);
        }

        // 6. Tax Categories (Decision D-004: configurable placeholders)
        $taxCategories = [
            ['code' => 'VAT-EXEMPT', 'name' => 'VAT Exempt Medicines (Schedule 1)', 'rate' => 0.0000, 'desc' => 'Essential and life-saving pharmaceuticals exempt from VAT in Nepal.'],
            ['code' => 'VAT-STANDARD', 'name' => 'Standard VAT (Configurable)', 'rate' => 13.0000, 'desc' => 'Standard rate if product or non-medicine item is VAT taxable.'],
        ];

        foreach ($taxCategories as $tc) {
            TaxCategory::firstOrCreate(
                ['code' => $tc['code']],
                ['name' => $tc['name'], 'rate' => $tc['rate'], 'description' => $tc['desc'], 'is_active' => true]
            );
        }

        // 7. Cash & Bank Accounts
        CashBankAccount::firstOrCreate(
            ['code' => 'ACC-CASH-MAIN'],
            [
                'name' => 'Main Cash Drawer',
                'type' => 'cash',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        CashBankAccount::firstOrCreate(
            ['code' => 'ACC-BANK-NABIL'],
            [
                'name' => 'Nabil Bank Operating Account',
                'type' => 'bank',
                'bank_name' => 'Nabil Bank Ltd.',
                'account_number' => '01201017500123',
                'branch' => 'Tripureshwor',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );
    }
}
