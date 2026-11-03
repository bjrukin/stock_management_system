@extends('layout.admin')
@section('content')
<div class="content ad_prdlt-pg">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title-main float-start">
                    {{$page_title}}
                </h4>
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
                                <th width="150">Profile</th>
                                <th>Name</th>
                                <th>Symbol Number</th>
                                <th>Registration Number</th>
                                <th>DOB</th>
                                <th width="150">Action</th>
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
<div class="modal" id="allocateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" id="modal-content">
        <div
            style="width: 400px; height: 400px; vertical-align: middle; text-align: center; margin-top: 40%; margin-left: 30%">
            <img src="assets/admin/images/loading.gif"> </div>
    </div>
</div>
@endsection
@push('footer-scripts')
@include('admin.kyc.js.list')
@endpush