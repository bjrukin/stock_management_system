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
                                    <a href="{{ route('professional.certificate') }}" class="btn btn-info">
                                        <i class="mdi mdi-history"></i> Request Old Certificate
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($certificates->count() > 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="mdi mdi-certificate"></i> Regular Certificates ({{ $certificates->count() }})
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
                                        @foreach($certificates as $certificate)
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
                                                            <i class="mdi mdi-sync-alert"></i>
                                                        </button>
                                                        <a href="{{ route('professional.certificate') }}"
                                                            class="btn text-info" title="Renew Requests"
                                                            data-bs-toggle="tooltip">
                                                            <i class="mdi mdi-history"></i>
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

          

            @if($certificates->count() == 0)
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
    @include('professional.certificate.js.list')
@endsection