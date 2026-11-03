<?php

namespace App\Http\Controllers\Admin\Pharma;

use App\Http\Controllers\Controller;
use App\Models\CashBankAccount;
use App\Models\CustomerReceipt;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;

class PharmaPaymentController extends Controller
{
    public function customerReceipts(Request $request)
    {
        $data['nav'] = 'pharma_payments';
        $data['sub_nav'] = 'customer_receipts';
        $data['page_title'] = 'Customer Receipts (AR Collection)';

        $data['receipts'] = CustomerReceipt::with(['customer', 'cashBankAccount', 'allocations.saleInvoice'])
            ->orderBy('id', 'desc')->paginate(20);

        return view('admin.pharma.payments.customer_receipts', $data);
    }

    public function createReceipt()
    {
        $data['nav'] = 'pharma_payments';
        $data['sub_nav'] = 'customer_receipts';
        $data['page_title'] = 'Record Customer Receipt';

        $data['customers'] = \App\Models\Customer::active()->get();
        $data['accounts'] = CashBankAccount::active()->get();
        $data['invoices'] = \App\Models\SaleInvoice::where('status', 'posted')
            ->where('credit_amount', '>', 0)
            ->get();

        return view('admin.pharma.payments.receipt_create', $data);
    }

    public function storeReceipt(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cash_bank_account_id' => 'required|exists:cash_bank_accounts,id',
            'amount' => 'required|numeric|gt:0',
            'receipt_date_ad' => 'required|date',
            'payment_method' => 'required|in:cash,cheque,bank_transfer',
            'cheque_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'allocations' => 'nullable|array',
            'allocations.*.sale_invoice_id' => 'required_with:allocations|exists:sale_invoices,id',
            'allocations.*.allocated_amount' => 'required_with:allocations|numeric|gt:0',
        ]);

        $header = [
            'customer_id' => $validated['customer_id'],
            'cash_bank_account_id' => $validated['cash_bank_account_id'],
            'amount' => $validated['amount'],
            'receipt_date_ad' => $validated['receipt_date_ad'],
            'payment_method' => $validated['payment_method'],
            'cheque_number' => $validated['cheque_number'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id() ?? 1,
        ];

        $allocations = $validated['allocations'] ?? [];

        try {
            $receipt = \App\Services\PaymentService::createCustomerReceipt($header, $allocations);

            return redirect()->route('admin.pharma.customer-receipt.show', $receipt->id)
                ->with('success', "Customer Receipt #{$receipt->receipt_number} created in draft status.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showReceipt(int $id)
    {
        $receipt = CustomerReceipt::with(['customer', 'cashBankAccount', 'allocations.saleInvoice'])
            ->findOrFail($id);

        $data['nav'] = 'pharma_payments';
        $data['sub_nav'] = 'customer_receipts';
        $data['page_title'] = "Customer Receipt #{$receipt->receipt_number}";
        $data['receipt'] = $receipt;

        return view('admin.pharma.payments.receipt_show', $data);
    }

    public function postReceipt(Request $request, int $id)
    {
        $receipt = CustomerReceipt::findOrFail($id);

        try {
            \App\Services\PaymentService::postCustomerReceipt($receipt, auth()->id() ?? 1);

            return redirect()->route('admin.pharma.customer-receipt.show', $receipt->id)
                ->with('success', "Customer Receipt #{$receipt->receipt_number} posted successfully. Accounts Receivable credited and Cash/Bank updated.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function supplierPayments(Request $request)
    {
        $data['nav'] = 'pharma_payments';
        $data['sub_nav'] = 'supplier_payments';
        $data['page_title'] = 'Supplier Payments (AP Disbursement)';

        $data['payments'] = SupplierPayment::with(['supplier', 'cashBankAccount', 'allocations.goodsReceipt'])
            ->orderBy('id', 'desc')->paginate(20);

        return view('admin.pharma.payments.supplier_payments', $data);
    }

    public function createPayment()
    {
        $data['nav'] = 'pharma_payments';
        $data['sub_nav'] = 'supplier_payments';
        $data['page_title'] = 'Issue Supplier Payment';

        $data['suppliers'] = \App\Models\Supplier::active()->get();
        $data['accounts'] = CashBankAccount::active()->get();
        $data['receipts'] = \App\Models\GoodsReceipt::where('status', 'posted')->take(50)->get();

        return view('admin.pharma.payments.payment_create', $data);
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'cash_bank_account_id' => 'required|exists:cash_bank_accounts,id',
            'amount' => 'required|numeric|gt:0',
            'payment_date_ad' => 'required|date',
            'payment_method' => 'required|in:cash,cheque,bank_transfer',
            'cheque_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'allocations' => 'nullable|array',
            'allocations.*.goods_receipt_id' => 'required_with:allocations|exists:goods_receipts,id',
            'allocations.*.allocated_amount' => 'required_with:allocations|numeric|gt:0',
        ]);

        $header = [
            'supplier_id' => $validated['supplier_id'],
            'cash_bank_account_id' => $validated['cash_bank_account_id'],
            'amount' => $validated['amount'],
            'payment_date_ad' => $validated['payment_date_ad'],
            'payment_method' => $validated['payment_method'],
            'cheque_number' => $validated['cheque_number'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id() ?? 1,
        ];

        $allocations = $validated['allocations'] ?? [];

        try {
            $payment = \App\Services\PaymentService::createSupplierPayment($header, $allocations);

            return redirect()->route('admin.pharma.supplier-payment.show', $payment->id)
                ->with('success', "Supplier Payment #{$payment->payment_number} created in draft status.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showPayment(int $id)
    {
        $payment = SupplierPayment::with(['supplier', 'cashBankAccount', 'allocations.goodsReceipt'])
            ->findOrFail($id);

        $data['nav'] = 'pharma_payments';
        $data['sub_nav'] = 'supplier_payments';
        $data['page_title'] = "Supplier Payment #{$payment->payment_number}";
        $data['payment'] = $payment;

        return view('admin.pharma.payments.payment_show', $data);
    }

    public function postPayment(Request $request, int $id)
    {
        $payment = SupplierPayment::findOrFail($id);

        try {
            \App\Services\PaymentService::postSupplierPayment($payment, auth()->id() ?? 1);

            return redirect()->route('admin.pharma.supplier-payment.show', $payment->id)
                ->with('success', "Supplier Payment #{$payment->payment_number} posted successfully. Accounts Payable debited and Cash/Bank credited.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cashBank(Request $request)
    {
        $data['nav'] = 'pharma_payments';
        $data['sub_nav'] = 'cash_bank';
        $data['page_title'] = 'Cash & Bank Accounts';

        $data['accounts'] = CashBankAccount::with(['transactions' => function ($q) {
            $q->orderBy('id', 'desc')->limit(10);
        }])->get();

        return view('admin.pharma.payments.cash_bank', $data);
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:100',
            'account_type' => 'required|in:cash,bank,digital_wallet',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'branch' => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $account = CashBankAccount::create([
            'code' => 'ACC-' . strtoupper(substr(uniqid(), -6)),
            'name' => $validated['account_name'],
            'type' => $validated['account_type'],
            'bank_name' => $validated['bank_name'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'branch' => $validated['branch'] ?? null,
            'opening_balance' => $validated['opening_balance'] ?? 0.00,
            'current_balance' => $validated['opening_balance'] ?? 0.00,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return redirect()->route('admin.pharma.cash-bank')
            ->with('success', "Account '{$account->name}' created successfully.");
    }
}

