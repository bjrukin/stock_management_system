<form enctype="multipart/form-data" method="post" action="{{ $action }}" id="form">
    @csrf
    <input type="hidden" class="form-control" name="id" value="{{ $professional_qualification->id  }}">
    <div class="card">
        <div class="card-body">
            <fieldset class="border rounded-3 p-3">
                <legend>{{$page_title}}</legend>
                <div class="row">
                    <div class="col-lg-4 col-sm-12 mb-3">
                        <label class="form-label">College Name</label>
                        <input name="college_name" class="form-control" type="text"
                            value="{{ $professional_qualification->college_name ?? old('college_name') }}">
                    </div>
                    <div class="col-lg-4 col-sm-12 mb-3">
                        <label class="form-label">Universities</label>
                        <input name="board_university" class="form-control" type="text"
                            value="{{ $professional_qualification->board_university ?? old('board_university') }}">
                    </div>
                    <div class="col-lg-4 col-sm-12 mb-3">
                        <label class="form-label">Admission Year </label>
                        <input name="admission_year" class="form-control" collegetype="text"
                            value="{{ $professional_qualification->admission_year ?? old('admission_year') }}"
                            minlength="4" maxlength="4" />
                    </div>
                    <div class="col-lg-4 col-sm-12 mb-3">
                        <label class="form-label">Passed Year </label>
                        <input name="passed_year" class="form-control" collegetype="text"
                            value="{{ $professional_qualification->passed_year ?? old('passed_year') }}" mminlength="4"
                            maxlength="4" />
                    </div>
                    <div class="col-lg-4 col-sm-12 mb-3">
                        <label class="form-label">Registration Number</label>
                        <input name="registration_number"
                            value="{{ $professional_qualification->registration_number ?? old('registration_number') }}"
                            class="form-control" id="basicInput" type="text">
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">Transcript Image *</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->transcript_mas_marksheet ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('transcript_mas_marksheet')"
                                        id="btn_transcript_mas_marksheet_delete">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="drag-area drag-area-transcript_mas_marksheet">
                                        <div
                                            class="dropify-message dropify-message-transcript_mas_marksheet {{ $professional_qualification && $professional_qualification->transcript_mas_marksheet ? 'd-none' : 'd-block' }}">
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
                                        <input name="transcript_mas_marksheet" type="hidden"
                                            id="transcript_mas_marksheet"
                                            value="{{ $professional_qualification->transcript_mas_marksheet ?? old('transcript_mas_marksheet') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->transcript_mas_marksheet)
                                            <img src="{{ $professional_qualification->full_transcript_mas_marksheet }}"
                                                id="display_transcript_mas_marksheet" class="preview-image">
                                            @else
                                            <img src="" id="display_transcript_mas_marksheet"
                                                class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-transcript_mas_marksheet"
                                        class="drag-image" accept="image/*" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">Transcript Image</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->transcript_bac_1 ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('transcript_bac_1')" id="btn_transcript_bac_1_delete">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="drag-area drag-area-transcript_bac_1">
                                        <div
                                            class="dropify-message dropify-message-transcript_bac_1 {{ $professional_qualification && $professional_qualification->transcript_bac_1 ? 'd-none' : 'd-block' }}">
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
                                        <input name="transcript_bac_1" type="hidden" id="transcript_bac_1"
                                            value="{{ $professional_qualification->transcript_bac_1 ?? old('transcript_bac_1') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->transcript_bac_1)
                                            <img src="{{ $professional_qualification->full_transcript_bac_1 }}"
                                                id="display_transcript_bac_1" class="preview-image">
                                            @else
                                            <img src="" id="display_transcript_bac_1" class="d-none preview-image">
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
                                <label class="form-label">Transcript Image</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->transcript_bac_2 ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('transcript_bac_2')" id="btn_transcript_bac_2_delete">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="drag-area drag-area-transcript_bac_2">
                                        <div
                                            class="dropify-message dropify-message-transcript_bac_2 {{ $professional_qualification && $professional_qualification->transcript_bac_2 ? 'd-none' : 'd-block' }}">
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
                                        <input name="transcript_bac_2" type="hidden" id="transcript_bac_2"
                                            value="{{ $professional_qualification->transcript_bac_2 ?? old('transcript_bac_2') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->transcript_bac_2)
                                            <img src="{{ $professional_qualification->full_transcript_bac_2 }}"
                                                id="display_transcript_bac_2" class="preview-image">
                                            @else
                                            <img src="" id="display_transcript_bac_2" class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-transcript_bac_2"
                                        class="drag-image" accept="image/*" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">Transcript Image</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->transcript_bac_3 ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('transcript_bac_3')" id="btn_transcript_bac_3_delete">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="drag-area drag-area-transcript_bac_3">
                                        <div
                                            class="dropify-message dropify-message-transcript_bac_3 {{ $professional_qualification && $professional_qualification->transcript_bac_3 ? 'd-none' : 'd-block' }}">
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
                                        <input name="transcript_bac_3" type="hidden" id="transcript_bac_3"
                                            value="{{ $professional_qualification->transcript_bac_3 ?? old('transcript_bac_3') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->transcript_bac_3)
                                            <img src="{{ $professional_qualification->full_transcript_bac_3 }}"
                                                id="display_transcript_bac_3" class="preview-image">
                                            @else
                                            <img src="" id="display_transcript_bac_3" class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-transcript_bac_3"
                                        class="drag-image" accept="image/*" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">Equivalence Certificate </label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->equivalence_certificate ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('equivalence_certificate')"
                                        id="btn_equivalence_certificate_delete"><i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="drag-area drag-area-equivalence_certificate">
                                        <div
                                            class="dropify-message dropify-message-equivalence_certificate {{ $professional_qualification && $professional_qualification->equivalence_certificate ? 'd-none' : 'd-block' }}">
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
                                        <input name="equivalence_certificate" type="hidden" id="equivalence_certificate"
                                            value="{{ $professional_qualification->equivalence_certificate ?? old('equivalence_certificate') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->equivalence_certificate)
                                            <img src="{{ $professional_qualification->full_equivalence_certificate }}"
                                                id="display_equivalence_certificate" class="preview-image">
                                            @else
                                            <img src="" id="display_equivalence_certificate"
                                                class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-equivalence_certificate"
                                        class="drag-image" accept="image/*" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">Provisional Image *</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->provisional_image ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('provisional_image')" id="btn_provisional_image_delete">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="drag-area drag-area-provisional_image">
                                        <div
                                            class="dropify-message dropify-message-provisional_image {{ $professional_qualification && $professional_qualification->provisional_image ? 'd-none' : 'd-block' }}">
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
                                        <input name="provisional_image" type="hidden" id="provisional_image"
                                            value="{{ $professional_qualification->provisional_image ?? old('provisional_image') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->provisional_image)
                                            <img src="{{ $professional_qualification->full_provisional_image }}"
                                                id="display_provisional_image" class="preview-image">
                                            @else
                                            <img src="" id="display_provisional_image" class="d-none preview-image">
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
                                        class="{{ $professional_qualification && $professional_qualification->character_image ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('character_image')" id="btn_character_image_delete">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="drag-area drag-area-character_image">
                                        <div
                                            class="dropify-message dropify-message-character_image {{ $professional_qualification && $professional_qualification->character_image ? 'd-none' : 'd-block' }}">
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
                                        <input name="character_image" type="hidden" id="character_image"
                                            value="{{ $professional_qualification->character_image ?? old('character_image') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->character_image)
                                            <img src="{{ $professional_qualification->full_character_image }}"
                                                id="display_character_image" class="preview-image">
                                            @else
                                            <img src="" id="display_character_image" class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-character_image" class="drag-image"
                                        accept="image/*" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">Intership Image *</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->intership_image ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('intership_image')" id="btn_intership_image_delete"><i
                                            class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="drag-area drag-area-intership_image">
                                        <div
                                            class="dropify-message dropify-message-intership_image {{ $professional_qualification && $professional_qualification->intership_image ? 'd-none' : 'd-block' }}">
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
                                        <input name="intership_image" type="hidden" id="intership_image"
                                            value="{{ $professional_qualification->intership_image ?? old('intership_image') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->intership_image)
                                            <img src="{{ $professional_qualification->full_intership_image }}"
                                                id="display_intership_image" class="preview-image">
                                            @else
                                            <img src="" id="display_intership_image" class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-intership_image" class="drag-image"
                                        accept="image/*" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">NHPC Study Permission Letter
                                    Image</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->noc_image ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('noc_image')" id="btn_noc_image_delete"><i
                                            class="bi bi-x-lg"></i></button>
                                    <div class="drag-area drag-area-noc_image">
                                        <div
                                            class="dropify-message dropify-message-noc_image {{ $professional_qualification && $professional_qualification->noc_image ? 'd-none' : 'd-block' }}">
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
                                        <input name="noc_image" type="hidden" id="noc_image"
                                            value="{{ $professional_qualification->noc_image ?? old('noc_image') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->noc_image)
                                            <img src="{{ $professional_qualification->full_noc_image }}"
                                                id="display_noc_image" class="preview-image">
                                            @else
                                            <img src="" id="display_noc_image" class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-noc_image" class="drag-image"
                                        accept="image/*" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">Visa</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->visa_image ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('visa_image')" id="btn_visa_image_delete"><i
                                            class="bi bi-x-lg"></i></button>
                                    <div class="drag-area drag-area-visa_image">
                                        <div
                                            class="dropify-message dropify-message-visa_image {{ $professional_qualification && $professional_qualification->visa_image ? 'd-none' : 'd-block' }}">
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
                                        <input name="visa_image" type="hidden" id="visa_image"
                                            value="{{ $professional_qualification->visa_image ?? old('visa_image') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->visa_image)
                                            <img src="{{ $professional_qualification->full_visa_image }}"
                                                id="display_visa_image" class="preview-image">
                                            @else
                                            <img src="" id="display_visa_image" class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-visa_image" class="drag-image"
                                        accept="image/*" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">Passport</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ $professional_qualification && $professional_qualification->passport_image ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('passport_image')" id="btn_passport_image_delete"><i
                                            class="bi bi-x-lg"></i></button>
                                    <div class="drag-area drag-area-passport_image">
                                        <div
                                            class="dropify-message dropify-message-passport_image {{ $professional_qualification && $professional_qualification->passport_image ? 'd-none' : 'd-block' }}">
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
                                        <input name="passport_image" type="hidden" id="passport_image"
                                            value="{{ $professional_qualification->passport_image ?? old('passport_image') }}"
                                            class="file-input" />
                                        <div class="image-preview">
                                            @if ($professional_qualification &&
                                            $professional_qualification->passport_image)
                                            <img src="{{ $professional_qualification->full_passport_image }}"
                                                id="display_passport_image" class="preview-image">
                                            @else
                                            <img src="" id="display_passport_image" class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-passport_image" class="drag-image"
                                        accept="image/*" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="mt-3"> <button type="button" class="main_btn btn-loading mb-3"
                    onclick="saveData(this.form)">Submit</button></div>
        </div>
    </div>
</form>