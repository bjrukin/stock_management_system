@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-arrow-return-left me-2"></i>Create Purchase Return (Debit Note)</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.pharma.purchase-returns') }}">Purchase Returns</a></li>
                            <li class="breadcrumb-item active" aria-current="page">New Return</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.pharma.purchase-returns') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Returns
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

        <form action="{{ route('admin.pharma.purchase-return.store') }}" method="POST" id="prtnForm">
            @csrf

            <!-- Header Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Return Header Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplierSelect" class="form-select" required>
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ (old('supplier_id') == $supplier->id || ($selectedGrn && $selectedGrn->supplier_id == $supplier->id)) ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" id="warehouseSelect" class="form-select" required>
                                <option value="">-- Select Source Warehouse --</option>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ (old('warehouse_id') == $wh->id || ($selectedGrn && $selectedGrn->warehouse_id == $wh->id) || $wh->is_primary) ? 'selected' : '' }}>
                                    {{ $wh->name }} ({{ $wh->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">GRN Reference (Optional)</label>
                            <select name="goods_receipt_id" id="grnSelect" class="form-select" onchange="onGrnChange(this)">
                                <option value="">-- Direct Return (No GRN Ref) --</option>
                                @foreach($postedReceipts as $grn)
                                <option value="{{ $grn->id }}" {{ (old('goods_receipt_id') == $grn->id || ($selectedGrn && $selectedGrn->id == $grn->id)) ? 'selected' : '' }} data-supplier="{{ $grn->supplier_id }}" data-warehouse="{{ $grn->warehouse_id }}">
                                    {{ $grn->grn_number }} - {{ $grn->supplier->name }} ({{ $grn->receipt_date_ad }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Return Date (AD) <span class="text-danger">*</span></label>
                            <input type="date" name="return_date_ad" class="form-control" value="{{ old('return_date_ad', $defaultDate) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reason for Return <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control" placeholder="e.g. Broken ampoules on delivery, near expiry batch, DDA quality recall..." value="{{ old('reason') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Common Presets</label>
                            <div class="d-flex gap-2 flex-wrap pt-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.querySelector('input[name=reason]').value='Damaged in transit / broken pack'">Damaged</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.querySelector('input[name=reason]').value='Near expiry shipment rejected'">Near Expiry</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.querySelector('input[name=reason]').value='Excess shipment received over PO'">Excess Qty</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.querySelector('input[name=reason]').value='Quality recall / defective packaging'">Quality Recall</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title m-0 fw-bold text-danger"><i class="bi bi-box-arrow-right me-2"></i>Returned Batches (Outflow Deduction)</h5>
                        <small class="text-muted">Stock will be atomically deducted from selected shelf location and supplier liability reduced</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i>Add Return Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="linesTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 24%;">Product <span class="text-danger">*</span></th>
                                    <th style="width: 18%;">Batch Number <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">Location <span class="text-danger">*</span></th>
                                    <th style="width: 10%;">Unit</th>
                                    <th style="width: 10%;">Return Qty <span class="text-danger">*</span></th>
                                    <th style="width: 11%;">Unit Price (NPR) <span class="text-danger">*</span></th>
                                    <th style="width: 8%;" class="text-end">Line Total</th>
                                    <th style="width: 4%;" class="text-center">Del</th>
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
                                    <td class="text-muted">Return Subtotal:</td>
                                    <td class="text-end fw-semibold">NPR <span id="lblSubtotal">0.00</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tax Amount:</td>
                                    <td class="text-end fw-semibold">+ NPR <span id="lblTax">0.00</span></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fs-5 fw-bold text-dark">Total Debit Note (AP Reduction):</td>
                                    <td class="fs-5 text-end fw-bold text-danger">NPR <span id="lblGrandTotal">0.00</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.pharma.purchase-returns') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" name="action" value="save_draft" class="btn btn-secondary">
                    <i class="bi bi-save me-1"></i>Save as Draft Return
                </button>
                <button type="submit" name="action" value="save_and_post" class="btn btn-danger px-4">
                    <i class="bi bi-arrow-return-left me-1"></i>Save & Post Return (Deduct Stock)
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
const batchesData = @json($batches);
let rowIndex = 0;

function addRow(data = null) {
    const tbody = document.getElementById('linesBody');
    const tr = document.createElement('tr');
    tr.id = `row-${rowIndex}`;

    let productOptions = '<option value="">-- Choose Product --</option>';
    productsData.forEach(p => {
        const isSel = (data && data.product_id == p.id) ? 'selected' : '';
        productOptions += `<option value="${p.id}" ${isSel} data-unit="${p.base_unit_id}">
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

    tr.innerHTML = `
        <td>
            <select name="lines[${rowIndex}][product_id]" class="form-select form-select-sm product-select" required onchange="onProductSelect(${rowIndex})">
                ${productOptions}
            </select>
            ${data && data.goods_receipt_line_id ? `<input type="hidden" name="lines[${rowIndex}][goods_receipt_line_id]" value="${data.goods_receipt_line_id}">` : ''}
        </td>
        <td>
            <select name="lines[${rowIndex}][batch_id]" class="form-select form-select-sm batch-select" required onchange="onBatchSelect(${rowIndex})">
                <option value="">-- Select Batch --</option>
            </select>
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
            <input type="number" step="any" min="0.0001" name="lines[${rowIndex}][returned_qty]" class="form-control form-control-sm qty-input text-end fw-bold text-danger" value="${data ? data.returned_qty : 1}" required oninput="calcRow(${rowIndex})">
        </td>
        <td>
            <input type="number" step="any" min="0" name="lines[${rowIndex}][unit_price]" class="form-control form-control-sm price-input text-end" value="${data ? data.unit_price : 0}" required oninput="calcRow(${rowIndex})">
            <input type="hidden" name="lines[${rowIndex}][tax_amount]" class="tax-input" value="0.00">
        </td>
        <td class="text-end fw-semibold text-danger">
            <span class="row-total" id="row-total-${rowIndex}">0.00</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);

    // Populate batches for initial selected product
    populateBatches(rowIndex, data ? data.batch_id : null);

    if (data) {
        calcRow(rowIndex);
    }

    rowIndex++;
}

function populateBatches(idx, selectedBatchId = null) {
    const row = document.getElementById(`row-${idx}`);
    if (!row) return;

    const prodId = row.querySelector('.product-select').value;
    const batchSelect = row.querySelector('.batch-select');
    batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';

    if (!prodId) return;

    const matchingBatches = batchesData.filter(b => b.product_id == prodId);
    matchingBatches.forEach(b => {
        const isSel = (selectedBatchId && selectedBatchId == b.id) ? 'selected' : '';
        batchSelect.innerHTML += `<option value="${b.id}" ${isSel} data-cost="${b.cost_price || 0}">
            ${b.batch_number} (Exp: ${b.expiry_date_ad || 'N/A'}, Cost: NPR ${parseFloat(b.cost_price || 0).toFixed(2)})
        </option>`;
    });

    if (selectedBatchId) {
        onBatchSelect(idx);
    }
}

function onProductSelect(idx) {
    const row = document.getElementById(`row-${idx}`);
    if (!row) return;

    const prodSelect = row.querySelector('.product-select');
    const opt = prodSelect.options[prodSelect.selectedIndex];

    if (opt && opt.value) {
        const unitId = opt.getAttribute('data-unit');
        if (unitId) {
            row.querySelector('.unit-select').value = unitId;
        }
    }

    populateBatches(idx);
    calcRow(idx);
}

function onBatchSelect(idx) {
    const row = document.getElementById(`row-${idx}`);
    if (!row) return;

    const batchSelect = row.querySelector('.batch-select');
    const opt = batchSelect.options[batchSelect.selectedIndex];

    if (opt && opt.value) {
        const cost = opt.getAttribute('data-cost') || 0;
        row.querySelector('.price-input').value = parseFloat(cost).toFixed(2);
    }

    calcRow(idx);
}

function removeRow(idx) {
    const tbody = document.getElementById('linesBody');
    if (tbody.children.length <= 1) {
        alert('Purchase Return must have at least one return item.');
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
    const lineTotal = qty * price;

    document.getElementById(`row-total-${idx}`).innerText = lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    calcTotals();
}

function calcTotals() {
    let subtotal = 0;

    const rows = document.querySelectorAll('#linesBody tr');
    rows.forEach(r => {
        const qty = parseFloat(r.querySelector('.qty-input').value) || 0;
        const price = parseFloat(r.querySelector('.price-input').value) || 0;
        subtotal += (qty * price);
    });

    document.getElementById('lblSubtotal').innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('lblTax').innerText = "0.00";
    document.getElementById('lblGrandTotal').innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function onGrnChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        const sup = opt.getAttribute('data-supplier');
        const wh = opt.getAttribute('data-warehouse');
        if (sup) document.getElementById('supplierSelect').value = sup;
        if (wh) document.getElementById('warehouseSelect').value = wh;
    }
}

// Prefill lines if GRN was selected
document.addEventListener('DOMContentLoaded', function () {
    @if($selectedGrn && $selectedGrn->lines->count() > 0)
        @foreach($selectedGrn->lines as $l)
            addRow({
                goods_receipt_line_id: {{ $l->id }},
                product_id: {{ $l->product_id }},
                batch_id: {{ $l->batch_id ?? 'null' }},
                location_id: {{ $l->location_id }},
                unit_id: {{ $l->unit_id }},
                returned_qty: 1,
                unit_price: {{ $l->effective_unit_cost ?? $l->unit_cost }}
            });
        @endforeach
    @else
        addRow();
    @endif
});
</script>
@endpush
@endsection
