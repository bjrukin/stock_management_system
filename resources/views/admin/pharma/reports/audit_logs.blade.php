@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-shield-check me-2"></i>Audit Trail & Immutable Activity Log</h4>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Log ID</th>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Action / Event</th>
                                <th>Target Entity</th>
                                <th>Entity ID</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $l)
                            <tr>
                                <td class="fw-bold font-monospace">#{{ $l->id }}</td>
                                <td><small>{{ $l->created_at->format('Y-m-d H:i:s') }}</small></td>
                                <td>{{ $l->user->name ?? 'System' }}</td>
                                <td><span class="badge bg-primary">{{ strtoupper($l->action) }}</span></td>
                                <td>{{ class_basename($l->auditable_type) }}</td>
                                <td class="fw-semibold">#{{ $l->auditable_id }}</td>
                                <td><small class="text-muted">{{ $l->ip_address ?? '127.0.0.1' }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No audit logs recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
