@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.suppliers') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Suppliers
                    </a>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-truck me-2"></i>Add New Supplier</h4>
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

        <form action="{{ route('admin.pharma.supplier.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-building me-1 text-primary"></i>Company / Vendor Information
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Supplier / Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Nepal Pharmaceuticals Pvt. Ltd." value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Supplier Code</label>
                                    <input type="text" name="code" class="form-control" placeholder="Auto-generated if blank" value="{{ old('code') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Contact Person</label>
                                    <input type="text" name="contact_person" class="form-control" placeholder="Key contact person" value="{{ old('contact_person') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" placeholder="01-XXXXXXX or 98XXXXXXXX" value="{{ old('phone') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="vendor@example.com" value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Address</label>
                                    <input type="text" name="address" class="form-control" placeholder="Street, City, Province" value="{{ old('address') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">PAN / VAT Number</label>
                                    <input type="text" name="pan_vat_number" class="form-control" placeholder="9-digit PAN/VAT" value="{{ old('pan_vat_number') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">DDA License Number</label>
                                    <input type="text" name="dda_license_number" class="form-control" placeholder="DDA Wholesale License" value="{{ old('dda_license_number') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-cash-stack me-1 text-success"></i>Credit & Terms
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Credit Period (Days)</label>
                                <input type="number" name="credit_period_days" class="form-control" placeholder="30" value="{{ old('credit_period_days', 30) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Credit Limit (NPR)</label>
                                <input type="number" step="0.01" min="0" name="credit_limit" class="form-control" placeholder="0.00" value="{{ old('credit_limit', '0.00') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Opening Balance (NPR)</label>
                                <input type="number" step="0.01" min="0" name="opening_balance" class="form-control" placeholder="0.00" value="{{ old('opening_balance', '0.00') }}">
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" @checked(old('is_active', true))>
                                <label class="form-check-label fw-semibold text-success" for="isActive">Supplier is Active</label>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-1"></i>Save Supplier
                                </button>
                                <a href="{{ route('admin.pharma.suppliers') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
