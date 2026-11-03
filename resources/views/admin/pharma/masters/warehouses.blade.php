@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-buildings me-2"></i>Warehouses</h4>
                    <small class="text-muted">Manage distribution centers, central hubs, and storage facilities</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.warehouse.locations.all') }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-geo-alt me-1"></i>All Locations
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createWarehouseModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Warehouse
                    </button>
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
                <form method="GET" action="{{ route('admin.pharma.warehouses') }}" class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, code, address, phone..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button>
                        <a href="{{ route('admin.pharma.warehouses') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 12%;">Code</th>
                                <th style="width: 25%;">Warehouse Name</th>
                                <th style="width: 22%;">Address</th>
                                <th style="width: 18%;">Contact Info</th>
                                <th style="width: 11%;">Locations</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 12%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($warehouses as $wh)
                            <tr>
                                <td>
                                    <span class="badge bg-primary font-monospace fs-6">{{ $wh->code }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold fs-6">{{ $wh->name }}</div>
                                    @if($wh->is_primary)
                                    <span class="badge bg-dark mt-1"><i class="bi bi-star-fill me-1 text-warning"></i>Primary Hub</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $wh->address ?? 'Nepal' }}</small>
                                </td>
                                <td>
                                    <small>
                                        @if($wh->phone) <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $wh->phone }}</div> @endif
                                        @if($wh->email) <div><i class="bi bi-envelope me-1 text-muted"></i>{{ $wh->email }}</div> @endif
                                        @if(!$wh->phone && !$wh->email) <span class="text-muted">—</span> @endif
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.pharma.warehouse.locations', $wh->id) }}" class="badge bg-info-subtle text-info border fs-6 text-decoration-none" title="Click to view locations">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $wh->locations_count }} {{ Str::plural('Location', $wh->locations_count) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge @if($wh->is_active) bg-success @else bg-secondary @endif">
                                        {{ $wh->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <!-- <a href="{{ route('admin.pharma.warehouse.locations', $wh->id) }}" class="btn btn-outline-info" title="View Locations of this Warehouse">
                                            <i class="bi bi-geo-alt me-1"></i>Locations
                                        </a> -->
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editWarehouseModal{{ $wh->id }}" title="Edit Warehouse">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.pharma.warehouse.delete', $wh->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete warehouse \'{{ $wh->name }}\'?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Warehouse">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Warehouse Modal -->
                            <div class="modal fade" id="editWarehouseModal{{ $wh->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.pharma.warehouse.update', $wh->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="bi bi-building me-1 text-primary"></i>Edit Warehouse: {{ $wh->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Warehouse Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $wh->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Warehouse Code <span class="text-danger">*</span></label>
                                                    <input type="text" name="code" class="form-control" value="{{ $wh->code }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Phone</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $wh->phone }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Email</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $wh->email }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Address</label>
                                                    <input type="text" name="address" class="form-control" value="{{ $wh->address }}">
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="is_primary" id="isPrimaryWh{{ $wh->id }}" value="1" @checked($wh->is_primary)>
                                                    <label class="form-check-label" for="isPrimaryWh{{ $wh->id }}">Primary Warehouse</label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveWh{{ $wh->id }}" value="1" @checked($wh->is_active)>
                                                    <label class="form-check-label" for="isActiveWh{{ $wh->id }}">Active</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Warehouse</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No warehouses configured yet. Click "Add Warehouse" to create one.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($warehouses->hasPages())
                <div class="mt-3">
                    {{ $warehouses->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Warehouse Modal -->
<div class="modal fade" id="createWarehouseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.pharma.warehouse.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-building me-1 text-primary"></i>Add New Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Warehouse Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Kathmandu Central Depot" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Warehouse Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. WH-KTM" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="01-XXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="warehouse@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Street, City, Province">
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_primary" id="isPrimaryWhNew" value="1">
                        <label class="form-check-label" for="isPrimaryWhNew">Set as Primary Warehouse</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveWhNew" value="1" checked>
                        <label class="form-check-label" for="isActiveWhNew">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Warehouse</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
