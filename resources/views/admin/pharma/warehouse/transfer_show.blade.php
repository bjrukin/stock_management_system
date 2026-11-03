@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.stock-transfers') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Transfers
                    </a>
                    <h4 class="page-title-main text-primary m-0">
                        <i class="bi bi-arrow-left-right me-2"></i>Stock Transfer #{{ $transfer->transfer_number }}
                    </h4>
                </div>
                <div class="d-flex gap-2">
                    @if($transfer->status === 'draft')
                    <form action="{{ route('admin.pharma.stock-transfer.dispatch', $transfer->id) }}" method="POST" onsubmit="return confirm('Dispatch this transfer from origin warehouse?');">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-truck me-1"></i>Dispatch Transfer
                        </button>
                    </form>
                    @elseif($transfer->status === 'dispatched')
                    <form action="{{ route('admin.pharma.stock-transfer.receive', $transfer->id) }}" method="POST" onsubmit="return confirm('Confirm receipt of these items at destination warehouse?');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-box-arrow-in-down me-1"></i>Receive Transfer at Destination
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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-geo-alt me-1 text-primary"></i>Transfer Route & Status</span>
                <span class="badge @if($transfer->status === 'received') bg-success @elseif($transfer->status === 'dispatched') bg-info @else bg-warning text-dark @endif fs-6">
                    {{ strtoupper($transfer->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="text-muted small">Origin Warehouse:</label>
                        <div class="fw-bold fs-6">{{ $transfer->fromWarehouse->name ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $transfer->fromWarehouse->code ?? '' }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Destination Warehouse:</label>
                        <div class="fw-bold fs-6">{{ $transfer->toWarehouse->name ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $transfer->toWarehouse->code ?? '' }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Transfer Date (AD/BS):</label>
                        <div class="fw-semibold">{{ $transfer->transfer_date_ad }} &bull; <span class="text-muted">{{ $transfer->transfer_date_bs }}</span></div>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Total Line Items:</label>
                        <div class="fw-semibold">{{ $transfer->total_items }} item(s)</div>
                    </div>
                    @if($transfer->notes)
                    <div class="col-12">
                        <label class="text-muted small">Notes / Purpose:</label>
                        <div class="text-muted">{{ $transfer->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-box-seam me-1 text-primary"></i>Transfer Items
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product / Medicine</th>
                                <th>Batch #</th>
                                <th>Source Bin / Rack</th>
                                <th>Dest Bin / Rack</th>
                                <th class="text-end">Transfer Qty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfer->lines as $idx => $line)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ $line->product->name ?? 'Product #' . $line->product_id }}</strong><br>
                                    <small class="text-muted">{{ $line->product->code ?? '' }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $line->batch->batch_number ?? 'N/A' }}</span></td>
                                <td>{{ $line->fromLocation->name ?? 'Default' }}</td>
                                <td>{{ $line->toLocation->name ?? 'Receiving Area' }}</td>
                                <td class="text-end fw-bold fs-6">{{ number_format($line->quantity, 0) }}</td>
                                <td>
                                    <span class="badge @if($line->status === 'received') bg-success @elseif($line->status === 'in_transit') bg-info @else bg-secondary @endif">
                                        {{ ucfirst($line->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
