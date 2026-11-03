@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.stock-adjustments') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Adjustments
                    </a>
                    <h4 class="page-title-main text-primary m-0">
                        <i class="bi bi-sliders me-2"></i>Stock Adjustment #{{ $adjustment->adjustment_number }}
                    </h4>
                </div>
                <div>
                    @if($adjustment->status === 'draft')
                    <form action="{{ route('admin.pharma.stock-adjustment.approve', $adjustment->id) }}" method="POST" onsubmit="return confirm('Approve this adjustment? Inventory balances will be updated with the variances.');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-shield-check me-1"></i>Approve & Post Adjustment (Checker)
                        </button>
                    </form>
                    @endif
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

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-info-circle me-1 text-primary"></i>Adjustment Information</span>
                        <span class="badge @if($adjustment->status === 'approved') bg-success @elseif($adjustment->status === 'rejected') bg-danger @else bg-warning text-dark @endif fs-6">
                            {{ strtoupper($adjustment->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="text-muted small">Warehouse:</label>
                                <div class="fw-bold fs-6">{{ $adjustment->warehouse->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Adjustment Type:</label>
                                <div class="fw-bold fs-6 text-primary">{{ str_replace('_', ' ', $adjustment->type) }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Date (AD/BS):</label>
                                <div class="fw-semibold">{{ $adjustment->adjustment_date_ad }} &bull; <span class="text-muted">{{ $adjustment->adjustment_date_bs }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Created By (Maker):</label>
                                <div>{{ $adjustment->createdBy->name ?? 'Admin #' . $adjustment->created_by }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Approved By (Checker):</label>
                                <div>{{ $adjustment->approvedBy->name ?? ($adjustment->approved_by ? 'Admin #' . $adjustment->approved_by : 'Pending Approval') }}</div>
                            </div>
                            @if($adjustment->reason)
                            <div class="col-12">
                                <label class="text-muted small">Reason / Remarks:</label>
                                <div class="text-muted">{{ $adjustment->reason }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold">
                        <i class="bi bi-list-check me-1 text-primary"></i>Adjusted Items & Variances
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product / Medicine</th>
                                        <th>Batch #</th>
                                        <th class="text-end">Book Qty</th>
                                        <th class="text-end">Counted Qty</th>
                                        <th class="text-center">Variance Qty</th>
                                        <th class="text-end">Variance Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($adjustment->lines as $idx => $line)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <strong>{{ $line->product->name ?? 'Product #' . $line->product_id }}</strong><br>
                                            <small class="text-muted">{{ $line->product->code ?? '' }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $line->batch->batch_number ?? 'N/A' }}</span></td>
                                        <td class="text-end">{{ number_format($line->book_quantity, 0) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($line->counted_quantity, 0) }}</td>
                                        <td class="text-center">
                                            @if($line->variance_quantity > 0)
                                            <span class="badge bg-success">+{{ $line->variance_quantity }}</span>
                                            @elseif($line->variance_quantity < 0)
                                            <span class="badge bg-danger">{{ $line->variance_quantity }}</span>
                                            @else
                                            <span class="badge bg-secondary">0</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">
                                            NPR {{ number_format($line->variance_value, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold text-dark">
                        <i class="bi bi-calculator me-1 text-success"></i>Total Variance Valuation
                    </div>
                    <div class="card-body">
                        <div class="text-center py-3">
                            <div class="text-muted small text-uppercase">Net Stock Valuation Impact</div>
                            <div class="fs-3 fw-bold @if($adjustment->total_variance_value >= 0) text-success @else text-danger @endif">
                                NPR {{ number_format($adjustment->total_variance_value, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
