@extends('layout.professional')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="h1">{{$page_title}}</div>
            </div>
        </div>

        {{-- Section to display session-flashed messages --}}
        @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        {{-- End of session-flashed messages section --}}

        <div class="card">
            <div class="card-body container">
                <h4 class="text-danger">
                    Note: Before applying for the licence please review your profile
                    <a class="btn btn-primary" href="{{route('professional.exam.status')}}"> Here</a>
                </h4>
            </div>
        </div>
        <div class="card">
            <div class="card-body container">
                <span class="text-justify">
                    <div class="row">
                        <form method="post" id="form" action="{{ route('professional.exam.save.application') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="exam_id" value="{{ $exam_id}}">
                            <div class="row">
                                <div class="{{ $level_id == 4 ? 'col-12' : 'col-8' }}">
                                    @include('professional.exam.application.bank')
                                    <div class="row col-12">
                                        @include('professional.exam.application.form')
                                        @include('professional.exam.application.biometric')
                                        <div class="mb-3">
                                            <button type="submit" id="submitButton" class="btn btn-primary btn-loading w-100"> Apply For Licence </button>
                                        </div>
                                    </div>
                                </div>
                                @if($level_id != 4)
                                <div class="col-4">
                                    @include('professional.exam.application.biometric_info')
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
@include('professional._partial.imagejs')
@include('professional.exam.js.application')
@if($level_id != 4)
@include('professional.exam.js.biometric_photo')
@endif
<style>
.camera-container {
    position: relative;
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
}

.camera-box {
    position: relative;
    width: 100%;
    height: 300px;
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 15px;
    background-color: #f8f9fa;
}

.camera-box video,
.camera-box canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.face-frame {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 10;
}

.face-frame svg {
    width: 100%;
    height: 100%;
}

.corner {
    stroke: #007bff;
    stroke-width: 3;
    stroke-linecap: round;
    fill: none;
}

.guide-oval {
    stroke: #007bff;
    stroke-width: 2;
    fill: none;
    opacity: 0.7;
}

.camera-controls {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 15px;
}

.preview-image {
    max-width: 100%;
    border-radius: 8px;
    border: 2px solid #28a745;
}

.photo-status {
    margin-top: 10px;
    font-weight: bold;
}

.photo-status .badge-success {
    background-color: #28a745;
}

.form-check-input:checked {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.form-check-input:checked:focus {
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}
.form-check-label { cursor: pointer;}
</style>
@endpush