@extends('layout.admin')
@section('content')
<div class="content adm-dash">
    <div class="container-fluid">
        <!-- Page Title & Header -->
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main m-0 text-primary"><i class="bi bi-capsule me-2"></i>Pharmaceutical Stock & Distribution Hub</h4>
                    <p class="text-muted small mb-0">Nepal Pharma Wholesale Operations & Distribution Management</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="reconcileStock()"><i class="bi bi-arrow-repeat me-1"></i>Reconcile Ledger</button>
                    <a href="{{ route('admin.pharma.products') }}" class="btn btn-sm btn-primary"><i class="bi bi-box-seam me-1"></i>View Catalog</a>
                </div>
            </div>
        </div>

        <!-- 4 Top KPI Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0 border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold small">Stock Valuation (Cost)</span>
                                <h3 class="my-1 text-primary">NPR {{ number_format($summary['stock_valuation_cost'], 2) }}</h3>
                                <small class="text-muted">MRP: NPR {{ number_format($summary['stock_valuation_mrp'], 2) }}</small>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                                <i class="bi bi-boxes fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0 border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold small">Sales Today</span>
                                <h3 class="my-1 text-success">NPR {{ number_format($summary['sales_today'], 2) }}</h3>
                                <small class="text-muted">Receivables: NPR {{ number_format($summary['total_receivables'], 2) }}</small>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                                <i class="bi bi-receipt fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0 border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold small">Cash & Bank Liquidity</span>
                                <h3 class="my-1 text-info">NPR {{ number_format($summary['cash_bank_liquidity'], 2) }}</h3>
                                <small class="text-muted">Payables: NPR {{ number_format($summary['total_payables'], 2) }}</small>
                            </div>
                            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                                <i class="bi bi-bank fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0 border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold small">Near Expiry Alerts</span>
                                <h3 class="my-1 text-warning">{{ $summary['near_expiry_count'] }} Batches</h3>
                                <small class="text-danger fw-semibold">{{ $summary['low_stock_count'] }} Low Stock Items</small>
                            </div>
                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                                <i class="bi bi-exclamation-triangle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-Time Stock Flow Process Widget (Inflow & Outflow Architecture) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary px-2 py-1"><i class="bi bi-arrow-left-right"></i></span>
                            <h5 class="card-title m-0 fw-bold text-dark">Authoritative Stock Flow Process</h5>
                            <span class="badge bg-light text-muted border">Inflows vs. Outflows</span>
                        </div>
                        <a href="{{ route('admin.pharma.stock-movements') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-journal-text me-1"></i>View Full Stock Ledger
                        </a>
                    </div>
                    <div class="card-body bg-light bg-opacity-50 p-4">
                        <div class="row align-items-center g-3 text-center">
                            
                            <!-- INFLOWS (Left) -->
                            <div class="col-lg-3 col-md-4">
                                <div class="text-uppercase small fw-bold text-primary mb-2 text-start">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Stock Inflows (+)
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <!-- Purchase -->
                                    <a href="{{ route('admin.pharma.goods-receipts') }}" class="card text-decoration-none border-0 shadow-sm p-3 text-start position-relative overflow-hidden" style="background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); color: #fff;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="small text-white-50 text-uppercase fw-bold">Purchase (GRN)</div>
                                                <h4 class="m-0 text-white fw-bold">+{{ number_format($stockFlow['inflows']['purchase']['quantity']) }} <small class="fs-6 fw-normal">units</small></h4>
                                                <small class="text-white-50">NPR {{ number_format($stockFlow['inflows']['purchase']['total_cost'], 2) }}</small>
                                            </div>
                                            <i class="bi bi-truck fs-1 text-white opacity-50"></i>
                                        </div>
                                    </a>

                                    <!-- Sell Return -->
                                    <a href="{{ route('admin.pharma.sales-returns') }}" class="card text-decoration-none border-0 shadow-sm p-3 text-start position-relative overflow-hidden" style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); color: #fff;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="small text-white-50 text-uppercase fw-bold">Sell Return</div>
                                                <h4 class="m-0 text-white fw-bold">+{{ number_format($stockFlow['inflows']['sell_return']['quantity']) }} <small class="fs-6 fw-normal">units</small></h4>
                                                <small class="text-white-50">NPR {{ number_format($stockFlow['inflows']['sell_return']['total_cost'], 2) }}</small>
                                            </div>
                                            <i class="bi bi-arrow-return-left fs-1 text-white opacity-50"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- INFLOW ARROW -->
                            <div class="col-lg-1 col-md-1 d-none d-md-flex justify-content-center align-items-center">
                                <div class="display-6 text-primary animate-pulse">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>

                            <!-- CENTRAL STOCK (Center) -->
                            <div class="col-lg-4 col-md-6">
                                <div class="card border-0 shadow text-white p-4 position-relative overflow-hidden" style="background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%); min-height: 240px;">
                                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                                        <i class="bi bi-box-seam" style="font-size: 5rem;"></i>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-light text-primary px-3 py-1 fw-bold text-uppercase">Central Repository</span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Reconciled</span>
                                    </div>
                                    <h2 class="display-5 fw-bold text-white mb-1">STOCK</h2>
                                    <p class="text-white-50 small mb-3">Authoritative Inventory Ledger</p>
                                    
                                    <div class="row g-2 text-start pt-2 border-top border-white border-opacity-25">
                                        <div class="col-6">
                                            <div class="small text-white-50">Physical On-Hand</div>
                                            <div class="fs-4 fw-bold text-white">{{ number_format($stockFlow['stock']['on_hand_quantity']) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small text-white-50">Available (FEFO)</div>
                                            <div class="fs-4 fw-bold text-info">{{ number_format($stockFlow['stock']['available_quantity']) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- OUTFLOW ARROW -->
                            <div class="col-lg-1 col-md-1 d-none d-md-flex justify-content-center align-items-center">
                                <div class="display-6 text-dark animate-pulse">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>

                            <!-- OUTFLOWS (Right) -->
                            <div class="col-lg-3 col-md-12">
                                <div class="text-uppercase small fw-bold text-danger mb-2 text-start">
                                    <i class="bi bi-box-arrow-right me-1"></i>Stock Outflows (-)
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <!-- Sell -->
                                    <a href="{{ route('admin.pharma.sales-invoices') }}" class="card text-decoration-none border-0 shadow-sm p-2 text-start" style="background: #2563eb; color: #fff;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem;">Sell (Dispatched)</div>
                                                <div class="fw-bold fs-6">-{{ number_format($stockFlow['outflows']['sell']['quantity']) }} <small class="text-white-50 fs-7">units</small></div>
                                            </div>
                                            <i class="bi bi-receipt text-white-50 fs-4"></i>
                                        </div>
                                    </a>

                                    <!-- Purchase Return (Black Theme matching user diagram) -->
                                    <a href="{{ route('admin.pharma.purchase-returns') }}" class="card text-decoration-none border-0 shadow-sm p-2 text-start" style="background: #0f172a; color: #fff;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem;">Purchase Return</div>
                                                <div class="fw-bold fs-6">-{{ number_format($stockFlow['outflows']['purchase_return']['quantity']) }} <small class="text-white-50 fs-7">units</small></div>
                                            </div>
                                            <i class="bi bi-arrow-counterclockwise text-white-50 fs-4"></i>
                                        </div>
                                    </a>

                                    <!-- Expired item -->
                                    <a href="{{ route('admin.pharma.reports.near-expiry') }}" class="card text-decoration-none border-0 shadow-sm p-2 text-start" style="background: #1e3a8a; color: #fff;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem;">Expired Item</div>
                                                <div class="fw-bold fs-6">-{{ number_format($stockFlow['outflows']['expired_item']['quantity']) }} <small class="text-white-50 fs-7">units</small></div>
                                            </div>
                                            <i class="bi bi-hourglass-bottom text-white-50 fs-4"></i>
                                        </div>
                                    </a>

                                    <!-- Damage -->
                                    <a href="{{ route('admin.pharma.stock-adjustments') }}" class="card text-decoration-none border-0 shadow-sm p-2 text-start" style="background: #1d4ed8; color: #fff;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem;">Damage</div>
                                                <div class="fw-bold fs-6">-{{ number_format($stockFlow['outflows']['damage']['quantity']) }} <small class="text-white-50 fs-7">units</small></div>
                                            </div>
                                            <i class="bi bi-heartbreak text-white-50 fs-4"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Near Expiry Alert Banner (if any) -->
        @if(count($nearExpiryBatches) > 0)
        <div class="row">
            <div class="col-12 mb-3">
                <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                        <strong>Near-Expiry Batches Notice:</strong> There are <strong>{{ count($nearExpiryBatches) }}</strong> pharmaceutical batches expiring within 90 days. Prioritize FEFO dispatch!
                    </div>
                    <a href="{{ route('admin.pharma.reports.near-expiry') }}" class="btn btn-sm btn-dark">Review Batches</a>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Invoices & Recent Goods Receipts -->
        <div class="row">
            <div class="col-xl-6 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title m-0 fw-bold"><i class="bi bi-file-earmark-medical me-2 text-primary"></i>Recent Sales Invoices</h5>
                        <a href="{{ route('admin.pharma.sales-invoices') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Customer</th>
                                        <th>Date (AD/BS)</th>
                                        <th>Total (NPR)</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentInvoices as $inv)
                                    <tr>
                                        <td class="fw-bold">{{ $inv->invoice_number }}</td>
                                        <td>{{ $inv->customer->name ?? 'N/A' }}</td>
                                        <td><small>{{ $inv->invoice_date_ad }}<br><span class="text-muted">{{ $inv->invoice_date_bs }}</span></small></td>
                                        <td class="fw-semibold">NPR {{ number_format($inv->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge @if($inv->status == 'posted') bg-success @elseif($inv->status == 'approved') bg-primary @else bg-warning @endif">
                                                {{ ucfirst($inv->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pharma.sale-invoice.print', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Print Invoice PDF">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center text-muted py-3">No sales invoices recorded yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title m-0 fw-bold"><i class="bi bi-box-arrow-in-down me-2 text-success"></i>Recent Goods Receipts (GRN)</h5>
                        <a href="{{ route('admin.pharma.goods-receipts') }}" class="btn btn-sm btn-outline-success">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>GRN #</th>
                                        <th>Supplier</th>
                                        <th>Date (AD/BS)</th>
                                        <th>Bill Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentReceipts as $grn)
                                    <tr>
                                        <td class="fw-bold">{{ $grn->grn_number }}</td>
                                        <td>{{ $grn->supplier->name ?? 'N/A' }}</td>
                                        <td><small>{{ $grn->receipt_date_ad }}<br><span class="text-muted">{{ $grn->receipt_date_bs }}</span></small></td>
                                        <td class="fw-semibold">NPR {{ number_format($grn->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge @if($grn->status == 'posted') bg-success @else bg-info @endif">
                                                {{ ucfirst($grn->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">No goods receipts recorded yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Navigation Grid -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title m-0 fw-bold"><i class="bi bi-grid-fill me-2 text-primary"></i>Operations & Department Shortcuts</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-2 col-sm-4 col-6">
                                <a href="{{ route('admin.pharma.products') }}" class="card text-decoration-none text-center p-3 h-100 border shadow-none bg-light hover-shadow">
                                    <i class="bi bi-capsule fs-2 text-primary mb-2"></i>
                                    <div class="fw-semibold text-dark">Products</div>
                                    <small class="text-muted">{{ $totalProducts }} Registered</small>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6">
                                <a href="{{ route('admin.pharma.batches') }}" class="card text-decoration-none text-center p-3 h-100 border shadow-none bg-light hover-shadow">
                                    <i class="bi bi-upc-scan fs-2 text-info mb-2"></i>
                                    <div class="fw-semibold text-dark">Batches & Expiry</div>
                                    <small class="text-muted">FEFO Managed</small>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6">
                                <a href="{{ route('admin.pharma.purchase-orders') }}" class="card text-decoration-none text-center p-3 h-100 border shadow-none bg-light hover-shadow">
                                    <i class="bi bi-cart-check fs-2 text-success mb-2"></i>
                                    <div class="fw-semibold text-dark">Purchasing</div>
                                    <small class="text-muted">POs & GRNs</small>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6">
                                <a href="{{ route('admin.pharma.sales-invoices') }}" class="card text-decoration-none text-center p-3 h-100 border shadow-none bg-light hover-shadow">
                                    <i class="bi bi-receipt fs-2 text-warning mb-2"></i>
                                    <div class="fw-semibold text-dark">Sales Invoicing</div>
                                    <small class="text-muted">Nepal VAT 13%</small>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6">
                                <a href="{{ route('admin.pharma.customer-receipts') }}" class="card text-decoration-none text-center p-3 h-100 border shadow-none bg-light hover-shadow">
                                    <i class="bi bi-cash-coin fs-2 text-success mb-2"></i>
                                    <div class="fw-semibold text-dark">Collections</div>
                                    <small class="text-muted">AR Receipts</small>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6">
                                <a href="{{ route('admin.pharma.reports.stock-valuation') }}" class="card text-decoration-none text-center p-3 h-100 border shadow-none bg-light hover-shadow">
                                    <i class="bi bi-bar-chart-line fs-2 text-danger mb-2"></i>
                                    <div class="fw-semibold text-dark">Reports</div>
                                    <small class="text-muted">Valuation & VAT</small>
                                </a>
                            </div>
                        </div>
                    </div>
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