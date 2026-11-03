@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-3">My Profile</h3>
                        <form id="frm_profile" method="post" action="{{ route('admin.account.update') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name"
                                value="{{old('name' , ($user) ? $user->name : '' )}}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone"
                                value="{{old('phone' , ($user) ? $user->phone : '' )}}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" name="email"
                                value="{{old('email' , ($user) ? $user->email : '' )}}" readonly>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary btn-loading" type="submit"> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-3">Change Password</h3>
                        <form id="frm_password" method="post" action="{{ route('admin.account.update.password') }}">
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
    @include('admin.account.js.index')
    @endpush