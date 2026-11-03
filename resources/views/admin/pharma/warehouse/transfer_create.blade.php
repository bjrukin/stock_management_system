@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.stock-transfers') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Transfers
                    </a>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-arrow-left-right me-2"></i>Create Inter-Warehouse Transfer</h4>
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

        <form action="{{ route('admin.pharma.stock-transfer.store') }}" method="POST">
            @csrf
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-bold text-dark">
                    <i class="bi bi-info-circle me-1 text-primary"></i>Transfer Route Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Origin (Source) Warehouse <span class="text-danger">*</span></label>
                            <select name="from_warehouse_id" id="fromWh" class="form-select" required>
                                <option value="">-- Select Source Warehouse --</option>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" @selected(old('from_warehouse_id') == $wh->id)>{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Destination Warehouse <span class="text-danger">*</span></label>
                            <select name="to_warehouse_id" id="toWh" class="form-select" required>
                                <option value="">-- Select Destination Warehouse --</option>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" @selected(old('to_warehouse_id') == $wh->id)>{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Transfer Date (AD) <span class="text-danger">*</span></label>
                            <input type="date" name="transfer_date_ad" class="form-control" value="{{ old('transfer_date_ad', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / Purpose of Transfer</label>
                            <input type="text" name="notes" class="form-control" placeholder="e.g. Branch stock replenishment, seasonal demand rebalance..." value="{{ old('notes') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold text-dark"><i class="bi bi-box-seam me-1 text-primary"></i>Items to Transfer</span>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i>Add Medicine Line
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%;">Product / Medicine</th>
                                    <th style="width: 35%;">Batch #</th>
                                    <th style="width: 20%;">Transfer Qty</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="transferBody">
                                <!-- Dynamic Rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.pharma.stock-transfers') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-check2-circle me-1"></i>Save Draft Transfer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const products = @json($products);
let rowIndex = 0;

function addRow() {
    const tbody = document.getElementById('transferBody');
    const tr = document.createElement('tr');
    tr.id = `row_${rowIndex}`;

    let prodOptions = '<option value="">-- Select Product --</option>';
    products.forEach(p => {
        prodOptions += `<option value="${p.id}">${p.name} (${p.code})</option>`;
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
            <input type="number" step="1" min="1" name="lines[${rowIndex}][quantity]" class="form-control form-control-sm" value="10" required>
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
    }
}

function removeRow(idx) {
    const tr = document.getElementById(`row_${idx}`);
    if (tr) tr.remove();
}

document.addEventListener('DOMContentLoaded', () => {
    addRow();
});
</script>
@endsection
