<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">


<!-- BEGIN: Head-->
@include('includes.head')
<!-- END: Head-->

<!-- BEGIN: Body-->
<body class="vertical-layout vertical-menu-modern 2-columns  navbar-sticky footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Header-->
    @include('includes.header')
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    @include('includes.main_menu')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-12 mb-2 mt-1">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <p class="float-left pr-1 mb-0">@yield('previous')</p>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb p-0 mb-0">
                                <li class="breadcrumb-item"><a href="/account"><i class="bx bx-home-alt"></i></a>
                                </li>
                                @yield('item')
                                @yield('active')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @yield('content')
    </div>
</div>
    <!-- END: Content-->

   
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('includes.footer')

</body>
<!-- END: Body-->

</html>