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
                                    @csrf
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
                                    <div class="mb-3">
                                        <label class="form-label">Minimum Percentage (%) </label>
                                        <input type="text" class="form-control" name="min_percentage"
                                            value="{{ ($row) ? $row->min_percentage : old('min_percentage')}}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">MInimum GRADE </label>
                                        <input type="text" class="form-control" name="min_gpa"
                                            value="{{ ($row) ? $row->min_gpa : old('min_gpa')}}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Min Physics Mark </label>
                                        <input type="text" class="form-control" name="min_physics_marks"
                                            value="{{ ($row) ? $row->min_physics_marks : old('min_physics_marks')}}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Min Chemistry Mark </label>
                                        <input type="text" class="form-control" name="min_chemistry_marks"
                                            value="{{ ($row) ? $row->min_chemistry_marks : old('min_chemistry_marks')}}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Min Biology Mark </label>
                                        <input type="text" class="form-control" name="min_bio_marks"
                                            value="{{ ($row) ? $row->min_bio_marks : old('min_bio_marks')}}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Total Days Passport</label>
                                        <input type="text" class="form-control" name="total_days_passport"
                                            value="{{ ($row) ? $row->total_days_passport : old('total_days_passport')}}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Total Days Internship </label>
                                        <input type="text" class="form-control" name="total_days_internship"
                                            value="{{ ($row) ? $row->total_days_internship : old('total_days_internship')}}">
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
@include('admin.eligibilitymark.js.form')
@endpush