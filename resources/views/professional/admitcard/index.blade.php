@extends('layout.professional')
@section('content')
<div class="container-fluid">
    <div class="mt-3">
        <button class="btn btn-primary" onclick="printDiv()">Print Admit Card</button>
    </div>
    <div id="print-content">
        @include("professional.admitcard.widgets.professional")
        @include("professional.admitcard.widgets.note")
        @include("professional.admitcard.widgets.official")
    </div>
</div>
@endsection
@push('footer-scripts')
@include('professional.admitcard.js.index')
@endpush