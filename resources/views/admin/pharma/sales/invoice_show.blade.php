@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.sales-invoices') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Invoices
                    </a>
                    <h4 class="page-title-main text-primary m-0">
                        <i class="bi bi-receipt me-2"></i>Sales Invoice #{{ $invoice->invoice_number }}
                    </h4>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pharma.sale-invoice.print', $invoice->id) }}" target="_blank" class="btn btn-outline-dark">
                        <i class="bi bi-printer me-1"></i>Print PDF Tax Invoice
                    </a>
                    @if($invoice->status === 'draft')
                    <form action="{{ route('admin.pharma.sale-invoice.post', $invoice->id) }}" method="POST" onsubmit="return confirm('Post this invoice? This will immediately deduct stock and update customer ledger.');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send-check me-1"></i>Post / Confirm Invoice
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

        <div class="row">
            <!-- Header Summary -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-info-circle me-1 text-primary"></i>Customer & Dispatch Details</span>
                        <span class="badge @if($invoice->status === 'posted') bg-success @elseif($invoice->status === 'cancelled') bg-danger @else bg-warning text-dark @endif fs-6">
                            {{ strtoupper($invoice->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Customer Name:</label>
                                <div class="fw-bold fs-6">{{ $invoice->customer_name }}</div>
                                <div class="small text-muted">
                                    PAN/VAT: {{ $invoice->customer_pan ?? 'N/A' }} &bull; Phone: {{ $invoice->customer?->phone ?? '—' }}
                                </div>
                                <div class="small text-muted">{{ $invoice->customer?->address ?? '' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <label class="text-muted small">Invoice Date (AD):</label>
                                        <div class="fw-semibold">{{ $invoice->invoice_date_ad }}</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="text-muted small">Invoice Date (BS):</label>
                                        <div class="fw-semibold">{{ $invoice->invoice_date_bs }}</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small">Dispatch Warehouse:</label>
                                        <div class="fw-semibold">{{ $invoice->warehouse->name ?? 'Primary' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small">Payment Method:</label>
                                        <div class="fw-semibold text-capitalize">{{ $invoice->payment_method }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lines Table -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold">
                        <i class="bi bi-list-check me-1 text-primary"></i>Invoice Line Items (FEFO Dispatched)
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product / Medicine</th>
                                        <th>Batch #</th>
                                        <th>Expiry (AD)</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Discount</th>
                                        <th class="text-end">Tax</th>
                                        <th class="text-end">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->lines as $idx => $line)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <strong>{{ $line->product_name }}</strong><br>
                                            <small class="text-muted">{{ $line->product_code }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $line->batch_number }}</span></td>
                                        <td><small>{{ $line->expiry_date_ad }}</small></td>
                                        <td class="text-end fw-bold">{{ number_format($line->qty, 0) }} {{ $line->unit_name }}</td>
                                        <td class="text-end">NPR {{ number_format($line->unit_price, 2) }}</td>
                                        <td class="text-end text-danger">NPR {{ number_format($line->discount_amount, 2) }}</td>
                                        <td class="text-end text-info">NPR {{ number_format($line->tax_amount, 2) }}</td>
                                        <td class="text-end fw-bold">NPR {{ number_format($line->line_total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold text-dark">
                        <i class="bi bi-calculator me-1 text-success"></i>Financial Breakdown (NPR)
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Subtotal (Gross):</td>
                                <td class="text-end fw-semibold">NPR {{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Discount:</td>
                                <td class="text-end text-danger fw-semibold">- NPR {{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Taxable Amount:</td>
                                <td class="text-end">NPR {{ number_format($invoice->taxable_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Non-Taxable Amount:</td>
                                <td class="text-end">NPR {{ number_format($invoice->non_taxable_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">VAT / Tax Amount:</td>
                                <td class="text-end text-info fw-semibold">+ NPR {{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @if($invoice->delivery_charge > 0)
                            <tr>
                                <td class="text-muted">Delivery Charge:</td>
                                <td class="text-end">+ NPR {{ number_format($invoice->delivery_charge, 2) }}</td>
                            </tr>
                            @endif
                            @if($invoice->round_off != 0)
                            <tr>
                                <td class="text-muted">Round Off:</td>
                                <td class="text-end">NPR {{ number_format($invoice->round_off, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="border-top">
                                <td class="fw-bold fs-5 text-dark pt-2">Grand Total:</td>
                                <td class="text-end fw-bold fs-5 text-success pt-2">NPR {{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Paid Immediately:</td>
                                <td class="text-end text-success fw-semibold">NPR {{ number_format($invoice->paid_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Credit Balance Added:</td>
                                <td class="text-end text-danger fw-bold">NPR {{ number_format($invoice->credit_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($invoice->notes)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold text-dark">
                        <i class="bi bi-sticky me-1 text-warning"></i>Remarks & Notes
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted">{{ $invoice->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
