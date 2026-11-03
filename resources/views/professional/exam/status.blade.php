@extends('layout.professional')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="col-lg-12 col-sm-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h3>{{$page_title}}</h3>
                        <div class="text-justify">Thank you, You have applied form successfully.</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-sm-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        @include('professional.exam.status.info')
                        @include('professional.exam.status.qualification')
                        @include('professional.exam.status.document')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('footer-scripts')
    @include('professional.exam.js.status')
@endpush