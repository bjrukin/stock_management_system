@php
use Illuminate\Support\Facades\Auth;
$user = Auth::guard('admin')->user();
@endphp
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-end mb-0">
        <li>
            <a class="nav-link">
               {{Auth::guard('admin')->user()->name}} 
            </a>
        </li>
        <li>
            <a href="{{ route('admin-account-setting')}}" class="nav-link">
              Account Setting
            </a>
        </li>
        <li>
            <a class="nav-link" href="{{route('admin-logout')}}">
                <i class="fe-log-out"></i> Logout
            </a>
        </li>
    </ul>
    <div class="logo-box d-none d-sm-block">
        <a href="./" class="logo logo-dark text-center">
            <span class="logo-lg">
                <img src="{{ asset('assets/admin/images/logo.png')}}" alt="{{env('APP_NAME')}}" height="60">
            </span>
        </a>
    </div>
    <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
        <li>
            <button class="button-menu-mobile disable-btn waves-effect"> <i class="fe-menu"></i> </button>
        </li>
        
    </ul>
    <div class="clearfix"></div>
</div>