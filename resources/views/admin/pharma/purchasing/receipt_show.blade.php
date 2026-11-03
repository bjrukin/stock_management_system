@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0">
                        <i class="bi bi-box-arrow-in-down me-2"></i>Goods Receipt #{{ $receipt->grn_number }}
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.pharma.goods-receipts') }}">Goods Receipts</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $receipt->grn_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.goods-receipts') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Receipts
                    </a>
                    @if($receipt->status == 'draft')
                    <form action="{{ route('admin.pharma.goods-receipt.post', $receipt->id) }}" method="POST" onsubmit="return confirm('Post Goods Receipt #{{ $receipt->grn_number }}? This will activate batches and increment stock.')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-box-arrow-in-down me-1"></i>Post Inward to Stock
                        </button>
                    </form>
                    @elseif($receipt->status == 'posted')
                    <a href="{{ route('admin.pharma.purchase-return.create', ['grn_id' => $receipt->id]) }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-arrow-return-left me-1"></i>Create Purchase Return
                    </a>
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
                        <h5 class="fw-bold text-dark mt-1 mb-1">{{ $receipt->supplier->name }}</h5>
                        <small class="text-muted">Invoice #: {{ $receipt->supplier_invoice_number ?? '—' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Warehouse & PO</div>
                        <h5 class="fw-bold text-dark mt-1 mb-1">{{ $receipt->warehouse->name }}</h5>
                        <small class="text-muted">PO Ref: {{ $receipt->purchaseOrder->po_number ?? 'Direct Receipt' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Receipt Date</div>
                        <h5 class="fw-bold text-dark mt-1 mb-0">{{ $receipt->receipt_date_ad }}</h5>
                        <small class="text-muted">BS: {{ $receipt->receipt_date_bs }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Status</div>
                        <div class="mt-1">
                            <span class="badge fs-6 @if($receipt->status == 'posted') bg-success @elseif($receipt->status == 'draft') bg-warning @else bg-danger @endif">
                                {{ strtoupper($receipt->status) }}
                            </span>
                        </div>
                        @if($receipt->posted_at)
                        <small class="text-muted d-block mt-1">Posted: {{ $receipt->posted_at->format('Y-m-d H:i') }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title m-0 fw-bold"><i class="bi bi-upc-scan me-2 text-primary"></i>Received Batches</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Batch #</th>
                                <th>Expiry Date</th>
                                <th>Shelf Location</th>
                                <th class="text-end">Received Qty</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">MRP</th>
                                <th class="text-end">Selling Price</th>
                                <th class="text-end">Landed Share</th>
                                <th class="text-end">Effective Unit Cost</th>
                                <th class="text-end">Line Total (NPR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipt->lines as $idx => $line)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $line->product->name }}</div>
                                    <small class="text-muted">{{ $line->product->code }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark font-monospace border">{{ $line->batch_number }}</span></td>
                                <td><small class="fw-semibold text-danger">{{ $line->expiry_date_ad }}</small></td>
                                <td>{{ $line->location->code ?? '—' }}</td>
                                <td class="text-end fw-bold text-success">{{ number_format($line->received_qty, 2) }} {{ $line->unit->abbreviation ?? '' }}</td>
                                <td class="text-end">NPR {{ number_format($line->unit_cost, 2) }}</td>
                                <td class="text-end text-muted">NPR {{ number_format($line->mrp, 2) }}</td>
                                <td class="text-end text-primary fw-semibold">NPR {{ number_format($line->selling_price, 2) }}</td>
                                <td class="text-end text-warning">NPR {{ number_format($line->landed_cost_share, 2) }}</td>
                                <td class="text-end fw-semibold">NPR {{ number_format($line->effective_unit_cost, 4) }}</td>
                                <td class="text-end fw-bold">NPR {{ number_format($line->line_total, 2) }}</td>
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
                                <td class="text-end fw-semibold">NPR {{ number_format($receipt->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Discounts:</td>
                                <td class="text-end text-danger fw-semibold">- NPR {{ number_format($receipt->discount_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tax / VAT:</td>
                                <td class="text-end text-info fw-semibold">+ NPR {{ number_format($receipt->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fw-bold text-dark">Supplier Payable (AP):</td>
                                <td class="text-end fw-bold text-success fs-6">NPR {{ number_format($receipt->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">+ Freight & Handling Charges:</td>
                                <td class="text-end text-warning fw-semibold">
                                    + NPR {{ number_format($receipt->freight_amount + $receipt->handling_charges + $receipt->other_charges, 2) }}
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td class="fs-5 fw-bold text-dark">Total Stock Valuation:</td>
                                <td class="fs-5 text-end fw-bold text-primary">NPR {{ number_format($receipt->total_landed_cost, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
