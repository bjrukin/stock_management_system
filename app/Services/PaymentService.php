<?php

namespace App\Services;

use App\Helpers\BsDateHelper;
use App\Models\CashBankAccount;
use App\Models\CashBankTransaction;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\CustomerReceipt;
use App\Models\CustomerReceiptAllocation;
use App\Models\FiscalYear;
use App\Models\GoodsReceipt;
use App\Models\SaleInvoice;
use App\Models\Supplier;
use App\Models\SupplierLedgerEntry;
use App\Models\SupplierPayment;
use App\Models\SupplierPaymentAllocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    /**
     * Create a Customer Receipt in draft status.
     *
     * @param array $header
     * @param array $allocations Array of ['sale_invoice_id' => int, 'allocated_amount' => float]
     * @return CustomerReceipt
     * @throws RuntimeException
     */
    public static function createCustomerReceipt(array $header, array $allocations = []): CustomerReceipt
    {
        return DB::transaction(function () use ($header, $allocations) {
            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            $customer = Customer::findOrFail($header['customer_id']);
            $account = CashBankAccount::findOrFail($header['cash_bank_account_id']);

            $amount = (string) $header['amount'];
            if (bccomp($amount, '0.00', 2) <= 0) {
                throw new RuntimeException("Receipt amount must be strictly greater than zero.");
            }

            $receiptNumber = DocumentSequenceService::getNextNumber('CRCPT', $fiscalYear);
            $receiptDateAd = $header['receipt_date_ad'] ?? Carbon::today()->format('Y-m-d');
            $receiptDateBs = $header['receipt_date_bs'] ?? BsDateHelper::toBs($receiptDateAd);

            $allocatedTotal = '0.00';
            foreach ($allocations as $alloc) {
                $allocAmount = (string) $alloc['allocated_amount'];
                if (bccomp($allocAmount, '0.00', 2) <= 0) {
                    throw new RuntimeException("Allocated amount for invoice #{$alloc['sale_invoice_id']} must be greater than zero.");
                }
                $allocatedTotal = bcadd($allocatedTotal, $allocAmount, 2);
            }

            if (bccomp($allocatedTotal, $amount, 2) > 0) {
                throw new RuntimeException("Allocated amount (NPR {$allocatedTotal}) exceeds total receipt amount (NPR {$amount}).");
            }

            $unallocatedTotal = bcsub($amount, $allocatedTotal, 2);

            $receipt = CustomerReceipt::create([
                'receipt_number' => $receiptNumber,
                'fiscal_year_id' => $fiscalYear->id,
                'customer_id' => $customer->id,
                'cash_bank_account_id' => $account->id,
                'receipt_date_ad' => $receiptDateAd,
                'receipt_date_bs' => $receiptDateBs,
                'payment_method' => $header['payment_method'] ?? 'cash',
                'amount' => $amount,
                'allocated_amount' => $allocatedTotal,
                'unallocated_amount' => $unallocatedTotal,
                'cheque_number' => $header['cheque_number'] ?? null,
                'cheque_date' => $header['cheque_date'] ?? null,
                'bank_name' => $header['bank_name'] ?? null,
                'transaction_reference' => $header['transaction_reference'] ?? null,
                'status' => 'draft',
                'created_by' => $header['created_by'] ?? null,
                'notes' => $header['notes'] ?? null,
            ]);

            foreach ($allocations as $alloc) {
                $invoice = SaleInvoice::where('id', $alloc['sale_invoice_id'])
                    ->where('customer_id', $customer->id)
                    ->firstOrFail();

                CustomerReceiptAllocation::create([
                    'customer_receipt_id' => $receipt->id,
                    'sale_invoice_id' => $invoice->id,
                    'allocated_amount' => $alloc['allocated_amount'],
                ]);
            }

            return $receipt->fresh('allocations');
        });
    }

    /**
     * Post a Customer Receipt:
     * 1. Updates allocated sale invoices (paid_amount, credit_amount, status).
     * 2. Decrements customer.current_balance and posts credit to customer_ledger_entries.
     * 3. Increments cash_bank_account.current_balance and records deposit in cash_bank_transactions.
     */
    public static function postCustomerReceipt(CustomerReceipt $receipt, int $adminId): CustomerReceipt
    {
        return DB::transaction(function () use ($receipt, $adminId) {
            $receipt->refresh();
            if ($receipt->status !== 'draft') {
                throw new RuntimeException("Receipt #{$receipt->receipt_number} is already {$receipt->status} and cannot be posted.");
            }

            $customer = Customer::where('id', $receipt->customer_id)->lockForUpdate()->firstOrFail();
            $account = CashBankAccount::where('id', $receipt->cash_bank_account_id)->lockForUpdate()->firstOrFail();

            // 1. Process allocations against sale invoices
            foreach ($receipt->allocations as $allocation) {
                $invoice = SaleInvoice::where('id', $allocation->sale_invoice_id)->lockForUpdate()->firstOrFail();

                $newPaid = bcadd((string) $invoice->paid_amount, (string) $allocation->allocated_amount, 2);
                $newCredit = bcsub((string) $invoice->credit_amount, (string) $allocation->allocated_amount, 2);

                $newStatus = bccomp($newPaid, (string) $invoice->total_amount, 2) >= 0 ? 'paid' : $invoice->status;

                $invoice->update([
                    'paid_amount' => $newPaid,
                    'credit_amount' => max('0.00', $newCredit),
                    'status' => $newStatus,
                ]);
            }

            // 2. Decrement Customer Accounts Receivable balance (Credit reduces receivable)
            $newCustomerBalance = bcsub((string) $customer->current_balance, (string) $receipt->amount, 2);

            CustomerLedgerEntry::create([
                'fiscal_year_id' => $receipt->fiscal_year_id,
                'customer_id' => $customer->id,
                'entry_date_ad' => $receipt->receipt_date_ad,
                'entry_date_bs' => $receipt->receipt_date_bs,
                'transaction_type' => 'RECEIPT',
                'reference_type' => 'CustomerReceipt',
                'reference_id' => $receipt->id,
                'reference_number' => $receipt->receipt_number,
                'debit_amount' => 0.00,
                'credit_amount' => $receipt->amount,
                'running_balance' => $newCustomerBalance,
                'notes' => "Receipt #{$receipt->receipt_number} ({$receipt->payment_method})",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $customer->update(['current_balance' => $newCustomerBalance]);

            // 3. Increment Cash / Bank Account Balance and append to cash_bank_transactions
            $newAccountBalance = bcadd((string) $account->current_balance, (string) $receipt->amount, 2);

            $status = ($receipt->payment_method === 'cheque' && empty($receipt->cheque_date)) ? 'pending' : 'cleared';

            CashBankTransaction::create([
                'fiscal_year_id' => $receipt->fiscal_year_id,
                'cash_bank_account_id' => $account->id,
                'transaction_date_ad' => $receipt->receipt_date_ad,
                'transaction_date_bs' => $receipt->receipt_date_bs,
                'transaction_type' => 'CUSTOMER_RECEIPT',
                'reference_type' => 'CustomerReceipt',
                'reference_id' => $receipt->id,
                'reference_number' => $receipt->receipt_number,
                'deposit_amount' => $receipt->amount,
                'withdrawal_amount' => 0.00,
                'running_balance' => $newAccountBalance,
                'payment_method' => $receipt->payment_method,
                'cheque_number' => $receipt->cheque_number,
                'cheque_date' => $receipt->cheque_date,
                'bank_name' => $receipt->bank_name,
                'status' => $status,
                'notes' => "Customer Receipt #{$receipt->receipt_number} from {$customer->name}",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $account->update(['current_balance' => $newAccountBalance]);

            // 4. Update Receipt Status
            $receipt->update([
                'status' => 'posted',
                'posted_by' => $adminId,
                'posted_at' => now(),
            ]);

            AuditService::log('customer_receipt_posted', $receipt, null, ['status' => 'posted'], 'admin', $adminId);

            return $receipt->fresh(['allocations', 'customer', 'account']);
        });
    }

    /**
     * Reverse a posted Customer Receipt (e.g. cheque bounced, cancelled receipt):
     * 1. Reverts sale invoice allocations (paid_amount, credit_amount, status).
     * 2. Reverts customer balance by adding debit entry to customer_ledger_entries.
     * 3. Reverts cash/bank balance by recording withdrawal in cash_bank_transactions.
     */
    public static function reverseCustomerReceipt(CustomerReceipt $receipt, int $adminId, string $reason): CustomerReceipt
    {
        return DB::transaction(function () use ($receipt, $adminId, $reason) {
            $receipt->refresh();
            if ($receipt->status !== 'posted') {
                throw new RuntimeException("Only posted receipts can be reversed. Current status: {$receipt->status}.");
            }

            $customer = Customer::where('id', $receipt->customer_id)->lockForUpdate()->firstOrFail();
            $account = CashBankAccount::where('id', $receipt->cash_bank_account_id)->lockForUpdate()->firstOrFail();

            // 1. Revert sale invoice allocations
            foreach ($receipt->allocations as $allocation) {
                $invoice = SaleInvoice::where('id', $allocation->sale_invoice_id)->lockForUpdate()->firstOrFail();

                $newPaid = max('0.00', bcsub((string) $invoice->paid_amount, (string) $allocation->allocated_amount, 2));
                $newCredit = bcadd((string) $invoice->credit_amount, (string) $allocation->allocated_amount, 2);

                $invoice->update([
                    'paid_amount' => $newPaid,
                    'credit_amount' => $newCredit,
                    'status' => 'posted',
                ]);
            }

            // 2. Revert Customer Balance (Debit increases receivable again)
            $newCustomerBalance = bcadd((string) $customer->current_balance, (string) $receipt->amount, 2);

            CustomerLedgerEntry::create([
                'fiscal_year_id' => $receipt->fiscal_year_id,
                'customer_id' => $customer->id,
                'entry_date_ad' => Carbon::today()->format('Y-m-d'),
                'entry_date_bs' => BsDateHelper::toBs(Carbon::today()->format('Y-m-d')),
                'transaction_type' => 'RECEIPT_REVERSAL',
                'reference_type' => 'CustomerReceipt',
                'reference_id' => $receipt->id,
                'reference_number' => $receipt->receipt_number,
                'debit_amount' => $receipt->amount,
                'credit_amount' => 0.00,
                'running_balance' => $newCustomerBalance,
                'notes' => "Reversal of Receipt #{$receipt->receipt_number}: {$reason}",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $customer->update(['current_balance' => $newCustomerBalance]);

            // 3. Revert Cash/Bank Account Balance (Withdrawal / Outflow)
            $newAccountBalance = bcsub((string) $account->current_balance, (string) $receipt->amount, 2);

            CashBankTransaction::create([
                'fiscal_year_id' => $receipt->fiscal_year_id,
                'cash_bank_account_id' => $account->id,
                'transaction_date_ad' => Carbon::today()->format('Y-m-d'),
                'transaction_date_bs' => BsDateHelper::toBs(Carbon::today()->format('Y-m-d')),
                'transaction_type' => 'REVERSAL',
                'reference_type' => 'CustomerReceipt',
                'reference_id' => $receipt->id,
                'reference_number' => $receipt->receipt_number,
                'deposit_amount' => 0.00,
                'withdrawal_amount' => $receipt->amount,
                'running_balance' => $newAccountBalance,
                'payment_method' => $receipt->payment_method,
                'status' => 'void',
                'notes' => "Reversal of Receipt #{$receipt->receipt_number}: {$reason}",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $account->update(['current_balance' => $newAccountBalance]);

            // 4. Update Receipt Status
            $receipt->update([
                'status' => 'reversed',
                'reversed_by' => $adminId,
                'reversed_at' => now(),
                'reversal_reason' => $reason,
            ]);

            AuditService::log('customer_receipt_reversed', $receipt, ['status' => 'posted'], ['status' => 'reversed', 'reason' => $reason], 'admin', $adminId);

            return $receipt->fresh(['allocations', 'customer', 'account']);
        });
    }

    /**
     * Create a Supplier Payment voucher in draft status.
     *
     * @param array $header
     * @param array $allocations Array of ['goods_receipt_id' => int, 'allocated_amount' => float]
     * @return SupplierPayment
     * @throws RuntimeException
     */
    public static function createSupplierPayment(array $header, array $allocations = []): SupplierPayment
    {
        return DB::transaction(function () use ($header, $allocations) {
            $fiscalYear = isset($header['fiscal_year_id'])
                ? FiscalYear::findOrFail($header['fiscal_year_id'])
                : FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();

            $supplier = Supplier::findOrFail($header['supplier_id']);
            $account = CashBankAccount::findOrFail($header['cash_bank_account_id']);

            $amount = (string) $header['amount'];
            if (bccomp($amount, '0.00', 2) <= 0) {
                throw new RuntimeException("Payment amount must be strictly greater than zero.");
            }

            $paymentNumber = DocumentSequenceService::getNextNumber('SPMT', $fiscalYear);
            $paymentDateAd = $header['payment_date_ad'] ?? Carbon::today()->format('Y-m-d');
            $paymentDateBs = $header['payment_date_bs'] ?? BsDateHelper::toBs($paymentDateAd);

            $allocatedTotal = '0.00';
            foreach ($allocations as $alloc) {
                $allocAmount = (string) $alloc['allocated_amount'];
                if (bccomp($allocAmount, '0.00', 2) <= 0) {
                    throw new RuntimeException("Allocated amount for GRN #{$alloc['goods_receipt_id']} must be greater than zero.");
                }
                $allocatedTotal = bcadd($allocatedTotal, $allocAmount, 2);
            }

            if (bccomp($allocatedTotal, $amount, 2) > 0) {
                throw new RuntimeException("Allocated amount (NPR {$allocatedTotal}) exceeds total payment amount (NPR {$amount}).");
            }

            $unallocatedTotal = bcsub($amount, $allocatedTotal, 2);

            $payment = SupplierPayment::create([
                'payment_number' => $paymentNumber,
                'fiscal_year_id' => $fiscalYear->id,
                'supplier_id' => $supplier->id,
                'cash_bank_account_id' => $account->id,
                'payment_date_ad' => $paymentDateAd,
                'payment_date_bs' => $paymentDateBs,
                'payment_method' => $header['payment_method'] ?? 'bank_transfer',
                'amount' => $amount,
                'allocated_amount' => $allocatedTotal,
                'unallocated_amount' => $unallocatedTotal,
                'cheque_number' => $header['cheque_number'] ?? null,
                'cheque_date' => $header['cheque_date'] ?? null,
                'bank_name' => $header['bank_name'] ?? null,
                'transaction_reference' => $header['transaction_reference'] ?? null,
                'status' => 'draft',
                'created_by' => $header['created_by'] ?? null,
                'notes' => $header['notes'] ?? null,
            ]);

            foreach ($allocations as $alloc) {
                $grn = GoodsReceipt::where('id', $alloc['goods_receipt_id'])
                    ->where('supplier_id', $supplier->id)
                    ->firstOrFail();

                SupplierPaymentAllocation::create([
                    'supplier_payment_id' => $payment->id,
                    'goods_receipt_id' => $grn->id,
                    'allocated_amount' => $alloc['allocated_amount'],
                ]);
            }

            return $payment->fresh('allocations');
        });
    }

    /**
     * Post a Supplier Payment voucher:
     * 1. Decrements supplier.current_balance and posts debit to supplier_ledger_entries.
     * 2. Decrements cash_bank_account.current_balance and records withdrawal in cash_bank_transactions.
     */
    public static function postSupplierPayment(SupplierPayment $payment, int $adminId): SupplierPayment
    {
        return DB::transaction(function () use ($payment, $adminId) {
            $payment->refresh();
            if ($payment->status !== 'draft') {
                throw new RuntimeException("Payment #{$payment->payment_number} is already {$payment->status} and cannot be posted.");
            }

            $supplier = Supplier::where('id', $payment->supplier_id)->lockForUpdate()->firstOrFail();
            $account = CashBankAccount::where('id', $payment->cash_bank_account_id)->lockForUpdate()->firstOrFail();

            // Guard against negative balance for cash physical accounts
            if ($account->type === 'cash' && bccomp((string) $account->current_balance, (string) $payment->amount, 2) < 0) {
                throw new RuntimeException("Insufficient cash balance in '{$account->name}'. Available: NPR {$account->current_balance}, Required: NPR {$payment->amount}.");
            }

            // 1. Decrement Supplier Accounts Payable liability (Debit reduces liability)
            $newSupplierBalance = bcsub((string) $supplier->current_balance, (string) $payment->amount, 2);

            SupplierLedgerEntry::create([
                'fiscal_year_id' => $payment->fiscal_year_id,
                'supplier_id' => $supplier->id,
                'entry_date_ad' => $payment->payment_date_ad,
                'entry_date_bs' => $payment->payment_date_bs,
                'transaction_type' => 'PAYMENT',
                'reference_type' => 'SupplierPayment',
                'reference_id' => $payment->id,
                'reference_number' => $payment->payment_number,
                'debit_amount' => $payment->amount,
                'credit_amount' => 0.00,
                'running_balance' => $newSupplierBalance,
                'notes' => "Payment #{$payment->payment_number} ({$payment->payment_method})",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $supplier->update(['current_balance' => $newSupplierBalance]);

            // 2. Decrement Cash/Bank Account Balance and append to cash_bank_transactions
            $newAccountBalance = bcsub((string) $account->current_balance, (string) $payment->amount, 2);

            CashBankTransaction::create([
                'fiscal_year_id' => $payment->fiscal_year_id,
                'cash_bank_account_id' => $account->id,
                'transaction_date_ad' => $payment->payment_date_ad,
                'transaction_date_bs' => $payment->payment_date_bs,
                'transaction_type' => 'SUPPLIER_PAYMENT',
                'reference_type' => 'SupplierPayment',
                'reference_id' => $payment->id,
                'reference_number' => $payment->payment_number,
                'deposit_amount' => 0.00,
                'withdrawal_amount' => $payment->amount,
                'running_balance' => $newAccountBalance,
                'payment_method' => $payment->payment_method,
                'cheque_number' => $payment->cheque_number,
                'cheque_date' => $payment->cheque_date,
                'bank_name' => $payment->bank_name,
                'status' => 'cleared',
                'notes' => "Supplier Payment #{$payment->payment_number} to {$supplier->name}",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $account->update(['current_balance' => $newAccountBalance]);

            // 3. Update Payment Status
            $payment->update([
                'status' => 'posted',
                'posted_by' => $adminId,
                'posted_at' => now(),
            ]);

            AuditService::log('supplier_payment_posted', $payment, null, ['status' => 'posted'], 'admin', $adminId);

            return $payment->fresh(['allocations', 'supplier', 'account']);
        });
    }

    /**
     * Reverse a posted Supplier Payment:
     * 1. Reverts supplier balance by adding credit entry to supplier_ledger_entries.
     * 2. Reverts cash/bank balance by recording deposit in cash_bank_transactions.
     */
    public static function reverseSupplierPayment(SupplierPayment $payment, int $adminId, string $reason): SupplierPayment
    {
        return DB::transaction(function () use ($payment, $adminId, $reason) {
            $payment->refresh();
            if ($payment->status !== 'posted') {
                throw new RuntimeException("Only posted payments can be reversed. Current status: {$payment->status}.");
            }

            $supplier = Supplier::where('id', $payment->supplier_id)->lockForUpdate()->firstOrFail();
            $account = CashBankAccount::where('id', $payment->cash_bank_account_id)->lockForUpdate()->firstOrFail();

            // 1. Revert Supplier Liability (Credit increases payable liability again)
            $newSupplierBalance = bcadd((string) $supplier->current_balance, (string) $payment->amount, 2);

            SupplierLedgerEntry::create([
                'fiscal_year_id' => $payment->fiscal_year_id,
                'supplier_id' => $supplier->id,
                'entry_date_ad' => Carbon::today()->format('Y-m-d'),
                'entry_date_bs' => BsDateHelper::toBs(Carbon::today()->format('Y-m-d')),
                'transaction_type' => 'PAYMENT_REVERSAL',
                'reference_type' => 'SupplierPayment',
                'reference_id' => $payment->id,
                'reference_number' => $payment->payment_number,
                'debit_amount' => 0.00,
                'credit_amount' => $payment->amount,
                'running_balance' => $newSupplierBalance,
                'notes' => "Reversal of Payment #{$payment->payment_number}: {$reason}",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $supplier->update(['current_balance' => $newSupplierBalance]);

            // 2. Revert Cash/Bank Account Balance (Deposit / Inflow)
            $newAccountBalance = bcadd((string) $account->current_balance, (string) $payment->amount, 2);

            CashBankTransaction::create([
                'fiscal_year_id' => $payment->fiscal_year_id,
                'cash_bank_account_id' => $account->id,
                'transaction_date_ad' => Carbon::today()->format('Y-m-d'),
                'transaction_date_bs' => BsDateHelper::toBs(Carbon::today()->format('Y-m-d')),
                'transaction_type' => 'REVERSAL',
                'reference_type' => 'SupplierPayment',
                'reference_id' => $payment->id,
                'reference_number' => $payment->payment_number,
                'deposit_amount' => $payment->amount,
                'withdrawal_amount' => 0.00,
                'running_balance' => $newAccountBalance,
                'payment_method' => $payment->payment_method,
                'status' => 'void',
                'notes' => "Reversal of Payment #{$payment->payment_number}: {$reason}",
                'created_by' => $adminId,
                'created_at' => now(),
            ]);

            $account->update(['current_balance' => $newAccountBalance]);

            // 3. Update Payment Status
            $payment->update([
                'status' => 'reversed',
                'reversed_by' => $adminId,
                'reversed_at' => now(),
                'reversal_reason' => $reason,
            ]);

            AuditService::log('supplier_payment_reversed', $payment, ['status' => 'posted'], ['status' => 'reversed', 'reason' => $reason], 'admin', $adminId);

            return $payment->fresh(['allocations', 'supplier', 'account']);
        });
    }

    /**
     * Inter-account Contra Transfer (e.g. Cash deposited into Bank or Bank withdrawal to Cash).
     *
     * @param int $fromAccountId
     * @param int $toAccountId
     * @param float|string $amount
     * @param string|null $dateAd
     * @param string|null $notes
     * @param int $adminId
     * @return array
     * @throws RuntimeException
     */
    public static function transferBetweenAccounts(
        int $fromAccountId,
        int $toAccountId,
        float|string $amount,
        ?string $dateAd,
        ?string $notes,
        int $adminId
    ): array {
        return DB::transaction(function () use ($fromAccountId, $toAccountId, $amount, $dateAd, $notes, $adminId) {
            if ($fromAccountId === $toAccountId) {
                throw new RuntimeException("Source and destination accounts must be distinct.");
            }

            $amountStr = (string) $amount;
            if (bccomp($amountStr, '0.00', 2) <= 0) {
                throw new RuntimeException("Transfer amount must be strictly greater than zero.");
            }

            // Lock accounts in consistent ascending order to prevent deadlocks
            $firstId = min($fromAccountId, $toAccountId);
            $secondId = max($fromAccountId, $toAccountId);

            $firstAccount = CashBankAccount::where('id', $firstId)->lockForUpdate()->firstOrFail();
            $secondAccount = CashBankAccount::where('id', $secondId)->lockForUpdate()->firstOrFail();

            $fromAccount = ($fromAccountId === $firstId) ? $firstAccount : $secondAccount;
            $toAccount = ($toAccountId === $firstId) ? $firstAccount : $secondAccount;

            if ($fromAccount->type === 'cash' && bccomp((string) $fromAccount->current_balance, $amountStr, 2) < 0) {
                throw new RuntimeException("Insufficient funds in source account '{$fromAccount->name}'. Available: NPR {$fromAccount->current_balance}, Transfer: NPR {$amountStr}.");
            }

            $fiscalYear = FiscalYear::current() ?? FiscalYear::orderByDesc('id')->firstOrFail();
            $dateAdFormatted = $dateAd ?? Carbon::today()->format('Y-m-d');
            $dateBs = BsDateHelper::toBs($dateAdFormatted);

            $transferNumber = DocumentSequenceService::getNextNumber('CTR', $fiscalYear);

            // 1. Source Account Outflow
            $newFromBalance = bcsub((string) $fromAccount->current_balance, $amountStr, 2);
            $outTxn = CashBankTransaction::create([
                'fiscal_year_id' => $fiscalYear->id,
                'cash_bank_account_id' => $fromAccount->id,
                'transaction_date_ad' => $dateAdFormatted,
                'transaction_date_bs' => $dateBs,
                'transaction_type' => 'CONTRA_OUT',
                'reference_type' => 'ContraTransfer',
                'reference_id' => null,
                'reference_number' => $transferNumber,
                'deposit_amount' => 0.00,
                'withdrawal_amount' => $amountStr,
                'running_balance' => $newFromBalance,
                'status' => 'cleared',
                'notes' => "Contra transfer to '{$toAccount->name}'. " . ($notes ?? ''),
                'created_by' => $adminId,
                'created_at' => now(),
            ]);
            $fromAccount->update(['current_balance' => $newFromBalance]);

            // 2. Destination Account Inflow
            $newToBalance = bcadd((string) $toAccount->current_balance, $amountStr, 2);
            $inTxn = CashBankTransaction::create([
                'fiscal_year_id' => $fiscalYear->id,
                'cash_bank_account_id' => $toAccount->id,
                'transaction_date_ad' => $dateAdFormatted,
                'transaction_date_bs' => $dateBs,
                'transaction_type' => 'CONTRA_IN',
                'reference_type' => 'ContraTransfer',
                'reference_id' => null,
                'reference_number' => $transferNumber,
                'deposit_amount' => $amountStr,
                'withdrawal_amount' => 0.00,
                'running_balance' => $newToBalance,
                'status' => 'cleared',
                'notes' => "Contra transfer from '{$fromAccount->name}'. " . ($notes ?? ''),
                'created_by' => $adminId,
                'created_at' => now(),
            ]);
            $toAccount->update(['current_balance' => $newToBalance]);

            return [
                'transfer_number' => $transferNumber,
                'out_transaction' => $outTxn,
                'in_transaction' => $inTxn,
                'from_account' => $fromAccount->fresh(),
                'to_account' => $toAccount->fresh(),
            ];
        });
    }
}
