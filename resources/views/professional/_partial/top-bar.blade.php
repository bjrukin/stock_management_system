<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-end mb-0">
        <li>
            <a class="nav-link" href="{{ route('professional.account') }}">
            <b>{{ ucwords(strtolower(auth()->guard('professional')->user()->name)) }}</b>
            </a>
        </li>
        <li>
            <a class="nav-link" href="{{route('professional.logout')}}">
                <i class="mdi mdi-logout"></i> Logout
            </a>
        </li>
    </ul>
    <div class="logo-box d-none d-sm-block text-center">
        <a href="{{ route('professional.dashboard') }}" class="logo logo-dark">
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