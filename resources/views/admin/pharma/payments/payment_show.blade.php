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
                    <h4 class="page-title-main text-primary m-0">
                        <i class="bi bi-send-dash me-2"></i>Supplier Payment #{{ $payment->payment_number }}
                    </h4>
                </div>
                <div>
                    @if($payment->status === 'draft')
                    <form action="{{ route('admin.pharma.supplier-payment.post', $payment->id) }}" method="POST" onsubmit="return confirm('Post this payment? This will credit cash/bank and debit the supplier payable ledger.');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2-circle me-1"></i>Post & Disburse Payment
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-info-circle me-1 text-primary"></i>Payment Voucher Information</span>
                        <span class="badge @if($payment->status === 'posted') bg-success @elseif($payment->status === 'reversed') bg-danger @else bg-warning text-dark @endif fs-6">
                            {{ strtoupper($payment->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Supplier / Vendor:</label>
                                <div class="fw-bold fs-6">{{ $payment->supplier->name ?? 'N/A' }}</div>
                                <div class="small text-muted">PAN/VAT: {{ $payment->supplier->pan_vat_number ?? '—' }} &bull; Code: {{ $payment->supplier->code ?? '' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Disbursing Account:</label>
                                <div class="fw-bold fs-6">{{ $payment->cashBankAccount->name ?? 'Bank Account' }}</div>
                                <div class="small text-muted">{{ strtoupper($payment->cashBankAccount->type ?? 'BANK') }} &bull; {{ $payment->cashBankAccount->bank_name ?? '' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Payment Date (AD/BS):</label>
                                <div class="fw-semibold">{{ $payment->payment_date_ad }} &bull; <span class="text-muted">{{ $payment->payment_date_bs }}</span></div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Payment Mode:</label>
                                <div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Reference / Cheque #:</label>
                                <div class="fw-semibold">{{ $payment->cheque_number ?? ($payment->transaction_reference ?? '—') }}</div>
                            </div>
                            @if($payment->notes)
                            <div class="col-12">
                                <label class="text-muted small">Notes / Remarks:</label>
                                <div class="text-muted">{{ $payment->notes }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold text-dark">
                        <i class="bi bi-cash me-1 text-danger"></i>Disbursement Value
                    </div>
                    <div class="card-body text-center py-4">
                        <div class="text-muted small text-uppercase">Total Payment Made</div>
                        <div class="fs-2 fw-bold text-danger">
                            NPR {{ number_format($payment->amount, 2) }}
                        </div>
                        <div class="mt-2 text-muted small">
                            Allocated: NPR {{ number_format($payment->allocated_amount, 2) }} &bull; Unallocated: NPR {{ number_format($payment->unallocated_amount, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
