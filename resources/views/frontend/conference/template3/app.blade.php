<?php 
    $setting = activeConferenceEdition();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ env('APP_NAME') }}"> 
    <meta property="og:image" content="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}"/> 
    <meta property="og:description" content="{{ $setting->conference_theme }}"/>
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>{{ env('APP_NAME') }}</title>

    <link href="{{ asset($setting->conference_favicon ?? 'frontend/img/favicon.png') }}" rel="icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($setting->conference_favicon ?? 'frontend/img/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($setting->conference_favicon ?? 'frontend/img/favicon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting->conference_favicon ?? 'frontend/img/favicon.png') }}">
    
    <!-- CSS Files
    ================================================== -->
    <link href="{{asset('conference_templates/template3/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" id="bootstrap">
    <link href="{{asset('conference_templates/template3/css/vendors.css')}}" rel="stylesheet" type="text/css" >
    <link href="{{asset('conference_templates/template3/css/style.css')}}" rel="stylesheet" type="text/css" >
    <!-- color scheme -->
    <link id="colors" href="{{asset('conference_templates/template3/css/colors/scheme-01.css')}}" rel="stylesheet" type="text/css" >

</head>

<body class="dark-scheme">
    <div id="wrapper">
        {{-- <div class="float-text show-on-scroll">
            <span><a href="#">Scroll to top</a></span>
        </div>
        <div class="scrollbar-v show-on-scroll"></div> --}}

        <!-- page preloader begin -->
        <div id="de-loader"></div>
        <!-- page preloader close -->

        <header class="transparent">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="de-flex">
                            <div class="de-flex-col">
                                <!-- logo begin -->
                                <div id="logo">
                                    <a href="/">
                                        <img class="logo-main" src="{{ asset($setting->conference_logo) }}" alt="" >
                                        <img class="logo-scroll" src="{{ asset($setting->conference_logo) }}" alt="" >
                                        <img class="logo-mobile" src="{{ asset($setting->conference_logo) }}" alt="" >
                                    </a>
                                </div>
                                <!-- logo close -->
                            </div>

                            <div class="de-flex-col">
                                <div class="de-flex-col header-col-mid">
                                    <ul id="mainmenu">
                                        <li><a class="menu-item" href="{{ url('/').'#section-about' }}">Details</a></li>
                                        @if($setting->speaker_section_status)
                                        <li><a class="menu-item" href="{{ url('/').'#section-speakers' }}">Speakers</a></li>
                                        @endif
                                        @if(!empty(conferenceSchedule()))
                                        <li><a class="menu-item" href="{{ url('/').'#section-schedule' }}">Schedule</a></li>
                                        @endif
                                        <li><a class="menu-item" href="{{ url('/').'#section-venue' }}">Venue</a></li>
                                        @if($setting->faq_section_status)
                                        <li><a class="menu-item" href="{{ url('/').'#section-faq' }}">FAQ</a></li>
                                        @endif
                                        {{-- <li><a class="menu-item" href="news.html">Donate</a></li> --}}
                                        @auth
                                        <li class="menu-item">
                                            <a target="_blank" href="{{  route('account') }}" class="">My Account</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="{{  route('logout') }}" class="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                        </li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>  
                                        @endauth
                                        @guest
                                        <li class="menu-item">
                                            <a target="_blank" href="{{ route('login') }}" class="">Login</a>
                                        </li>
                                        @endguest
                                    </ul>
                                </div>
                            </div>

                            <div class="de-flex-col">
                                <a class="btn-main fx-slide w-100" href="{{ url('/').'#section-tickets' }}"><span>Save your seat</span></a>

                                <div class="menu_side_area">
                                    <span id="menu-btn"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @yield('content')
    </div>

    <!-- footer begin -->
    <footer class="text-light section-dark">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-12">
                    <div class="d-lg-flex align-items-center justify-content-between text-center">
                        <div>
                        </div>
                        <div>
                            <img src="{{ asset($setting->conference_logo) }}" class="w-70px" alt=""><br>
                            <div class="social-icons mb-sm-30 mt-4">
                                <div class="social-icons mb-sm-30 mt-4">
                                    @if(!empty($setting->facebook))
                                        <a href="{{ $setting->facebook }}" target="_blank" rel="noopener noreferrer">
                                            <i class="fa-brands fa-facebook-f"></i>
                                        </a>
                                    @endif

                                    @if(!empty($setting->facebook_event_page))
                                        <a href="{{ $setting->facebook_event_page }}" target="_blank" rel="noopener noreferrer">
                                            <i class="fa-brands fa-facebook"></i>
                                        </a>
                                    @endif

                                    @if(!empty($setting->instagram))
                                        <a href="{{ $setting->instagram }}" target="_blank" rel="noopener noreferrer">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                    @endif

                                    @if(!empty($setting->telegram))
                                        <a href="{{ $setting->telegram }}" target="_blank" rel="noopener noreferrer">
                                            <i class="fa-brands fa-telegram"></i>
                                        </a>
                                    @endif
                                </div>

                            </div>

                        </div>
                        <div>
                        
                        </div>
                    </div>
                </div>                    
            </div>
        </div>
        <div class="subfooter">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        Copyright {{date('Y')}} - {{env('APP_NAME')}}
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer end -->
    
    <!-- Javascript Files
    <-- ================================================== -->
    <script src="{{ asset('conference_templates/template3/js/vendors.js')}}"></script>
    <script src="{{ asset('conference_templates/template3/js/designesia.js')}}"></script>
    @yield('script')
</body>
</html>