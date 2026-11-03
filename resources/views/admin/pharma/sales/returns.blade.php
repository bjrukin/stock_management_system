@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-arrow-return-left me-2"></i>Sales Returns (Customer Returns & Credit Notes)</h4>
                <a href="{{ route('admin.pharma.sales-return.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Create Sales Return
                </a>
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
                                <th>Return #</th>
                                <th>Invoice Ref</th>
                                <th>Customer</th>
                                <th>Warehouse</th>
                                <th>Date (AD/BS)</th>
                                <th>Credit Total</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $ret)
                            <tr>
                                <td class="fw-bold">
                                    <a href="{{ route('admin.pharma.sales-return.show', $ret->id) }}" class="text-decoration-none">
                                        {{ $ret->return_number }}
                                    </a>
                                </td>
                                <td>
                                    @if($ret->saleInvoice)
                                    <a href="{{ route('admin.pharma.sale-invoice.show', $ret->saleInvoice->id) }}">
                                        {{ $ret->saleInvoice->invoice_number }}
                                    </a>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $ret->customer->name ?? 'Walk-in Customer' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $ret->warehouse->name ?? 'N/A' }}</span></td>
                                <td><small>{{ $ret->return_date_ad }}<br><span class="text-muted">{{ $ret->return_date_bs }}</span></small></td>
                                <td class="fw-bold text-success">NPR {{ number_format($ret->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge @if($ret->status == 'posted') bg-success @else bg-warning text-dark @endif">
                                        {{ ucfirst($ret->status) }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $ret->reason ?? '—' }}</small></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.pharma.sales-return.show', $ret->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No sales returns recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $returns->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
