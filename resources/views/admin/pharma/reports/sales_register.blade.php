@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Sales Register (Nepal Inland Revenue VAT Format)</h4>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print Register</button>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.pharma.reports.sales-register') }}" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small">From Date (AD)</label>
                        <input type="date" name="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">To Date (AD)</label>
                        <input type="date" name="to" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i>Generate</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date (BS / AD)</th>
                                <th>Invoice #</th>
                                <th>Buyer Name</th>
                                <th>Buyer PAN/VAT</th>
                                <th>Total Sales</th>
                                <th>Non-Taxable</th>
                                <th>Taxable Amount</th>
                                <th>VAT (13%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($register['invoices'] as $r)
                            <tr>
                                <td><small>{{ $r['invoice_date_bs'] }}<br><span class="text-muted">{{ $r['invoice_date_ad'] }}</span></small></td>
                                <td class="fw-bold">{{ $r['invoice_number'] }}</td>
                                <td>{{ $r['customer_name'] }}</td>
                                <td class="font-monospace">{{ $r['customer_pan'] ?? '—' }}</td>
                                <td class="fw-semibold">NPR {{ number_format($r['total_amount'], 2) }}</td>
                                <td>NPR {{ number_format($r['non_taxable_amount'], 2) }}</td>
                                <td>NPR {{ number_format($r['taxable_amount'], 2) }}</td>
                                <td class="fw-bold text-primary">NPR {{ number_format($r['tax_amount'], 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No sales records in selected period.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">TOTALS:</td>
                                <td>NPR {{ number_format($register['total_grand'], 2) }}</td>
                                <td>NPR {{ number_format($register['total_non_taxable'], 2) }}</td>
                                <td>NPR {{ number_format($register['total_taxable'], 2) }}</td>
                                <td class="text-primary">NPR {{ number_format($register['total_tax'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
