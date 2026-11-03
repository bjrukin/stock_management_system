@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-sliders me-2"></i>Stock Adjustments (Maker-Checker Control)</h4>
                <a href="{{ route('admin.pharma.stock-adjustment.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Create Stock Adjustment
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
                                <th>Adjustment #</th>
                                <th>Warehouse</th>
                                <th>Type</th>
                                <th>Reason</th>
                                <th>Created By</th>
                                <th>Approved By</th>
                                <th>Date (AD/BS)</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($adjustments as $adj)
                            <tr>
                                <td class="fw-bold">
                                    <a href="{{ route('admin.pharma.stock-adjustment.show', $adj->id) }}" class="text-decoration-none">
                                        {{ $adj->adjustment_number }}
                                    </a>
                                </td>
                                <td>{{ $adj->warehouse->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ str_replace('_', ' ', $adj->type) }}</span></td>
                                <td><small class="text-muted">{{ $adj->reason ?? '—' }}</small></td>
                                <td><small>{{ $adj->createdBy->name ?? 'Maker #' . $adj->created_by }}</small></td>
                                <td><small>{{ $adj->approvedBy->name ?? ($adj->approved_by ? 'Checker #' . $adj->approved_by : 'Pending') }}</small></td>
                                <td><small>{{ $adj->adjustment_date_ad }}<br><span class="text-muted">{{ $adj->adjustment_date_bs }}</span></small></td>
                                <td>
                                    <span class="badge @if($adj->status == 'approved') bg-success @elseif($adj->status == 'rejected') bg-danger @else bg-warning text-dark @endif">
                                        {{ ucfirst($adj->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.pharma.stock-adjustment.show', $adj->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No stock adjustments recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $adjustments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
