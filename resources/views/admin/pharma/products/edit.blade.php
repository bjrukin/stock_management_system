@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.products') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Products
                    </a>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-pencil-square me-2"></i>Edit Product: {{ $product->name }}</h4>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong><i class="bi bi-exclamation-triangle me-1"></i>Validation Errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('admin.pharma.product.update', $product->id) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <!-- General Information Card -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-info-circle me-1 text-primary"></i>Basic Product Details
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Item / SKU Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" value="{{ old('code', $product->code) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Generic Name / Composition <span class="text-danger">*</span></label>
                                    <input type="text" name="generic_name" class="form-control" value="{{ old('generic_name', $product->generic_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Brand / Trade Name</label>
                                    <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Dosage Form</label>
                                    <input type="text" name="dosage_form" class="form-control" value="{{ old('dosage_form', $product->dosage_form) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Strength</label>
                                    <input type="text" name="strength" class="form-control" value="{{ old('strength', $product->strength) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">DDA Registration No.</label>
                                    <input type="text" name="dda_registration_number" class="form-control" value="{{ old('dda_registration_number', $product->dda_registration_number) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Manufacturer</label>
                                    <select name="manufacturer_id" class="form-select">
                                        <option value="">-- Select Manufacturer --</option>
                                        @foreach($manufacturers as $mfg)
                                        <option value="{{ $mfg->id }}" @selected(old('manufacturer_id', $product->manufacturer_id) == $mfg->id)>{{ $mfg->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Base Dispensing Unit <span class="text-danger">*</span></label>
                                    <select name="base_unit_id" class="form-select" required>
                                        <option value="">-- Select Base Unit --</option>
                                        @foreach($units as $u)
                                        <option value="{{ $u->id }}" @selected(old('base_unit_id', $product->base_unit_id) == $u->id)>{{ $u->name }} ({{ $u->abbreviation }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Storage Condition</label>
                                    <input type="text" name="storage_requirement" class="form-control" placeholder="e.g. Store below 25°C, Protect from light, Cold chain (2-8°C)" value="{{ old('storage_requirement', $product->storage_requirement) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Notes / Description</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes, special handling or clinical instructions...">{{ old('notes', $product->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    <!-- Inventory Controls Card -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-box-seam me-1 text-info"></i>Stock Thresholds
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Reorder Alert Level</label>
                                <input type="number" name="reorder_level" class="form-control" value="{{ old('reorder_level', $product->reorder_level) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Minimum Safety Stock</label>
                                <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Maximum Stock Cap</label>
                                <input type="number" name="max_level" class="form-control" value="{{ old('max_level', $product->max_level) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Regulatory & Compliance Flags -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-shield-check me-1 text-danger"></i>Regulatory Flags
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="prescription_required" id="rxRequired" value="1" @checked(old('prescription_required', $product->prescription_required))>
                                <label class="form-check-label fw-semibold" for="rxRequired">Prescription Required (Rx Only)</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="narcotic_psychotropic" id="narcotic" value="1" @checked(old('narcotic_psychotropic', $product->narcotic_psychotropic))>
                                <label class="form-check-label fw-semibold text-danger" for="narcotic">Narcotic / Psychotropic</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="allow_fractional_base" id="allowFrac" value="1" @checked(old('allow_fractional_base', $product->allow_fractional_base))>
                                <label class="form-check-label fw-semibold" for="allowFrac">Allow Fractional Quantities</label>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" @checked(old('is_active', $product->is_active))>
                                <label class="form-check-label fw-semibold text-success" for="isActive">Product is Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-save me-1"></i>Update Product
                            </button>
                            <a href="{{ route('admin.pharma.products') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
