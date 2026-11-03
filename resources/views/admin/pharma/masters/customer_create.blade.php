@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.customers') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Customers
                    </a>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-person-badge me-2"></i>Add New Customer (Pharmacy / Hospital / Clinic)</h4>
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

        <form action="{{ route('admin.pharma.customer.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-hospital me-1 text-primary"></i>Customer / Facility Information
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Pharmacy / Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Kathmandu Care Pharmacy" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Customer Code</label>
                                    <input type="text" name="code" class="form-control" placeholder="Auto-generated if blank" value="{{ old('code') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Contact Person / Pharmacist</label>
                                    <input type="text" name="contact_person" class="form-control" placeholder="Pharmacist name" value="{{ old('contact_person') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" placeholder="01-XXXXXXX or 98XXXXXXXX" value="{{ old('phone') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="pharmacy@example.com" value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Address / Location</label>
                                    <input type="text" name="address" class="form-control" placeholder="Street, Ward, City" value="{{ old('address') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">PAN / VAT Number</label>
                                    <input type="text" name="pan_vat_number" class="form-control" placeholder="9-digit PAN/VAT" value="{{ old('pan_vat_number') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">DDA License Number</label>
                                    <input type="text" name="dda_license_number" class="form-control" placeholder="DDA Retail / Hospital License" value="{{ old('dda_license_number') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="bi bi-credit-card me-1 text-success"></i>Credit Limits & Controls
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Credit Period (Days)</label>
                                <input type="number" name="credit_period_days" class="form-control" placeholder="15" value="{{ old('credit_period_days', 15) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Credit Limit (NPR)</label>
                                <input type="number" step="0.01" min="0" name="credit_limit" class="form-control" placeholder="50000.00" value="{{ old('credit_limit', '50000.00') }}">
                                <small class="text-muted">Invoices exceeding this will be blocked.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Opening Balance (NPR)</label>
                                <input type="number" step="0.01" min="0" name="opening_balance" class="form-control" placeholder="0.00" value="{{ old('opening_balance', '0.00') }}">
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="credit_stop" id="creditStop" value="1" @checked(old('credit_stop'))>
                                <label class="form-check-label fw-semibold text-danger" for="creditStop">Place on Credit Stop</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" @checked(old('is_active', true))>
                                <label class="form-check-label fw-semibold text-success" for="isActive">Customer is Active</label>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-1"></i>Save Customer
                                </button>
                                <a href="{{ route('admin.pharma.customers') }}" class="btn btn-outline-secondary">
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
