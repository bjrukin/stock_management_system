@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="page-title-main text-primary m-0"><i class="bi bi-gear me-2"></i>System & Regulatory Configuration</h4>
            </div>
        </div>

        <div class="row">
            @forelse($settings as $group => $items)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title m-0 fw-bold text-uppercase text-primary">{{ $group }} Settings</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($items as $s)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <strong class="text-dark">{{ $s->name ?? $s->key }}</strong>
                                    <div class="small text-muted font-monospace">{{ $s->key }}</div>
                                </div>
                                <span class="badge bg-light text-dark border fs-6 font-monospace">{{ $s->value }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12"><div class="alert alert-info">No settings found.</div></div>
            @endforelse
        </div>
    </div>
</div>
@endsection
