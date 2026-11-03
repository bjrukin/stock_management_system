@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-clock-history me-2"></i>Accounts Receivable (AR) & Accounts Payable (AP) Aging</h4>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title m-0 fw-bold text-success"><i class="bi bi-person-check me-2"></i>Customer Receivables (AR Aging Summary)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Current (0-30 Days)</th>
                                        <th>31 - 60 Days</th>
                                        <th>61 - 90 Days</th>
                                        <th>90+ Days (Overdue)</th>
                                        <th>Total Outstanding</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($arAging['customers'] as $custName => $c)
                                    <tr>
                                        <td class="fw-bold">{{ $custName }}</td>
                                        <td>NPR {{ number_format($c['current'], 2) }}</td>
                                        <td>NPR {{ number_format($c['days_31_60'], 2) }}</td>
                                        <td>NPR {{ number_format($c['days_61_90'], 2) }}</td>
                                        <td class="@if($c['over_90'] > 0) text-danger fw-bold @endif">NPR {{ number_format($c['over_90'], 2) }}</td>
                                        <td class="fw-bold text-primary">NPR {{ number_format($c['total'], 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center text-muted py-3">No outstanding receivables found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title m-0 fw-bold text-danger"><i class="bi bi-truck me-2"></i>Supplier Payables (AP Aging Summary)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Supplier Name</th>
                                        <th>Current (0-30 Days)</th>
                                        <th>31 - 60 Days</th>
                                        <th>61 - 90 Days</th>
                                        <th>90+ Days (Overdue)</th>
                                        <th>Total Outstanding</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($apAging['suppliers'] as $supName => $s)
                                    <tr>
                                        <td class="fw-bold">{{ $supName }}</td>
                                        <td>NPR {{ number_format($s['current'], 2) }}</td>
                                        <td>NPR {{ number_format($s['days_31_60'], 2) }}</td>
                                        <td>NPR {{ number_format($s['days_61_90'], 2) }}</td>
                                        <td class="@if($s['over_90'] > 0) text-danger fw-bold @endif">NPR {{ number_format($s['over_90'], 2) }}</td>
                                        <td class="fw-bold text-danger">NPR {{ number_format($s['total'], 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center text-muted py-3">No outstanding payables found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
