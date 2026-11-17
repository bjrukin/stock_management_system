@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <a href="{{ route('admin.pharma.sales-returns') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Sales Returns
                    </a>
                    <h4 class="page-title-main text-primary m-0">
                        <i class="bi bi-arrow-return-left me-2"></i>Sales Return #{{ $return->return_number }}
                    </h4>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @include('admin.pharma.sales._draft_actions', ['document' => $return, 'prefix' => 'sales-return', 'module' => 'SaleReturn'])
                    @if($return->status === 'draft' && auth()->guard('admin')->user()->hasPermission('SaleReturn', 'post'))
                    <form action="{{ route('admin.pharma.sales-return.post', $return->id) }}" method="POST" onsubmit="return confirm('Post this sales return? This will restock the inventory and apply the credit note to the customer ledger.');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send-check me-1"></i>Post Return (Restock & Credit Note)
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
            <div class="col-lg-8">
                <!-- Return Header Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-info-circle me-1 text-primary"></i>Customer & Warehouse Information</span>
                        <span class="badge @if($return->status === 'posted') bg-success @else bg-warning text-dark @endif fs-6">
                            {{ strtoupper($return->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Customer Name:</label>
                                <div class="fw-bold fs-6">{{ $return->customer->name ?? 'Walk-in Customer' }}</div>
                                <div class="small text-muted">
                                    PAN/VAT: {{ $return->customer->pan_vat_number ?? '—' }} &bull; Phone: {{ $return->customer->phone ?? '—' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <label class="text-muted small">Return Date (AD):</label>
                                        <div class="fw-semibold">{{ $return->return_date_ad }}</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="text-muted small">Return Date (BS):</label>
                                        <div class="fw-semibold">{{ $return->return_date_bs }}</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small">Receiving Warehouse:</label>
                                        <div class="fw-semibold">{{ $return->warehouse->name ?? 'Primary' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small">Original Invoice:</label>
                                        <div class="fw-semibold">
                                            @if($return->saleInvoice)
                                            <a href="{{ route('admin.pharma.sale-invoice.show', $return->saleInvoice->id) }}">
                                                #{{ $return->saleInvoice->invoice_number }}
                                            </a>
                                            @else
                                            <span class="text-muted">None (Direct Return)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Returned Lines Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold">
                        <i class="bi bi-capsule me-1 text-primary"></i>Returned Line Items
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product / Item</th>
                                        <th>Batch #</th>
                                        <th>Condition</th>
                                        <th class="text-end">Returned Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Line Total</th>
                                        <th>Restocked</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($return->lines as $idx => $line)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <strong>{{ $line->product->name ?? 'Product #' . $line->product_id }}</strong><br>
                                            <small class="text-muted">{{ $line->product->code ?? '' }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $line->batch->batch_number ?? 'N/A' }}</span></td>
                                        <td>
                                            <span class="badge @if($line->condition === 'saleable') bg-success-subtle text-success @elseif($line->condition === 'damaged') bg-danger-subtle text-danger @else bg-dark-subtle text-dark @endif text-capitalize">
                                                {{ $line->condition }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($line->returned_qty, 0) }} {{ $line->unit->abbreviation ?? '' }}</td>
                                        <td class="text-end">NPR {{ number_format($line->unit_price, 2) }}</td>
                                        <td class="text-end fw-bold">NPR {{ number_format($line->line_total, 2) }}</td>
                                        <td>
                                            @if($line->restocked)
                                            <span class="badge bg-success"><i class="bi bi-check me-1"></i>Yes</span>
                                            @else
                                            <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold text-dark">
                        <i class="bi bi-calculator me-1 text-success"></i>Return Valuation (NPR)
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Subtotal:</td>
                                <td class="text-end fw-semibold">NPR {{ number_format($return->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tax Amount:</td>
                                <td class="text-end text-info">NPR {{ number_format($return->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fw-bold fs-5 text-dark pt-2">Total Credit:</td>
                                <td class="text-end fw-bold fs-5 text-success pt-2">NPR {{ number_format($return->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Credit Note:</td>
                                <td class="text-end">
                                    @if($return->credit_note_issued)
                                    <span class="badge bg-success">Issued</span>
                                    @else
                                    <span class="badge bg-secondary">Not Issued</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($return->reason)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-bold text-dark">
                        <i class="bi bi-sticky me-1 text-warning"></i>Reason / Remarks
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted">{{ $return->reason }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
