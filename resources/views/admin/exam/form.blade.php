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
                                            <div class="mb-3">
                                                <label class="form-label">Nepali Name</label>
                                                <input type="text" class="form-control" name="name_nep"
                                                    value="{{ ($row) ? $row->name_nep : old('name_nep')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Opening Date</label>
                                                <input type="text" class="form-control date" name="opening_date"
                                                    placeholder="YY-MM-DD" data-date-format="yyyy-mm-dd"
                                                    data-date-autoclose="true"
                                                    value="{{ ($row) ? $row->opening_date : old('opening_date')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Closing Date</label>
                                                <input type="text" class="form-control date" name="closing_date"
                                                    placeholder="YY-MM-DD" data-date-format="yyyy-mm-dd"
                                                    data-date-autoclose="true"
                                                    value="{{ ($row) ? $row->closing_date : old('closing_date')}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Exam Number</label>
                                                <input type="text" class="form-control" name="exam_number"
                                                    value="{{ ($row) ? $row->exam_number : old('exam_number')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Exam Fee</label>
                                                <input type="text" class="form-control" name="exam_fee"
                                                    value="{{ ($row) ? $row->exam_fee : old('exam_fee')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Late Fee Start Date</label>
                                                <input type="text" class="form-control date" name="late_fee_start_date"
                                                    placeholder="YY-MM-DD" data-date-format="yyyy-mm-dd"
                                                    data-date-autoclose="true"
                                                    value="{{ ($row) ? $row->opening_date : old('late_fee_start_date')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Late Fee</label>
                                                <input type="text" class="form-control" name="late_fee"
                                                    value="{{ ($row) ? $row->late_fee : old('late_fee')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <input type="text" class="form-control" name="description"
                                            value="{{ ($row) ? $row->description : old('description')}}">
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
@include('admin.exam.js.form')
@endpush