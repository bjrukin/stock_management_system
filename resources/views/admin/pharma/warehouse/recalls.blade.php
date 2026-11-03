@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-danger m-0"><i class="bi bi-shield-slash me-2"></i>DDA Mandatory Batch Recalls & Quarantine</h4>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Recall Ref #</th>
                                <th>Batch Number</th>
                                <th>Product</th>
                                <th>Directive Number</th>
                                <th>Reason</th>
                                <th>Quarantine Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recalls as $rec)
                            <tr class="table-danger">
                                <td class="fw-bold">{{ $rec->recall_number }}</td>
                                <td class="fw-bold font-monospace">{{ $rec->batch->batch_number ?? 'N/A' }}</td>
                                <td>{{ $rec->batch->product->name ?? 'N/A' }}</td>
                                <td>{{ $rec->directive_number ?? 'DDA-DIRECTIVE' }}</td>
                                <td>{{ $rec->reason }}</td>
                                <td><small>{{ $rec->created_at->format('Y-m-d') }}</small></td>
                                <td><span class="badge bg-danger">{{ strtoupper($rec->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No active batch recalls or quarantine notices.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $recalls->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
