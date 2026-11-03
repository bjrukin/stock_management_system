<form enctype="multipart/form-data" method="post" action="{{ $action }}" id="form">
    @csrf
    <input type="hidden" class="form-control" name="id" value="{{ $professional_qualification->id  }}">
    <div class="card">
        <div class="card-body">
            <fieldset class="border rounded-3 p-3">
                <legend>{{$page_title}}</legend>
                <div class="row">
                    <div class="col-lg-4 col-sm-12 mb-3">
                        <label class="form-label">School Name</label>
                        <input name="school_name" class="form-control" type="text"
                            value="{{ $professional_qualification->college_name ??  old('school_name') }}">
                    </div>
                    <div class="col-lg-4 col-sm-12 mb-3">
                        <label class="form-label">Board</label>
                        <input name="board_university" class="form-control" type="text"
                            value="{{ $professional_qualification->board_university ??  'SLC / SEE' }}">
                    </div>
                    <div class="col-lg-4 col-sm-12 mb-3">
                        <label class="form-label">Passed Year </label>
                        <input name="passed_year" class="form-control" collegetype="text"
                            value="{{ $professional_qualification->passed_year ??  old('passed_year') }}" mminlength="4"
                            maxlength="4" />
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <div class="col-md-12 col-lg-12">
                                <label class="form-label">Transcript Image *</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ ($professional_qualification && $professional_qualification->transcript_image) ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('transcript_image')" id="btn_transcript_image_delete"><i
                                            class="bi bi-x-lg"></i></button>
                                    <div class="drag-area drag-area-transcript_image">
                                        <div
                                            class="dropify-message dropify-message-transcript_image {{ ($professional_qualification && $professional_qualification->transcript_image) ? 'd-none' : 'd-block' }}">
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
                                        <input name="transcript_image" type="hidden" id="transcript_image"
                                            value="{{ $professional_qualification->transcript_image ??  old('transcript_image') }}"
                                            class="file-input" />
                                        <div class="image-preview"> @if($professional_qualification &&
                                            $professional_qualification->transcript_image) <img
                                                src="{{ $professional_qualification->full_transcript_image }}"
                                                id="display_transcript_image" class="preview-image"> @else <img src=""
                                                id="display_transcript_image" class="d-none preview-image">
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
                                <label class="form-label">Transcript Image </label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ ($professional_qualification && $professional_qualification->transcript_bac_1) ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('transcript_bac_1')" id="btn_transcript_bac_1_delete"><i
                                            class="bi bi-x-lg"></i></button>
                                    <div class="drag-area drag-area-transcript_bac_1">
                                        <div
                                            class="dropify-message dropify-message-transcript_bac_1 {{ ($professional_qualification && $professional_qualification->transcript_bac_1) ? 'd-none' : 'd-block' }}">
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
                                            value="{{ $professional_qualification->transcript_bac_1 ??  old('transcript_bac_1') }}"
                                            class="file-input" />
                                        <div class="image-preview"> @if($professional_qualification &&
                                            $professional_qualification->transcript_bac_1) <img
                                                src="{{ $professional_qualification->full_transcript_bac_1 }}"
                                                id="display_transcript_bac_1" class="preview-image"> @else <img src=""
                                                id="display_transcript_bac_1" class="d-none preview-image">
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
                                        onclick="imageDelete('provisional_image')" id="btn_provisional_image_delete"><i
                                            class="bi bi-x-lg"></i></button>
                                    <div class="drag-area drag-area-provisional_image">
                                        <div
                                            class="dropify-message dropify-message-provisional_image {{ ($professional_qualification && $professional_qualification->provisional_image) ? 'd-none' : 'd-block' }}">
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
                                            value="{{ $professional_qualification->provisional_image ??  old('provisional_image') }}"
                                            class="file-input" />
                                        <div class="image-preview"> @if($professional_qualification &&
                                            $professional_qualification->provisional_image) <img
                                                src="{{ $professional_qualification->full_provisional_image }}"
                                                id="display_provisional_image" class="preview-image"> @else <img src=""
                                                id="display_provisional_image" class="d-none preview-image">
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
                                        onclick="imageDelete('character_image')" id="btn_character_image_delete"><i
                                            class="bi bi-x-lg"></i></button>
                                    <div class="drag-area drag-area-character_image">
                                        <div
                                            class="dropify-message dropify-message-character_image {{ ($professional_qualification && $professional_qualification->character_image) ? 'd-none' : 'd-block' }}">
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
                                            value="{{ $professional_qualification->character_image ??  old('character_image') }}"
                                            class="file-input" />
                                        <div class="image-preview"> @if($professional_qualification &&
                                            $professional_qualification->character_image) <img
                                                src="{{ $professional_qualification->full_character_image }}"
                                                id="display_character_image" class="preview-image"> @else <img src=""
                                                id="display_character_image" class="d-none preview-image">
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
                                <label class="form-label">Equivalent Certificate</label><br>
                                <div class="drag-container">
                                    <button type="button"
                                        class="{{ ($professional_qualification && $professional_qualification->equivalence_certificate) ? 'd-block' : 'd-none' }} img-delete-btn"
                                        onclick="imageDelete('equivalence_certificate')"
                                        id="btn_equivalence_certificate_delete"><i class="bi bi-x-lg"></i></button>
                                    <div class="drag-area drag-area-equivalence_certificate">
                                        <div
                                            class="dropify-message dropify-message-equivalence_certificate {{ ($professional_qualification && $professional_qualification->equivalence_certificate) ? 'd-none' : 'd-block' }}">
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
                                            value="{{ $professional_qualification->equivalence_certificate ??  old('equivalence_certificate') }}"
                                            class="file-input" />
                                        <div class="image-preview"> @if($professional_qualification &&
                                            $professional_qualification->equivalence_certificate) <img
                                                src="{{ $professional_qualification->full_equivalence_certificate }}"
                                                id="display_equivalence_certificate" class="preview-image"> @else <img
                                                src="" id="display_equivalence_certificate"
                                                class="d-none preview-image">
                                            @endif
                                        </div>
                                    </div>
                                    <input type="file" style="display:none" id="drag-equivalence_certificate"
                                        class="drag-image" accept="image/*" />
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