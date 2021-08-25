<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>GSF Campus Tracker</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="GSF Campus Tracker"> 
          <meta property="og:image" content="{{ asset('frontend/img/logo.png') }}"/> 

        <meta property="og:description" content="GSF Campus Tracker"/>
        <meta property="og:site_name" content="GSF" />

        <!-- Favicon -->
        <link href="{{ asset('frontend/img/logo.png') }}" rel="icon">

        <!-- Google Font -->
        <!-- <link href="https://fonts.googleapis.com/css2?family=Lato&family=Oswald:wght@200;300;400&display=swap" rel="stylesheet"> -->
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
                <h2>{{ $chapter->name }}</h2>
            </div>
            <div class="row align-items-center">
            
                <div class="col-md-12">
                   
                    <br>
                    <p style="text-align:center">Details not correct? Kindly contact the National Publicity Secretary for guidance on how to update your fellowship details</p>
                    @include('includes.falerts')
                </div>
                
                <div class="col-md-12 col-sm-12">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <hr>
                                <div class="contact-form">
                                   
                                    <form action="{{ route('campus.save', $chapter->id) }}" method="POST">
                                        @csrf
                                            
                                            <div class="control-group">
                                                <label>Fellowship Email (Optional)</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{  old('email') ?? $chapter->email }}">
                                            </div>
                                            <div class="control-group">
                                                <label>Fellowship Phone Numbers</label>
                                                <input type="text" class="form-control" id="phone" name="phone"
                                                value="{{ old('phone') ?? $chapter->phone }}" required>
                                            </div>
                                            <div class="control-group">
                                                <label>Fellowship Address</label>
                                                <input type="text" class="form-control" id="address" name="address"
                                                value="{{ old('address') ?? $chapter->address }}" required>
                                            </div>
                                            <div class="control-group">
                                                <label>Fellowship Facebook link (Optional)<small style="color:green"> e.g: https://www.facebook.com/gsfnational/</small></label>
                                                <input type="url" class="form-control" id="facebook" name="facebook"
                                                value="{{ old('facebook') ?? $chapter->facebook }}" >
                                            </div>
                                            <div class="control-group">
                                                <label>Fellowship Twitter link (Optional)<small style="color:green"> e.g: https://twitter.com/GSF_National</small></label>
                                                <input type="url" class="form-control" id="twitter" name="twitter"
                                                value="{{ old('twitter') ?? $chapter->twitter }}" >
                                            </div>
                                            <div class="control-group">
                                                <label style="color:red">Token (Official use)</label>
                                                <input type="password" id="password" onkeyup="showUpdate()" class="form-control" id="token" name="token"
                                                value="{{ old('token') }}" required>
                                            </div>
                                            <input type="hidden" name="chapter" value={{ $chapter->id }}>
                                            <br>
                                            
                                        <div class="control-group">
                                            <button class="btn submitregistration" id ="submitbutton" type="submit" style="width:100%; display:none">Update details (Official Use)</button>
                                        </div>
                                        <script>
                                            function showUpdate(){
                                                var x = document.getElementById("submitbutton");
                                                var y = document.getElementById("password").value;
                                                
                                                if(y != ''){
                                                    x.value = x.style.display = 'block';
                                                }else{
                                                    x.value = x.style.display = 'none';
                                                }
                                                
                                            }
                                        </script>
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
