@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.pharma.stock-adjustments') }}" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i>Back to Adjustments
                    </a>
                    <h4 class="page-title-main text-primary m-0"><i class="bi bi-sliders me-2"></i>Create Stock Adjustment (Physical Count / Write-off)</h4>
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

        <form action="{{ route('admin.pharma.stock-adjustment.store') }}" method="POST">
            @csrf
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-bold text-dark">
                    <i class="bi bi-info-circle me-1 text-primary"></i>Adjustment Header Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" @selected(old('warehouse_id') == $wh->id)>{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Adjustment Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="PHYSICAL_COUNT" @selected(old('type') == 'PHYSICAL_COUNT')>Physical Stock Count (Audit Variance)</option>
                                <option value="DAMAGE_WRITE_OFF" @selected(old('type') == 'DAMAGE_WRITE_OFF')>Damaged Goods Write-off</option>
                                <option value="EXPIRY_DISPOSAL" @selected(old('type') == 'EXPIRY_DISPOSAL')>Expired Goods Disposal</option>
                                <option value="SAMPLE_ISSUE" @selected(old('type') == 'SAMPLE_ISSUE')>Doctor Sample Issue</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Adjustment Date (AD) <span class="text-danger">*</span></label>
                            <input type="date" name="adjustment_date_ad" class="form-control" value="{{ old('adjustment_date_ad', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Reason / Audit Remarks</label>
                            <input type="text" name="reason" class="form-control" placeholder="Audit findings, incident report #, or stock count sheet reference..." value="{{ old('reason') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold text-dark"><i class="bi bi-list-check me-1 text-primary"></i>Items for Adjustment</span>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i>Add Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 32%;">Product / Medicine</th>
                                    <th style="width: 25%;">Batch #</th>
                                    <th style="width: 14%;">Book Qty</th>
                                    <th style="width: 14%;">Counted / Physical Qty</th>
                                    <th style="width: 10%;">Variance</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="adjBody">
                                <!-- Dynamic Rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.pharma.stock-adjustments') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-check2-circle me-1"></i>Submit for Maker-Checker Approval
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const products = @json($products);
let rowIndex = 0;

function addRow() {
    const tbody = document.getElementById('adjBody');
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
            <input type="number" step="1" min="0" name="lines[${rowIndex}][book_quantity]" id="book_${rowIndex}" class="form-control form-control-sm" value="0" required oninput="calcVariance(${rowIndex})">
        </td>
        <td>
            <input type="number" step="1" min="0" name="lines[${rowIndex}][counted_quantity]" id="counted_${rowIndex}" class="form-control form-control-sm" value="0" required oninput="calcVariance(${rowIndex})">
        </td>
        <td class="text-center fw-bold">
            <span id="var_${rowIndex}" class="badge bg-secondary">0</span>
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

function calcVariance(idx) {
    const book = parseInt(document.getElementById(`book_${idx}`)?.value || 0);
    const counted = parseInt(document.getElementById(`counted_${idx}`)?.value || 0);
    const variance = counted - book;

    const span = document.getElementById(`var_${idx}`);
    if (span) {
        span.innerText = (variance > 0 ? `+${variance}` : `${variance}`);
        if (variance > 0) {
            span.className = 'badge bg-success';
        } else if (variance < 0) {
            span.className = 'badge bg-danger';
        } else {
            span.className = 'badge bg-secondary';
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
