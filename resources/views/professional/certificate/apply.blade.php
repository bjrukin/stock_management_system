@extends('layout.professional')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h1 class="h1">{{ $page_title }}</h1>
                </div>
            </div>

            {{-- In your apply.blade.php --}}
            @if($has_passed_exam)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    You need to pass an exam before you can request a certificate.
                    Please check your exam status first.
                </div>
            @else
                {{-- Your certificate request form --}}
                <div class="card">
                    <div class="card-body container">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>दस्तुर</strong><br>
                                <span>(क) विशिष्ठ तहको लागि रु. {{ $applicant->level_id == 1 ? '2050' : '' }}।</span><br>
                                <span>(ख) प्रथम तहको लागि रु. {{ $applicant->level_id == 2 ? '1550' : '' }}।</span><br>
                                <span>(ग) द्वितीय तहको लागि रु. {{ $applicant->level_id == 3 ? '1050' : '' }}।</span><br>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <form method="post" id="certificateApplicationForm"
                                action="{{ route('professional.certificate.apply') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="professional_id" value="{{ $applicant->professional_id ?? '' }}">
                                <input type="hidden" name="exam_id" value="{{ $applicant->exam_id ?? '' }}">
                                <input type="hidden" name="program_id" value="{{ $applicant->program_id ?? '' }}">
                                <input type="hidden" name="level_id" value="{{ $applicant->level_id ?? '' }}">
                                <input type="hidden" name="amount"
                                    value="{{ $applicant->level_id == 1 ? 2050 : ($applicant->level_id == 2 ? 1550 : 1050) }}"
                                    id="payment_amount">

                                <div class="row">
                                    <div class="col-md-4 col-sm-12 col-xs-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="symbol_number_input">Symbol No. *</label>
                                            <input type="text" name="symbol_number" id="symbol_number_input"
                                                class="form-control" value="{{ $symbol_number ?? '' }}"
                                                placeholder="eg:1234454 / A-123-PH" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">College Location: *</label><br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="college_location"
                                                    id="collegeNational" value="national" checked>
                                                <label class="form-check-label" for="collegeNational">National College
                                                    (Nepal)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="college_location"
                                                    id="collegeInternational" value="international">
                                                <label class="form-check-label" for="collegeInternational">International College
                                                    (Other Country)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Choose Payment Method:</label><br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="payment_option"
                                                    id="radioVoucher" value="voucher" checked>
                                                <label class="form-check-label" for="radioVoucher">Voucher</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="payment_option"
                                                    id="radioKhalti" value="khalti">
                                                <label class="form-check-label" for="radioKhalti">Khalti</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Voucher Section --}}
                                <div class="row" id="voucherSection">
                                    <div class="col-lg-4">
                                        <h1 style="color: red; font-size: 26px; font-family: bold;">Voucher Image</h1>
                                        <br>
                                        <div class="drag-container">
                                            <button type="button"
                                                class="{{ $professional && $professional->info?->voucher_image ? 'd-block' : 'd-none' }}"
                                                onclick="imageDelete('voucher_image')" id="btn_voucher_image_delete"><i
                                                    class="fa fa-times"></i></button>
                                            <label class="drag-area drag-area-voucher_image" for="drag-voucher_image"
                                                style="cursor: pointer; display: block;">
                                                <div
                                                    class="dropify-message dropify-message-voucher_image {{ $professional && $professional->info?->voucher_image ? 'd-none' : 'd-block' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px"
                                                        width="64px" height="64px" viewBox="0 0 64 64"
                                                        enable-background="new 0 0 64 64" xml:space="preserve">
                                                        <path fill="none" stroke="#8a8a8a" stroke-width="2"
                                                            stroke-miterlimit="10"
                                                            d="M41,50h14c4.565,0,8-3.582,8-8s-3.435-8-8-8  c0-11.046-9.52-20-20.934-20C23.966,14,14.8,20.732,13,30c0,0-0.831,0-1.667,0C5.626,30,1,34.477,1,40s4.293,10,10,10H41" />
                                                        <polyline fill="none" stroke="#8a8a8a" stroke-width="2"
                                                            stroke-linejoin="bevel" stroke-miterlimit="10"
                                                            points="23.998,34   31.998,26 39.998,34 " />
                                                        <g>
                                                            <line fill="none" stroke="#8a8a8a" stroke-width="2"
                                                                stroke-miterlimit="10" x1="31.998" y1="26" x2="31.998"
                                                                y2="46" />
                                                        </g>
                                                    </svg>
                                                    <p>Click here to upload image</p>
                                                </div>
                                                <input type="file" hidden id="drag-voucher_image" name="voucher_image"
                                                    accept="image/*" />
                                                <input type="hidden" id="voucher_image"
                                                    value="{{ $professional->info->voucher_image ?? old('voucher_image') }}" />
                                                <div class="image-preview">
                                                    @if ($professional && $professional->info?->voucher_image)
                                                        <img src="{{ $professional->info?->full_voucher_image }}"
                                                            id="display_voucher_image">
                                                    @else
                                                        <img src="" id="display_voucher_image" class="d-none">
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Khalti Section --}}
                                <div class="row mt-3" id="khaltiSection" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="khalti-container">
                                            <h1>Pay with Khalti</h1>
                                            {{-- No extra Khalti form here, the main form handles submission --}}
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary float-left mt-2 btn-loading" id="submitButton">
                                    <i class="fa fa-check"></i> Apply For Certificate (Voucher)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('footer-scripts')
    @include('professional.certificate.js.apply')
@endsection