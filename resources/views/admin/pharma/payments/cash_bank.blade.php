@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-bank me-2"></i>Cash & Bank Treasury Accounts</h4>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Account
                </button>
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

        <div class="row mb-4">
            @forelse($accounts as $acc)
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="card-title m-0 fw-bold">{{ $acc->name }}</h5>
                            <small class="text-muted">{{ strtoupper($acc->type) }} &bull; {{ $acc->bank_name ?? 'Cash In Hand' }} &bull; {{ $acc->account_number ?? 'Counter' }}</small>
                        </div>
                        <span class="badge @if($acc->is_active) bg-success @else bg-secondary @endif">
                            {{ $acc->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded text-center mb-3">
                            <span class="text-muted small text-uppercase">Current Cleared Balance</span>
                            <h3 class="fw-bold text-primary my-1">NPR {{ number_format($acc->current_balance, 2) }}</h3>
                        </div>

                        <h6 class="fw-bold mb-2 small text-muted text-uppercase">Recent Transactions</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Deposit</th>
                                        <th>Withdrawal</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($acc->transactions as $tx)
                                    <tr>
                                        <td><small>{{ $tx->transaction_date_ad }}</small></td>
                                        <td><span class="badge bg-light text-dark border">{{ strtoupper(str_replace('_', ' ', $tx->transaction_type)) }}</span></td>
                                        <td class="text-success fw-semibold">
                                            {{ $tx->deposit_amount > 0 ? '+ ' . number_format($tx->deposit_amount, 2) : '—' }}
                                        </td>
                                        <td class="text-danger fw-semibold">
                                            {{ $tx->withdrawal_amount > 0 ? '- ' . number_format($tx->withdrawal_amount, 2) : '—' }}
                                        </td>
                                        <td class="fw-bold">NPR {{ number_format($tx->running_balance, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center text-muted small py-2">No transactions recorded.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12"><div class="alert alert-info">No cash or bank accounts found. Click "Add Account" to create one.</div></div>
            @endforelse
        </div>
    </div>
</div>

<!-- Create Account Modal -->
<div class="modal fade" id="createAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.pharma.cash-bank.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-bank me-1 text-primary"></i>Add Treasury Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" class="form-control" placeholder="e.g. Nabil Current A/C or Main Cash Drawer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Type <span class="text-danger">*</span></label>
                        <select name="account_type" class="form-select" required>
                            <option value="bank">Commercial Bank Account</option>
                            <option value="cash">Cash In Hand / Till</option>
                            <option value="digital_wallet">Digital Wallet (Fonepay / eSewa)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="e.g. Nabil Bank Ltd.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Number</label>
                        <input type="text" name="account_number" class="form-control" placeholder="e.g. 01234567890123">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Branch Name</label>
                        <input type="text" name="branch" class="form-control" placeholder="e.g. Putalisadak Branch">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Opening Balance (NPR)</label>
                        <input type="number" step="0.01" min="0" name="opening_balance" class="form-control" placeholder="0.00" value="0.00">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveAcc" value="1" checked>
                        <label class="form-check-label" for="isActiveAcc">Account is Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Account</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
