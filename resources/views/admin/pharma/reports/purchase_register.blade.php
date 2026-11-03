@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Purchase Register (Nepal Inland Revenue VAT Format)</h4>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print Register</button>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.pharma.reports.purchase-register') }}" class="row g-2 mb-3">
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
                                <th>GRN #</th>
                                <th>Supplier Bill #</th>
                                <th>Supplier Name</th>
                                <th>Supplier PAN/VAT</th>
                                <th>Taxable Amount</th>
                                <th>VAT (13%)</th>
                                <th>Total Purchases</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($register['receipts'] as $r)
                            <tr>
                                <td><small>{{ $r['receipt_date_bs'] }}<br><span class="text-muted">{{ $r['receipt_date_ad'] }}</span></small></td>
                                <td class="fw-bold">{{ $r['grn_number'] }}</td>
                                <td>{{ $r['supplier_invoice_number'] ?? '—' }}</td>
                                <td>{{ $r['supplier_name'] }}</td>
                                <td class="font-monospace">{{ $r['supplier_pan'] ?? '—' }}</td>
                                <td>NPR {{ number_format($r['taxable_amount'], 2) }}</td>
                                <td class="fw-bold text-primary">NPR {{ number_format($r['tax_amount'], 2) }}</td>
                                <td class="fw-semibold">NPR {{ number_format($r['total_amount'], 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No purchase records in selected period.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end">TOTALS:</td>
                                <td>NPR {{ number_format($register['total_taxable'], 2) }}</td>
                                <td class="text-primary">NPR {{ number_format($register['total_tax'], 2) }}</td>
                                <td>NPR {{ number_format($register['total_grand'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
