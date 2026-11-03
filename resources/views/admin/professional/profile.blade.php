@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="form-section">
                    <form enctype="multipart/form-data" method="post" action="{{ $action }}" id="form">
                        @csrf
                        <input type="hidden" class="form-control" name="id" value="{{ ($professional) ? $professional->id : 0 }}">
                        <div class="card">
                            <div class="card-body">
                                <fieldset class="border rounded-3 p-3">
                                    <legend>{{$page_title}}</legend>
                                    <div class="mb-3">
                                    <label class="form-label">Level *</label>
                                    <select class="form-select select2" name="level">
                                        <option value="">Select level</option>
                                        @if ($levels->isNotEmpty())
                                            @foreach ($levels as $level)
                                                <option value="{{ $level->id }}"
                                                    @if ($professional->info && $professional->info->level_id == $level->id) selected @endif>{{ $level->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"> Profile Image </label>
                                    <div class="drag-container">
                                        <button type="button"
                                            class="{{ $professional->info && $professional->info->profile_picture ? 'd-block' : 'd-none' }} img-delete-btn"
                                            onclick="imageDelete('profile_picture')" id="btn_profile_picture_delete"><i
                                                class="bi bi-x-lg"></i></button>
                                        <div class="drag-area drag-area-profile_picture">
                                            <div
                                                class="dropify-message dropify-message-profile_picture {{ $professional->info && $professional->info->profile_picture ? 'd-none' : 'd-block' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px"
                                                    y="0px" width="64px" height="64px" viewBox="0 0 64 64"
                                                    enable-background="new 0 0 64 64" xml:space="preserve">
                                                    <path fill="none" stroke="#8a8a8a" stroke-width="2"
                                                        stroke-miterlimit="10"
                                                        d="M41,50h14c4.565,0,8-3.582,8-8s-3.435-8-8-8  c0-11.046-9.52-20-20.934-20C23.966,14,14.8,20.732,13,30c0,0-0.831,0-1.667,0C5.626,30,1,34.477,1,40s4.293,10,10,10H41" />
                                                    <polyline fill="none" stroke="#8a8a8a" stroke-width="2"
                                                        stroke-linejoin="bevel" stroke-miterlimit="10"
                                                        points="23.998,34   31.998,26 39.998,34 " />
                                                    <g>
                                                        <line fill="none" stroke="#8a8a8a" stroke-width="2"
                                                            stroke-miterlimit="10" x1="31.998" y1="26"
                                                            x2="31.998" y2="46" />
                                                    </g>
                                                </svg>
                                                <p>Click here to upload image</p>
                                            </div>
                                            <input name="profile_picture" type="hidden" id="profile_picture"
                                                value="{{ $professional->info->profile_picture ?? old('profile_picture') }}"
                                                class="file-input" />
                                            <div class="image-preview">
                                                @if ($professional->info && $professional->info->profile_picture)
                                                    <img src="{{ $professional->info->full_profile_picture }}"
                                                        id="display_profile_picture" class="preview-image">
                                                @else
                                                    <img src="" id="display_profile_picture"
                                                        class="d-none preview-image">
                                                @endif
                                            </div>
                                        </div>
                                        <input type="file" style="display:none" id="drag-profile_picture"
                                            class="drag-image" accept="image/*" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">First Name *</label>
                                        <input class="form-control" name="first_name"
                                            value="{{ $professional->info->first_name ?? old('first_name') }}" type="text">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Middle Name</label>
                                        <input class="form-control" name="middle_name"
                                            value="{{ $professional->info->middle_name ?? old('middle_name') }}" type="text">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input class="form-control" name="last_name"
                                            value="{{ $professional->info->last_name ?? old('last_name') }}" type="text">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">पहिलो नाम *</label>
                                        <input class="form-control" name="first_name_nep"
                                            value="{{ $professional->info->first_name_nep ?? old('first_name_nep') }}"
                                            type="text">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">बिचको नाम</label>
                                        <input class="form-control" name="middle_name_nep"
                                            value="{{ $professional->info->middle_name_nep ?? old('middle_name_nep') }}"
                                            type="text">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">थर *</label>
                                        <input class="form-control" name="last_name_nep"
                                            value="{{ $professional->info->last_name_nep ?? old('last_name_nep') }}"
                                            type="text">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Date of Birth(B.S) *</label>
                                        <input class="form-control" readonly name="dob_nep" id="dob_nep"
                                            value="{{ $professional->info->dob_nep ?? old('dob_nep') }}" type="text">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Date of Birth(A.D) *</label>

                                        <input type="text" class="form-control date" name="dob_eng" id="dob_eng"
                                            value="{{ $professional->info->dob_eng ?? old('dob_eng') }}"data-date-format="yyyy-mm-dd" data-date-autoclose="true">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Gender *</label>
                                        <select class="form-select" name="gender">
                                            <option value="">Select</option>
                                            <option value="Male" @if($professional->info && $professional->info->gender == 'male') selected @endif>Male
                                            </option>
                                            <option value="Female" @if($professional->info && $professional->info->gender == 'female') selected @endif>Female
                                            </option>
                                            <option value="Other" @if($professional->info && $professional->info->gender == 'other') selected @endif>Other
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Ethinicity *</label>
                                        <select class="form-select select2" name="ethinic">
                                            <option value="">Select Ethnicity</option>
                                            <option value="Brahamin/Chettri"
                                                @if ($professional->info && $professional->info->ethinic == 'Brahamin/Chettri') selected @endif>Brahamin/Chettri</option>
                                            <option value="Dalits" @if ($professional->info && $professional->info->ethinic == 'Dalits') selected @endif>Dalits
                                            </option>
                                            <option value="Janjati" @if ($professional->info && $professional->info->ethinic == 'Janjati') selected @endif>
                                                Janjati</option>
                                            <option value="Tarai/Madhesi"
                                                @if ($professional->info && $professional->info->ethinic == 'Tarai/Madhesi') selected @endif>Tarai/Madhesi</option>
                                            <option value="Other" @if ($professional->info && $professional->info->ethinic == 'Other') selected @endif>Other
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Marital Status *</label>
                                        <select class="form-select" name="marital_status">
                                            <option value="">Select Marital Status</option>
                                            <option value="Unmarried" @if ($professional->info && $professional->info->marital_status == 'unmarried') selected @endif>
                                                Unmarried</option>
                                            <option value="Married" @if ($professional->info && $professional->info->marital_status == 'married') selected @endif>
                                                Married</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-sm-12 col-xs-12">
                                        <label class="form-label">Province *</label>
                                        <select class="form-control select2" name="province" id="province">
                                            <option value="">Select Your Province</option>
                                            @if ($provinces->isNotEmpty())
                                                @foreach ($provinces as $province)
                                                    <option value="{{ $province->id }}"
                                                        @if ($professional->info && $professional->info->province_id == $province->id) selected @endif>
                                                        {{ $province->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">District *</label>
                                        <select class="form-control select2" name="district" id="district">
                                            <option value="">Select District</option>
                                            @if ($districts->isNotEmpty())
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->id }}"
                                                        class="{{ $district->province_id }}"
                                                        @if ($professional->info && $professional->info->district_id == $district->id) selected @endif>
                                                        {{ $district->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Municipality</label>
                                        <select class="form-control select2" name="municipality" id="municipality">
                                            <option value="">Select Municipality</option>
                                            @if ($municipalities->isNotEmpty())
                                                @foreach ($municipalities as $municipality)
                                                    <option value="{{ $municipality->id }}"
                                                        class="{{ $municipality->district_id }}"
                                                        @if ($professional->info && $professional->info->municipality_id == $municipality->id) selected @endif>
                                                        {{ $municipality->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Ward No *</label>
                                        <input class="form-control" name="ward_no"
                                            value="{{ $professional->info->ward_no ?? old('ward_no') }}" type="text">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Citizenship Number*</label>
                                        <input class="form-control" name="citizenship_number"
                                            value="{{ $professional->info->citizenship_number ?? old('citizenship_number') }}"
                                            type="text">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Citizenship Issue Date</label>
                                        <input class="form-control" name="citizenship_issue_date"
                                            id="citizenship_issue_date"
                                            value="{{ $professional->info->citizenship_issue_date ?? old('citizenship_issue_date') }}"
                                            type="text">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Citizenship Issue District*</label>
                                        <select class="form-control select2" name="citizenship_issue_district">
                                            <option value="">Select District</option>
                                            @if ($districts->isNotEmpty())
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->name }}"
                                                        @if ($professional->info && $professional->info->citizenship_issue_district == $district->name) selected @endif>
                                                        {{ $district->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-4 col-md-4 col-sm-12 col-xs-12">
                                    <label class="form-label">Citizenship Front Image</label>
                                        <div class="drag-container">
                                            <button type="button"
                                                class="{{ $professional->info && $professional->info->citizenship_front ? 'd-block' : 'd-none' }} img-delete-btn"
                                                onclick="imageDelete('citizenship_front')"
                                                id="btn_citizenship_front_delete"><i class="bi bi-x-lg"></i></button>
                                            <div class="drag-area drag-area-citizenship_front">
                                                <div
                                                    class="dropify-message dropify-message-citizenship_front {{ $professional->info && $professional->info->citizenship_front ? 'd-none' : 'd-block' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px"
                                                        y="0px" width="64px" height="64px" viewBox="0 0 64 64"
                                                        enable-background="new 0 0 64 64" xml:space="preserve">
                                                        <path fill="none" stroke="#8a8a8a" stroke-width="2"
                                                            stroke-miterlimit="10"
                                                            d="M41,50h14c4.565,0,8-3.582,8-8s-3.435-8-8-8  c0-11.046-9.52-20-20.934-20C23.966,14,14.8,20.732,13,30c0,0-0.831,0-1.667,0C5.626,30,1,34.477,1,40s4.293,10,10,10H41" />
                                                        <polyline fill="none" stroke="#8a8a8a" stroke-width="2"
                                                            stroke-linejoin="bevel" stroke-miterlimit="10"
                                                            points="23.998,34   31.998,26 39.998,34 " />
                                                        <g>
                                                            <line fill="none" stroke="#8a8a8a" stroke-width="2"
                                                                stroke-miterlimit="10" x1="31.998" y1="26"
                                                                x2="31.998" y2="46" />
                                                        </g>
                                                    </svg>
                                                    <p>Click here to upload image</p>
                                                </div>
                                                <input name="citizenship_front" type="hidden" id="citizenship_front"
                                                    value="{{ $professional->info->citizenship_front ?? old('citizenship_front') }}"
                                                    class="file-input" />
                                                <div class="image-preview">
                                                    @if ($professional->info && $professional->info->citizenship_front)
                                                        <img src="{{ $professional->info->full_citizenship_front }}"
                                                            id="display_citizenship_front" class="preview-image">
                                                    @else
                                                        <img src="" id="display_citizenship_front"
                                                            class="d-none preview-image">
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="file" style="display:none" id="drag-citizenship_front"
                                                class="drag-image" accept="image/*" />
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-sm-12 col-xs-12">
                                          <label class="form-label">Citizenship Back Image</label>
                                        <div class="drag-container">
                                            <button type="button"
                                                class="{{ $professional->info && $professional->info->citizenship_back ? 'd-block' : 'd-none' }} img-delete-btn"
                                                onclick="imageDelete('citizenship_back')"
                                                id="btn_citizenship_back_delete"><i class="bi bi-x-lg"></i></button>
                                            <div class="drag-area drag-area-citizenship_back">
                                                <div
                                                    class="dropify-message dropify-message-citizenship_back {{ $professional->info && $professional->info->citizenship_back ? 'd-none' : 'd-block' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px"
                                                        y="0px" width="64px" height="64px" viewBox="0 0 64 64"
                                                        enable-background="new 0 0 64 64" xml:space="preserve">
                                                        <path fill="none" stroke="#8a8a8a" stroke-width="2"
                                                            stroke-miterlimit="10"
                                                            d="M41,50h14c4.565,0,8-3.582,8-8s-3.435-8-8-8  c0-11.046-9.52-20-20.934-20C23.966,14,14.8,20.732,13,30c0,0-0.831,0-1.667,0C5.626,30,1,34.477,1,40s4.293,10,10,10H41" />
                                                        <polyline fill="none" stroke="#8a8a8a" stroke-width="2"
                                                            stroke-linejoin="bevel" stroke-miterlimit="10"
                                                            points="23.998,34   31.998,26 39.998,34 " />
                                                        <g>
                                                            <line fill="none" stroke="#8a8a8a" stroke-width="2"
                                                                stroke-miterlimit="10" x1="31.998" y1="26"
                                                                x2="31.998" y2="46" />
                                                        </g>
                                                    </svg>
                                                    <p>Click here to upload image</p>
                                                </div>
                                                <input name="citizenship_back" type="hidden" id="citizenship_back"
                                                    value="{{ $professional->info->citizenship_back ?? old('citizenship_back') }}"
                                                    class="file-input" />
                                                <div class="image-preview">
                                                    @if ($professional->info && $professional->info->citizenship_back)
                                                        <img src="{{ $professional->info->full_citizenship_back }}"
                                                            id="display_citizenship_back" class="preview-image">
                                                    @else
                                                        <img src="" id="display_citizenship_back"
                                                            class="d-none preview-image">
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="file" style="display:none" id="drag-citizenship_back"
                                                class="drag-image" accept="image/*" />
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-sm-12 col-xs-12">
                                          <label class="form-label">Signature Image </label>
                                        <div class="drag-container">
                                            <button type="button"
                                                class="{{ $professional->info && $professional->info->signature_image ? 'd-block' : 'd-none' }} img-delete-btn"
                                                onclick="imageDelete('signature_image')"
                                                id="btn_signature_image_delete"><i class="bi bi-x-lg"></i></button>
                                            <div class="drag-area drag-area-signature_image">
                                                <div
                                                    class="dropify-message dropify-message-signature_image {{ $professional->info && $professional->info->signature_image ? 'd-none' : 'd-block' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px"
                                                        y="0px" width="64px" height="64px" viewBox="0 0 64 64"
                                                        enable-background="new 0 0 64 64" xml:space="preserve">
                                                        <path fill="none" stroke="#8a8a8a" stroke-width="2"
                                                            stroke-miterlimit="10"
                                                            d="M41,50h14c4.565,0,8-3.582,8-8s-3.435-8-8-8  c0-11.046-9.52-20-20.934-20C23.966,14,14.8,20.732,13,30c0,0-0.831,0-1.667,0C5.626,30,1,34.477,1,40s4.293,10,10,10H41" />
                                                        <polyline fill="none" stroke="#8a8a8a" stroke-width="2"
                                                            stroke-linejoin="bevel" stroke-miterlimit="10"
                                                            points="23.998,34   31.998,26 39.998,34 " />
                                                        <g>
                                                            <line fill="none" stroke="#8a8a8a" stroke-width="2"
                                                                stroke-miterlimit="10" x1="31.998" y1="26"
                                                                x2="31.998" y2="46" />
                                                        </g>
                                                    </svg>
                                                    <p>Click here to upload image</p>
                                                </div>
                                                <input name="signature_image" type="hidden" id="signature_image"
                                                    value="{{ $professional->info->signature_image ?? old('signature_image') }}"
                                                    class="file-input" />
                                                <div class="image-preview">
                                                    @if ($professional->info && $professional->info->signature_image)
                                                        <img src="{{ $professional->info->full_signature_image }}"
                                                            id="display_signature_image" class="preview-image">
                                                    @else
                                                        <img src="" id="display_signature_image"
                                                            class="d-none preview-image">
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="file" style="display:none" id="drag-signature_image"
                                                class="drag-image" accept="image/*" />
                                        </div>
                                    </div>
                                </div>
                                </fieldset>
                                <div class="mt-3"> <button type="button"
                                        class="main_btn btn-loading mb-3" onclick="saveData(this.form)">Submit</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-scripts')
@include('admin._partial.imagejs')
@include('admin.professional.js.profile')
@endpush