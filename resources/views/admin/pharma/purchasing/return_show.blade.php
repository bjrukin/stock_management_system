@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-danger m-0">
                        <i class="bi bi-arrow-return-left me-2"></i>Purchase Return #{{ $return->return_number }}
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.pharma.purchase-returns') }}">Purchase Returns</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $return->return_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.purchase-returns') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Returns
                    </a>
                    @if($return->status == 'draft')
                    <form action="{{ route('admin.pharma.purchase-return.post', $return->id) }}" method="POST" onsubmit="return confirm('Post Purchase Return #{{ $return->return_number }}? This will deduct warehouse stock and record a supplier debit note.')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="bi bi-arrow-return-left me-1"></i>Post Return (Deduct Stock)
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Summary Information Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Supplier</div>
                        <h5 class="fw-bold text-dark mt-1 mb-1">{{ $return->supplier->name }}</h5>
                        <small class="text-muted">Tel: {{ $return->supplier->phone }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Source Warehouse</div>
                        <h5 class="fw-bold text-dark mt-1 mb-1">{{ $return->warehouse->name }}</h5>
                        <small class="text-muted">GRN Ref: {{ $return->goodsReceipt->grn_number ?? 'Direct Return' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Return Date</div>
                        <h5 class="fw-bold text-dark mt-1 mb-0">{{ $return->return_date_ad }}</h5>
                        <small class="text-muted">BS: {{ $return->return_date_bs }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Return Status</div>
                        <div class="mt-1">
                            <span class="badge fs-6 @if($return->status == 'posted') bg-success @elseif($return->status == 'draft') bg-warning @else bg-danger @endif">
                                {{ strtoupper($return->status) }}
                            </span>
                        </div>
                        @if($return->reason)
                        <small class="text-muted d-block mt-2"><strong>Reason:</strong> {{ $return->reason }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title m-0 fw-bold text-danger"><i class="bi bi-box-arrow-right me-2"></i>Returned Batches</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Batch #</th>
                                <th>Location</th>
                                <th class="text-end">Returned Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Tax</th>
                                <th class="text-end">Line Total (NPR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($return->lines as $idx => $line)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $line->product->name }}</div>
                                    <small class="text-muted">{{ $line->product->code }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark font-monospace border">{{ $line->batch->batch_number ?? '—' }}</span></td>
                                <td>{{ $line->location->code ?? '—' }}</td>
                                <td class="text-end fw-bold text-danger">-{{ number_format($line->returned_qty, 2) }} {{ $line->unit->abbreviation ?? '' }}</td>
                                <td class="text-end">NPR {{ number_format($line->unit_price, 2) }}</td>
                                <td class="text-end">NPR {{ number_format($line->tax_amount, 2) }}</td>
                                <td class="text-end fw-bold text-danger">NPR {{ number_format($line->line_total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Financial Summary Footer -->
            <div class="card-footer bg-light p-3">
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Subtotal:</td>
                                <td class="text-end fw-semibold">NPR {{ number_format($return->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tax Amount:</td>
                                <td class="text-end fw-semibold">NPR {{ number_format($return->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fs-5 fw-bold text-dark">Total Debit Note (AP Deducted):</td>
                                <td class="fs-5 text-end fw-bold text-danger">NPR {{ number_format($return->total_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
