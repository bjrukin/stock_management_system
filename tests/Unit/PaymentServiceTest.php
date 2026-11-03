<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\CashBankAccount;
use App\Models\CashBankTransaction;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\FiscalYear;
use App\Models\GoodsReceipt;
use App\Models\Role;
use App\Models\SaleInvoice;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use App\Models\Warehouse;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FiscalYear $fiscalYear;
    protected Warehouse $warehouse;
    protected Customer $customer;
    protected Supplier $supplier;
    protected CashBankAccount $bankAccount;
    protected CashBankAccount $cashAccount;
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fiscalYear = FiscalYear::create([
            'name' => '2081/82',
            'start_date_ad' => '2024-07-16',
            'start_date_bs' => '2081-04-01',
            'end_date_ad' => '2025-07-15',
            'end_date_bs' => '2082-03-31',
            'is_current' => true,
        ]);

        $this->warehouse = Warehouse::create([
            'code' => 'WH-PAY-TEST',
            'name' => 'Payment Test Hub',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'code' => 'CUST-TEST-PAY',
            'name' => 'Bir Hospital Inhouse Pharmacy',
            'pan_vat_number' => '300998877',
            'credit_limit' => 500000.00,
            'current_balance' => 10000.00,
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-TEST-PAY',
            'name' => 'Nepal Pharmaceuticals Pvt. Ltd.',
            'pan_vat_number' => '300112233',
            'current_balance' => 20000.00,
            'is_active' => true,
        ]);

        $this->bankAccount = CashBankAccount::create([
            'code' => 'ACC-NABIL-TEST',
            'name' => 'Nabil Bank Operating Account',
            'type' => 'bank',
            'bank_name' => 'Nabil Bank Ltd.',
            'account_number' => '01234567890123',
            'opening_balance' => 50000.00,
            'current_balance' => 50000.00,
            'is_active' => true,
        ]);

        $this->cashAccount = CashBankAccount::create([
            'code' => 'ACC-CASH-TEST',
            'name' => 'Main Cash Drawer',
            'type' => 'cash',
            'opening_balance' => 25000.00,
            'current_balance' => 25000.00,
            'is_active' => true,
        ]);

        $role = Role::create(['name' => 'Admin']);
        $this->admin = Admin::create([
            'role_id' => $role->id,
            'name' => 'Accountant',
            'username' => 'accountant_admin',
            'status' => 1,
        ]);
    }

    public function test_creates_and_posts_customer_receipt_with_invoice_allocation(): void
    {
        $invoice = SaleInvoice::create([
            'invoice_number' => 'INV-TEST-001',
            'fiscal_year_id' => $this->fiscalYear->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'invoice_date_ad' => '2026-10-18',
            'invoice_date_bs' => '2083-07-02',
            'customer_name' => $this->customer->name,
            'payment_method' => 'credit',
            'total_amount' => 10000.00,
            'paid_amount' => 0.00,
            'credit_amount' => 10000.00,
            'status' => 'posted',
        ]);

        $receipt = PaymentService::createCustomerReceipt(
            [
                'fiscal_year_id' => $this->fiscalYear->id,
                'customer_id' => $this->customer->id,
                'cash_bank_account_id' => $this->bankAccount->id,
                'payment_method' => 'bank_transfer',
                'amount' => 6000.00,
                'transaction_reference' => 'TXN-NABIL-9988',
                'created_by' => $this->admin->id,
            ],
            [
                [
                    'sale_invoice_id' => $invoice->id,
                    'allocated_amount' => 6000.00,
                ],
            ]
        );

        $this->assertEquals('draft', $receipt->status);
        $this->assertEquals('6000.00', $receipt->amount);
        $this->assertEquals('6000.00', $receipt->allocated_amount);
        $this->assertEquals('0.00', $receipt->unallocated_amount);

        // Post receipt
        $posted = PaymentService::postCustomerReceipt($receipt, $this->admin->id);

        $this->assertEquals('posted', $posted->status);

        // Invoice allocation updated: 6,000 paid, 4,000 credit remaining
        $invoice->refresh();
        $this->assertEquals('6000.00', $invoice->paid_amount);
        $this->assertEquals('4000.00', $invoice->credit_amount);

        // Customer AR balance reduced: 10,000 - 6,000 = 4,000
        $this->customer->refresh();
        $this->assertEquals('4000.00', $this->customer->current_balance);

        // Customer ledger has credit entry
        $ledger = CustomerLedgerEntry::where('reference_id', $posted->id)
            ->where('transaction_type', 'RECEIPT')
            ->first();
        $this->assertNotNull($ledger);
        $this->assertEquals('6000.00', $ledger->credit_amount);
        $this->assertEquals('4000.00', $ledger->running_balance);

        // Bank balance increased: 50,000 + 6,000 = 56,000
        $this->bankAccount->refresh();
        $this->assertEquals('56000.00', $this->bankAccount->current_balance);

        // Cash/Bank ledger has deposit entry
        $txn = CashBankTransaction::where('reference_id', $posted->id)
            ->where('transaction_type', 'CUSTOMER_RECEIPT')
            ->first();
        $this->assertNotNull($txn);
        $this->assertEquals('6000.00', $txn->deposit_amount);
        $this->assertEquals('56000.00', $txn->running_balance);
    }

    public function test_reverses_posted_customer_receipt(): void
    {
        $invoice = SaleInvoice::create([
            'invoice_number' => 'INV-TEST-002',
            'fiscal_year_id' => $this->fiscalYear->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'invoice_date_ad' => '2026-10-18',
            'invoice_date_bs' => '2083-07-02',
            'customer_name' => $this->customer->name,
            'payment_method' => 'credit',
            'total_amount' => 10000.00,
            'paid_amount' => 0.00,
            'credit_amount' => 10000.00,
            'status' => 'posted',
        ]);

        $receipt = PaymentService::createCustomerReceipt(
            [
                'customer_id' => $this->customer->id,
                'cash_bank_account_id' => $this->bankAccount->id,
                'amount' => 6000.00,
            ],
            [
                [
                    'sale_invoice_id' => $invoice->id,
                    'allocated_amount' => 6000.00,
                ],
            ]
        );
        PaymentService::postCustomerReceipt($receipt, $this->admin->id);

        // Reverse receipt
        $reversed = PaymentService::reverseCustomerReceipt($receipt, $this->admin->id, 'Cheque bounced');

        $this->assertEquals('reversed', $reversed->status);
        $this->assertEquals('Cheque bounced', $reversed->reversal_reason);

        // Invoice allocations reverted
        $invoice->refresh();
        $this->assertEquals('0.00', $invoice->paid_amount);
        $this->assertEquals('10000.00', $invoice->credit_amount);

        // Customer balance reverted to 10,000
        $this->customer->refresh();
        $this->assertEquals('10000.00', $this->customer->current_balance);

        // Customer ledger has reversal debit entry
        $reversalLedger = CustomerLedgerEntry::where('reference_id', $reversed->id)
            ->where('transaction_type', 'RECEIPT_REVERSAL')
            ->first();
        $this->assertNotNull($reversalLedger);
        $this->assertEquals('6000.00', $reversalLedger->debit_amount);
        $this->assertEquals('10000.00', $reversalLedger->running_balance);

        // Bank balance reverted to 50,000
        $this->bankAccount->refresh();
        $this->assertEquals('50000.00', $this->bankAccount->current_balance);

        // Bank ledger has withdrawal entry
        $withdrawalTxn = CashBankTransaction::where('reference_id', $reversed->id)
            ->where('transaction_type', 'REVERSAL')
            ->first();
        $this->assertNotNull($withdrawalTxn);
        $this->assertEquals('6000.00', $withdrawalTxn->withdrawal_amount);
        $this->assertEquals('50000.00', $withdrawalTxn->running_balance);
    }

    public function test_creates_and_posts_supplier_payment_with_grn_allocation(): void
    {
        $grn = GoodsReceipt::create([
            'grn_number' => 'GRN-TEST-001',
            'fiscal_year_id' => $this->fiscalYear->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'receipt_date_ad' => '2026-10-18',
            'receipt_date_bs' => '2083-07-02',
            'total_amount' => 20000.00,
            'status' => 'posted',
        ]);

        $payment = PaymentService::createSupplierPayment(
            [
                'fiscal_year_id' => $this->fiscalYear->id,
                'supplier_id' => $this->supplier->id,
                'cash_bank_account_id' => $this->cashAccount->id,
                'payment_method' => 'cash',
                'amount' => 15000.00,
                'created_by' => $this->admin->id,
            ],
            [
                [
                    'goods_receipt_id' => $grn->id,
                    'allocated_amount' => 15000.00,
                ],
            ]
        );

        $this->assertEquals('draft', $payment->status);
        $this->assertEquals('15000.00', $payment->amount);
        $this->assertEquals('15000.00', $payment->allocated_amount);

        // Post payment
        $posted = PaymentService::postSupplierPayment($payment, $this->admin->id);

        $this->assertEquals('posted', $posted->status);

        // Supplier AP liability reduced: 20,000 - 15,000 = 5,000
        $this->supplier->refresh();
        $this->assertEquals('5000.00', $this->supplier->current_balance);

        // Supplier ledger has debit entry
        $ledger = SupplierLedgerEntry::where('reference_id', $posted->id)
            ->where('transaction_type', 'PAYMENT')
            ->first();
        $this->assertNotNull($ledger);
        $this->assertEquals('15000.00', $ledger->debit_amount);
        $this->assertEquals('5000.00', $ledger->running_balance);

        // Cash account balance decreased: 25,000 - 15,000 = 10,000
        $this->cashAccount->refresh();
        $this->assertEquals('10000.00', $this->cashAccount->current_balance);

        // Cash transaction recorded
        $txn = CashBankTransaction::where('reference_id', $posted->id)
            ->where('transaction_type', 'SUPPLIER_PAYMENT')
            ->first();
        $this->assertNotNull($txn);
        $this->assertEquals('15000.00', $txn->withdrawal_amount);
        $this->assertEquals('10000.00', $txn->running_balance);
    }

    public function test_blocks_supplier_payment_if_insufficient_cash_balance(): void
    {
        $this->cashAccount->update(['current_balance' => 500.00]);

        $payment = PaymentService::createSupplierPayment([
            'supplier_id' => $this->supplier->id,
            'cash_bank_account_id' => $this->cashAccount->id,
            'payment_method' => 'cash',
            'amount' => 2000.00,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Insufficient cash balance');

        PaymentService::postSupplierPayment($payment, $this->admin->id);
    }

    public function test_reverses_posted_supplier_payment(): void
    {
        $payment = PaymentService::createSupplierPayment([
            'supplier_id' => $this->supplier->id,
            'cash_bank_account_id' => $this->cashAccount->id,
            'payment_method' => 'cash',
            'amount' => 15000.00,
        ]);
        PaymentService::postSupplierPayment($payment, $this->admin->id);

        // Reverse payment
        $reversed = PaymentService::reverseSupplierPayment($payment, $this->admin->id, 'Duplicate voucher error');

        $this->assertEquals('reversed', $reversed->status);
        $this->assertEquals('Duplicate voucher error', $reversed->reversal_reason);

        // Supplier liability restored to 20,000
        $this->supplier->refresh();
        $this->assertEquals('20000.00', $this->supplier->current_balance);

        // Supplier ledger has credit reversal entry
        $ledger = SupplierLedgerEntry::where('reference_id', $reversed->id)
            ->where('transaction_type', 'PAYMENT_REVERSAL')
            ->first();
        $this->assertNotNull($ledger);
        $this->assertEquals('15000.00', $ledger->credit_amount);
        $this->assertEquals('20000.00', $ledger->running_balance);

        // Cash account balance restored to 25,000
        $this->cashAccount->refresh();
        $this->assertEquals('25000.00', $this->cashAccount->current_balance);
    }

    public function test_inter_account_contra_transfer(): void
    {
        // Transfer 5,000 from Cash (25,000) to Bank (50,000)
        $res = PaymentService::transferBetweenAccounts(
            $this->cashAccount->id,
            $this->bankAccount->id,
            5000.00,
            '2026-10-18',
            'Daily cash sales deposit',
            $this->admin->id
        );

        $this->assertNotNull($res['transfer_number']);

        // Cash decreased: 25,000 - 5,000 = 20,000
        $this->cashAccount->refresh();
        $this->assertEquals('20000.00', $this->cashAccount->current_balance);

        // Bank increased: 50,000 + 5,000 = 55,000
        $this->bankAccount->refresh();
        $this->assertEquals('55000.00', $this->bankAccount->current_balance);

        // Verify out and in transactions
        $out = CashBankTransaction::where('reference_number', $res['transfer_number'])
            ->where('transaction_type', 'CONTRA_OUT')
            ->first();
        $in = CashBankTransaction::where('reference_number', $res['transfer_number'])
            ->where('transaction_type', 'CONTRA_IN')
            ->first();

        $this->assertNotNull($out);
        $this->assertNotNull($in);
        $this->assertEquals('5000.00', $out->withdrawal_amount);
        $this->assertEquals('20000.00', $out->running_balance);
        $this->assertEquals('5000.00', $in->deposit_amount);
        $this->assertEquals('55000.00', $in->running_balance);
    }

    public function test_cash_bank_transaction_is_immutable(): void
    {
        $txn = CashBankTransaction::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'cash_bank_account_id' => $this->cashAccount->id,
            'transaction_date_ad' => '2026-10-18',
            'transaction_type' => 'DEPOSIT',
            'deposit_amount' => 1000.00,
            'running_balance' => 26000.00,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cash and Bank ledger transactions are immutable');

        $txn->update(['deposit_amount' => 2000.00]);
    }
}
