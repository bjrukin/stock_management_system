@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0">
                        <i class="bi bi-cart-check me-2"></i>Purchase Order #{{ $order->po_number }}
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.pharma.purchase-orders') }}">Purchase Orders</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $order->po_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.purchase-orders') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to List
                    </a>
                    @if($order->status == 'draft')
                    <form action="{{ route('admin.pharma.purchase-order.approve', $order->id) }}" method="POST" onsubmit="return confirm('Approve this purchase order?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-check-circle me-1"></i>Approve PO
                        </button>
                    </form>
                    @elseif(in_array($order->status, ['approved', 'partially_received']))
                    <a href="{{ route('admin.pharma.goods-receipt.create', ['po_id' => $order->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-box-arrow-in-down me-1"></i>Receive Items (Create GRN)
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
                        <h5 class="fw-bold text-dark mt-1 mb-1">{{ $order->supplier->name }}</h5>
                        <small class="text-muted">{{ $order->supplier->company_name ?? '' }} | Tel: {{ $order->supplier->phone }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Target Warehouse</div>
                        <h5 class="fw-bold text-dark mt-1 mb-1">{{ $order->warehouse->name }}</h5>
                        <small class="text-muted">Code: {{ $order->warehouse->code }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Order Date</div>
                        <h5 class="fw-bold text-dark mt-1 mb-0">{{ $order->order_date_ad }}</h5>
                        <small class="text-muted">BS: {{ $order->order_date_bs }}</small>
                        @if($order->expected_delivery_date_ad)
                        <div class="small text-muted mt-1">Delivery: {{ $order->expected_delivery_date_ad }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Order Status</div>
                        <div class="mt-1">
                            <span class="badge fs-6 @if($order->status == 'approved') bg-success @elseif($order->status == 'received') bg-info @elseif($order->status == 'partially_received') bg-warning @else bg-secondary @endif">
                                {{ strtoupper($order->status) }}
                            </span>
                        </div>
                        @if($order->notes)
                        <small class="text-muted d-block mt-2">{{ $order->notes }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title m-0 fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Ordered Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product Code & Name</th>
                                <th>Packaging Unit</th>
                                <th class="text-end">Ordered Qty</th>
                                <th class="text-end">Received Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Tax Rate</th>
                                <th class="text-end">Line Total (NPR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->lines as $idx => $line)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $line->product->name }}</div>
                                    <small class="text-muted">{{ $line->product->code }} | {{ $line->product->generic_name }}</small>
                                </td>
                                <td>{{ $line->unit->name ?? 'Base Unit' }}</td>
                                <td class="text-end fw-semibold">{{ number_format($line->ordered_qty, 2) }}</td>
                                <td class="text-end">
                                    <span class="badge @if($line->received_base_qty >= $line->ordered_base_qty) bg-success @elseif($line->received_base_qty > 0) bg-warning @else bg-light text-dark border @endif">
                                        {{ number_format($line->received_base_qty) }} / {{ number_format($line->ordered_base_qty) }}
                                    </span>
                                </td>
                                <td class="text-end">NPR {{ number_format($line->unit_price, 2) }}</td>
                                <td class="text-end text-danger">-{{ number_format($line->discount_pct, 1) }}%</td>
                                <td class="text-end">{{ number_format($line->tax_rate, 1) }}%</td>
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
                                <td class="text-end fw-semibold">NPR {{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Discount:</td>
                                <td class="text-end text-danger fw-semibold">- NPR {{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Taxable:</td>
                                <td class="text-end fw-semibold">NPR {{ number_format($order->taxable_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tax Total:</td>
                                <td class="text-end text-info fw-semibold">+ NPR {{ number_format($order->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fs-5 fw-bold text-dark">Total Amount:</td>
                                <td class="fs-5 text-end fw-bold text-primary">NPR {{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
