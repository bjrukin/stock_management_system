@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-capsule me-2"></i>Product Catalog & Inventory</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.batches') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-upc-scan me-1"></i>View Batch Register</a>
                    <a href="{{ route('admin.pharma.product.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle me-1"></i>Add New Product</a>
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
                <form method="GET" action="{{ route('admin.pharma.products') }}" class="row g-2 mb-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, code, generic formula..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="category_id" class="form-select">
                            <option value="">-- All Categories --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                        <a href="{{ route('admin.pharma.products') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Product / Item Name</th>
                                <th>Generic / Composition</th>
                                <th>Category</th>
                                <th>Manufacturer</th>
                                <th>Unit</th>
                                <th>Stock On Hand</th>
                                <th>Available</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $prod)
                            @php
                                $totalOnHand = $prod->inventoryBalances->sum('quantity_on_hand');
                                $totalAvailable = $prod->inventoryBalances->sum('quantity_available');
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ $prod->code }}</td>
                                <td>
                                    <strong>{{ $prod->name }}</strong>
                                    @if($prod->prescription_required)
                                    <span class="badge bg-danger ms-1">Rx Only</span>
                                    @endif
                                    @if($prod->narcotic_psychotropic)
                                    <span class="badge bg-dark ms-1">Narcotic</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $prod->generic_name ?? '—' }}</small></td>
                                <td><span class="badge bg-light text-dark border">{{ $prod->category->name ?? 'N/A' }}</span></td>
                                <td>{{ $prod->manufacturer->name ?? 'N/A' }}</td>
                                <td>{{ $prod->baseUnit->abbreviation ?? 'Unit' }}</td>
                                <td>
                                    <span class="badge @if($totalOnHand > 0) bg-success @else bg-danger @endif fs-6">
                                        {{ number_format($totalOnHand, 0) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info fs-6">
                                        {{ number_format($totalAvailable, 0) }}
                                    </span>
                                </td>
                                <td>
                                    @if($prod->is_active)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.pharma.product.edit', $prod->id) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.pharma.product.delete', $prod->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="10" class="text-center text-muted py-4">No products found matching criteria.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
