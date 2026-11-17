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
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-receipt me-2"></i>{{ $invoice ? 'Edit Sales Invoice' : 'Create Sales Invoice' }}</h4>
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

        <form action="{{ $invoice ? route('admin.pharma.sale-invoice.update', $invoice->id) : route('admin.pharma.sale-invoice.store') }}" method="POST" id="invoiceForm">
            @csrf
            <!-- Header Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-bold text-dark">
                    <i class="bi bi-person-lines-fill me-1 text-primary"></i>Customer & Invoice Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Customer / Pharmacy <span class="text-danger">*</span></label>
                            <select name="customer_id" id="customerSelect" class="form-select" required onchange="updateCustomerInfo()">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" 
                                    data-pan="{{ $c->pan_vat_number }}" 
                                    data-credit-limit="{{ $c->credit_limit }}" 
                                    data-balance="{{ $c->current_balance }}"
                                    data-credit-stop="{{ $c->credit_stop ? '1' : '0' }}"
                                    @selected(old('customer_id', $invoice->customer_id ?? null) == $c->id)>
                                    {{ $c->name }} ({{ $c->code }})
                                </option>
                                @endforeach
                            </select>
                            <div id="customerAlert" class="mt-1 small" style="display:none;"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dispatch Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" id="warehouseSelect" onchange="onWarehouseChange()" class="form-select" required>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" @selected(old('warehouse_id', $invoice->warehouse_id ?? null) == $wh->id)>{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Invoice Date (AD) <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date_ad" class="form-control" value="{{ old('invoice_date_ad', $invoice?->invoice_date_ad?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Payment Terms <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="credit" @selected(old('payment_method', $invoice->payment_method ?? null) == 'credit')>Credit Account</option>
                                <option value="cash" @selected(old('payment_method', $invoice->payment_method ?? null) == 'cash')>Immediate Cash</option>
                                <option value="bank_transfer" @selected(old('payment_method', $invoice->payment_method ?? null) == 'bank_transfer')>Bank Transfer</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Notes / Remarks</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional delivery instructions or reference..." value="{{ old('notes', $invoice->notes ?? null) }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold" for="paidAmount">Paid Amount</label>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="paidAmount" class="form-control" value="{{ old('paid_amount', $invoice->paid_amount ?? 0) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold" for="roundOff">Round Off</label>
                            <input type="number" step="0.01" name="round_off" id="roundOff" class="form-control" value="{{ old('round_off', $invoice->round_off ?? 0) }}" oninput="calculateTotals()">
                        </div>
                        <div class="col-md-4 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="auto_fefo" value="0">
                                <input class="form-check-input" type="checkbox" name="auto_fefo" id="autoFefo" value="1" @checked(old('auto_fefo', 1))>
                                <label class="form-check-label fw-bold text-success" for="autoFefo">
                                    <i class="bi bi-magic me-1"></i>Automated FEFO Batch Allocation
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Items Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold text-dark"><i class="bi bi-cart-plus me-1 text-primary"></i>Invoice Line Items</span>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i>Add Medicine Line
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="itemsTable" style="min-width: 1100px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 32%;">Product / Medicine</th>
                                    <th>Batch (or automatic)</th>
                                    <th>Location</th>
                                    <th style="width: 14%;">Unit</th>
                                    <th style="width: 12%;">Qty</th>
                                    <th style="width: 14%;">Unit Price (NPR)</th>
                                    <th style="width: 10%;">Disc %</th>
                                    <th style="width: 10%;">Tax %</th>
                                    <th style="width: 14%;">Line Total</th>
                                    <th style="width: 4%;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Dynamic Rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
                                <i class="bi bi-plus-lg me-1"></i>Add Another Item
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless text-end mb-0">
                                    <tr>
                                        <td class="fw-semibold">Subtotal:</td>
                                        <td class="fw-bold" style="width: 140px;">NPR <span id="lblSubtotal">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Total Discount:</td>
                                        <td class="text-danger">NPR <span id="lblDiscount">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">VAT / Tax:</td>
                                        <td class="text-info">NPR <span id="lblTax">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Delivery Charge:</td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="delivery_charge" id="deliveryCharge" class="form-control form-control-sm text-end" value="{{ old('delivery_charge', $invoice->delivery_charge ?? 0) }}" oninput="calculateTotals()">
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="fw-bold fs-5 text-dark">Grand Total:</td>
                                        <td class="fw-bold fs-5 text-success">NPR <span id="lblGrandTotal">0.00</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.pharma.sales-invoices') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-check2-circle me-1"></i>{{ $invoice ? 'Save Changes' : 'Generate Draft Invoice' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const products = {{ Illuminate\Support\Js::from($products) }};
const units = {{ Illuminate\Support\Js::from($units) }};
const warehouses = {{ Illuminate\Support\Js::from($warehouses) }};
const initialItems = {{ Illuminate\Support\Js::from(old('items', $initialItems)) }};
const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[c]));

let rowIndex = 0;

function updateCustomerInfo() {
    const sel = document.getElementById('customerSelect');
    const opt = sel.options[sel.selectedIndex];
    const alertDiv = document.getElementById('customerAlert');
    if (!opt || !opt.value) {
        alertDiv.style.display = 'none';
        return;
    }
    const limit = parseFloat(opt.dataset.creditLimit || 0);
    const balance = parseFloat(opt.dataset.balance || 0);
    const stop = opt.dataset.creditStop === '1';

    alertDiv.style.display = 'block';
    if (stop) {
        alertDiv.className = 'mt-1 small text-danger fw-bold';
        alertDiv.innerHTML = '<i class="bi bi-x-circle me-1"></i>Customer is ON CREDIT STOP. Invoicing will fail.';
    } else {
        alertDiv.className = 'mt-1 small text-muted';
        alertDiv.innerHTML = `Credit Limit: NPR ${limit.toFixed(2)} | Current Receivable: NPR ${balance.toFixed(2)}`;
    }
}

function addRow(item = {}) {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = `row_${rowIndex}`;

    let prodOptions = '<option value="">-- Select Product --</option>';
    products.forEach(p => {
        prodOptions += `<option value="${p.id}" data-price="${p.selling_price}" data-unit="${p.base_unit_id}">${escapeHtml(p.name)} (${escapeHtml(p.code)})</option>`;
    });

    let unitOptions = '';
    units.forEach(u => {
        unitOptions += `<option value="${u.id}">${escapeHtml(u.name)} (${escapeHtml(u.abbreviation)})</option>`;
    });

    tr.innerHTML = `
        <td>
            <input type="hidden" name="items[${rowIndex}][tax_category_id]" id="taxCategory_${rowIndex}" value="">
            <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm" required onchange="onProductChange(${rowIndex})">
                ${prodOptions}
            </select>
        </td>
        <td>
            <select name="items[${rowIndex}][batch_id]" id="batch_${rowIndex}" class="form-select form-select-sm" aria-label="Batch"><option value="">Automatic allocation</option></select>
        </td>
        <td>
            <select name="items[${rowIndex}][location_id]" id="location_${rowIndex}" class="form-select form-select-sm" aria-label="Location"><option value="">Default location</option></select>
        </td>
        <td>
            <select name="items[${rowIndex}][unit_id]" id="unit_${rowIndex}" class="form-select form-select-sm" required>
                ${unitOptions}
            </select>
        </td>
        <td>
            <input type="number" step="0.0001" min="0.0001" name="items[${rowIndex}][qty]" id="qty_${rowIndex}" class="form-control form-control-sm" value="1" required oninput="calcLine(${rowIndex})">
        </td>
        <td>
            <input type="number" step="0.0001" min="0" name="items[${rowIndex}][unit_price]" id="price_${rowIndex}" class="form-control form-control-sm" value="0.00" required oninput="calcLine(${rowIndex})">
        </td>
        <td>
            <input type="number" step="0.0001" min="0" max="100" name="items[${rowIndex}][discount_pct]" id="disc_${rowIndex}" class="form-control form-control-sm" value="0" oninput="calcLine(${rowIndex})">
        </td>
        <td>
            <input type="number" step="0.0001" min="0" max="100" name="items[${rowIndex}][tax_rate]" id="tax_${rowIndex}" class="form-control form-control-sm" value="0" oninput="calcLine(${rowIndex})">
        </td>
        <td class="fw-bold text-end">
            <span id="lineTotal_${rowIndex}">0.00</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1" onclick="removeRow(${rowIndex})">
                <i class="bi bi-x"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    const idx = rowIndex++;
    tr.querySelector(`[name="items[${idx}][product_id]"]`).value = item.product_id ?? '';
    populateBatches(idx);
    populateLocations(idx);
    ['batch_id', 'location_id', 'unit_id', 'qty', 'unit_price', 'discount_pct', 'tax_rate', 'tax_category_id'].forEach(key => {
        if (item[key] !== undefined && item[key] !== null) tr.querySelector(`[name="items[${idx}][${key}]"]`).value = item[key];
    });
    calcLine(idx);
}

function populateBatches(idx) {
    const productId = document.querySelector(`[name="items[${idx}][product_id]"]`).value;
    const product = products.find(p => String(p.id) === productId);
    const select = document.getElementById(`batch_${idx}`);
    select.replaceChildren(new Option('Automatic allocation', ''));
    (product?.batches ?? []).forEach(batch => select.add(new Option(batch.batch_number, batch.id)));
}

function populateLocations(idx) {
    const warehouseId = document.getElementById('warehouseSelect').value;
    const warehouse = warehouses.find(w => String(w.id) === warehouseId);
    const select = document.getElementById(`location_${idx}`);
    select.replaceChildren(new Option('Default location', ''));
    (warehouse?.locations ?? []).forEach(location => select.add(new Option(location.name, location.id)));
}

function onWarehouseChange() {
    document.querySelectorAll('#itemsBody tr').forEach(row => populateLocations(row.id.replace('row_', '')));
}

function onProductChange(idx) {
    document.getElementById(`taxCategory_${idx}`).value = '';
    const row = document.getElementById(`row_${idx}`);
    const sel = row.querySelector(`select[name="items[${idx}][product_id]"]`);
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        const price = opt.dataset.price || '0.00';
        const unitId = opt.dataset.unit;
        document.getElementById(`price_${idx}`).value = price;
        if (unitId) {
            document.getElementById(`unit_${idx}`).value = unitId;
        }
    }
    populateBatches(idx);
    calcLine(idx);
}

function calcLine(idx) {
    const qty = parseFloat(document.getElementById(`qty_${idx}`)?.value || 0);
    const price = parseFloat(document.getElementById(`price_${idx}`)?.value || 0);
    const discPct = parseFloat(document.getElementById(`disc_${idx}`)?.value || 0);
    const taxPct = parseFloat(document.getElementById(`tax_${idx}`)?.value || 0);

    const gross = qty * price;
    const disc = (gross * discPct) / 100;
    const net = gross - disc;
    const tax = (net * taxPct) / 100;
    const total = net + tax;

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
    let subtotal = 0;
    let totalDiscount = 0;
    let totalTax = 0;

    const rows = document.querySelectorAll('#itemsBody tr');
    rows.forEach(tr => {
        const id = tr.id.replace('row_', '');
        const qty = parseFloat(document.getElementById(`qty_${id}`)?.value || 0);
        const price = parseFloat(document.getElementById(`price_${id}`)?.value || 0);
        const discPct = parseFloat(document.getElementById(`disc_${id}`)?.value || 0);
        const taxPct = parseFloat(document.getElementById(`tax_${id}`)?.value || 0);

        const gross = qty * price;
        const disc = (gross * discPct) / 100;
        const net = gross - disc;
        const tax = (net * taxPct) / 100;

        subtotal += gross;
        totalDiscount += disc;
        totalTax += tax;
    });

    const delivery = parseFloat(document.getElementById('deliveryCharge')?.value || 0);
    const roundOff = parseFloat(document.getElementById('roundOff')?.value || 0);
    const grand = (subtotal - totalDiscount) + totalTax + delivery + roundOff;

    document.getElementById('lblSubtotal').innerText = subtotal.toFixed(2);
    document.getElementById('lblDiscount').innerText = totalDiscount.toFixed(2);
    document.getElementById('lblTax').innerText = totalTax.toFixed(2);
    document.getElementById('lblGrandTotal').innerText = grand.toFixed(2);
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = Object.values(initialItems ?? {});
    if (saved.length) saved.forEach(item => addRow(item));
    else addRow();
    updateCustomerInfo();
});
</script>
@endsection
