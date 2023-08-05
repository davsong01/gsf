<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>GSF - Campus Tracker</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="GSF - Campus Tracker"> 
        <meta property="og:image" content="{{ asset('frontend/img/logo.png') }}"/> 

        <meta property="og:description" content="Do you need periodical, urgent and handy information on admission processes in the institution of your choice. Visit GSF campus tracker now"/>
        <meta property="og:site_name" content="GSF" />

        <!-- Favicon -->
        <link href="{{ asset('frontend/img/logo.png') }}" rel="icon">

        <!-- Google Font -->
        <link href="{{ asset('frontend/css/opensans.css') }}" rel="stylesheet">

        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <link href="{{ asset('frontend/lib/animate/animate.min.css') }}" rel="stylesheet">
        <link href="{{ asset('frontend/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
        
        {{-- Select2 --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    </head>
    <!-- Nav Bar Start -->
       <div class="navbar navbar-expand-lg bg-dark navbar-dark" >
     
        <div class="container-fluid">
            <a href="/" class="navbar-brand"> <img src="{{ asset('frontend/img/logo.png') }}" alt="logo"></a><b>GSF CAMPUS UPDATE</b>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ml-auto">
                    <a href="https://www.gsfnational.org/" class="nav-item nav-link active">GSF HOME</a>
                                       
                </div>
            </div>
        </div>
    </div>
    <!-- Nav Bar End -->
    <body>
    <!-- About End -->
    <div class="contact" id="register">
    </div>
    <!-- Contact Start -->
    <div class="contact">
        <div class="container">
            <div class="section-header">
                <h2>GSF CAMPUS TRACKER</h2>
            </div>
            <div class="row align-items-center">
                <div class="col-md-12">
                    <br>
                    <p style="text-align:center">Select GSF campus and click view details</p>
                    @include('includes.falerts')
                </div>
                <div class="col-md-12 col-sm-12" id="individualregbutton">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <hr>
                                <div class="contact-form">
                                   
                                    <form action="{{ route('campus.view') }}" method="GET">
                                        
                                        <div class="control-group">
                                            <label for="chapter">GSF Campus</label><br>
                                            <select name="chapter" class="form-control select2 chapter" required>
                                                <option value="">--Select Campus</option>
                                                @foreach($chapters as $chapter)
                                                <option value="{{ $chapter->id }}" {{ old('chapter') == $chapter->id ? 'selected' : ''}}>
                                                    {{ $chapter->name }}</option>
                                                @endforeach
                                               
                                            </select>
                                        </div>
                                       
                                        <div class="control-group">
                                            <button class="btn submitregistration" type="submit" style="width:100%">View details</button>
                                        </div>
                                    </form>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>        

            </div>
        </div>
    </div>


            

        @include('includes.ffooter')

        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

				<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- JavaScript Libraries -->

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('frontend/lib/easing/easing.min.js') }}"></script>
        <script src="{{ asset('frontend/lib/owlcarousel/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('frontend/lib/waypoints/waypoints.min.js') }}"></script>
        <script src="{{ asset('frontend/lib/counterup/counterup.min.js') }}"></script>
        
        <!-- Contact Javascript File -->
        <script src="{{ asset('frontend/mail/jqBootstrapValidation.min.js') }}"></script>
        <script src="{{ asset('frontend/mail/contact.js') }}"></script>

        <!-- Template Javascript -->
        <script src="{{ asset('frontend/js/main.js') }}"></script>

        <script>
            $(document).ready(function() {
                $('.chapter').select2();
            });
        
            $(document).ready(function() {
                $('.chapterind').select2();
            }); 
            
                        
        </script>
    
    </body>
</html>
