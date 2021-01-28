@extends('layouts.index')
@section('sliders')
@include('includes.sliders')
@endsection
@section('body')
    <!-- About Start -->
    <div class="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="section-header">
                        <h2>{{ $setting->conference_theme }}</h2>
                    </div>
                    <div class="about-text">
                        <div >
                            <img class="" src="{{ asset('frontend/img/banner.jpg') }}" alt="Image" style=" border-radius: 5%; display: block; margin-left: auto; margin-right: auto; width: 60%;"><br>
                        </div> 
                        <p>
                            {!! $setting->conference_overview!!}
                        </p>
                    
                        {{-- <a class="btn submit" href="https://theGSF.com/english/history" style="width:100%" target="_blank">Read more</a> --}}
                    </div>
                </div>
            
            </div>
        </div>
    </div>
    <!-- About End -->

     <div class="contact" id="register">
    </div>
    <!-- Contact Start -->
    <div class="contact">
        <div class="container">
            <div class="section-header">
                <h2>Register</h2>
            </div>
            <div class="row align-items-center">
            
                <div class="col-md-12">
                    <img class="center" src="{{ asset('frontend/img/register.jpg') }}" alt="Image" style=" border-radius: 10%; display: block; margin-left: auto; margin-right: auto; width: 20%;">
                    <br>
                    <p style="text-align:center">Toggle corresponding button below to register as an individual, register in bulk for your fellowship or register as an alumni. You will be required to make this payment online click proceed to payment</p>
                    @include('includes.falerts')
                </div>
                 @if($setting->close_registration >= date('Y-m-d'))
                <div class="col-md-4 col-sm-6" id="individualregbutton">
                    <a class="btn submit" onclick="myFunction()" id="individualregbutton" data-toggle="tooltip" data-placement="top"
                        title="Individual registration" style="width:100%">Individual</a>
                </div>
                <div class="col-md-4 col-sm-6" id="fellowshipregbutton">
                    <a class="btn submit" onclick="myFunction2()" id="fellowshipregbutton" data-toggle="tooltip" data-placement="top"
                        title="Register on behalf of your fellowship members" style="width:100%">Fellowship</a>
                </div>
                <div class="col-md-4 col-sm-6" id="alumniregbutton">
                    <a class="btn submit" onclick="myFunction3()" id="alumniregbutton" data-toggle="tooltip" data-placement="top"
                        title="Register as an alumni"  style="width:100%">Alumni</a>
                </div>               
                @else
                <div class="col-md-12 col-sm-12">
                <h2 style="text-align: center;">REGISTRATION HAS NOW CLOSED!!!</h2>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="about" id="individualreg" style="display:none;">
        @include('includes.individualform')
    </div>
    <div class="about" id="fellowshipreg" style="display:none;">
        @include('includes.fellowshipform')
    </div>
     <div class="about" id="alumnireg" style="display:none;">
        @include('includes.alumniform')
    </div>    

    <div class="contact">
    </div>
    <div class="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="section-header">
                        {{-- <p>GSF History</p> --}}
                        <h2>Sponsor the conference</h2>
                    </div>
                    <div class="about-text">
                        <div >
                            <img class="center" src="{{ asset('frontend/img/sponsor.jpg') }}" alt="Image" style=" border-radius: 10%; display: block; margin-left: auto; margin-right: auto; width: 60%;"> <br>

                        </div> 
                        <p style="text-align:center">Are you led to sponsor the conference, no amount is too small nor big. Please click button below to donate
                        </p>
                       
                        <a class="btn submit" onclick="myFunction4()" id="donationbutton" data-toggle="tooltip" data-placement="top"
                        title="Register as an alumni"  style="width:100%">Make Donation</a>
                       
                        <div class="about" id="donation" style="display:none;">
                            @include('includes.donationform')
                        </div>
                       
                    </div>
                </div>
            
            </div>
        </div>
    </div>
@endsection
@section('extra-scripts')
<script>
    var individualreg = document.getElementById("individualreg");
    var fellowshipreg = document.getElementById("fellowshipreg");
    var alunmireg = document.getElementById("alumnireg");
    var individualregbutton = document.getElementById("individualregbutton");
    var fellowshipregbutton = document.getElementById("fellowshipregbutton");
    var alumniregbutton = document.getElementById("alumniregbutton");
    var donation = document.getElementById("donation");
    
    function myFunction() {

    if (individualreg.style.display == "none") {
      individualreg.style.display = "block";
      fellowshipregbutton.style.display = "none";
      alumniregbutton.style.display = "none";
      donationbutton.style.display = "none";
    } 
    else {
      individualreg.style.display = "none";
      fellowshipregbutton.style.display = "block";
      alumniregbutton.style.display = "block";
      donationbutton.style.display = "block";
    }
  };

  function myFunction2() {

    if (fellowshipreg.style.display == "none") {
        fellowshipreg.style.display = "block";
        individualregbutton.style.display = "none";
        alumniregbutton.style.display = "none";
        donationbutton.style.display = "none";
    } 
    else {
        fellowshipregbutton.style.display = "block";
        fellowshipreg.style.display = "none";
        individualregbutton.style.display = "block";
        alumniregbutton.style.display = "block";
        donationbutton.style.display = "block";
    }
  }

   function myFunction3() {

    if (alumnireg.style.display == "none") {
        alumnireg.style.display = "block";
        individualregbutton.style.display = "none";
        fellowshipregbutton.style.display = "none";
        donationbutton.style.display = "none";
    } 
    else {
        fellowshipregbutton.style.display = "block";
        fellowshipreg.style.display = "none";
        alumnireg.style.display = "none";
        individualregbutton.style.display = "block";
        alumniregbutton.style.display = "block";
        donationbutton.style.display = "block";
    }
  }

  function myFunction4() {

    if (donation.style.display == "none") {
         donation.style.display = "block";
    } 
    else {
        donation.style.display = "block";
        donation.style.display = "none";
    }
  }
  

</script>
<script>
    $(document).ready(function() {
        $('.chapter').select2();
    });

    $(document).ready(function() {
        $('.chapterind').select2();
    }); 
    
                
</script>
                        
@endsection