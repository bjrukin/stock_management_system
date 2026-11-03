@extends('layout.admin')
@section('content')
<div class="content ad_prdlt-pg">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title-main float-start">
                    {{$page_title}}
                </h4>
                @if(auth()->guard('admin')->user() && auth()->guard('admin')->user()->hasPermission('College',
                'create'))
                <a href="{{route('admin.college.addEdit')}}"
                    class="btn btn-outline-secondary rounded-pill btn-filter text-">
                    Add College</a>
                @endif
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
                                <th>Name</th>
                                <th>Status</th>
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
@endsection
@push('footer-scripts')
@include('admin.college.js.list')
@endpush