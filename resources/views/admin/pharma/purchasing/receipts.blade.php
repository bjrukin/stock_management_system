@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-box-arrow-in-down me-2"></i>Goods Receipts (GRN) & Inward Batches</h4>
                    <p class="text-muted small mb-0">Inward inventory shipments, batch generation & landed cost distribution</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.goods-receipt.create') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle me-1"></i>Add Goods Receipt (GRN)
                    </a>
                    <a href="{{ route('admin.pharma.purchase-orders') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-cart-check me-1"></i>Purchase Orders
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
                                <th>GRN Number</th>
                                <th>PO Ref</th>
                                <th>Supplier</th>
                                <th>Supplier Bill #</th>
                                <th>Receipt Date (AD/BS)</th>
                                <th>Total Bill Amount</th>
                                <th>Total Landed Cost</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $grn)
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="{{ route('admin.pharma.goods-receipt.show', $grn->id) }}" class="text-decoration-none">
                                        {{ $grn->grn_number }}
                                    </a>
                                </td>
                                <td>
                                    @if($grn->purchaseOrder)
                                    <a href="{{ route('admin.pharma.purchase-order.show', $grn->purchase_order_id) }}" class="badge bg-light text-primary border text-decoration-none">
                                        {{ $grn->purchaseOrder->po_number }}
                                    </a>
                                    @else
                                    <span class="text-muted small">Direct</span>
                                    @endif
                                </td>
                                <td>{{ $grn->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $grn->supplier_invoice_number ?? '—' }}</td>
                                <td><small>{{ $grn->receipt_date_ad }}<br><span class="text-muted">{{ $grn->receipt_date_bs }}</span></small></td>
                                <td class="fw-bold">NPR {{ number_format($grn->total_amount, 2) }}</td>
                                <td>NPR {{ number_format($grn->total_landed_cost, 2) }}</td>
                                <td>
                                    <span class="badge @if($grn->status == 'posted') bg-success @elseif($grn->status == 'draft') bg-warning @else bg-danger @endif">
                                        {{ ucfirst($grn->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.pharma.goods-receipt.show', $grn->id) }}" class="btn btn-outline-secondary" title="View GRN Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($grn->status == 'draft')
                                        <form action="{{ route('admin.pharma.goods-receipt.post', $grn->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Post Goods Receipt #{{ $grn->grn_number }} inward to stock?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Post to Stock">
                                                <i class="bi bi-box-arrow-in-down"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No goods receipts found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $receipts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
