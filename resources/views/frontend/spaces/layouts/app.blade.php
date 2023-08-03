<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
@include('frontend.spaces.includes.head')
<body>
    @include('frontend.spaces.includes.nav')
     <main>
        <div class="preloader bg-dark flex-column justify-content-center align-items-center">
            <div class="position-relative">
                <img src="{{asset('frontend/img/logo.png')}}" class="rotate-letter" alt="Letter loader">
            </div>
        </div>
        @yield('content')
    </main>
    @include('frontend.spaces.includes.footer')
</body>

</html>