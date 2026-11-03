@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-warning m-0"><i class="bi bi-clock-history me-2"></i>Near-Expiry Batches Report (FEFO Priority)</h4>
                <div class="btn-group">
                    <a href="{{ route('admin.pharma.reports.near-expiry', ['days' => 30]) }}" class="btn btn-sm @if($days == 30) btn-warning @else btn-outline-warning @endif">< 30 Days</a>
                    <a href="{{ route('admin.pharma.reports.near-expiry', ['days' => 60]) }}" class="btn btn-sm @if($days == 60) btn-warning @else btn-outline-warning @endif">< 60 Days</a>
                    <a href="{{ route('admin.pharma.reports.near-expiry', ['days' => 90]) }}" class="btn btn-sm @if($days == 90) btn-warning @else btn-outline-warning @endif">< 90 Days</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="alert alert-warning border-0 small">
                    <i class="bi bi-info-circle me-1"></i> Showing all batches expiring within <strong>{{ $days }} days</strong>. Ensure prompt dispatch under First-Expiry-First-Out (FEFO) rules.
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product Name</th>
                                <th>Batch Number</th>
                                <th>Expiry Date (AD)</th>
                                <th>Expiry Date (BS)</th>
                                <th>Days Remaining</th>
                                <th>Stock On Hand</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $b)
                            <tr class="@if($b['days_remaining'] <= 30) table-danger @else table-warning @endif">
                                <td><strong>{{ $b['product_name'] }}</strong></td>
                                <td class="font-monospace fw-bold">{{ $b['batch_number'] }}</td>
                                <td>{{ $b['expiry_date_ad'] }}</td>
                                <td>{{ $b['expiry_date_bs'] }}</td>
                                <td><span class="badge bg-danger fs-6">{{ $b['days_remaining'] }} days</span></td>
                                <td class="fw-bold">{{ number_format($b['quantity_on_hand'], 0) }}</td>
                                                                @php
                                    // Replicate the accessor logic for the array
                                    $status = 'active';
                                    if (!empty($b['is_recalled'])) {
                                        $status = 'recalled';
                                    } elseif (!empty($b['is_quarantined'])) {
                                        $status = 'quarantined';
                                    } elseif (!empty($b['is_expired'])) {
                                        $status = 'expired';
                                    }
                                @endphp
                                <td>
                                    <span class="badge bg-dark">{{ strtoupper($status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No batches expiring within {{ $days }} days.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
