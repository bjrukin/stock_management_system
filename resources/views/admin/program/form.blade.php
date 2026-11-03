@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="form-section">
                    <form enctype="multipart/form-data" method="post" action="{{ $action }}" id="form">
                        @csrf
                        <input type="hidden" class="form-control" name="id" value="{{ ($row) ? $row->id : 0 }}">
                        <div class="card">
                            <div class="card-body">
                                <fieldset class="border rounded-3 p-3">
                                    <legend>{{$page_title}}</legend>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ ($row) ? $row->name : old('name')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <label class="form-label">Code</label>
                                            <div class="mb-3">
                                                <input type="text" class="form-control" name="code"
                                                    value="{{ ($row) ? $row->code : old('code')}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Certificate Name</label>
                                                <input type="text" class="form-control" name="certificate_name"
                                                    value="{{ ($row) ? $row->certificate_name : old('certificate_name')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Subject Committee</label>
                                                <select class="form-control select2" name="subject_committee_id">
                                                    @if($subject_committees->count() > 0)
                                                    @foreach($subject_committees as $subject_committee)
                                                    <option value="{{$subject_committee->id}}" @if($row && $row->
                                                        subject_committee_id == $subject_committee->id) selected
                                                        @endif>{{$subject_committee->name}}</option>
                                                    @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Qualification</label>
                                                <input type="text" class="form-control" name="qualification"
                                                    value="{{ ($row) ? $row->qualification : old('qualification')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Level</label>
                                                <select class="form-control select2" name="level_id">
                                                    @if($levels->count() > 0)
                                                    @foreach($levels as $level)
                                                    <option value="{{$level->id}}" @if($row && $row->level_id ==
                                                        $level->id)
                                                        selected @endif>{{$level->name}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Program Type</label>
                                                <select class="form-control select2" name="program_type">
                                                    <option value="yearly" @if($row && $row->program_type == 'yearly')
                                                        selected
                                                        @endif>Yearly</option>
                                                    <option value="semester" @if($row && $row->program_type ==
                                                        'semester') selected
                                                        @endif>Semester</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label"> Program Duration</label>
                                                <input type="text" class="form-control" name="program_duration"
                                                    value="{{ ($row) ? $row->program_duration : old('program_duration')}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Duration Type</label>
                                                <select class="form-control select2" name="duration_type" required>
                                                    <option value="year" @if($row && $row->duration_type == 'year')
                                                        selected
                                                        @endif>Year</option>
                                                    <option value="month" @if($row && $row->duration_type == 'month')
                                                        selected
                                                        @endif>Month</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Has Exam</label>
                                                <label class="switch">
                                                    <input type="checkbox" class="switch-input" name="has_exam"
                                                        value="1"
                                                        {{ ($row && $row->has_exam == 1) ||  (!$row) ? 'checked' : '' }} />
                                                    <span class="switch-label" data-on="Yes" data-off="No"></span>
                                                    <span class="switch-handle"></span> </label>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="mt-3"> <button type="button" class="main_btn btn-loading mb-3"
                                        onclick="saveData(this.form)">Submit</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@push('footer-scripts')
@include('admin.program.js.form')
@endpush