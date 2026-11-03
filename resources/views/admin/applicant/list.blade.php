@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h4 class="page-title-main">
                            {{$page_title}}
                        </h4>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <div class="float-end">
                            <a href="{{route('admin.exam.detail', ['exam_id' => $exam_id])}}"
                                class="btn btn-primary" type="submit"><i class="fas fa-file-excel"></i> Generate Admit Card
                            </a>
                            <a href="{{route('admin.applicant.export', ['exam_id' => $exam_id, 'level_id' => $level_id, 'program_id' => $program_id, 'state' => $state, 'status' => $status])}}"
                                class="btn btn-primary" type="submit"><i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-3" style="padding-left:20px;">
                    @include("admin.applicant.widgets.{$search_form}")
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
                                <th>Name</th>
                                <th width="130">Citizenship</th>
                                <th width="100">D.O.B</th>
                                <th>Email</th>
                                <th>Applied Date</th>
                                <th>Exam</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th>State</th>
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
@include('admin.applicant.js.list')
@endpush