@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-box-arrow-in-down me-2"></i>Add Goods Receipt (GRN)</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.pharma.goods-receipts') }}">Goods Receipts</a></li>
                            <li class="breadcrumb-item active" aria-current="page">New GRN</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.pharma.goods-receipts') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Receipts
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

        <form action="{{ route('admin.pharma.goods-receipt.store') }}" method="POST" id="grnForm">
            @csrf

            <!-- Header Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Receipt & Supplier Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplierSelect" class="form-select" required>
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ (old('supplier_id') == $supplier->id || ($selectedPo && $selectedPo->supplier_id == $supplier->id)) ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" id="warehouseSelect" class="form-select" required>
                                <option value="">-- Select Warehouse --</option>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ (old('warehouse_id') == $wh->id || ($selectedPo && $selectedPo->warehouse_id == $wh->id) || $wh->is_primary) ? 'selected' : '' }}>
                                    {{ $wh->name }} ({{ $wh->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Purchase Order (Optional)</label>
                            <select name="purchase_order_id" id="poSelect" class="form-select" onchange="onPoChange(this)">
                                <option value="">-- Direct Receipt (No PO) --</option>
                                @foreach($approvedOrders as $po)
                                <option value="{{ $po->id }}" {{ (old('purchase_order_id') == $po->id || ($selectedPo && $selectedPo->id == $po->id)) ? 'selected' : '' }} data-supplier="{{ $po->supplier_id }}" data-warehouse="{{ $po->warehouse_id }}">
                                    {{ $po->po_number }} - {{ $po->supplier->name }} (NPR {{ number_format($po->total_amount, 2) }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Receipt Date (AD) <span class="text-danger">*</span></label>
                            <input type="date" name="receipt_date_ad" class="form-control" value="{{ old('receipt_date_ad', $defaultDate) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Supplier Bill / Invoice #</label>
                            <input type="text" name="supplier_invoice_number" class="form-control" placeholder="e.g. INV-9821" value="{{ old('supplier_invoice_number') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Supplier Bill Date (AD)</label>
                            <input type="date" name="supplier_invoice_date_ad" class="form-control" value="{{ old('supplier_invoice_date_ad') }}">
                        </div>

                        <!-- Landed Cost Components -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Freight (NPR)</label>
                            <input type="number" step="any" min="0" name="freight_amount" id="freightInput" class="form-control text-end" value="{{ old('freight_amount', 0) }}" oninput="calcTotals()">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Handling (NPR)</label>
                            <input type="number" step="any" min="0" name="handling_charges" id="handlingInput" class="form-control text-end" value="{{ old('handling_charges', 0) }}" oninput="calcTotals()">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Other (NPR)</label>
                            <input type="number" step="any" min="0" name="other_charges" id="otherInput" class="form-control text-end" value="{{ old('other_charges', 0) }}" oninput="calcTotals()">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Receipt Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Delivery vehicle number, driver name, condition on arrival..." value="{{ old('notes') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title m-0 fw-bold"><i class="bi bi-upc-scan me-2 text-primary"></i>Received Batches & Pricing Information</h5>
                        <small class="text-muted">Record inward batch details, tax category, MRP, purchase rate, and selling / wholesale prices</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i>Add Batch Line
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="linesTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 180px;">Product <span class="text-danger">*</span></th>
                                    <th style="min-width: 110px;">Batch # <span class="text-danger">*</span></th>
                                    <th style="min-width: 120px;">Expiry (AD) <span class="text-danger">*</span></th>
                                    <th style="min-width: 110px;">Shelf Location <span class="text-danger">*</span></th>
                                    <th style="min-width: 85px;">Unit</th>
                                    <th style="min-width: 80px;">Qty <span class="text-danger">*</span></th>
                                    <th style="min-width: 100px;">Purchase Rate <span class="text-danger">*</span></th>
                                    <th style="min-width: 75px;">Disc %</th>
                                    <th style="min-width: 130px;">Tax Category (VAT)</th>
                                    <th style="min-width: 95px;">MRP</th>
                                    <th style="min-width: 100px;">Selling Price</th>
                                    <th style="min-width: 100px;">Wholesale Price</th>
                                    <th style="min-width: 95px;" class="text-end">Line Total</th>
                                    <th style="width: 40px;" class="text-center">Del</th>
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
                                    <td class="text-muted">Item Subtotal:</td>
                                    <td class="text-end fw-semibold">NPR <span id="lblSubtotal">0.00</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Item Discounts:</td>
                                    <td class="text-end text-danger fw-semibold">- NPR <span id="lblDiscount">0.00</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Taxable Amount:</td>
                                    <td class="text-end fw-semibold">NPR <span id="lblTaxable">0.00</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">VAT / Tax Total:</td>
                                    <td class="text-end text-info fw-semibold">+ NPR <span id="lblTax">0.00</span></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-dark">Supplier Bill Total (AP):</td>
                                    <td class="text-end fw-bold text-success fs-6">NPR <span id="lblGrandTotal">0.00</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">+ Landed Charges (Freight/Handling):</td>
                                    <td class="text-end text-warning fw-semibold">+ NPR <span id="lblLanded">0.00</span></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fs-5 fw-bold text-dark">Total Stock Valuation:</td>
                                    <td class="fs-5 text-end fw-bold text-primary">NPR <span id="lblValuation">0.00</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.pharma.goods-receipts') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" name="action" value="save_draft" class="btn btn-secondary">
                    <i class="bi bi-save me-1"></i>Save as Draft GRN
                </button>
                <button type="submit" name="action" value="save_and_post" class="btn btn-success px-4">
                    <i class="bi bi-box-arrow-in-down me-1"></i>Save & Post Inward to Stock
                </button>
            </div>
        </form>
    </div>
</div>

@push('footer-scripts')
<script>
const productsData = @json($products);
const locationsData = @json($locations);
const unitsData = @json($units);
const taxCategoriesData = @json($taxCategories);
let rowIndex = 0;

function addRow(data = null) {
    const tbody = document.getElementById('linesBody');
    const tr = document.createElement('tr');
    tr.id = `row-${rowIndex}`;

    let productOptions = '<option value="">-- Choose Product --</option>';
    productsData.forEach(p => {
        const isSel = (data && data.product_id == p.id) ? 'selected' : '';
        productOptions += `<option value="${p.id}" ${isSel} 
            data-price="${p.purchase_price || 0}" 
            data-unit="${p.base_unit_id}" 
            data-tax-id="${p.tax_category_id || ''}" 
            data-tax="${p.tax_category ? p.tax_category.rate : 0}"
            data-mrp="${p.mrp || 0}"
            data-selling="${p.selling_price || 0}"
            data-wholesale="${p.wholesale_price || 0}">
            ${p.code} - ${p.name}
        </option>`;
    });

    let locationOptions = '<option value="">-- Location --</option>';
    locationsData.forEach(l => {
        const isSel = (data && data.location_id == l.id) ? 'selected' : '';
        locationOptions += `<option value="${l.id}" ${isSel}>${l.code} (${l.name})</option>`;
    });

    let unitOptions = '';
    unitsData.forEach(u => {
        const isSel = (data && data.unit_id == u.id) ? 'selected' : '';
        unitOptions += `<option value="${u.id}" ${isSel}>${u.name}</option>`;
    });

    let taxOptions = '<option value="" data-rate="0">0% (None)</option>';
    taxCategoriesData.forEach(t => {
        const isTaxSel = (data && data.tax_category_id == t.id) ? 'selected' : '';
        taxOptions += `<option value="${t.id}" data-rate="${t.rate}" ${isTaxSel}>${t.name} (${parseFloat(t.rate)}%)</option>`;
    });

    // Default expiry 2 years from today if empty
    const defaultExp = new Date();
    defaultExp.setFullYear(defaultExp.getFullYear() + 2);
    const expStr = defaultExp.toISOString().split('T')[0];

    tr.innerHTML = `
        <td>
            <select name="lines[${rowIndex}][product_id]" class="form-select form-select-sm product-select" required onchange="onProductSelect(${rowIndex})">
                ${productOptions}
            </select>
            ${data && data.purchase_order_line_id ? `<input type="hidden" name="lines[${rowIndex}][purchase_order_line_id]" value="${data.purchase_order_line_id}">` : ''}
        </td>
        <td>
            <input type="text" name="lines[${rowIndex}][batch_number]" class="form-control form-control-sm font-monospace fw-semibold" placeholder="e.g. B-${Date.now().toString().slice(-5)}" value="${data ? data.batch_number : ''}" required>
        </td>
        <td>
            <input type="date" name="lines[${rowIndex}][expiry_date_ad]" class="form-control form-control-sm" value="${data ? data.expiry_date_ad : expStr}" required>
        </td>
        <td>
            <select name="lines[${rowIndex}][location_id]" class="form-select form-select-sm" required>
                ${locationOptions}
            </select>
        </td>
        <td>
            <select name="lines[${rowIndex}][unit_id]" class="form-select form-select-sm unit-select" required>
                ${unitOptions}
            </select>
            <input type="hidden" name="lines[${rowIndex}][conversion_factor]" value="1.0">
        </td>
        <td>
            <input type="number" step="any" min="0.0001" name="lines[${rowIndex}][received_qty]" class="form-control form-control-sm qty-input text-end fw-bold" value="${data ? data.received_qty : 10}" required oninput="calcRow(${rowIndex})">
        </td>
        <td>
            <input type="number" step="any" min="0" name="lines[${rowIndex}][unit_cost]" class="form-control form-control-sm price-input text-end" placeholder="0.00" value="${data ? data.unit_cost : 0}" required oninput="calcRow(${rowIndex})">
        </td>
        <td>
            <input type="number" step="any" min="0" max="100" name="lines[${rowIndex}][discount_pct]" class="form-control form-control-sm disc-input text-end" placeholder="0" value="${data ? data.discount_pct : 0}" oninput="calcRow(${rowIndex})">
        </td>
        <td>
            <select name="lines[${rowIndex}][tax_category_id]" class="form-select form-select-sm tax-select" onchange="onTaxChange(${rowIndex})">
                ${taxOptions}
            </select>
            <input type="hidden" name="lines[${rowIndex}][tax_rate]" class="tax-input" value="${data ? data.tax_rate : 0}">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="lines[${rowIndex}][mrp]" class="form-control form-control-sm mrp-input text-end" placeholder="0.00" value="${data && data.mrp ? data.mrp : 0}">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="lines[${rowIndex}][selling_price]" class="form-control form-control-sm selling-input text-end" placeholder="0.00" value="${data && data.selling_price ? data.selling_price : 0}">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="lines[${rowIndex}][wholesale_price]" class="form-control form-control-sm wholesale-input text-end" placeholder="0.00" value="${data && data.wholesale_price ? data.wholesale_price : 0}">
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
        onProductSelect(rowIndex);
    }

    rowIndex++;
}

function onTaxChange(idx) {
    const row = document.getElementById(`row-${idx}`);
    if (!row) return;
    const taxSelect = row.querySelector('.tax-select');
    const opt = taxSelect.options[taxSelect.selectedIndex];
    const rate = opt ? (opt.getAttribute('data-rate') || 0) : 0;
    row.querySelector('.tax-input').value = parseFloat(rate).toFixed(2);
    calcRow(idx);
}

function onProductSelect(idx) {
    const row = document.getElementById(`row-${idx}`);
    if (!row) return;

    const prodSelect = row.querySelector('.product-select');
    const opt = prodSelect.options[prodSelect.selectedIndex];

    if (opt && opt.value) {
        const price = opt.getAttribute('data-price') || 0;
        const unitId = opt.getAttribute('data-unit');
        const taxCatId = opt.getAttribute('data-tax-id') || '';
        const taxRate = opt.getAttribute('data-tax') || 0;
        const mrp = opt.getAttribute('data-mrp') || 0;
        const selling = opt.getAttribute('data-selling') || 0;
        const wholesale = opt.getAttribute('data-wholesale') || 0;

        row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
        row.querySelector('.mrp-input').value = parseFloat(mrp).toFixed(2);
        row.querySelector('.selling-input').value = parseFloat(selling).toFixed(2);
        row.querySelector('.wholesale-input').value = parseFloat(wholesale).toFixed(2);

        const taxSelect = row.querySelector('.tax-select');
        if (taxCatId) {
            taxSelect.value = taxCatId;
            row.querySelector('.tax-input').value = parseFloat(taxRate).toFixed(2);
        } else {
            taxSelect.value = '';
            row.querySelector('.tax-input').value = '0.00';
        }

        if (unitId) {
            row.querySelector('.unit-select').value = unitId;
        }
    }

    calcRow(idx);
}

function removeRow(idx) {
    const tbody = document.getElementById('linesBody');
    if (tbody.children.length <= 1) {
        alert('Goods Receipt must have at least one batch item.');
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

    const freight = parseFloat(document.getElementById('freightInput').value) || 0;
    const handling = parseFloat(document.getElementById('handlingInput').value) || 0;
    const other = parseFloat(document.getElementById('otherInput').value) || 0;
    const landedTotal = freight + handling + other;
    const totalValuation = grandTotal + landedTotal;

    document.getElementById('lblSubtotal').innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblDiscount').innerText = totalDisc.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblTaxable').innerText = taxable.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblTax').innerText = totalTax.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblGrandTotal').innerText = grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblLanded').innerText = landedTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblValuation').innerText = totalValuation.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function onPoChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        const sup = opt.getAttribute('data-supplier');
        const wh = opt.getAttribute('data-warehouse');
        if (sup) document.getElementById('supplierSelect').value = sup;
        if (wh) document.getElementById('warehouseSelect').value = wh;
    }
}

// Prefill lines if PO was selected
document.addEventListener('DOMContentLoaded', function () {
    @if($selectedPo && $selectedPo->lines->count() > 0)
        @foreach($selectedPo->lines as $l)
            addRow({
                purchase_order_line_id: {{ $l->id }},
                product_id: {{ $l->product_id }},
                batch_number: 'BATCH-{{ strtoupper(Str::random(5)) }}',
                expiry_date_ad: '{{ now()->addYears(2)->format("Y-m-d") }}',
                location_id: {{ $locations->first()?->id ?? 1 }},
                unit_id: {{ $l->unit_id }},
                received_qty: {{ $l->ordered_qty - $l->received_base_qty > 0 ? $l->ordered_qty - $l->received_base_qty : $l->ordered_qty }},
                unit_cost: {{ $l->unit_price }},
                discount_pct: {{ $l->discount_pct }},
                tax_category_id: '{{ $l->tax_category_id ?? ($l->product?->tax_category_id ?? "") }}',
                tax_rate: {{ $l->tax_rate }},
                mrp: {{ $l->product?->mrp ?? 0 }},
                selling_price: {{ $l->product?->selling_price ?? 0 }},
                wholesale_price: {{ $l->product?->wholesale_price ?? 0 }}
            });
        @endforeach
    @else
        addRow();
    @endif
});
</script>
@endpush
@endsection
