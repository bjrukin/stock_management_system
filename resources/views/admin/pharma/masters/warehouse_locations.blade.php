@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.warehouses') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Warehouses
                    </a>
                    <h4 class="page-title-main text-primary m-0">
                        <i class="bi bi-geo-alt me-2"></i>
                        @if($warehouse)
                            Locations: {{ $warehouse->name }} <span class="badge bg-primary fs-6 font-monospace ms-1">{{ $warehouse->code }}</span>
                        @else
                            Warehouse Storage Locations
                        @endif
                    </h4>
                    <small class="text-muted">Manage racks, bins, cold rooms, receiving bays, and quarantine zones</small>
                </div>
                <div class="d-flex gap-2">
                    @if($warehouse)
                    <a href="{{ route('admin.pharma.warehouse.locations.all') }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-list-ul me-1"></i>View All Locations
                    </a>
                    @endif
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createLocationModal">
                        <i class="bi bi-plus-circle me-1"></i>Add New Location
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
                <form method="GET" action="{{ $warehouse ? route('admin.pharma.warehouse.locations', $warehouse->id) : route('admin.pharma.warehouse.locations.all') }}" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, code, condition..." value="{{ request('search') }}">
                    </div>
                    @if(!$warehouse)
                    <div class="col-md-3">
                        <select name="warehouse_id" class="form-select">
                            <option value="">-- All Warehouses --</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->name }} ({{ $wh->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">-- All Location Types --</option>
                            <option value="storage" @selected(request('type') == 'storage')>Storage Rack / Shelf / Bin</option>
                            <option value="cold_storage" @selected(request('type') == 'cold_storage')>Cold Storage (2-8°C)</option>
                            <option value="receiving" @selected(request('type') == 'receiving')>Receiving Bay</option>
                            <option value="dispatch" @selected(request('type') == 'dispatch')>Dispatch Area</option>
                            <option value="quarantine" @selected(request('type') == 'quarantine')>Quarantine Area</option>
                            <option value="damaged" @selected(request('type') == 'damaged')>Damaged Goods Area</option>
                            <option value="expired_disposal" @selected(request('type') == 'expired_disposal')>Expired / Disposal Zone</option>
                            <option value="returns" @selected(request('type') == 'returns')>Returns Area</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                        <a href="{{ $warehouse ? route('admin.pharma.warehouse.locations', $warehouse->id) : route('admin.pharma.warehouse.locations.all') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 12%;">Code</th>
                                <th style="width: 22%;">Location / Bin Name</th>
                                <th style="width: 18%;">Warehouse</th>
                                <th style="width: 12%;">Type</th>
                                <th style="width: 13%;">Storage Condition</th>
                                <th style="width: 9%;">Capacity</th>
                                <th style="width: 12%;">Attributes</th>
                                <th style="width: 8%;">Status</th>
                                <th style="width: 10%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locations as $loc)
                            @php
                                $typeBadges = [
                                    'storage' => 'bg-primary-subtle text-primary border',
                                    'cold_storage' => 'bg-info-subtle text-info border',
                                    'receiving' => 'bg-success-subtle text-success border',
                                    'dispatch' => 'bg-dark-subtle text-dark border',
                                    'quarantine' => 'bg-warning-subtle text-warning border',
                                    'damaged' => 'bg-danger-subtle text-danger border',
                                    'expired_disposal' => 'bg-danger text-white',
                                    'returns' => 'bg-secondary-subtle text-secondary border',
                                ];
                                $typeLabels = [
                                    'storage' => 'Storage Rack',
                                    'cold_storage' => 'Cold Storage',
                                    'receiving' => 'Receiving Bay',
                                    'dispatch' => 'Dispatch Area',
                                    'quarantine' => 'Quarantine Area',
                                    'damaged' => 'Damaged Goods',
                                    'expired_disposal' => 'Expired / Disposal',
                                    'returns' => 'Returns Area',
                                ];
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark font-monospace border fs-6">{{ $loc->code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $loc->name }}</strong>
                                </td>
                                <td>
                                    @if($loc->warehouse)
                                    <a href="{{ route('admin.pharma.warehouse.locations', $loc->warehouse->id) }}" class="text-decoration-none fw-semibold">
                                        {{ $loc->warehouse->name }}
                                    </a>
                                    <span class="badge bg-secondary ms-1">{{ $loc->warehouse->code }}</span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $typeBadges[$loc->type] ?? 'bg-light text-dark border' }}">
                                        {{ $typeLabels[$loc->type] ?? ucfirst($loc->type) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-thermometer-half me-1"></i>{{ $loc->storage_condition ?? 'Ambient' }}
                                    </small>
                                </td>
                                <td>
                                    <small>{{ $loc->capacity ? number_format($loc->capacity, 0) . ' units' : '—' }}</small>
                                </td>
                                <td>
                                    @if($loc->is_sellable)
                                    <span class="badge bg-info-subtle text-info border">Sellable</span>
                                    @endif
                                    @if($loc->is_available)
                                    <span class="badge bg-success-subtle text-success border">Available</span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger border">Locked</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge @if($loc->is_active) bg-success @else bg-secondary @endif">
                                        {{ $loc->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editLocationModal{{ $loc->id }}" title="Edit Location">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.pharma.warehouse.location.delete', $loc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete location \'{{ $loc->name }}\'?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Location">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Location Modal -->
                            <div class="modal fade" id="editLocationModal{{ $loc->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.pharma.warehouse.location.update', $loc->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="bi bi-geo-alt me-1 text-primary"></i>Edit Location: {{ $loc->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Warehouse <span class="text-danger">*</span></label>
                                                    <select name="warehouse_id" class="form-select" required>
                                                        @foreach($warehouses as $wh)
                                                        <option value="{{ $wh->id }}" @selected($loc->warehouse_id == $wh->id)>{{ $wh->name }} ({{ $wh->code }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Location / Bin Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $loc->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Location Code <span class="text-danger">*</span></label>
                                                    <input type="text" name="code" class="form-control" value="{{ $loc->code }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Location Type</label>
                                                    <select name="type" class="form-select">
                                                        <option value="storage" @selected($loc->type == 'storage')>Storage Rack / Shelf / Bin</option>
                                                        <option value="cold_storage" @selected($loc->type == 'cold_storage')>Cold Storage (2-8°C)</option>
                                                        <option value="receiving" @selected($loc->type == 'receiving')>Receiving Bay</option>
                                                        <option value="dispatch" @selected($loc->type == 'dispatch')>Dispatch Area</option>
                                                        <option value="quarantine" @selected($loc->type == 'quarantine')>Quarantine Area</option>
                                                        <option value="damaged" @selected($loc->type == 'damaged')>Damaged Goods Area</option>
                                                        <option value="expired_disposal" @selected($loc->type == 'expired_disposal')>Expired / Disposal Zone</option>
                                                        <option value="returns" @selected($loc->type == 'returns')>Returns Area</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Storage Condition</label>
                                                    <input type="text" name="storage_condition" class="form-control" value="{{ $loc->storage_condition }}" placeholder="Ambient, Cold chain (2-8°C), Cool below 25°C">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Capacity (Units / Boxes)</label>
                                                    <input type="number" step="0.01" min="0" name="capacity" class="form-control" value="{{ $loc->capacity }}" placeholder="Optional capacity limit">
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="is_sellable" id="isSellableLoc{{ $loc->id }}" value="1" @checked($loc->is_sellable)>
                                                    <label class="form-check-label" for="isSellableLoc{{ $loc->id }}">Is Sellable (Pickable for Sales)</label>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="is_available" id="isAvailLoc{{ $loc->id }}" value="1" @checked($loc->is_available)>
                                                    <label class="form-check-label" for="isAvailLoc{{ $loc->id }}">Is Available (Accepts Inward Stock)</label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveLoc{{ $loc->id }}" value="1" @checked($loc->is_active)>
                                                    <label class="form-check-label" for="isActiveLoc{{ $loc->id }}">Active</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Location</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No warehouse locations found matching criteria. Click "Add New Location" to create one.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($locations->hasPages())
                <div class="mt-3">
                    {{ $locations->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Location Modal -->
<div class="modal fade" id="createLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.pharma.warehouse.location.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-geo-alt me-1 text-primary"></i>Add New Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Warehouse <span class="text-danger">*</span></label>
                        <select name="warehouse_id" class="form-select" required>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" @selected(($warehouse && $warehouse->id == $wh->id) || old('warehouse_id') == $wh->id)>
                                {{ $wh->name }} ({{ $wh->code }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Location / Bin Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Rack A - Shelf 2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Location Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. RACK-A-02" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Location Type</label>
                        <select name="type" class="form-select">
                            <option value="storage">Storage Rack / Shelf / Bin</option>
                            <option value="cold_storage">Cold Storage (2-8°C)</option>
                            <option value="receiving">Receiving Bay</option>
                            <option value="dispatch">Dispatch Area</option>
                            <option value="quarantine">Quarantine Area</option>
                            <option value="damaged">Damaged Goods Area</option>
                            <option value="expired_disposal">Expired / Disposal Zone</option>
                            <option value="returns">Returns Area</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Storage Condition</label>
                        <input type="text" name="storage_condition" class="form-control" placeholder="Ambient (15-25°C), Cold chain (2-8°C)...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Capacity (Units / Boxes)</label>
                        <input type="number" step="0.01" min="0" name="capacity" class="form-control" placeholder="Optional capacity limit">
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_sellable" id="isSellableLocNew" value="1" checked>
                        <label class="form-check-label" for="isSellableLocNew">Is Sellable (Pickable for Sales)</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_available" id="isAvailLocNew" value="1" checked>
                        <label class="form-check-label" for="isAvailLocNew">Is Available (Accepts Inward Stock)</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveLocNew" value="1" checked>
                        <label class="form-check-label" for="isActiveLocNew">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Location</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
