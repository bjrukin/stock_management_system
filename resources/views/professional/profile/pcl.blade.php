@extends('layout.professional')
@section('content')
    <div class="content">
        <div class="container-fluid">
            @include('professional.profile.widgets.breadcrumb')
            @include('professional.profile.widgets.note')

            <div class="form-section">

                <form enctype="multipart/form-data" method="post" action="{{ route('professional.profile.save.pcl') }}"
                    id="form">
                    @csrf
                    <input name="id" value="{{ $professional_qualification->id ?? null }}" type="hidden">
                    <input name="level_id" value="{{ $level_id }}" type="hidden">
                    <div class="card">
                        <div class="card-body">
                            <fieldset class="border rounded-3 p-3">
                                <legend>{{$page_title}}</legend>
                                <div class="row">
                                    @if($professional_qualification)
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <label class="form-label">College Name</label>
                                            <input name="college_name" class="form-control" type="text"
                                                value="{{ $professional_qualification->college_name ?? old('college_name') }}">
                                        </div>

                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <label class="form-label">Board</label>
                                            <input name="board_university" class="form-control" type="text"
                                                value="{{ $professional_qualification->board_university ?? old('board_university') }}">

                                        </div>
                                    @else
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <label class="form-label">College Type</label>
                                            <select class="form-control" name="college_type" id="college_type">
                                                <option value="nepal">Nepal</option>
                                                <option value="international">International</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <label class="form-label">Board</label>
                                            <select class="form-control select2" name="board_university" id="board_university">
                                                <option value="HSEB" @if(old('board_university') == "HSEB") selected @endif>HSEB /
                                                    +2</option>
                                                <option value="CTEVT, NEPAL" @if(old('board_university') == "CTEVT, NEPAL")
                                                selected @endif>CTEVT, NEPAL</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <label class="form-label">College Name</label>
                                            <input name="international_college_name" id="international_college_name"
                                                class="form-control d-none" type="text"
                                                value="{{old('international_college_name')}}">
                                            <div id="college_name">
                                                <select class="form-control select2" name="college_name">
                                                    @if($colleges->count() > 0)
                                                        @foreach($colleges as $college)
                                                            <option value="{{ $college->name}}">{{ $college->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">Admission Year </label>
                                        <input name="admission_year" class="form-control" type="text"
                                            value="{{ $professional_qualification->admission_year ?? old('admission_year') }}"
                                            minlength="4" maxlength="4" />
                                    </div>

                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">Passed Year </label>
                                        <input name="passed_year" class="form-control" type="text"
                                            value="{{ $professional_qualification->passed_year ?? old('passed_year') }}"
                                            minlength="4" maxlength="4" />
                                    </div>
                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">Registration Number</label>
                                        <input name="registration_number"
                                            value="{{ $professional_qualification->registration_number ?? old('registration_number') }}"
                                            class="form-control" id="basicInput" type="text">
                                    </div>
                                    <div class="col-lg-12 col-sm-12 mb-4 bg-light p-3">
                                        <h5 class="mb-3">Marks</h5>
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-12 mb-3">
                                                <label class="form-label">Result Type *</label>
                                                <select name="result_type" id="result_type" class="form-select">
                                                    <option value="">Select Result Type</option>
                                                    <option value="gpa" {{ (old('result_type', $professional_qualification->result_type ?? '') == 'gpa') ? 'selected' : '' }}>
                                                        GPA
                                                    </option>
                                                    <option value="percentage" {{ (old('result_type', $professional_qualification->result_type ?? '') == 'percentage') ? 'selected' : '' }}>
                                                        Percentage
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4 col-sm-12 mb-3" id="gpa_div" style="display:none;">
                                                <label class="form-label">GPA *</label>
                                                <input name="gpa" class="form-control" type="text"
                                                    placeholder="e.g. 3.45"
                                                    value="{{ $professional_qualification->gpa ?? old('gpa') }}">
                                            </div>
                                            <div class="col-lg-4 col-sm-12 mb-3" id="percentage_div"
                                                style="display:none;">
                                                <label class="form-label">Percentage *</label>
                                                <input name="percentage" class="form-control" type="text"
                                                    placeholder="e.g. 85"
                                                    value="{{ $professional_qualification->percentage ?? old('percentage') }}">
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-lg-3 col-sm-12">
                                                <label class="form-label">Physics *</label>
                                                <input name="physics_marks" id="pclPhysics" class="form-control"
                                                    type="text"
                                                    value="{{ $professional_qualification->physics_marks ?? '' }}">
                                            </div>
                                            <div class="col-lg-3 col-sm-12">
                                                <label class="form-label">Chemistry *</label>
                                                <input name="chemistry_marks" id="pclChemistry" class="form-control"
                                                    type="text"
                                                    value="{{ $professional_qualification->chemistry_marks ?? '' }}">
                                            </div>
                                            <div class="col-lg-3 col-sm-12">
                                                <label class="form-label">Biology *</label>
                                                <input name="biology_marks" id="pclBiology" class="form-control"
                                                    type="text"
                                                    value="{{ $professional_qualification->biology_marks ?? '' }}">
                                            </div>
                                            <div class="col-lg-3 col-sm-12">
                                                <label class="form-label">Average PCB</label>
                                                <input name="average" id="pclAveragePcb" class="form-control"
                                                    type="text"
                                                    value="{{ $professional_qualification->average ?? '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <div class="col-md-12 col-lg-12">
                                                <label class="form-label">Transcript Image *</label><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($professional_qualification && $professional_qualification->transcript_image) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                        onclick="imageDelete('transcript_image')"
                                                        id="btn_transcript_image_delete"><i class="bi bi-x-lg"></i></button>
                                                    <div class="drag-area drag-area-transcript_image">
                                                        <div
                                                            class="dropify-message dropify-message-transcript_image {{ ($professional_qualification && $professional_qualification->transcript_image) ? 'd-none' : 'd-block' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                x="0px" y="0px" width="64px" height="64px"
                                                                viewBox="0 0 64 64" enable-background="new 0 0 64 64"
                                                                xml:space="preserve">
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
                                                        <input name="transcript_image" type="hidden" id="transcript_image"
                                                            value="{{ $professional_qualification->transcript_image ?? old('transcript_image') }}"
                                                            class="file-input" />
                                                        <div class="image-preview"> @if(
                                                                $professional_qualification &&
                                                                $professional_qualification->transcript_image
                                                            ) <img
                                                                    src="{{ $professional_qualification->full_transcript_image }}"
                                                        id="display_transcript_image" class="preview-image"> @else
                                                                    <img src="" id="display_transcript_image"
                                                                        class="d-none preview-image">
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <input type="file" style="display:none" id="drag-transcript_image"
                                                        class="drag-image" accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <div class="col-md-12 col-lg-12">
                                                <label class="form-label">Back Transcript Image/Certificate</label><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($professional_qualification && $professional_qualification->transcript_bac_1) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                        onclick="imageDelete('transcript_bac_1')"
                                                        id="btn_transcript_bac_1_delete"><i class="bi bi-x-lg"></i></button>
                                                    <div class="drag-area drag-area-transcript_bac_1">
                                                        <div
                                                            class="dropify-message dropify-message-transcript_bac_1 {{ ($professional_qualification && $professional_qualification->transcript_bac_1) ? 'd-none' : 'd-block' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                x="0px" y="0px" width="64px" height="64px"
                                                                viewBox="0 0 64 64" enable-background="new 0 0 64 64"
                                                                xml:space="preserve">
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
                                                        <input name="transcript_bac_1" type="hidden" id="transcript_bac_1"
                                                            value="{{ $professional_qualification->transcript_bac_1 ?? old('transcript_bac_1') }}"
                                                            class="file-input" />
                                                        <div class="image-preview"> @if(
                                                                $professional_qualification &&
                                                                $professional_qualification->transcript_bac_1
                                                            ) <img
                                                                    src="{{ $professional_qualification->full_transcript_bac_1 }}"
                                                        id="display_transcript_bac_1" class="preview-image"> @else
                                                                    <img src="" id="display_transcript_bac_1"
                                                                        class="d-none preview-image">
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <input type="file" style="display:none" id="drag-transcript_bac_1"
                                                        class="drag-image" accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <div class="col-md-12 col-lg-12">
                                                <label class="form-label">Provisional Image *</label><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($professional_qualification && $professional_qualification->provisional_image) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                        onclick="imageDelete('provisional_image')"
                                                        id="btn_provisional_image_delete"><i
                                                            class="bi bi-x-lg"></i></button>
                                                    <div class="drag-area drag-area-provisional_image">
                                                        <div
                                                            class="dropify-message dropify-message-provisional_image {{ ($professional_qualification && $professional_qualification->provisional_image) ? 'd-none' : 'd-block' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                x="0px" y="0px" width="64px" height="64px"
                                                                viewBox="0 0 64 64" enable-background="new 0 0 64 64"
                                                                xml:space="preserve">
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
                                                        <input name="provisional_image" type="hidden" id="provisional_image"
                                                            value="{{ $professional_qualification->provisional_image ?? old('provisional_image') }}"
                                                            class="file-input" />
                                                        <div class="image-preview"> @if(
                                                                $professional_qualification &&
                                                                $professional_qualification->provisional_image
                                                            )
                                                                <img src="{{ $professional_qualification->full_provisional_image }}"
                                                        id="display_provisional_image" class="preview-image"> @else
                                                                    <img src="" id="display_provisional_image"
                                                                        class="d-none preview-image">
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <input type="file" style="display:none" id="drag-provisional_image"
                                                        class="drag-image" accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <div class="col-md-12 col-lg-12">
                                                <label class="form-label">Character Image *</label><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($professional_qualification && $professional_qualification->character_image) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                        onclick="imageDelete('character_image')"
                                                        id="btn_character_image_delete"><i class="bi bi-x-lg"></i></button>
                                                    <div class="drag-area drag-area-character_image">
                                                        <div
                                                            class="dropify-message dropify-message-character_image {{ ($professional_qualification && $professional_qualification->character_image) ? 'd-none' : 'd-block' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                x="0px" y="0px" width="64px" height="64px"
                                                                viewBox="0 0 64 64" enable-background="new 0 0 64 64"
                                                                xml:space="preserve">
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
                                                        <input name="character_image" type="hidden" id="character_image"
                                                            value="{{ $professional_qualification->character_image ?? old('character_image') }}"
                                                            class="file-input" />
                                                        <div class="image-preview"> @if(
                                                                $professional_qualification &&
                                                                $professional_qualification->character_image
                                                            ) <img
                                                                    src="{{ $professional_qualification->full_character_image }}"
                                                        id="display_character_image" class="preview-image"> @else
                                                                    <img src="" id="display_character_image"
                                                                        class="d-none preview-image">
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <input type="file" style="display:none" id="drag-character_image"
                                                        class="drag-image" accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <div class="col-md-12 col-lg-12">
                                                <label class="form-label">Equivalence Certificate </label><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($professional_qualification && $professional_qualification->equivalence_certificate) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                        onclick="imageDelete('equivalence_certificate')"
                                                        id="btn_equivalence_certificate_delete"><i
                                                            class="bi bi-x-lg"></i></button>
                                                    <div class="drag-area drag-area-equivalence_certificate">
                                                        <div
                                                            class="dropify-message dropify-message-equivalence_certificate {{ ($professional_qualification && $professional_qualification->equivalence_certificate) ? 'd-none' : 'd-block' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                x="0px" y="0px" width="64px" height="64px"
                                                                viewBox="0 0 64 64" enable-background="new 0 0 64 64"
                                                                xml:space="preserve">
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
                                                        <input name="equivalence_certificate" type="hidden"
                                                            id="equivalence_certificate"
                                                            value="{{ $professional_qualification->equivalence_certificate ?? old('equivalence_certificate') }}"
                                                            class="file-input" />
                                                        <div class="image-preview">
                                                            @if(
                                                                    $professional_qualification &&
                                                                    $professional_qualification->equivalence_certificate
                                                                ) <img
                                                                    src="{{ $professional_qualification->full_equivalence_certificate }}"
                                                                    id="display_equivalence_certificate" class="preview-image">
                                                            @else <img src="" id="display_equivalence_certificate"
                                                                class="d-none preview-image">
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <input type="file" style="display:none"
                                                        id="drag-equivalence_certificate" class="drag-image"
                                                        accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <div class="col-md-12 col-lg-12">
                                                <label class="form-label">Clinical / Community Work Image 1
                                                    *</label><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($professional_qualification && $professional_qualification->ojt_pcl_community_1_image) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                        onclick="imageDelete('ojt_pcl_community_1_image')"
                                                        id="btn_ojt_pcl_community_1_image_delete"><i
                                                            class="bi bi-x-lg"></i></button>
                                                    <div class="drag-area drag-area-ojt_pcl_community_1_image">
                                                        <div
                                                            class="dropify-message dropify-message-ojt_pcl_community_1_image {{ ($professional_qualification && $professional_qualification->ojt_pcl_community_1_image) ? 'd-none' : 'd-block' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                x="0px" y="0px" width="64px" height="64px"
                                                                viewBox="0 0 64 64" enable-background="new 0 0 64 64"
                                                                xml:space="preserve">
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
                                                        <input name="ojt_pcl_community_1_image" type="hidden"
                                                            id="ojt_pcl_community_1_image"
                                                            value="{{ $professional_qualification->ojt_pcl_community_1_image ?? old('ojt_pcl_community_1_image') }}"
                                                            class="file-input" />
                                                        <div class="image-preview">
                                                            @if(
                                                                    $professional_qualification &&
                                                                    $professional_qualification->ojt_pcl_community_1_image
                                                                ) <img
                                                                    src="{{ $professional_qualification->full_ojt_pcl_community_1_image }}"
                                                                    id="display_ojt_pcl_community_1_image"
                                                            class="preview-image"> @else <img src=""
                                                                    id="display_ojt_pcl_community_1_image"
                                                                    class="d-none preview-image">
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <input type="file" style="display:none"
                                                        id="drag-ojt_pcl_community_1_image" class="drag-image"
                                                        accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <div class="col-md-12 col-lg-12">
                                                <label class="form-label">Clinical / Community Work Image 2*</label><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($professional_qualification && $professional_qualification->ojt_pcl_community_2_image) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                        onclick="imageDelete('ojt_pcl_community_2_image')"
                                                        id="btn_ojt_pcl_community_2_image_delete"><i
                                                            class="bi bi-x-lg"></i></button>
                                                    <div class="drag-area drag-area-ojt_pcl_community_2_image">
                                                        <div
                                                            class="dropify-message dropify-message-ojt_pcl_community_2_image {{ ($professional_qualification && $professional_qualification->ojt_pcl_community_2_image) ? 'd-none' : 'd-block' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                x="0px" y="0px" width="64px" height="64px"
                                                                viewBox="0 0 64 64" enable-background="new 0 0 64 64"
                                                                xml:space="preserve">
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
                                                        <input name="ojt_pcl_community_2_image" type="hidden"
                                                            id="ojt_pcl_community_2_image"
                                                            value="{{ $professional_qualification->ojt_pcl_community_2_image ?? old('ojt_pcl_community_2_image') }}"
                                                            class="file-input" />
                                                        <div class="image-preview">
                                                            @if(
                                                                    $professional_qualification &&
                                                                    $professional_qualification->ojt_pcl_community_2_image
                                                                ) <img
                                                                    src="{{ $professional_qualification->full_ojt_pcl_community_2_image }}"
                                                                    id="display_ojt_pcl_community_2_image"
                                                            class="preview-image"> @else <img src=""
                                                                    id="display_ojt_pcl_community_2_image"
                                                                    class="d-none preview-image">
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <input type="file" style="display:none"
                                                        id="drag-ojt_pcl_community_2_image" class="drag-image"
                                                        accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <div class="col-md-12 col-lg-12">
                                                <label class="form-label">Council Registration Certificate</label><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($professional_qualification && $professional_qualification->council_registration_certificate) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                        onclick="imageDelete('council_registration_certificate')"
                                                        id="btn_council_registration_certificate_delete"><i
                                                            class="bi bi-x-lg"></i></button>
                                                    <div class="drag-area drag-area-council_registration_certificate">
                                                        <div
                                                            class="dropify-message dropify-message-council_registration_certificate {{ ($professional_qualification && $professional_qualification->council_registration_certificate) ? 'd-none' : 'd-block' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                x="0px" y="0px" width="64px" height="64px"
                                                                viewBox="0 0 64 64" enable-background="new 0 0 64 64"
                                                                xml:space="preserve">
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
                                                        <input name="council_registration_certificate" type="hidden"
                                                            id="council_registration_certificate"
                                                            value="{{ $professional_qualification->council_registration_certificate ?? old('council_registration_certificate') }}"
                                                            class="file-input" />
                                                        <div class="image-preview">
                                                            @if(
                                                                    $professional_qualification &&
                                                                    $professional_qualification->council_registration_certificate
                                                                ) <img
                                                                    src="{{ $professional_qualification->full_council_registration_certificate }}"
                                                                    id="display_council_registration_certificate"
                                                            class="preview-image"> @else <img src=""
                                                                    id="display_council_registration_certificate"
                                                                    class="d-none preview-image">
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <input type="file" style="display:none"
                                                        id="drag-council_registration_certificate" class="drag-image"
                                                        accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12 mb-3">
                                            <div class="col-md-12 col-lg-12">
                                                <label class="form-label">NOC / Others</label><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($professional_qualification && $professional_qualification->noc_image) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                        onclick="imageDelete('noc_image')" id="btn_noc_image_delete"><i
                                                            class="bi bi-x-lg"></i></button>
                                                    <div class="drag-area drag-area-noc_image">
                                                        <div
                                                            class="dropify-message dropify-message-noc_image {{ ($professional_qualification && $professional_qualification->noc_image) ? 'd-none' : 'd-block' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                x="0px" y="0px" width="64px" height="64px"
                                                                viewBox="0 0 64 64" enable-background="new 0 0 64 64"
                                                                xml:space="preserve">
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
                                                        <input name="noc_image" type="hidden" id="noc_image"
                                                            value="{{ $professional_qualification->noc_image ?? old('noc_image') }}"
                                                            class="file-input" />
                                                        <div class="image-preview"> @if(
                                                                $professional_qualification &&
                                                                $professional_qualification->noc_image
                                                            ) <img src="{{ $professional_qualification->full_noc_image }}"
                                                        id="display_noc_image" class="preview-image"> @else <img
                                                                    src="" id="display_noc_image" class="d-none preview-image">
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <input type="file" style="display:none" id="drag-noc_image"
                                                        class="drag-image" accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    </fieldset>
                    <div class="mt-3">
                        <a href="{{ route('professional.profile.slc')}}" class="btn btn-primary w-25"> Back</a>

                        <button type="button" class="main_btn btn-loading mb-3" onclick="saveData(this.form)">Next</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('footer-scripts')
    @include('professional._partial.imagejs')
    @include('professional.profile.js.pcl')
@endpush