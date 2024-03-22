<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-starter">
<title>@yield('title')</title>
@include('layouts.includes.head')
</head>

<body>

    <body>

        @yield('content')

        <!-- JAVASCRIPT -->
        @include('auth.layouts.includes.vendor-scripts')
        @yield('scripts')

    </body>

</html>
