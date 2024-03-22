<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-starter">

<head>
    <title>@yield('title')</title>
    @include('layouts.includes.head')
</head>

<body data-sidebar="dark">

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            @include('layouts.includes.menu')

            <div class="layout-page">

                @include('layouts.includes.navbar')

                <div class="content-wrapper p-4">
                    @yield('content')
                </div>

                @include('layouts.includes.footer')
            </div>

        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    @include('layouts.includes.vendor-scripts')
    @yield('scripts')


    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>
