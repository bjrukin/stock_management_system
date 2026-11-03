@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-cash-stack me-2"></i>Customer Receipts & AR Collections</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.reports.aging') }}" class="btn btn-sm btn-outline-info"><i class="bi bi-clock-history me-1"></i>AR Aging Summary</a>
                    <a href="{{ route('admin.pharma.customer-receipt.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Record Customer Receipt
                    </a>
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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt #</th>
                                <th>Customer</th>
                                <th>Account</th>
                                <th>Payment Instrument</th>
                                <th>Date (AD/BS)</th>
                                <th>Amount Collected</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $rc)
                            <tr>
                                <td class="fw-bold">
                                    <a href="{{ route('admin.pharma.customer-receipt.show', $rc->id) }}" class="text-decoration-none">
                                        {{ $rc->receipt_number }}
                                    </a>
                                </td>
                                <td>{{ $rc->customer->name ?? 'N/A' }}</td>
                                <td>{{ $rc->cashBankAccount->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ strtoupper($rc->payment_method) }}</span></td>
                                <td><small>{{ $rc->receipt_date_ad }}<br><span class="text-muted">{{ $rc->receipt_date_bs }}</span></small></td>
                                <td class="fw-bold text-success">NPR {{ number_format($rc->amount, 2) }}</td>
                                <td>
                                    <span class="badge @if($rc->status == 'posted') bg-success @elseif($rc->status == 'reversed') bg-danger @else bg-warning text-dark @endif">
                                        {{ ucfirst($rc->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.pharma.customer-receipt.show', $rc->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No customer receipts recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $receipts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
