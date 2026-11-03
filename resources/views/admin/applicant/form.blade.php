@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-xl-6">
                <form enctype="multipart/form-data" method="post" action="{{ $action }}" id="form">
                    @csrf
                    <input type="hidden" class="form-control" name="id" value="{{ ($row) ? $row->id : 0 }}">
                    <div class="card">
                        <div class="card-body">
                            <fieldset class="border rounded-3 p-3">
                                <legend>{{$page_title}}</legend>
                                <div class="mb-3">
                                    <label class="form-label">Exam *</label>
                                    <select class="form-select select2" name="exam_id">
                                        <option value="">Select exam</option>
                                        @if ($exams->isNotEmpty())
                                        @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}" @if ($row->exam_id == $exam->id) selected
                                            @endif>
                                            {{ $exam->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Level *</label>
                                    <select class="form-select select2" name="level" id="level">
                                        <option value="">Select level</option>
                                        @if ($levels->isNotEmpty())
                                        @foreach ($levels as $level)
                                        <option value="{{ $level->id }}" @if ($row->level_id == $level->id) selected
                                            @endif>
                                            {{ $level->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Program Name</label>
                                    <select class="form-select select2" name="program" id="program">
                                        <option value="">Select program</option>
                                        @if ($programs->isNotEmpty())
                                        @foreach ($programs as $program)
                                        <option value="{{ $program->id }}" @if ($row->program_id == $program->id)
                                            selected @endif
                                            class="{{ $program->level_id }}">{{ $program->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                <label class="form-label">Voucher Image*</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ ($row->voucher_image) ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('voucher_image')" id="btn_voucher_image_delete">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="drag-area drag-area-voucher_image">
                                        <div
                                            class="dropify-message dropify-message-voucher_image {{ ($row->voucher_image) ? 'd-none' : 'd-block' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px"
                                                width="64px" height="64px" viewBox="0 0 64 64"
                                                enable-background="new 0 0 64 64" xml:space="preserve">
                                                <path fill="none" stroke="#8a8a8a" stroke-width="2"
                                                    stroke-miterlimit="10"
                                                    d="M41,50h14c4.565,0,8-3.582,8-8s-3.435-8-8-8  c0-11.046-9.52-20-20.934-20C23.966,14,14.8,20.732,13,30c0,0-0.831,0-1.667,0C5.626,30,1,34.477,1,40s4.293,10,10,10H41" />
                                                <polyline fill="none" stroke="#8a8a8a" stroke-width="2"
                                                    stroke-linejoin="bevel" stroke-miterlimit="10"
                                                    points="23.998,34   31.998,26 39.998,34 " />
                                                <g>
                                                    <line fill="none" stroke="#8a8a8a" stroke-width="2"
                                                        stroke-miterlimit="10" x1="31.998" y1="26" x2="31.998"
                                                        y2="46" />
                                                </g>
                                            </svg>
                                            <p>Click here to upload image</p>
                                        </div>
                                        <input name="voucher_image" type="hidden" id="voucher_image"
                                            value="{{ $row->voucher_image ??  old('voucher_image') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if($row && $row->voucher_image)
                                            <img src="{{ $row->full_voucher_image }}"
                                                id="display_voucher_image" class="preview-image">
                                            @else
                                            <img src="" id="display_voucher_image" class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-voucher_image"
                                        class="drag-image" accept="image/*" />
                                </div>
                            </div>

                            </fieldset>
                            <div class="mt-3"> <button type="button" class="main_btn btn-loading mb-3"
                                    onclick="saveData(this.form)">Submit</button></div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3"> <strong>बैंक विवरण</strong><br>
                            <span>(क) राष्ट्रिय बाणिज्य बैंक ११५०१००००२१३३००१ </span><br>
                            <span>(ख) नेपाल एस वि आई बैंक २०४३५२४०१००००८</span><br>
                            <span>(ग) हिमालयन बैंक ००२००५७४६६००१६</span><br>
                        </div>
                        <div class="mb-3"> <strong>दस्तुर</strong><br>
                            <span>(क) विशिष्ठ तहको लागि रु. 3000।</span><br>
                            <span>(ख) प्रथम तहको लागि रु. 3000।</span><br>
                            <span>(ग) द्वितीय तहको लागि रु. 3000।</span><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-scripts')
@include('admin._partial.imagejs')
@include('admin.applicant.js.form')
@endpush