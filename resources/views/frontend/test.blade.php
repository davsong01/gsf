<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>WAACSP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:url" content="{{ config('app.url').'/induction' }}">
        <meta property="og:title" content="WAACSP - Induction 2021"> 
          <meta property="og:image" content="{{ config('app.url').'/induction-files/real/img/logo-big.jpg' }}"/> 

        <meta property="og:description" content="WAACSP - Induction 2021"/>
        <meta property="og:site_name" content="WAACSP" />

        <!-- Favicon -->
        <link href="{{ asset('induction-files/real/img/logo.png') }}" rel="icon">

        <!-- Google Font -->
        <!-- <link href="https://fonts.googleapis.com/css2?family=Lato&family=Oswald:wght@200;300;400&display=swap" rel="stylesheet"> -->
         <link href="{{ asset('induction-files/real/css/opensans.css') }}" rel="stylesheet">

        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <link href="{{ asset('induction-files/real/lib/animate/animate.min.css') }}" rel="stylesheet">
        <link href="{{ asset('induction-files/real/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="{{ asset('induction-files/real/css/style.css') }}" rel="stylesheet">
    </head>

    <body>

        <!-- Nav Bar Start -->
        <div class="navbar navbar-expand-lg bg-dark navbar-dark">
            <div class="container-fluid">
               <a href="/english" class="navbar-brand"> <img src="{{ asset('induction-files/real/img/logo.png') }}" alt="logo"></a><b>WAACSP INDUCTION 2021</b>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav ml-auto">
                        <a href="/english" class="nav-item nav-link active">WAACSP HOME</a>
                        <a href="#history" class="nav-item nav-link">History</a>
                        <a href="#apply" class="nav-item nav-link">APPLY NOW</a>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- Nav Bar End -->


        <!-- Carousel Start -->
        <div class="carousel">
            <div class="container-fluid">
                <div class="owl-carousel">
                    @foreach($sliders as $slider)
                     <div class="carousel-item">
                        <div class="carousel-img">
                            <img src="{{ asset('induction-files/real/img/sliders/'.$slider->title) }}" alt="slider">
                        </div>
                        <div class="carousel-text">
                            <div class="banner_text">
                                <h2 style="color:white">WAACSP INDUCTION</h2>
                                <P> <b>2021 First Quarter</b></P>
                                <p>
                                    <a class="btn" href="#apply"><i class="fa fa-link"></i>APPLY NOW</a>
                                </p>
                                {{-- @if($days_left > 0)
                                    <h2>{{ $days_left }} Days</h2>
                                    
                                @else
                                 <h2>HAPPENING TODAY!</h2>
                                @endif --}}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Carousel End -->
        <div class="contact" id="history">
       
        <!-- About Start -->
        <div class="about">
            <div class="container">
                <div class="row align-items-center">
                     <div class="col-md-12">
                        <div class="section-header">
                            {{-- <p>WAACSP History</p> --}}
                            <h2>WAACSP History</h2>
                        </div>
                        <div class="about-text">
                            <div >
                                <img class="center" src="{{ asset('induction-files/real/img/yvonne.jpeg') }}" alt="Image" style=" border-radius: 50%; display: block; margin-left: auto; margin-right: auto; width: 20%;">
                            </div> 
                            <p>
                            Arising from the continuous growth in key service industries of financial services and telecommunications sectors of the West Africa economies, in 2014, key customer service professionals from the region with background in service delivery in banking and telecommunications started a
                            network of like minds educating and imparting customer service skills and training in this spheres.<br> <br> 
                                
                            This network combined education and best practices translating to grooming of service officers and operating systems for organizations. While Nigeria
                                and Ghana network of professionals pioneered this frontier, the network also attracted customer service practitioners from Cote D&rsquo;Ivore, The Gambia and Senegal.
                            </p>
                           
                            <a class="btn submit" href="https://theWAACSP.com/english/history" style="width:100%" target="_blank">Read more</a>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>

        <!-- About End -->
        <div class="contact" id="apply">
        </div>
        <!-- Contact Start -->
        <div class="contact">
            <div class="container">
                <div class="section-header">
                    <h2>Apply</h2>
                </div>
                <div class="row align-items-center">
                   
                     <div class="col-md-12">
                        <p style="text-align:center">Are you a certifed WAACSP member and part of the 2019/20 cohort?<br>Apply for the induction by filling the form below</p>
                        @include('layouts.falerts')
                    </div>
                    <div class="col-md-6">
                        <img src="{{ asset('induction-files/real/img/brochure.jpeg') }}" width = "100%"; alt="brochure">
                    </div>
                    <div class="col-md-6">
                        <div class="contact-form">
                            <div id="success"></div>
                            <form action="{{ route('inductionapply.store') }}" method="POST">
                                @csrf
                                <div class="control-group">
                                    <label>Membership ID</label>
                                    <input type="text" class="form-control" id="membership_id" name="membership_id" placeholder="Enter Your WAACSP membership ID" required="required" data-validation-required-message="Please enter your WAACSP membership ID" />
                                    <p class="help-block text-success" style="font-size: 14px;">No WAACSP membership ID? Click <a target="_blank" href="https://theWAACSP.com/english/get-started"> <b>HERE</b></a> to get yours!</p>
                                    @if($errors->has('membership_id'))
                                        <span class="help-block">
                                            <i style="color:red">{{ $errors->first('membership_id') }}</i>
                                        </span>
                                    @endif
                                </div>

                                <div class="control-group">
                                    <label>Location of training</label>
                                    <select name="location" id="location" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="Online" {{ old('location') == 'Online' ? 'selected' : '' }}>Online</option>
                                        <option value="Ikeja" {{ old('location') == 'Ikeja' ? 'selected' : '' }}>Ikeja</option>
                                        <option value="Lekki" {{ old('location') == 'Lekki' ? 'selected' : '' }}>Lekki</option>
                                        <option value="Abuja" {{ old('location') == 'Abuja' ? 'selected' : '' }}>Abuja</option>
                                        <option value="PHC" {{ old('location') == 'PHC' ? 'selected' : '' }}>PHC</option>
                                        <option value="Accra" {{ old('location') == 'Accra' ? 'selected' : '' }}>Accra</option>
                                        <option value="Banjul" {{ old('location') == 'Banjul' ? 'selected' : '' }}>Banjul</option>
                                        <option value="Freetown" {{ old('location') == 'Freetown' ? 'selected' : '' }}>Freetown</option>
                                        <option value="Monrovia " {{ old('location') == 'Monrovia ' ? 'selected' : '' }}>Monrovia </option>
                                    </select>
                                   @if($errors->has('location'))
                                        <span class="help-block">
                                            <i style="color:red">{{ $errors->first('location') }}</i>
                                        </span>
                                    @endif
                                </div>
                                <div class="control-group">
                                    <label>Diet</label>
                                    <select name="diet" id="diet" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="2019 1st diet [May-June]" {{ old('diet') == '2019 1st diet [May-June]' ? 'selected' : '' }}>2019 1st diet [May - June]</option>
                                        <option value="2019 -2nd diet [Oct - Nov]" {{ old('diet') == '2019 -2nd diet [Oct - Nov]' ? 'selected' : '' }}>2019 -2nd diet [Oct - Nov]</option>
                                        <option value="2020 - 1st Diet [June-July]" {{ old('diet') == '2020 - 1st Diet [June-July]' ? 'selected' : '' }}>2020 - 1st Diet [June - July]</option>
                                        <option value="2020- 2nd diet [Oct-Nov]" {{ old('diet') == '2020- 2nd diet [Oct-Nov]' ? 'selected' : '' }}>2020 - 2nd diet [Oct - Nov]</option>
                                    </select>
                                    @if($errors->has('diet'))
                                        <span class="help-block">
                                            <i style="color:red">{{ $errors->first('diet') }}</i>
                                        </span>
                                    @endif
                                </div>
                                <br>
                                <div class="control-group">
                                    <button class="btn" type="submit" style="width:100%">Submit Application</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="contact">
        </div>
        <div class="about">
            <div class="container">
                <div class="row align-items-center">
                     <div class="col-md-12">
                        <div class="section-header">
                            {{-- <p>WAACSP History</p> --}}
                            <h2>Professional Membership Application</h2>
                        </div>
                        <div class="about-text">
                            <div >
                                <img class="center" src="{{ asset('induction-files/real/img/segment2.jpeg') }}" alt="Image" style=" border-radius: 10%; display: block; margin-left: auto; margin-right: auto; width: 30%;"> <br>

                            </div> 
                            <p style="text-align:center">
                                Are you a practicing customer service professional but not yet  a registered member of WAACSP? <br>

                                Join waacsp in Professional or Fellows category and be part of 2021 induction.
                                
                            </p>
                           
                            <a class="btn submit" href="https://theWAACSP.com/english/get-started" target="_blank" style="width:100%">Click Here</a>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>

           <div class="contact">
        </div>
        <div class="about">
            <div class="container">
                <div class="row align-items-center">
                     <div class="col-md-12">
                        <div class="section-header">
                            {{-- <p>WAACSP History</p> --}}
                            <h2>Join the WAACSP 2021-2023 Education Network</h2>
                        </div>
                        <div class="about-text">
                            <div >
                                <img class="center" src="{{ asset('induction-files/real/img/network.jpeg') }}" alt="Image" style=" border-radius: 10%; display: block; margin-left: auto; margin-right: auto; width: 40%;"> <br>

                            </div> 
                            <p style="text-align:center">Training organizations can join as WAACSP affiliate training partners. Become a WAACSP Certified CS trainer for the 2021 - 23 curriculum
                            </p>
                           
                            <a class="btn submit" href="{{ route('network') }}" target="_blank" style="width:100%">Read more on how to join</a>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>

        <!-- Footer Start -->
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="footer-contact">
                                    <p style="text-align:center"><i class="fa fa-phone-alt"></i>Ghana:- +233 57 076 0360 | Nigeria :- +234 702 500 3527<br>
                                         <i class="fa fa-envelope"></i>waacsp@gmail.com, info@thewaacsp.com</p>
                                    <div style="text-align:center">
                                        <a href="https://www.linkedin.com/in/WAACSP-2936011b4" style="color:white"><i class="fab fa-linkedin-in"></i></a>
                                        {{-- <a href="https://twitter.com/WAACSP" style="color:white"><i class="fab fa-twitter"></i></a> --}}
                                        <a href="https://web.facebook.com/WAACSP" style="color:white"><i class="fab fa-facebook-f"></i></a>
                                        <a href="https://www.instagram.com/WAACSP_english" style="color:white"><i class="fab fa-instagram"></i></a>
                                        {{-- <a href="https://wa.me/2347025003527" style="color:white"><i class="fab fa-whatsapp"></i></a> --}}
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('induction-files/real/lib/easing/easing.min.js') }}"></script>
        <script src="{{ asset('induction-files/real/lib/owlcarousel/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('induction-files/real/lib/waypoints/waypoints.min.js') }}"></script>
        <script src="{{ asset('induction-files/real/lib/counterup/counterup.min.js') }}"></script>
        
        <!-- Contact Javascript File -->
        <script src="{{ asset('induction-files/real/mail/jqBootstrapValidation.min.js') }}"></script>
        <script src="{{ asset('induction-files/real/mail/contact.js') }}"></script>

        <!-- Template Javascript -->
        <script src="{{ asset('induction-files/real/js/main.js') }}"></script>
    
    </body>
</html>
