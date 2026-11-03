@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="text-center"> <span class="text-danger mx-2"> "!!! Please click this button to print the
                        admit card !!!"</span>
                    <button onclick="printDiv()" class="btn btn-primary">Print Admit Card</button>
                </div>

            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div id="print-content">
                    <div class="row">
                        <div class="col-lg-12 m-b-3">
                            @include("admin.applicant.admincard.professional")
                            @include("admin.applicant.admincard.note")
                            @include("admin.applicant.admincard.official")
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-scripts')
@include('admin.applicant.js.admin_card')
@endpush