@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-shop me-2"></i>Customers (Pharmacies, Hospitals & Clinics)</h4>
                <a href="{{ route('admin.pharma.customer.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Add New Customer
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
                <form method="GET" action="{{ route('admin.pharma.customers') }}" class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, code, PAN, DDA..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button>
                        <a href="{{ route('admin.pharma.customers') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Customer / Pharmacy Name</th>
                                <th>PAN / VAT #</th>
                                <th>DDA License #</th>
                                <th>Credit Limit</th>
                                <th>Credit Days</th>
                                <th>Current Balance</th>
                                <th>Credit Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $c)
                            <tr>
                                <td class="fw-bold">{{ $c->code }}</td>
                                <td><strong>{{ $c->name }}</strong><br><small class="text-muted">{{ $c->address ?? 'Nepal' }}</small></td>
                                <td class="font-monospace fw-semibold">{{ $c->pan_vat_number ?? '—' }}</td>
                                <td>{{ $c->dda_license_number ?? '—' }}</td>
                                <td>NPR {{ number_format($c->credit_limit, 2) }}</td>
                                <td>{{ $c->credit_period_days ?? 0 }} days</td>
                                <td class="fw-bold @if($c->credit_limit > 0 && $c->current_balance > $c->credit_limit) text-danger @else text-dark @endif">
                                    NPR {{ number_format($c->current_balance, 2) }}
                                </td>
                                <td>
                                    @if($c->credit_stop)
                                    <span class="badge bg-danger">Credit Stopped</span>
                                    @else
                                    <span class="badge bg-success">Normal</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.pharma.customer.edit', $c->id) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.pharma.customer.delete', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No customers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
