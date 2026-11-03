<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\CashBankAccount;
use App\Models\Customer;
use App\Models\CustomerReceipt;
use App\Models\GoodsReceipt;
use App\Models\SaleInvoice;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PharmaPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();
        $adminId = $admin ? $admin->id : 1;

        $nabilBank = CashBankAccount::where('code', 'ACC-BANK-NABIL')->first();
        $cashMain = CashBankAccount::where('code', 'ACC-CASH-MAIN')->first();

        if (! $nabilBank || ! $cashMain) {
            return;
        }

        // 1. Ensure initial liquidity if balance is 0
        if (bccomp((string) $nabilBank->current_balance, '100000.00', 2) < 0) {
            $nabilBank->update([
                'opening_balance' => 500000.00,
                'current_balance' => 500000.00,
            ]);
        }

        if (bccomp((string) $cashMain->current_balance, '50000.00', 2) < 0) {
            $cashMain->update([
                'opening_balance' => 100000.00,
                'current_balance' => 100000.00,
            ]);
        }

        // 2. Demo Customer Receipt from Alka Hospital Pharmacy
        $alka = Customer::where('code', 'CUST-ALKA-PHARM')->first();
        if ($alka) {
            $invoice = SaleInvoice::where('customer_id', $alka->id)
                ->where('status', 'posted')
                ->where('credit_amount', '>', 0)
                ->first();

            $existingReceipt = CustomerReceipt::where('notes', 'Pharma Seeded Demo Receipt')->first();
            if ($invoice && ! $existingReceipt) {
                $payAmount = min((float) $invoice->credit_amount, 500.00);

                $receipt = PaymentService::createCustomerReceipt(
                    [
                        'customer_id' => $alka->id,
                        'cash_bank_account_id' => $nabilBank->id,
                        'receipt_date_ad' => Carbon::today()->format('Y-m-d'),
                        'payment_method' => 'bank_transfer',
                        'amount' => $payAmount,
                        'transaction_reference' => 'NABIL-EFT-889911',
                        'created_by' => $adminId,
                        'notes' => 'Pharma Seeded Demo Receipt',
                    ],
                    [
                        [
                            'sale_invoice_id' => $invoice->id,
                            'allocated_amount' => $payAmount,
                        ],
                    ]
                );

                PaymentService::postCustomerReceipt($receipt, $adminId);
            }
        }

        // 3. Demo Supplier Payment to Deurali-Janta
        $djpl = Supplier::where('code', 'SUP-DJPL-DIST')->first();
        if ($djpl) {
            $grn = GoodsReceipt::where('supplier_id', $djpl->id)
                ->where('status', 'posted')
                ->first();

            $existingPayment = SupplierPayment::where('notes', 'Pharma Seeded Demo Payment')->first();
            if ($grn && ! $existingPayment) {
                $payment = PaymentService::createSupplierPayment(
                    [
                        'supplier_id' => $djpl->id,
                        'cash_bank_account_id' => $nabilBank->id,
                        'payment_date_ad' => Carbon::today()->format('Y-m-d'),
                        'payment_method' => 'bank_transfer',
                        'amount' => 1000.00,
                        'transaction_reference' => 'NABIL-VOUCHER-0012',
                        'created_by' => $adminId,
                        'notes' => 'Pharma Seeded Demo Payment',
                    ],
                    [
                        [
                            'goods_receipt_id' => $grn->id,
                            'allocated_amount' => 1000.00,
                        ],
                    ]
                );

                PaymentService::postSupplierPayment($payment, $adminId);
            }
        }
    }
}
