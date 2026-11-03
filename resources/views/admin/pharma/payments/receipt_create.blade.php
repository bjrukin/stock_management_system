@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.customer-receipts') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Receipts
                    </a>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-cash-stack me-2"></i>Record Customer Receipt (AR Collection)</h4>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong><i class="bi bi-exclamation-triangle me-1"></i>Validation Errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('admin.pharma.customer-receipt.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-person-lines-fill me-1 text-primary"></i>Customer & Receipt Details
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Customer / Pharmacy <span class="text-danger">*</span></label>
                                    <select name="customer_id" class="form-select" required>
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $c)
                                        <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>
                                            {{ $c->name }} (Receivable: NPR {{ number_format($c->current_balance, 2) }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Deposit Account <span class="text-danger">*</span></label>
                                    <select name="cash_bank_account_id" class="form-select" required>
                                        <option value="">-- Select Cash / Bank Account --</option>
                                        @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" @selected(old('cash_bank_account_id') == $acc->id)>
                                            {{ $acc->name }} ({{ strtoupper($acc->type) }}) &bull; Bal: NPR {{ number_format($acc->current_balance, 2) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Receipt Amount (NPR) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" value="{{ old('amount') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Receipt Date (AD) <span class="text-danger">*</span></label>
                                    <input type="date" name="receipt_date_ad" class="form-control" value="{{ old('receipt_date_ad', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Payment Instrument <span class="text-danger">*</span></label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="cash" @selected(old('payment_method') == 'cash')>Cash</option>
                                        <option value="bank_transfer" @selected(old('payment_method') == 'bank_transfer')>Bank Transfer / Fonepay</option>
                                        <option value="cheque" @selected(old('payment_method') == 'cheque')>Bank Cheque</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Cheque Number</label>
                                    <input type="text" name="cheque_number" class="form-control" placeholder="Optional cheque #" value="{{ old('cheque_number') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Issuing Bank</label>
                                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. Nabil Bank" value="{{ old('bank_name') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Transaction / Ref #</label>
                                    <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. TXN-123456" value="{{ old('transaction_reference') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Notes / Remarks</label>
                                    <input type="text" name="notes" class="form-control" placeholder="Optional remarks..." value="{{ old('notes') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-info-circle me-1 text-info"></i>Collection Rules
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                <strong>Posting Effect:</strong> Once posted, this receipt immediately debits Cash/Bank and credits the Customer Subledger, reducing their outstanding receivable balance.
                            </p>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-1"></i>Save Draft Receipt
                                </button>
                                <a href="{{ route('admin.pharma.customer-receipts') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
