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
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-receipt me-2"></i>Create Sales Invoice</h4>
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

        <form action="{{ route('admin.pharma.sale-invoice.store') }}" method="POST" id="invoiceForm">
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
                                    @selected(old('customer_id') == $c->id)>
                                    {{ $c->name }} ({{ $c->code }})
                                </option>
                                @endforeach
                            </select>
                            <div id="customerAlert" class="mt-1 small" style="display:none;"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dispatch Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" @selected(old('warehouse_id') == $wh->id)>{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Invoice Date (AD) <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date_ad" class="form-control" value="{{ old('invoice_date_ad', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Payment Terms <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="credit" @selected(old('payment_method') == 'credit')>Credit Account</option>
                                <option value="cash" @selected(old('payment_method') == 'cash')>Immediate Cash</option>
                                <option value="bank_transfer" @selected(old('payment_method') == 'bank_transfer')>Bank Transfer</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Notes / Remarks</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional delivery instructions or reference..." value="{{ old('notes') }}">
                        </div>

                        <div class="col-md-4 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="auto_fefo" id="autoFefo" value="1" checked>
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
                        <table class="table table-bordered align-middle mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 32%;">Product / Medicine</th>
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
                                            <input type="number" step="0.01" min="0" name="delivery_charge" id="deliveryCharge" class="form-control form-control-sm text-end" value="0.00" oninput="calculateTotals()">
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

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.pharma.sales-invoices') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-check2-circle me-1"></i>Generate Draft Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const products = @json($products);
const units = @json($units);

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

function addRow() {
    const tbody = document.getElementById('itemsBody');
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
            <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm" required onchange="onProductChange(${rowIndex})">
                ${prodOptions}
            </select>
        </td>
        <td>
            <select name="items[${rowIndex}][unit_id]" id="unit_${rowIndex}" class="form-select form-select-sm" required>
                ${unitOptions}
            </select>
        </td>
        <td>
            <input type="number" step="1" min="1" name="items[${rowIndex}][qty]" id="qty_${rowIndex}" class="form-control form-control-sm" value="1" required oninput="calcLine(${rowIndex})">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" id="price_${rowIndex}" class="form-control form-control-sm" value="0.00" required oninput="calcLine(${rowIndex})">
        </td>
        <td>
            <input type="number" step="0.01" min="0" max="100" name="items[${rowIndex}][discount_pct]" id="disc_${rowIndex}" class="form-control form-control-sm" value="0" oninput="calcLine(${rowIndex})">
        </td>
        <td>
            <input type="number" step="0.01" min="0" max="100" name="items[${rowIndex}][tax_rate]" id="tax_${rowIndex}" class="form-control form-control-sm" value="0" oninput="calcLine(${rowIndex})">
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
    rowIndex++;
}

function onProductChange(idx) {
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
    const grand = (subtotal - totalDiscount) + totalTax + delivery;

    document.getElementById('lblSubtotal').innerText = subtotal.toFixed(2);
    document.getElementById('lblDiscount').innerText = totalDiscount.toFixed(2);
    document.getElementById('lblTax').innerText = totalTax.toFixed(2);
    document.getElementById('lblGrandTotal').innerText = grand.toFixed(2);
}

document.addEventListener('DOMContentLoaded', () => {
    addRow();
    updateCustomerInfo();
});
</script>
@endsection
