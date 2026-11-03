@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-journal-text me-2"></i>Stock Movements (Append-Only Ledger)</h4>
                    <p class="text-muted small mb-0">Complete audit trail of all Inflow & Outflow inventory operations</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="reconcileStock()"><i class="bi bi-arrow-repeat me-1"></i>Run Reconciliation</button>
                </div>
            </div>
        </div>

        <!-- 6-Channel Stock Flow Filter Bar (Conforming to Authoritative Architecture) -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-2 px-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-1">
                        <span class="small fw-bold text-muted me-2"><i class="bi bi-funnel me-1"></i>Stock Flow Channels:</span>
                        
                        <a href="{{ route('admin.pharma.stock-movements') }}" class="btn btn-sm {{ $currentFlow == 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">
                            All
                        </a>
                        
                        <!-- Inflow Group -->
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-2">INFLOWS:</span>
                        <a href="{{ route('admin.pharma.stock-movements', ['flow_type' => 'purchase']) }}" class="btn btn-sm {{ $currentFlow == 'purchase' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-truck me-1"></i>Purchase <span class="badge bg-white text-primary ms-1">+{{ $stockFlow['inflows']['purchase']['quantity'] }}</span>
                        </a>
                        <a href="{{ route('admin.pharma.stock-movements', ['flow_type' => 'sell_return']) }}" class="btn btn-sm {{ $currentFlow == 'sell_return' ? 'btn-info text-white' : 'btn-outline-info' }}">
                            <i class="bi bi-arrow-return-left me-1"></i>Sell Return <span class="badge bg-white text-info ms-1">+{{ $stockFlow['inflows']['sell_return']['quantity'] }}</span>
                        </a>

                        <!-- Outflow Group -->
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-2">OUTFLOWS:</span>
                        <a href="{{ route('admin.pharma.stock-movements', ['flow_type' => 'sell']) }}" class="btn btn-sm {{ $currentFlow == 'sell' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-receipt me-1"></i>Sell <span class="badge bg-white text-primary ms-1">-{{ $stockFlow['outflows']['sell']['quantity'] }}</span>
                        </a>
                        <a href="{{ route('admin.pharma.stock-movements', ['flow_type' => 'purchase_return']) }}" class="btn btn-sm {{ $currentFlow == 'purchase_return' ? 'btn-dark' : 'btn-outline-dark' }}">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Purchase Return <span class="badge bg-white text-dark ms-1">-{{ $stockFlow['outflows']['purchase_return']['quantity'] }}</span>
                        </a>
                        <a href="{{ route('admin.pharma.stock-movements', ['flow_type' => 'expired_item']) }}" class="btn btn-sm {{ $currentFlow == 'expired_item' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                            <i class="bi bi-hourglass-bottom me-1"></i>Expired Item <span class="badge bg-white text-warning ms-1">-{{ $stockFlow['outflows']['expired_item']['quantity'] }}</span>
                        </a>
                        <a href="{{ route('admin.pharma.stock-movements', ['flow_type' => 'damage']) }}" class="btn btn-sm {{ $currentFlow == 'damage' ? 'btn-danger' : 'btn-outline-danger' }}">
                            <i class="bi bi-heartbreak me-1"></i>Damage <span class="badge bg-white text-danger ms-1">-{{ $stockFlow['outflows']['damage']['quantity'] }}</span>
                        </a>
                    </div>

                    <div class="small fw-semibold text-muted">
                        Total Ledger On-Hand: <span class="text-primary fw-bold">{{ number_format($stockFlow['stock']['on_hand_quantity']) }}</span> units
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ledger ID</th>
                                <th>Timestamp</th>
                                <th>Flow Direction</th>
                                <th>Channel Type</th>
                                <th>Product</th>
                                <th>Batch #</th>
                                <th>Warehouse</th>
                                <th>Movement Qty</th>
                                <th>Unit Cost</th>
                                <th>Reference Doc</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $m)
                            <tr>
                                <td class="fw-bold font-monospace">#{{ $m->id }}</td>
                                <td><small>{{ $m->created_at->format('Y-m-d H:i') }}</small></td>
                                <td>
                                    @if($m->quantity > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-arrow-down-left me-1"></i>INFLOW</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="bi bi-arrow-up-right me-1"></i>OUTFLOW</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($m->movement_type) {
                                            'PURCHASE_RECEIPT' => 'bg-primary',
                                            'SALE_RETURN' => 'bg-info',
                                            'SALE_DISPATCH' => 'bg-primary',
                                            'PURCHASE_RETURN' => 'bg-dark',
                                            'EXPIRED_DISPOSAL' => 'bg-warning text-dark',
                                            'DAMAGE_WRITE_OFF' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $m->flow_label ?? strtoupper(str_replace('_', ' ', $m->movement_type)) }}
                                    </span>
                                </td>
                                <td>{{ $m->product->name ?? 'N/A' }}</td>
                                <td class="font-monospace fw-semibold">{{ $m->batch->batch_number ?? '—' }}</td>
                                <td>{{ $m->warehouse->name ?? 'N/A' }}</td>
                                <td class="fw-bold fs-6 @if($m->quantity > 0) text-success @else text-danger @endif">
                                    {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity, 0) }}
                                </td>
                                <td>NPR {{ number_format($m->unit_cost, 2) }}</td>
                                <td><small class="text-muted">{{ class_basename($m->reference_type) }} #{{ $m->reference_id }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No stock movements recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('footer-scripts')
<script>
function reconcileStock() {
    if (!confirm('Run automated stock reconciliation against ledger?')) return;
    fetch("{{ route('admin.pharma.reconcile') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message);
        location.reload();
    })
    .catch(err => alert('Reconciliation failed. Check server logs.'));
}
</script>
@endpush
@endsection
