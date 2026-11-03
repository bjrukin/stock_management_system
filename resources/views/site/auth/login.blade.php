@extends('layout.site')
@section('content')
<div class="p-3">
    <form id="form" method="post" action="{{ route('checkLogin') }}" class="form">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="text" class="form-control" name="email" value="{{old('email')}}">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group input-group-merge">
                <input type="password" name="password" placeholder="Password" class="form-control" spellcheck="false"
                    autocorrect="off" autocapitalize="off">
                <div class="input-group-text" data-password="false">
                    <span class="password-eye"></span>
                </div>
            </div>
        </div>
        <div class="mb-3 d-grid text-center">
            <button class="btn btn-primary btn-loading" type="submit"> Log In </button>
        </div>
    </form>
        <hr />
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="mb-3 d-grid text-center">
                    <a href="{{route('register',['nepal'])}}" class="btn btn-info py-2"> Register</a>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 d-none">
                <div class="mb-3 d-grid text-center">
                    <a href="{{route('register',['foreign'])}}" class="btn btn-info py-2"> Foreign Register</a>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="mb-3 d-grid text-center">
                    <a href="{{route('forgot.password')}}" class="btn btn-danger py-2"> Forgot Password?</a>
                </div>
            </div>
        </div>
</div>
@endsection
@push('footer-scripts')
@include('site.auth.js.login')
@endpush