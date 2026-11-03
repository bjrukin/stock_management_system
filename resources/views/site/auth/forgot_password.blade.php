@extends('layout.site')
@section('content')
<div class="card">
    <div class="card-body p-4">
        <div class="mb-4">
            <h4 class="text-uppercase mt-0">Forgot Password</h4>
        </div>
        <form id="form" method="post" action="{{ route('otp') }}" class="form">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" name="email" value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <button class="btn btn-primary btn-loading" type="submit"> Reset
                    Password</button>
            </div>
        </form>
        <div class="row mb-3">
            <div class="col-12 text-center">
                <p> <a href="{{ route('login') }}" class="btn btn-info py-2"> Back To Log
                        In</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-scripts')
@include('site.auth.js.forgot')
@endpush