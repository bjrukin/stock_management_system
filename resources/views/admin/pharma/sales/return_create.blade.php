@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.sales-returns') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Sales Returns
                    </a>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-arrow-return-left me-2"></i>Create Customer Sales Return</h4>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong><i class="bi bi-exclamation-triangle me-1"></i>Validation Errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('admin.pharma.sales-return.store') }}" method="POST">
            @csrf
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-bold text-dark">
                    <i class="bi bi-info-circle me-1 text-primary"></i>Return Header Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Customer / Pharmacy <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->name }} ({{ $c->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Receiving Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" @selected(old('warehouse_id') == $wh->id)>{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Against Sales Invoice (Optional)</label>
                            <select name="sale_invoice_id" class="form-select">
                                <option value="">-- Standalone Return --</option>
                                @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" @selected(old('sale_invoice_id') == $inv->id)>{{ $inv->invoice_number }} - {{ $inv->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Return Date (AD) <span class="text-danger">*</span></label>
                            <input type="date" name="return_date_ad" class="form-control" value="{{ old('return_date_ad', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Return Reason / Notes</label>
                            <input type="text" name="reason" class="form-control" placeholder="e.g. Near expiry exchange, damaged packaging, excess order..." value="{{ old('reason') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="credit_note_issued" id="creditNote" value="1" checked>
                                <label class="form-check-label fw-bold text-success" for="creditNote">
                                    Issue Credit Note (Reduce Customer Receivable)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lines Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold text-dark"><i class="bi bi-capsule me-1 text-primary"></i>Returned Medicine Items</span>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i>Add Returned Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="returnTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 28%;">Product / Medicine</th>
                                    <th style="width: 20%;">Batch #</th>
                                    <th style="width: 14%;">Unit</th>
                                    <th style="width: 10%;">Returned Qty</th>
                                    <th style="width: 12%;">Unit Price</th>
                                    <th style="width: 12%;">Condition</th>
                                    <th class="text-end" style="width: 12%;">Line Total</th>
                                    <th style="width: 4%;"></th>
                                </tr>
                            </thead>
                            <tbody id="returnBody">
                                <!-- Dynamic Rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                    <div class="fs-5 fw-bold text-dark">
                        Total Return Amount: NPR <span id="lblTotal" class="text-success">0.00</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.pharma.sales-returns') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-check2-circle me-1"></i>Save Draft Return
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const products = @json($products);
const units = @json($units);

let rowIndex = 0;

function addRow() {
    const tbody = document.getElementById('returnBody');
    const tr = document.createElement('tr');
    tr.id = `row_${rowIndex}`;

    let prodOptions = '<option value="">-- Select Product --</option>';
    products.forEach(p => {
        prodOptions += `<option value="${p.id}" data-price="${p.selling_price}" data-unit="${p.base_unit_id}">${p.name} (${p.code})</option>`;
    });

    let unitOptions = '';
    units.forEach(u => {
        unitOptions += `<option value="${u.id}">${u.name} (${u.abbreviation})</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="lines[${rowIndex}][product_id]" class="form-select form-select-sm" required onchange="onProductChange(${rowIndex})">
                ${prodOptions}
            </select>
        </td>
        <td>
            <select name="lines[${rowIndex}][batch_id]" id="batch_${rowIndex}" class="form-select form-select-sm" required>
                <option value="">-- Select Product First --</option>
            </select>
        </td>
        <td>
            <select name="lines[${rowIndex}][unit_id]" id="unit_${rowIndex}" class="form-select form-select-sm" required>
                ${unitOptions}
            </select>
        </td>
        <td>
            <input type="number" step="1" min="1" name="lines[${rowIndex}][returned_qty]" id="qty_${rowIndex}" class="form-control form-control-sm" value="1" required oninput="calcLine(${rowIndex})">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="lines[${rowIndex}][unit_price]" id="price_${rowIndex}" class="form-control form-control-sm" value="0.00" required oninput="calcLine(${rowIndex})">
        </td>
        <td>
            <select name="lines[${rowIndex}][condition]" class="form-select form-select-sm">
                <option value="saleable">Saleable (Resell)</option>
                <option value="damaged">Damaged (Quarantine)</option>
                <option value="expired">Expired (Disposal)</option>
            </select>
        </td>
        <td class="text-end fw-bold">
            <span id="lineTotal_${rowIndex}">0.00</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1" onclick="removeRow(${rowIndex})">
                <i class="bi bi-x"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    rowIndex++;
}

function onProductChange(idx) {
    const row = document.getElementById(`row_${idx}`);
    const sel = row.querySelector(`select[name="lines[${idx}][product_id]"]`);
    const opt = sel.options[sel.selectedIndex];
    const prodId = opt ? parseInt(opt.value) : null;

    const batchSel = document.getElementById(`batch_${idx}`);
    batchSel.innerHTML = '<option value="">-- Select Batch --</option>';

    if (prodId) {
        const prod = products.find(p => p.id === prodId);
        if (prod && prod.batches) {
            prod.batches.forEach(b => {
                batchSel.innerHTML += `<option value="${b.id}">${b.batch_number} (Exp: ${b.expiry_date_ad})</option>`;
            });
        }
        if (opt.dataset.price) {
            document.getElementById(`price_${idx}`).value = opt.dataset.price;
        }
        if (opt.dataset.unit) {
            document.getElementById(`unit_${idx}`).value = opt.dataset.unit;
        }
    }
    calcLine(idx);
}

function calcLine(idx) {
    const qty = parseFloat(document.getElementById(`qty_${idx}`)?.value || 0);
    const price = parseFloat(document.getElementById(`price_${idx}`)?.value || 0);
    const total = qty * price;

    const span = document.getElementById(`lineTotal_${idx}`);
    if (span) span.innerText = total.toFixed(2);

    calculateTotals();
}

function removeRow(idx) {
    const tr = document.getElementById(`row_${idx}`);
    if (tr) tr.remove();
    calculateTotals();
}

function calculateTotals() {
    let grand = 0;
    const rows = document.querySelectorAll('#returnBody tr');
    rows.forEach(tr => {
        const id = tr.id.replace('row_', '');
        const qty = parseFloat(document.getElementById(`qty_${id}`)?.value || 0);
        const price = parseFloat(document.getElementById(`price_${id}`)?.value || 0);
        grand += (qty * price);
    });

    document.getElementById('lblTotal').innerText = grand.toFixed(2);
}

document.addEventListener('DOMContentLoaded', () => {
    addRow();
});
</script>
@endsection
