@extends('layout.professional')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header">My Account</div>
                    <div class="card-body">

                        <form id="frm_profile" method="post" action="{{ route('professional.account.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" value="{{ $user->name ??  old('name') }}"
                                    type="text">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input class="form-control" name="phone" value="{{ $user->phone ??  old('phone') }}"
                                    type="text">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control" name="email" value="{{ $user->email ??  old('email') }}"
                                    type="text" readonly>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary btn-loading" type="submit"> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header">Change Password</div>
                    <div class="card-body">
                        <form id="frm_password" method="post"
                            action="{{ route('professional.account.update.password') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Old Password</label>
                                <input class="form-control" name="old_password" type="password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input class="form-control" name="new_password" type="password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input class="form-control" name="confirm_password" type="password">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary btn-loading" type="submit"> Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



    </div>
    @endsection
    @push('footer-scripts')
    @include('professional.account.js.index')
    @endpush