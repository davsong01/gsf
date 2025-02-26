<?php 
   $setting = activeConferenceEdition();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">

  <meta property="og:url" content="{{ env('APP_URL') }}">
  <meta property="og:title" content="{{ env('APP_NAME') }}"> 
  <meta property="og:image" content="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}"/> 
  <meta property="og:description" content="{{ $setting->conference_theme . ' Conference '.date('Y') }}"/>
  <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>{{ env('APP_NAME') }}</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

  <!-- Favicon -->
  <link href="{{ asset($setting->conference_favicon ?? 'frontend/img/favicon.png') }}" rel="icon">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($setting->conference_favicon ?? 'frontend/img/favicon.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($setting->conference_favicon ?? 'frontend/img/favicon.png') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting->conference_favicon ?? 'frontend/img/favicon.png') }}">
  <link rel="manifest" href="{{ asset('conference_templates/template1/assets/img/favicons/manifest.json')}}">
  <meta name="msapplication-TileImage" content="{{ asset($setting->conference_logo ?? 'frontend/img/logo.png') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- CSS ================================================== -->
  <!-- Bootstrap -->
  <link rel="stylesheet" href="{{ asset('conference_templates/template2/css/bootstrap.min.css') }}">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="{{ asset('conference_templates/template2/css/font-awesome.min.css') }}">
  <!-- Animation -->
  <link rel="stylesheet" href="{{ asset('conference_templates/template2/css/animate.css') }}">
  <!-- magnific -->
  <link rel="stylesheet" href="{{ asset('conference_templates/template2/css/magnific-popup.css') }}">
  <!-- carousel -->
  <link rel="stylesheet" href="{{ asset('conference_templates/template2/css/owl.carousel.min.css') }}">
  <!-- isotop -->
  <link rel="stylesheet" href="{{ asset('conference_templates/template2/css/isotop.css') }}">
  <!-- ico fonts -->
  <link rel="stylesheet" href="{{ asset('conference_templates/template2/css/xsIcon.css') }}">
  <!-- Template styles-->
  <link rel="stylesheet" href="{{ asset('conference_templates/template2/css/style.css') }}">
  <!-- Responsive styles-->
  <link rel="stylesheet" href="{{ asset('conference_templates/template2/css/responsive.css') }}">

  <style>
      .image-container {
         width: 100%;
         max-width: 100%;
         height: auto;
         overflow: hidden;
      }

      .image-container img {
         display: block;
         width: 100%;
         height: auto;
      }
  </style>
  @yield('css')
</head>
<body>
   <div class="body-inner">
      <!-- Header start -->
      <header id="header" class="header header-transparent">
         <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
               <!-- logo-->
               <a class="navbar-brand" href="/">
                  <img style="width: 85px;" src="{{ asset($setting->conference_logo) }}" alt="">
               </a>
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                  aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"><i class="icon icon-menu"></i></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarNavDropdown">
                  <ul class="navbar-nav ml-auto">
                     <li class="nav-item active">
                        <a href="{{ url('/').'#details' }}" class="">Details</a>
                     </li>
                     <li class="nav-item">
                        <a href="{{ url('/').'#ts-speakers' }}" class="">Speakers</a>
                     </li>
                     <li class="nav-item">
                        <a href="{{ url('/').'#faq' }}" class="">FAQ</a>
                     </li>
                     <li class="nav-item">
                        <a href="{{ url('/').'#donate' }}" class="">Donate</a>
                     </li>
                     @auth
                     <li class="nav-item">
                        <a target="_blank" href="{{  route('account') }}" class="">My Account</a>
                     </li>
                     <li class="nav-item">
                        <a href="{{  route('logout') }}" class="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                     </li>
                     <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                     </form>  
                     @endauth
                     @guest
                     <li class="nav-item">
                        <a target="_blank" href="{{ route('login') }}" class="">Login</a>
                     </li>
                     @endguest
                     <li class="header-ticket nav-item">
                        <a class="ticket-btn btn" href="#register"> 
                        </a>
                     </li>
                  </ul>
               </div>
            </nav>
         </div>
      </header>
      <!--/ Header end -->
   @yield('content')
      <!-- ts footer area start-->
      <div class="footer-area">
         <!-- ts-book-seat start-->
         <section id="donate" class="ts-book-seat" style="background-color: black">
            <div class="container">
               <div class="row">
                  <div class="col-lg-8 mx-auto">
                     <div class="book-seat-content text-center mb-100">
                        <h2 class="section-title white">
                           <span></span>
                           Special Support
                        </h2>
                        <a href="{{route('conference.registration',5) }}" class="btn">Donate</a>
                     </div><!-- book seat end-->
                  </div><!-- col end-->

               </div><!-- row end-->
            </div><!-- container end-->
         </section>
         <!-- book seat  end-->

         <!-- footer start-->
         <footer class="ts-footer">
            <div class="container">
               <div class="row">
                  <div class="col-lg-8 mx-auto">
                     <div class="ts-footer-social text-center mb-30">
                        <ul>
                           @if(!is_null($setting->facebook_event_page))
                           <li>
                              <a target="_blank" href="{{ $setting->facebook_event_page }}"><i class="fa fa-facebook"></i></a>
                           </li>
                           @endif
                           @if(!is_null($setting->instagram))
                           <li>
                              <a target="_blank" href="{{ $setting->instagram }}"><i class="fa fa-instagram"></i></a>
                           </li>
                           @endif
                        </ul>
                     </div>
                     <!-- footer social end-->
                     <div class="footer-menu text-center mb-25">
                     </div><!-- footer menu end-->
                     <div class="copyright-text text-center">
                        <p style="text-align:center">If you are having challenges registering, whatsapp: <b style="color:white"> {{ $setting->official_phone }}</b> for assistance/guidance</p>

                        <p>Copyright © {{date('Y')}} <a class="text-900" href="{{ config('app.url') }}" target="_blank">{{ config('app.name') }}</a>. All Rights Reserved.</p>
                     </div>
                  </div>
               </div>
            </div>
         </footer>
         <!-- footer end-->
         <div class="BackTo">
            <a href="#" class="fa fa-angle-up" aria-hidden="true"></a>
         </div>

      </div>
      <!-- ts footer area end-->




      <!-- Javascript Files
            ================================================== -->
      <!-- initialize jQuery Library -->
      <script data-cfasync="false" src="https://demo.themewinter.com/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="{{ asset('conference_templates/template2/js/jquery.js') }}"></script>

      <script src="{{ asset('conference_templates/template2/js/popper.min.js')}}"></script>
      <!-- Bootstrap jQuery -->
      <script src="{{ asset('conference_templates/template2/js/bootstrap.min.js')}}"></script>
      <!-- Counter -->
      <script src="{{ asset('conference_templates/template2/js/jquery.appear.min.js')}}"></script>
      <!-- Countdown -->
      <script src="{{ asset('conference_templates/template2/js/jquery.jCounter.js')}}"></script>
      <!-- magnific-popup -->
      <script src="{{ asset('conference_templates/template2/js/jquery.magnific-popup.min.js')}}"></script>
      <!-- carousel -->
      <script src="{{ asset('conference_templates/template2/js/owl.carousel.min.js')}}"></script>
      <!-- Waypoints -->
      <script src="{{ asset('conference_templates/template2/js/wow.min.js')}}"></script>
    
      <!-- isotop -->
      <script src="{{ asset('conference_templates/template2/js/isotope.pkgd.min.js')}}"></script>

      <!-- Template custom -->
      <script src="{{ asset('conference_templates/template2/js/main.js')}}"></script>
         
      <script>
         $(document).ready(function() {
            // var startDate = new Date("2025-04-05T00:00:00");
            var startDate = new Date("{{ \Carbon\Carbon::parse($setting->start_date)->toIso8601String() }}");

            function updateTimer() {
               var now = new Date();
               var timeDiff = startDate - now;

               if (timeDiff < 0) {
                  timeDiff = 0;
               }

               var days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
               var hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
               var minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
               var seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

               $(".days").text(days < 10 ? "0" + days : days);
               $(".hours").text(hours < 10 ? "0" + hours : hours);
               $(".minutes").text(minutes < 10 ? "0" + minutes : minutes);
               $(".seconds").text(seconds < 10 ? "0" + seconds : seconds);
            }

            setInterval(updateTimer, 1000);
            updateTimer(); // Initial call to set the timer immediately
         });
      </script>
      @yield('script')
   </div>
   <!-- Body inner end -->
</body>


</html>