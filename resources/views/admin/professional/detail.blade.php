@extends('layout.admin')
@section('content')
<div class="container-fluid">
    @include('admin.professional.widgets.detail.info')
    @include('admin.professional.widgets.detail.qualification')
    @include('admin.professional.widgets.detail.document')
    @include('admin.professional.widgets.detail.status')
    @include('admin.professional.widgets.detail.applicant')
</div>
@endsection
@push('footer-scripts')
@include('admin.professional.js.detail')
@endpush