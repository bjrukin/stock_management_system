@include('professional._partial.metahead')

<body data-layout-color="light" data-layout-mode="default" data-layout-size="fluid" data-topbar-color="light"
    data-leftbar-position="fixed" data-leftbar-color="light" data-leftbar-size='default' data-sidebar-user='true'>
    <div id="wrapper">
        @include('professional._partial.top-bar')
        @include('professional._partial.nav')
        <div class="content-page">
            @yield('content')
            @include('professional._partial.footer')
        </div>
    </div>
    @include('professional._partial.scripts')
    @stack('footer-scripts')
</body>

</html>