<!DOCTYPE html><!--[if IE 8]>
<html class="ie8" lang="en"><![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"><![endif]-->
<!--[if !IE]><!-->
<html lang="en"><!--<![endif]-->
<head>
    <title>
        @section('title')
            {{ config('codex.display_name') }}
        @show
    </title>

    @section('meta')
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @show

    @section('stylesheets')
        <link href="{{ asset('vendor/codex/styles/stylesheet.css') }}" type="text/css" rel="stylesheet">
        <link href="{{ asset('vendor/codex/styles/themes/theme-codex.css') }}" type="text/css" rel="stylesheet" id="theme-style">
    @show

    @section('styles')@show

</head>

<body class="@yield('bodyClass', 'page-loading page-header-fixed page-footer-fixed page-edged page-sidebar-condensed page-sidebar-fixed')">

    @section('page-loader')
        <div id="page-loader">
            <div class="loader loader-page"></div>
        </div>
    @show

    @yield('body')


    @section('javascripts')
        <script src="{{ asset('vendor/codex/scripts/vendor.js') }}"></script>
        <script src="{{ asset('vendor/codex/scripts/codex.js') }}"></script>
        <script src="{{ asset('vendor/codex/scripts/addons.js') }}"></script>
    @show

    <script src="{{ asset('vendor/codex/bower_components/jstree/dist/jstree.js') }}"></script>

    @section('scripts')@show

    @section('init-script')
        <script>
            (function() {
                var app = codex.Application.instance;
                if ( ! app.isInitialised ) {
                    app.init({
                        assetPath: '/vendor/codex'
                    });
                }
            }.call())

        </script>
    @show

    @section('init-scripts')@show


    @section('boot-script')
        <script>
            (function() {
                var app = codex.Application.instance;
                app.boot().then(function (app) {
                    app.debug.log('BOOTED FROM boot-script');
                });
            }.call())
        </script>
    @show

    @stack('boot-scripts')

    </body>
</html>
