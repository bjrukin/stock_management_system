@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-upc-scan me-2"></i>Batch Register & Expiry Tracker (FEFO)</h4>
                <a href="{{ route('admin.pharma.reports.near-expiry') }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-clock-history me-1"></i>Near-Expiry Report</a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.pharma.batches') }}" class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search by batch # or product name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- All Statuses --</option>
                            <option value="active" @selected(request('status') == 'active')>Active</option>
                            <option value="quarantined" @selected(request('status') == 'quarantined')>Quarantined</option>
                            <option value="recalled" @selected(request('status') == 'recalled')>Recalled</option>
                            <option value="expired" @selected(request('status') == 'expired')>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                        <a href="{{ route('admin.pharma.batches') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Batch #</th>
                                <th>Product Name</th>
                                <th>Mfg Date (AD/BS)</th>
                                <th>Expiry Date (AD/BS)</th>
                                <th>Cost Price</th>
                                <th>MRP</th>
                                <th>Stock On Hand</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $b)
                            @php
                                $stock = $b->inventoryBalances->sum('quantity_on_hand');
                                $isNearExpiry = \Carbon\Carbon::parse($b->expiry_date_ad)->diffInDays(now(), false) > -90;
                            @endphp
                            <tr @if($b->status != 'active') class="table-danger" @elseif($isNearExpiry) class="table-warning" @endif>
                                <td class="fw-bold font-monospace">{{ $b->batch_number }}</td>
                                <td><strong>{{ $b->product->name ?? 'N/A' }}</strong></td>
                                <td><small>{{ $b->manufacturing_date_ad }}<br><span class="text-muted">{{ $b->manufacturing_date_bs }}</span></small></td>
                                <td>
                                    <strong>{{ $b->expiry_date_ad }}</strong><br>
                                    <small class="text-muted">{{ $b->expiry_date_bs }}</small>
                                </td>
                                <td>NPR {{ number_format($b->cost_price, 2) }}</td>
                                <td class="fw-bold">NPR {{ number_format($b->mrp, 2) }}</td>
                                <td>
                                    <span class="badge @if($stock > 0) bg-success @else bg-secondary @endif fs-6">
                                        {{ number_format($stock, 0) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge @if($b->status == 'active') bg-success @elseif($b->status == 'quarantined') bg-warning @else bg-danger @endif">
                                        {{ ucfirst($b->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No batches found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $batches->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
