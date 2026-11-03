@extends('layout.site')
@section('content')
<div class="card">
<div class="card-header">Reset Password</div>
    <div class="card-body">
        <form id="form" method="post" action="{{ route('save.password') }}" class="form">
            @csrf
            <div class="mb-3">
                <label class="form-label">OTP</label>
                <input type="text" class="form-control" name="otp" value="{{old('otp')}}">
            </div>
            <div class="mb-3">
                <label class="form-label">New password</label>
                <input type="text" class="form-control" name="new_password" value="{{old('new_password')}}">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="text" class="form-control" name="password_confirmation" value="{{old('password_confirmation')}}">
            </div>
            <div class="mb-3">
                <button class="btn btn-primary btn-loading" type="submit"> Change
                    Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('footer-scripts')
@include('site.auth.js.reset')
@endpush