@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-receipt me-2"></i>Sales Invoices (Nepal VAT 13%)</h4>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.pharma.reports.sales-register') }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>VAT Sales Register
                    </a>
                    @if(auth()->guard('admin')->user()->hasPermission('SaleInvoice', 'create'))
                    <a href="{{ route('admin.pharma.sale-invoice.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create Sales Invoice
                    </a>
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
            <div class="card-body">
                <form method="GET" action="{{ route('admin.pharma.sales-invoices') }}" class="row g-2 mb-3">
                    <div class="col-md-5">
                        <select name="customer_id" class="form-select">
                            <option value="">-- All Customers --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" @selected(request('customer_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">-- All Statuses --</option>
                            <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                            <option value="posted" @selected(request('status') == 'posted')>Posted</option>
                            <option value="cancelled" @selected(request('status') == 'cancelled')>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                        <a href="{{ route('admin.pharma.sales-invoices') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer (Pharmacy/Clinic)</th>
                                <th>Date (AD/BS)</th>
                                <th>Taxable Amount</th>
                                <th>Tax Amount</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                            <tr>
                                <td class="fw-bold">
                                    <a href="{{ route('admin.pharma.sale-invoice.show', $inv->id) }}" class="text-decoration-none">
                                        {{ $inv->invoice_number }}
                                    </a>
                                </td>
                                <td>
                                    <strong>{{ $inv->customer->name ?? $inv->customer_name }}</strong><br>
                                    <small class="text-muted">PAN: {{ $inv->customer->pan_vat_number ?? ($inv->customer_pan ?? '—') }}</small>
                                </td>
                                <td><small>{{ $inv->invoice_date_ad }}<br><span class="text-muted">{{ $inv->invoice_date_bs }}</span></small></td>
                                <td>NPR {{ number_format($inv->taxable_amount, 2) }}</td>
                                <td>NPR {{ number_format($inv->tax_amount, 2) }}</td>
                                <td class="fw-bold text-success">NPR {{ number_format($inv->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge @if($inv->status == 'posted') bg-success @elseif($inv->status == 'draft') bg-warning text-dark @else bg-danger @endif">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-wrap justify-content-end gap-2">
                                        @include('admin.pharma.sales._draft_actions', ['document' => $inv, 'prefix' => 'sale-invoice', 'module' => 'SaleInvoice'])
                                        <a href="{{ route('admin.pharma.sale-invoice.show', $inv->id) }}" class="btn btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.pharma.sale-invoice.print', $inv->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print PDF">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No sales invoices recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
