@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-arrow-return-left me-2"></i>Purchase Returns (Debit Notes)</h4>
                    <p class="text-muted small mb-0">Supplier returns, damaged goods, recalls & credit memo management</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.purchase-return.create') }}" class="btn btn-sm btn-danger">
                        <i class="bi bi-plus-circle me-1"></i>Create Purchase Return
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
                                <th>Return Number</th>
                                <th>GRN Ref</th>
                                <th>Supplier</th>
                                <th>Warehouse</th>
                                <th>Return Date (AD/BS)</th>
                                <th>Debit Amount</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $ret)
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="{{ route('admin.pharma.purchase-return.show', $ret->id) }}" class="text-decoration-none text-danger">
                                        {{ $ret->return_number }}
                                    </a>
                                </td>
                                <td>
                                    @if($ret->goodsReceipt)
                                    <a href="{{ route('admin.pharma.goods-receipt.show', $ret->goods_receipt_id) }}" class="badge bg-light text-primary border text-decoration-none">
                                        {{ $ret->goodsReceipt->grn_number }}
                                    </a>
                                    @else
                                    <span class="text-muted small">Direct</span>
                                    @endif
                                </td>
                                <td>{{ $ret->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $ret->warehouse->name ?? 'N/A' }}</td>
                                <td><small>{{ $ret->return_date_ad }}<br><span class="text-muted">{{ $ret->return_date_bs }}</span></small></td>
                                <td class="fw-bold text-danger">NPR {{ number_format($ret->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge @if($ret->status == 'posted') bg-success @elseif($ret->status == 'draft') bg-warning @else bg-danger @endif">
                                        {{ ucfirst($ret->status) }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ Str::limit($ret->reason ?? '—', 30) }}</small></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.pharma.purchase-return.show', $ret->id) }}" class="btn btn-outline-secondary" title="View Return Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($ret->status == 'draft')
                                        <form action="{{ route('admin.pharma.purchase-return.post', $ret->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Post Purchase Return #{{ $ret->return_number }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="Post Return">
                                                <i class="bi bi-arrow-return-left"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No purchase returns recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $returns->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
