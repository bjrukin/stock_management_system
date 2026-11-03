@php
$admin = auth()->guard('admin')->user();
if($admin->role_id == 6) {
    $role =  $admin->subject_committee_users->pluck('subject_committee.name')->implode(',');
} else {
    $role =  $admin->role->name;
}
@endphp
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-end mb-0">
        <li>
            <a class="nav-link" href="{{ route('admin.account.setting') }}">
            {{  $admin->name }}   (<b> {{ $role  }} </b>)
            </a>
        </li>
        <li>
            <a class="nav-link" href="{{route('admin.logout')}}">
                <i class="mdi mdi-logout"></i> Logout
            </a>
        </li>
    </ul>
    <div class="logo-box d-none d-sm-block text-center">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-lg">
            <img src="{{ asset('assets/images/logo.png')}}" alt="{{env('APP_NAME')}}" style="height:96px; width:100%;">
            </span>
        </a>
    </div>
    <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
        <li>
            <button class="button-menu-mobile disable-btn waves-effect"> <i class="mdi mdi-menu"></i> </button>
        </li>
    </ul>
    <div class="clearfix"></div>
</div>