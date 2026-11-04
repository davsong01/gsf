<!DOCTYPE html>
<html lang="en">
   @include('includes.main.head')
   
    <body>
    <!-- Preloader -->
        <div class="loader">
            <img src="{{ asset('main/images/spin.gif') }}" alt="">
        </div>
        <!-- Static navbar -->
        @include('includes.main.nav')
        @include('includes.toastr')
        @yield('content')
        @include('includes.main.footer')
        @include('frontend.site.partials.legacy-legal-links')
        @include('frontend.site.partials.privacy-notice')
    </body>
</html>
