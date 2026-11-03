@extends('layout.admin')
@section('content')
<div class="content ad_prdlt-pg">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title-main float-start">
                    {{$page_title}}
                </h4>
                @if(auth()->guard('admin')->user() && auth()->guard('admin')->user()->hasPermission('Level', 'create'))
                <a href="{{route('admin.level.addEdit')}}"
                    class="btn btn-outline-secondary rounded-pill btn-filter text-">
                    Add Level</a>
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
                                <th>Code</th>
                                <th>Name</th>
                                <th>Short Name In English</th>
                                <th>Short Name In Nepali</th>
                                <th width="100">Order</th>
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
@include('admin.level.js.list')
@endpush