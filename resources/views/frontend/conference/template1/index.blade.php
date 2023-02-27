<!DOCTYPE html>
<html lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Gofamint Students' Fellowship - {{ $setting->conference_theme }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="GSF | @yield('ogtitle', "Gofamint Students' Fellowship")"> 
    <meta property="og:image" content="{{ asset('frontend/img/logo.png') }}"/> 

    <meta property="og:description" content="GSF - {{ $setting->conference_theme }}"/>
    <meta property="og:site_name" content="{{ config('aap_name') }}" />

    <!-- Favicon -->
    <link href="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}" rel="icon">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}">
    <link rel="manifest" href="{{ asset('conference_templates/template1/assets/img/favicons/manifest.json')}}">
    <meta name="msapplication-TileImage" content="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta name="theme-color" content="#ffffff">
    <style>
      .small-container{
        width: 50% !important;
        margin-top: 50px !important;
      }
      .socials {
        width: 30px;
      }
    </style>
    @yield('css')

    <link href="{{ asset('conference_templates/template1/assets/css/theme.css') }}" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- <script src="{{ asset('conference_templates/template1/assets/js/custom.js')}}"></script> --}}
    
  </head>

  <body>
    <main class="main" id="top">
      <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" data-navbar-on-scroll="data-navbar-on-scroll">
        <div class="container"><a class="navbar-brand" href="#"><img src="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}" alt="" width="90">
        </a>
         
          <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto border-bottom border-lg-bottom-0 pt-2 pt-lg-0 mb-2">
              {{-- <li class="nav-item"><a class="nav-link active active" aria-current="page" href="#">About</a></li> --}}
              <li class="nav-item"><a class="nav-link" href="{{ url('/').'#details' }}">Details </a></li>
              <li class="nav-item"><a class="nav-link" href="{{ url('/').'#expectation' }}">Expectation</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ url('/').'#testimonies' }}">Testimonies</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ url('/').'#donate' }}">Donate</a></li>
              <li class="nav-item"><a class="nav-link" href="#register">Register </a></li>
            </ul>                   
            @auth
                <a href="/account"  class="btn btn-outline-danger rounded-pill order-0" >My Account</a>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <button class="btn btn-link text-1000 fw-medium order-1 order-lg-0" type="button">Log out</button>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>  
                @endauth
                @guest
                <a href="{{ '/login' }}" class="btn btn-outline-danger rounded-pill order-0" type="submit">Login</a>
                @endguest
          </div>
        </div>
      </nav>
      
        @yield('sec-content')
    </main>
    @include('frontend.conference.template1.footer')
    <script src="{{ asset('conference_templates/template1/vendors/@popperjs/popper.min.js')}}"></script>
    <script src="{{ asset('conference_templates/template1/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{ asset('conference_templates/template1/vendors/is/is.min.js')}}"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>

    <script src="{{ asset('conference_templates/template1/assets/js/theme.js')}}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">

    @yield('script')
  </body>

</html>