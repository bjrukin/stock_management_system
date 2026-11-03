@extends('layout.admin')
@section('content')
<div class="content ad_prdlt-pg">
    <div class="container-fluid">

        <div class="card">
            <div class="card-body">
                <h4 class="page-title-main">
                    {{$page_title}}
                </h4>
                <div class="mt-3" style="padding-left:20px;">@include('admin.kyc.widgets.certificate_search')
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive clear">
                    <table class="table table-striped table-borderless mb-0 dataTable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th width="50">SN.</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Allocate To</th>
                                <th>Decision Date</th>
                                <th>Citizenship</th>
                                <th>Registration Number</th>
                                <th>Serial No</th>
                                <th>Level</th>
                                <th>Program</th>
                                <th>Status</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="allocateModal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="modal-content">
        <div
            style="width: 400px; height: 400px; vertical-align: middle; text-align: center; margin-top: 80%; margin-left: 50%">
            <div class="spinner-border avatar-lg text-primary m-2" role="status"></div>
        </div>
    </div>
</div>
@endsection
@push('footer-scripts')
@include('admin.kyc.js.certificate')
@endpush