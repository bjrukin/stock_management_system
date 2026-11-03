@include('site._partial.metahead')

<body>
   <div class="account-pages my-4">
        <div class="container">
            <div class="mb-4">
                @include('site._partial.header')
                <div class="card">
                    <div class="card-body py-4">
                        <div class="row">
                            <div class="col-md-6 col-lg-6 col-sm-12 d-none d-sm-block text-start px-5"
                                style="border-right: solid 1px #dddddd;">
                                @include('site._partial.info')
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('site._partial.scripts')
    @stack('footer-scripts')
</body>

</html>