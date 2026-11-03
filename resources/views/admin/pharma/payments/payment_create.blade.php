@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.supplier-payments') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Payments
                    </a>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-send-dash me-2"></i>Issue Supplier Payment (AP Disbursement)</h4>
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

        <form action="{{ route('admin.pharma.supplier-payment.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-building me-1 text-primary"></i>Vendor & Payment Details
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Supplier / Vendor <span class="text-danger">*</span></label>
                                    <select name="supplier_id" class="form-select" required>
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}" @selected(old('supplier_id') == $sup->id)>
                                            {{ $sup->name }} (Payable: NPR {{ number_format($sup->current_balance, 2) }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Disbursing Account <span class="text-danger">*</span></label>
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
                                    <label class="form-label fw-semibold">Payment Amount (NPR) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" value="{{ old('amount') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Payment Date (AD) <span class="text-danger">*</span></label>
                                    <input type="date" name="payment_date_ad" class="form-control" value="{{ old('payment_date_ad', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Payment Mode <span class="text-danger">*</span></label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="bank_transfer" @selected(old('payment_method') == 'bank_transfer')>Bank Transfer / RTGS / IPS</option>
                                        <option value="cheque" @selected(old('payment_method') == 'cheque')>Bank Cheque</option>
                                        <option value="cash" @selected(old('payment_method') == 'cash')>Cash</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Cheque Number</label>
                                    <input type="text" name="cheque_number" class="form-control" placeholder="Optional cheque #" value="{{ old('cheque_number') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control" placeholder="Bank name" value="{{ old('bank_name') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Transaction / Ref #</label>
                                    <input type="text" name="transaction_reference" class="form-control" placeholder="Bank reference or voucher #" value="{{ old('transaction_reference') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Notes / Purpose of Payment</label>
                                    <input type="text" name="notes" class="form-control" placeholder="Optional remarks..." value="{{ old('notes') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-info-circle me-1 text-info"></i>Disbursement Rules
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                <strong>Posting Effect:</strong> Once confirmed, this payment debits the Supplier Payable Ledger and credits the selected Cash/Bank Account, reducing your outstanding vendor debt.
                            </p>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-1"></i>Save Draft Payment
                                </button>
                                <a href="{{ route('admin.pharma.supplier-payments') }}" class="btn btn-outline-secondary">
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
