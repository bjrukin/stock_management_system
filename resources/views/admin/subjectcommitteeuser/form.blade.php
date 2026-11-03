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
                                    <div class="mb-3">
                                        <label class="form-label"> User</label>
                                        <select class="form-control select2" name="user">
                                            @if($users->count() > 0)
                                            @foreach($users as $user)
                                            <option value="{{$user->id}}" @if($row && $row->
                                                user_id == $user->id) selected
                                                @endif>{{$user->name}}</option>
                                            @endforeach
                                            @endif

                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Subject Committee</label>
                                        <select class="form-control select2" name="subject_committee">
                                            @if($subject_committees->count() > 0)
                                            @foreach($subject_committees as $subject_committee)
                                            <option value="{{$subject_committee->id}}" @if($row && $row->
                                                subject_committee_id == $subject_committee->id) selected
                                                @endif>{{$subject_committee->name}}</option>
                                            @endforeach
                                            @endif

                                        </select>
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
@include('admin.subjectcommitteeuser.js.form')
@endpush