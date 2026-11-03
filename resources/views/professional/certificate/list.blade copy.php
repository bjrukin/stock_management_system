@extends('layout.professional')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <h3>{{ $page_title }}</h3>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="btn-group float-end">
                                    <a href="{{ route('professional.certificate') }}" class="btn btn-primary">
                                        <i class="mdi mdi-plus"></i> Request New Certificate
                                    </a>
                                    <a href="{{ route('professional.certificate.renew.search') }}" class="btn btn-info">
                                        <i class="mdi mdi-history"></i> Request Old Certificate
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="get">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                            placeholder="Search by certificate number">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-search fa-6" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if($regular_certificates->count() > 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="mdi mdi-certificate"></i> Regular Certificates ({{ $regular_certificates->count() }})
                            </h5>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th width="50">SN.</th>
                                            <th>Certificate Number</th>
                                            <th width="130">Registration Number</th>
                                            <th>Level</th>
                                            <th>Program</th>
                                            <th>Issued Date</th>
                                            <th>Valid Till</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th width="150">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $count = 1; @endphp
                                        @foreach($regular_certificates as $certificate)
                                            <tr>
                                                <td>{{ $count++ }}</td>
                                                <td>{{ $certificate->cert_registration_number ?? 'N/A' }}</td>
                                                <td>{{ $certificate->registration_number ?? 'N/A' }}</td>
                                                <td>{{ $certificate->level->name ?? 'N/A' }}</td>
                                                <td>{{ $certificate->program->name ?? 'N/A' }}</td>
                                                <td>{{ $certificate->issued_date }}</td>
                                                <td>{{ $certificate->valid_till }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $certificate->type == 'old_certificate' ? 'Old Certificate' : 'Regular Certificate' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span style="
                                                                @if($certificate->certificate_status == 'active') background-color: green; 
                                                                @elseif($certificate->certificate_status == 'expired') background-color: red; 
                                                                @elseif($certificate->certificate_status == 'pending') background-color: orange; 
                                                                @else background-color: gray; 
                                                                @endif
                                                                color:#fff;
                                                                padding:3px;
                                                                border-radius:4px;
                                                            ">
                                                        {{ strtoupper($certificate->certificate_status ?? 'unknown') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        @php
                                                            $isExpired = $certificate->valid_till && \Carbon\Carbon::parse($certificate->valid_till)->isPast();
                                                            $renewClass = $isExpired ? 'text-danger' : 'text-warning';
                                                            $renewTitle = $isExpired ? 'Renew Expired Certificate' : 'Renew Certificate';
                                                        @endphp

                                                        <button class="btn {{ $renewClass }} renew-certificate"
                                                            title="{{ $renewTitle }}" data-bs-toggle="tooltip"
                                                            data-certificate-id="{{ $certificate->id }}"
                                                            data-certificate-number="{{ $certificate->cert_registration_number }}"
                                                            data-is-expired="{{ $isExpired ? 'true' : 'false' }}">
                                                            <i class="fas fa-sync-alt"></i>
                                                        </button>
                                                        <a href="{{ route('professional.certificate_renew.list') }}"
                                                            class="btn text-info" title="view Renew Requests"
                                                            data-bs-toggle="tooltip">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($old_certificate_requests->count() > 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="mdi mdi-history"></i> Old Certificate Requests
                                ({{ $old_certificate_requests->count() }})
                            </h5>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th width="50">SN.</th>
                                            <th>Certificate Number</th>
                                            <th width="130">Registration Number</th>
                                            <th width="130">Registrar</th>
                                            <th>Level</th>
                                            <th>Program</th>
                                            <th>Decision Date</th>
                                            <th>Valid Till</th>
                                            <th>Status</th>
                                            <th width="">Certificate Image</th>
                                            <th width="">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $old_count = 1; @endphp
                                        @foreach($old_certificate_requests as $request)
                                            <tr>
                                                <td>{{ $old_count++ }}</td>
                                                <td>{{ $request->cert_registration_number ?? 'N/A' }}</td>
                                                <td>{{ $request->registration_number ?? 'N/A' }}</td>
                                                <td>{{ $request->registrar ?? 'N/A' }}</td>
                                                <td>{{ $request->level->name ?? 'N/A' }}</td>
                                                <td>{{ $request->program->name ?? 'N/A' }}</td>
                                                <td>{{ $request->decision_date }}</td>
                                                <td>{{ $request->valid_till }}</td>
                                                <td>
                                                    <span style="
                                                                @if($request->status == 'approved') background-color: green; 
                                                                @elseif($request->status == 'rejected') background-color: red; 
                                                                @else background-color: orange; 
                                                                @endif
                                                                color:#fff;
                                                                padding:3px;
                                                                border-radius:4px;
                                                            ">
                                                        {{ strtoupper($request->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if($request->certificate_image_front)
                                                            <a href="{{ Storage::url($request->certificate_image_front) }}"
                                                                target="_blank" class="btn text-success" title="View Front Image"
                                                                data-bs-toggle="tooltip">
                                                                <i class="fas fa-image"></i>
                                                            </a>
                                                        @endif
                                                        @if($request->certificate_image_back)
                                                            <a href="{{ Storage::url($request->certificate_image_back) }}"
                                                                target="_blank" class="btn text-info" title="View Back Image"
                                                                data-bs-toggle="tooltip">
                                                                <i class="fas fa-image"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if($request->status == 'pending')
                                                            <a href="{{ route('professional.old_certificate.edit', $request->id) }}"
                                                                class="btn text-warning confirm-edit" title="Edit Request"
                                                                data-bs-toggle="tooltip" data-request-id="{{ $request->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                        <button class="btn text-secondary status-info"
                                                            data-status="{{ $request->status }}" title="Status Information"
                                                            data-bs-toggle="tooltip">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($regular_certificates->count() == 0 && $old_certificate_requests->count() == 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-warning alert-message" role="alert">
                                No certificates found.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('footer-scripts')
    @include('professional.certificate.js.certificate_list')
@endsection