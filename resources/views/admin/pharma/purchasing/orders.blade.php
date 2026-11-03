@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-cart-check me-2"></i>Purchase Orders</h4>
                    <p class="text-muted small mb-0">Procurement & supplier order management</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.purchase-order.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create Purchase Order
                    </a>
                    <a href="{{ route('admin.pharma.goods-receipts') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-box-arrow-in-down me-1"></i>Goods Receipts (GRN)
                    </a>
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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Warehouse</th>
                                <th>Date (AD/BS)</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $po)
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="{{ route('admin.pharma.purchase-order.show', $po->id) }}" class="text-decoration-none">
                                        {{ $po->po_number }}
                                    </a>
                                </td>
                                <td>{{ $po->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $po->warehouse->name ?? 'N/A' }}</td>
                                <td><small>{{ $po->order_date_ad }}<br><span class="text-muted">{{ $po->order_date_bs }}</span></small></td>
                                <td class="fw-bold">NPR {{ number_format($po->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge @if($po->status == 'approved') bg-success @elseif($po->status == 'received') bg-info @elseif($po->status == 'partially_received') bg-warning @else bg-secondary @endif">
                                        {{ ucfirst($po->status) }}
                                    </span>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $po->lines->count() }} line(s)</span></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.pharma.purchase-order.show', $po->id) }}" class="btn btn-outline-secondary" title="View PO Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($po->status == 'draft')
                                        <form action="{{ route('admin.pharma.purchase-order.approve', $po->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve Purchase Order #{{ $po->po_number }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Approve PO">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        @elseif(in_array($po->status, ['approved', 'partially_received']))
                                        <a href="{{ route('admin.pharma.goods-receipt.create', ['po_id' => $po->id]) }}" class="btn btn-outline-primary" title="Create GRN from this PO">
                                            <i class="bi bi-box-arrow-in-down"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No purchase orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
