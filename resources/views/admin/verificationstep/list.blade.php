@extends('layout.admin')
@section('content')
<div class="content ad_prdlt-pg">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title-main float-start">
                    {{$page_title}}
                </h4>
                @if(auth()->guard('admin')->user() && auth()->guard('admin')->user()->hasPermission('VerificationStep',
                'create'))
                <a href="{{route('admin.verificationstep.addEdit')}}"
                    class="btn btn-outline-secondary rounded-pill btn-filter text-">
                    Add Verification Step</a>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive clear">
                    <table class="table table-striped table-borderless mb-0 dataTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">SN.</th>
                                <th>Role</th>
                                <th width="100">Step</th>
                                <th>Action</th>
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
@endsection
@push('footer-scripts')
@include('admin.verificationstep.js.list')
@endpush