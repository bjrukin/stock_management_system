@extends('layout.professional')
@section('content')
    <div class="content">
        <div class="container-fluid">
            @if(session('message'))
                <div class="card">
                    <div class="card-body conatiner">
                        <span class="text-justify text-danger">
                            {{ session('message') }}
                        </span>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mb-3">{{ $page_title}}</h3>
                            <form id="form" method="post" action="{{ route('professional.kyc.save') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label"> Profile Image </label>
                                    <div class="drag-container">
                                        @if(!$kyc)
                                            <button type="button"
                                                class="{{ (old('profile_img') || isset($professional->info->profile_picture)) ? 'd-block' : 'd-none' }} img-delete-btn"
                                                onclick="imageDelete('profile_img')" id="btn_profile_img_delete"><i
                                                    class="bi bi-x-lg"></i></button>
                                        @endif

                                        <div class="drag-area drag-area-profile_img {{ $kyc ? 'drag-area-saved' : '' }}">
                                            <div
                                                class="dropify-message dropify-message-image {{ ($kyc && $kyc->profile_img || old('profile_img') || (!$kyc && isset($professional->info->profile_picture))) ? 'd-none' : 'd-block' }}">
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
                                            <input name="profile_img" type="hidden" id="profile_img"
                                                value="{{ old('profile_img') ?? $kyc->profile_img ?? $professional->info->profile_picture ?? '' }}"
                                                class="file-input" />
                                            <div class="image-preview text-center">
                                                @if($kyc && $kyc->profile_img)
                                                    <img src="{{$kyc->full_profile_img }}" class="preview-image mt-2"
                                                        style="max-height: 150px;">
                                                @elseif(old('profile_img'))
                                                    <img src="{{ old('profile_img') }}" class="preview-image mt-2"
                                                        style="max-height: 150px;">
                                                @elseif(isset($professional->info->profile_picture))
                                                    <img src="{{ $professional->info->full_profile_picture }}"
                                                        class="preview-image mt-2" style="max-height: 150px;">
                                                @else
                                                    <img src="" class="d-none preview-image mt-2" style="max-height: 150px;">
                                                @endif
                                            </div>
                                        </div>
                                        <input type="file" style="display:none" id="drag-profile_img" class="drag-image"
                                            accept="image/*" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Name *</label>
                                        <input class="form-control" name="name"
                                            value="{{ $kyc->name ?? $professional->info->full_name ?? $professional->name ?? old('name') }}"
                                            type="text" {{ $kyc ? 'disabled' : '' }}>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Date of Birth(B.S) *</label>
                                        <input class="form-control" name="dob" id="dob"
                                            value="{{ $kyc->dob ?? $professional->info->dob_nep ?? old('dob') }}"
                                            type="text" placeholder="YYYY-MM-DD" {{ $kyc ? 'disabled' : '' }}>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Symbol Number</label>
                                        <input class="form-control" name="symbol_number"
                                            value="{{ $kyc->symbol_number ?? old('symbol_number') }}" type="text" {{ $kyc ? 'disabled' : '' }}>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Registration Number</label>
                                        <input class="form-control" name="registration_number"
                                            value="{{ $kyc->registration_number ?? old('registration_number') }}"
                                            type="text" {{ $kyc ? 'disabled' : '' }}>
                                    </div>
                                </div>
                                @if(!$kyc)
                                    <div class="mt-3 mb-3">
                                        <button type="submit" class="btn btn-primary  btn-loading">Submit</button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('footer-scripts')
    @include('professional.kyc.js.index')
@endpush