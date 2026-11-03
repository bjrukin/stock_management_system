@extends('layout.professional')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    @include('professional.profile.widgets.breadcrumb')
                    @include('professional.profile.widgets.note')
                    <div class="card">
                        <div class="card-body">
                            <form id="form" method="post" action="{{ route('professional.profile.save.voucher') }}">
                                @csrf
                                <input name="exam_apply_id" value="{{ $applicant->id ?? null }}" type="hidden">
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="col-md-12 col-lg-12">
                                                <h1 style="color: red; font-size: 26px; font-family: bold;">Voucher Image
                                                </h1><br>
                                                <div class="drag-container">
                                                    <button type="button"
                                                        class="{{ ($applicant && $applicant?->voucher_image) ? 'd-block' : 'd-none' }}"
                                                        onclick="imageDelete('voucher_image')"
                                                        id="btn_voucher_image_delete"><i class="fa fa-times"></i></button>
                                                    <div class="drag-area drag-area-voucher_image">
                                                        <div
                                                            class="dropify-message dropify-message-voucher_image {{ ($applicant && $applicant?->voucher_image) ? 'd-none' : 'd-block' }}">
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
                                                        <input type="file" hidden="" id="drag-voucher_image" />
                                                        <input name="voucher_image" type="hidden" id="voucher_image"
                                                            value="{{ $applicant->voucher_image ?? old('voucher_image') }}" />
                                                        <div class="image-preview"> @if(
                                                                $applicant &&
                                                                $applicant?->voucher_image
                                                            )
                                                                <img src="{{ $applicant?->full_voucher_image }}"
                                                        id="display_voucher_image"> @else <img src=""
                                                                    id="display_voucher_image" class="d-none">
                                                                @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <a href="{{ route('professional.profile.personal')}}" class="btn btn-primary"> Back</a>
                                    <button class="btn btn-primary btn-loading" type="submit"> Next</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@push('footer-scripts')
@include('professional.profile.js.voucher')
@endpush