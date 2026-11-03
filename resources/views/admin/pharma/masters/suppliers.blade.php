@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-truck me-2"></i>Pharmaceutical Suppliers & Vendors</h4>
                <a href="{{ route('admin.pharma.supplier.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Add New Supplier
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
                <form method="GET" action="{{ route('admin.pharma.suppliers') }}" class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, code, PAN..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button>
                        <a href="{{ route('admin.pharma.suppliers') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Supplier Name</th>
                                <th>PAN / VAT #</th>
                                <th>DDA License #</th>
                                <th>Contact / Phone</th>
                                <th>Total POs</th>
                                <th>Payable Balance</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $sup)
                            <tr>
                                <td class="fw-bold">{{ $sup->code }}</td>
                                <td><strong>{{ $sup->name }}</strong><br><small class="text-muted">{{ $sup->address ?? 'Nepal' }}</small></td>
                                <td class="font-monospace fw-semibold">{{ $sup->pan_vat_number ?? '—' }}</td>
                                <td>{{ $sup->dda_license_number ?? '—' }}</td>
                                <td><small>{{ $sup->phone ?? '—' }}<br>{{ $sup->email ?? '—' }}</small></td>
                                <td><span class="badge bg-light text-dark border">{{ $sup->purchase_orders_count }} PO(s)</span></td>
                                <td class="fw-bold text-danger">NPR {{ number_format($sup->current_balance, 2) }}</td>
                                <td>
                                    <span class="badge @if($sup->is_active) bg-success @else bg-secondary @endif">
                                        {{ $sup->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.pharma.supplier.edit', $sup->id) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.pharma.supplier.delete', $sup->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No suppliers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
