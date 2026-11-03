@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-arrow-left-right me-2"></i>Inter-Warehouse Stock Transfers</h4>
                <a href="{{ route('admin.pharma.stock-transfer.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Create Stock Transfer
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
                                <th>Transfer #</th>
                                <th>From Warehouse</th>
                                <th>To Warehouse</th>
                                <th>Transfer Date (AD/BS)</th>
                                <th>Total Units</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $tr)
                            <tr>
                                <td class="fw-bold">
                                    <a href="{{ route('admin.pharma.stock-transfer.show', $tr->id) }}" class="text-decoration-none">
                                        {{ $tr->transfer_number }}
                                    </a>
                                </td>
                                <td>{{ $tr->fromWarehouse->name ?? 'N/A' }}</td>
                                <td>{{ $tr->toWarehouse->name ?? 'N/A' }}</td>
                                <td><small>{{ $tr->transfer_date_ad }}<br><span class="text-muted">{{ $tr->transfer_date_bs }}</span></small></td>
                                <td><span class="badge bg-light text-dark border">{{ $tr->lines->sum('quantity') }} unit(s)</span></td>
                                <td>
                                    <span class="badge @if($tr->status == 'received') bg-success @elseif($tr->status == 'dispatched') bg-info @else bg-warning text-dark @endif">
                                        {{ strtoupper(str_replace('_', ' ', $tr->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.pharma.stock-transfer.show', $tr->id) }}" class="btn btn-sm btn-outline-primary" title="View Transfer">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No inter-warehouse transfers recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transfers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
