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
                    <h4 class="page-title-main text-primary m-0">
                        <i class="bi bi-cash-stack me-2"></i>Customer Receipt #{{ $receipt->receipt_number }}
                    </h4>
                </div>
                <div>
                    @if($receipt->status === 'draft')
                    <form action="{{ route('admin.pharma.customer-receipt.post', $receipt->id) }}" method="POST" onsubmit="return confirm('Post this receipt? This will debit cash/bank and credit the customer ledger.');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2-circle me-1"></i>Post & Reconcile Receipt
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
                        <span class="fw-bold"><i class="bi bi-info-circle me-1 text-primary"></i>Receipt Voucher Information</span>
                        <span class="badge @if($receipt->status === 'posted') bg-success @elseif($receipt->status === 'reversed') bg-danger @else bg-warning text-dark @endif fs-6">
                            {{ strtoupper($receipt->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Customer Name:</label>
                                <div class="fw-bold fs-6">{{ $receipt->customer->name ?? 'N/A' }}</div>
                                <div class="small text-muted">PAN/VAT: {{ $receipt->customer->pan_vat_number ?? '—' }} &bull; Code: {{ $receipt->customer->code ?? '' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Deposit Account:</label>
                                <div class="fw-bold fs-6">{{ $receipt->cashBankAccount->name ?? 'Cash Account' }}</div>
                                <div class="small text-muted">{{ strtoupper($receipt->cashBankAccount->type ?? 'CASH') }} &bull; {{ $receipt->cashBankAccount->bank_name ?? '' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Receipt Date (AD/BS):</label>
                                <div class="fw-semibold">{{ $receipt->receipt_date_ad }} &bull; <span class="text-muted">{{ $receipt->receipt_date_bs }}</span></div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Payment Instrument:</label>
                                <div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $receipt->payment_method) }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Reference / Cheque #:</label>
                                <div class="fw-semibold">{{ $receipt->cheque_number ?? ($receipt->transaction_reference ?? '—') }}</div>
                            </div>
                            @if($receipt->notes)
                            <div class="col-12">
                                <label class="text-muted small">Notes / Remarks:</label>
                                <div class="text-muted">{{ $receipt->notes }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold text-dark">
                        <i class="bi bi-cash me-1 text-success"></i>Amount Collected
                    </div>
                    <div class="card-body text-center py-4">
                        <div class="text-muted small text-uppercase">Total Receipt Value</div>
                        <div class="fs-2 fw-bold text-success">
                            NPR {{ number_format($receipt->amount, 2) }}
                        </div>
                        <div class="mt-2 text-muted small">
                            Allocated: NPR {{ number_format($receipt->allocated_amount, 2) }} &bull; Unallocated: NPR {{ number_format($receipt->unallocated_amount, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
