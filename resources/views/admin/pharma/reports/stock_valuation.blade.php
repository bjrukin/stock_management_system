@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-pie-chart me-2"></i>Stock Valuation Report</h4>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print Report</button>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-primary text-white p-3">
                    <span class="small text-uppercase">Total Inventory Cost Valuation</span>
                    <h2 class="my-1 fw-bold">NPR {{ number_format($report['total_cost_value'], 2) }}</h2>
                    <small>Valued at landed acquisition cost</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-info text-white p-3">
                    <span class="small text-uppercase">Total MRP Potential Value</span>
                    <h2 class="my-1 fw-bold">NPR {{ number_format($report['total_mrp_value'], 2) }}</h2>
                    <small>Gross retail potential</small>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Batch #</th>
                                <th>Expiry (AD/BS)</th>
                                <th>Warehouse</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Total Cost (NPR)</th>
                                <th>Total MRP (NPR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['rows'] as $item)
                            <tr>
                                <td class="fw-bold">{{ $item['product_code'] }}</td>
                                <td>{{ $item['product_name'] }}</td>
                                <td class="font-monospace fw-semibold">{{ $item['batch_number'] }}</td>
                                <td><small>{{ $item['expiry_date_ad'] }}<br><span class="text-muted">{{ $item['expiry_date_bs'] }}</span></small></td>
                                <td>{{ $item['warehouse'] }}</td>
                                <td class="fw-bold">{{ number_format($item['quantity_on_hand'], 0) }}</td>
                                <td>NPR {{ number_format($item['unit_cost'], 2) }}</td>
                                <td class="fw-bold text-primary">NPR {{ number_format($item['cost_value'], 2) }}</td>
                                <td class="text-muted">NPR {{ number_format($item['mrp_value'], 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No inventory balances found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
