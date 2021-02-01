@extends('layouts.index')
@section('body')

<div class="contact" id="register">
</div>
<!-- Contact Start -->
<div class="contact">
    <div class="container">
        <div class="section-header">
            <h2>NEC ONLY REGISTRATION PORTAL</h2>
        </div>
        <div class="row align-items-center">

            <div class="col-md-12">
                @include('includes.falerts')
            </div>
            @if($setting->close_registration >= date('Y-m-d'))
                {{-- Include form --}}
                <div class="about">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <hr>
                                <div class="contact-form">
                                    <div id="success">
                                        <h6 style="color:green">Kindly fill the form below and click proceed to payment
                                        </h6>
                                    </div>
                                    <form action="{{ route('pay') }}" method="POST">
                                        @csrf
                                        <div class="control-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Enter your full name" required="required">
                                        </div>

                                        <div class="control-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Enter your email" required="required">
                                        </div>
                                        <div class="control-group">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                placeholder="Enter your phone number" required="required">
                                        </div>

                                        <div class="control-group">
                                            <label>Enter amount you want to pay for registration (Cannot be less than
                                                #{{ $setting->alumni_fee }})</label>
                                            <input type="number" class="form-control" id="participants"
                                                name="participants" value="{{ old('participants') }}"
                                                placeholder="Amount you want to register with"
                                                min="{{ $setting->alumni_fee }}" required="required">
                                        </div>

                                        <br>
                                        {{-- <input type="hidden" name="orderID" value="345"> --}}
                                        <input type="hidden" name="amount" id="amount" value="">
                                        {{-- required in kobo --}}
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="currency" value="NGN">
                                        <input type="hidden" name="metadata"
                                            value="{{ json_encode($array = ['type' => '4',]) }}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <script>
                                            var participants = document.getElementById('participants');
                                            var amount = document.getElementById('amount');

                                            participants.addEventListener('input', function () {
                                                amount.value = this.value * 100;
                                            });
                                        </script>

                                        <div class="control-group">
                                            <button class="btn submitregistration" type="submit"
                                                style="width:100%">Proceed to Payment </button>
                                        </div>
                                    </form>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-md-12 col-sm-12">
                    <h2 style="text-align: center;">REGISTRATION HAS NOW CLOSED!!!</h2>
                </div>
            @endif
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
                    {{-- <p>GSF History</p> --}}
                    <h2>You can also sponsor the conference</h2>
                </div>
                <div class="about-text">
                    <div>
                        <img class="center" src="{{ asset('frontend/img/sponsor.jpg') }}"
                            alt="Image"
                            style=" border-radius: 10%; display: block; margin-left: auto; margin-right: auto; width: 60%;">
                        <br>

                    </div>
                    <p style="text-align:center">Are you led to sponsor the conference, no amount is too small nor big.
                        Please click button below to donate
                    </p>

                    <a class="btn submit" onclick="myFunction4()" id="donationbutton" data-toggle="tooltip"
                        data-placement="top" title="Register as an alumni" style="width:100%">Make Donation</a>

                    <div class="about" id="donation" style="display:none;">
                       {{-- include donation form --}}
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
        } else {
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
        } else {
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
        } else {
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
        } else {
            donation.style.display = "block";
            donation.style.display = "none";
        }
    }
</script>
<script>
    $(document).ready(function () {
        $('.chapter').select2();
    });

    $(document).ready(function () {
        $('.chapterind').select2();
    });
</script>

@endsection