@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-cart-plus me-2"></i>Create Purchase Order</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.pharma.purchase-orders') }}">Purchase Orders</a></li>
                            <li class="breadcrumb-item active" aria-current="page">New PO</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.pharma.purchase-orders') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Orders
                </a>
            </div>
        </div>

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-2"></i>Please resolve the following errors:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('admin.pharma.purchase-order.store') }}" method="POST" id="poForm">
            @csrf

            <!-- Header Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select select2" required>
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }} ({{ $supplier->company_name ?? $supplier->phone }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Target Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                <option value="">-- Select Destination Warehouse --</option>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ (old('warehouse_id') == $wh->id || $wh->is_primary) ? 'selected' : '' }}>
                                    {{ $wh->name }} ({{ $wh->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Order Date (AD) <span class="text-danger">*</span></label>
                            <input type="date" name="order_date_ad" class="form-control" value="{{ old('order_date_ad', $defaultDate) }}" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Expected Delivery</label>
                            <input type="date" name="expected_delivery_date_ad" class="form-control" value="{{ old('expected_delivery_date_ad') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Order Notes / Terms</label>
                            <input type="text" name="notes" class="form-control" placeholder="Special delivery instructions, payment terms, or reference notes..." value="{{ old('notes') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0 fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Purchase Order Items</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i>Add Product Line
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="linesTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Product <span class="text-danger">*</span></th>
                                    <th style="width: 14%;">Unit</th>
                                    <th style="width: 12%;">Ordered Qty <span class="text-danger">*</span></th>
                                    <th style="width: 14%;">Unit Price (NPR) <span class="text-danger">*</span></th>
                                    <th style="width: 10%;">Disc %</th>
                                    <th style="width: 10%;">Tax Rate %</th>
                                    <th style="width: 14%;">Line Total (NPR)</th>
                                    <th style="width: 6%;" class="text-center">Remove</th>
                                </tr>
                            </thead>
                            <tbody id="linesBody">
                                <!-- Dynamic rows added here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals Summary Footer -->
                <div class="card-footer bg-light p-3">
                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Gross Subtotal:</td>
                                    <td class="text-end fw-semibold">NPR <span id="lblSubtotal">0.00</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total Discount:</td>
                                    <td class="text-end text-danger fw-semibold">- NPR <span id="lblDiscount">0.00</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Taxable Amount:</td>
                                    <td class="text-end fw-semibold">NPR <span id="lblTaxable">0.00</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total Tax:</td>
                                    <td class="text-end text-info fw-semibold">+ NPR <span id="lblTax">0.00</span></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fs-5 fw-bold text-dark">Grand Total:</td>
                                    <td class="fs-5 text-end fw-bold text-primary">NPR <span id="lblGrandTotal">0.00</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.pharma.purchase-orders') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" name="action" value="save_draft" class="btn btn-secondary">
                    <i class="bi bi-save me-1"></i>Save as Draft
                </button>
                <button type="submit" name="action" value="save_and_approve" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i>Save & Approve PO
                </button>
            </div>
        </form>
    </div>
</div>

@push('footer-scripts')
<script>
const productsData = @json($products);
const unitsData = @json($units);
let rowIndex = 0;

function addRow(data = null) {
    const tbody = document.getElementById('linesBody');
    const tr = document.createElement('tr');
    tr.id = `row-${rowIndex}`;

    let productOptions = '<option value="">-- Choose Product --</option>';
    productsData.forEach(p => {
        const isSel = (data && data.product_id == p.id) ? 'selected' : '';
        productOptions += `<option value="${p.id}" ${isSel} data-price="${p.purchase_price || 0}" data-unit="${p.base_unit_id}" data-tax="${p.tax_category ? p.tax_category.rate : 0}">
            ${p.code} - ${p.name} (${p.generic_name})
        </option>`;
    });

    let unitOptions = '';
    unitsData.forEach(u => {
        const isSel = (data && data.unit_id == u.id) ? 'selected' : '';
        unitOptions += `<option value="${u.id}" ${isSel}>${u.name} (${u.abbreviation})</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="lines[${rowIndex}][product_id]" class="form-select form-select-sm product-select" required onchange="onProductSelect(${rowIndex})">
                ${productOptions}
            </select>
        </td>
        <td>
            <select name="lines[${rowIndex}][unit_id]" class="form-select form-select-sm unit-select" required>
                ${unitOptions}
            </select>
            <input type="hidden" name="lines[${rowIndex}][conversion_factor]" value="1.0">
        </td>
        <td>
            <input type="number" step="any" min="0.0001" name="lines[${rowIndex}][ordered_qty]" class="form-control form-control-sm qty-input text-end" value="${data ? data.ordered_qty : 1}" required oninput="calcRow(${rowIndex})">
        </td>
        <td>
            <input type="number" step="any" min="0" name="lines[${rowIndex}][unit_price]" class="form-control form-control-sm price-input text-end" value="${data ? data.unit_price : 0}" required oninput="calcRow(${rowIndex})">
        </td>
        <td>
            <input type="number" step="any" min="0" max="100" name="lines[${rowIndex}][discount_pct]" class="form-control form-control-sm disc-input text-end" value="${data ? data.discount_pct : 0}" oninput="calcRow(${rowIndex})">
        </td>
        <td>
            <input type="number" step="any" min="0" max="100" name="lines[${rowIndex}][tax_rate]" class="form-control form-control-sm tax-input text-end" value="${data ? data.tax_rate : 0}" oninput="calcRow(${rowIndex})">
        </td>
        <td class="text-end fw-semibold">
            <span class="row-total" id="row-total-${rowIndex}">0.00</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);

    if (data) {
        calcRow(rowIndex);
    } else {
        // Trigger default selection
        onProductSelect(rowIndex);
    }

    rowIndex++;
}

function onProductSelect(idx) {
    const row = document.getElementById(`row-${idx}`);
    if (!row) return;

    const prodSelect = row.querySelector('.product-select');
    const opt = prodSelect.options[prodSelect.selectedIndex];

    if (opt && opt.value) {
        const price = opt.getAttribute('data-price') || 0;
        const unitId = opt.getAttribute('data-unit');
        const taxRate = opt.getAttribute('data-tax') || 0;

        row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
        row.querySelector('.tax-input').value = parseFloat(taxRate).toFixed(2);

        if (unitId) {
            row.querySelector('.unit-select').value = unitId;
        }
    }

    calcRow(idx);
}

function removeRow(idx) {
    const tbody = document.getElementById('linesBody');
    if (tbody.children.length <= 1) {
        alert('Purchase Order must have at least one product line.');
        return;
    }
    const row = document.getElementById(`row-${idx}`);
    if (row) row.remove();
    calcTotals();
}

function calcRow(idx) {
    const row = document.getElementById(`row-${idx}`);
    if (!row) return;

    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const discPct = parseFloat(row.querySelector('.disc-input').value) || 0;
    const taxRate = parseFloat(row.querySelector('.tax-input').value) || 0;

    const gross = qty * price;
    const disc = gross * (discPct / 100);
    const net = gross - disc;
    const tax = net * (taxRate / 100);
    const lineTotal = net + tax;

    document.getElementById(`row-total-${idx}`).innerText = lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    calcTotals();
}

function calcTotals() {
    let subtotal = 0;
    let totalDisc = 0;
    let taxable = 0;
    let totalTax = 0;
    let grandTotal = 0;

    const rows = document.querySelectorAll('#linesBody tr');
    rows.forEach(r => {
        const qty = parseFloat(r.querySelector('.qty-input').value) || 0;
        const price = parseFloat(r.querySelector('.price-input').value) || 0;
        const discPct = parseFloat(r.querySelector('.disc-input').value) || 0;
        const taxRate = parseFloat(r.querySelector('.tax-input').value) || 0;

        const gross = qty * price;
        const disc = gross * (discPct / 100);
        const net = gross - disc;
        const tax = net * (taxRate / 100);

        subtotal += gross;
        totalDisc += disc;
        taxable += net;
        totalTax += tax;
        grandTotal += (net + tax);
    });

    document.getElementById('lblSubtotal').innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblDiscount').innerText = totalDisc.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblTaxable').innerText = taxable.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblTax').innerText = totalTax.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblGrandTotal').innerText = grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Initialise with 1 empty row on load
document.addEventListener('DOMContentLoaded', function () {
    addRow();
});
</script>
@endpush
@endsection
