@extends('frontend.conference.template1.index')
@section('sec-content')
<!-- Contact Start -->
        <div class="contact">
            <div class="container mt-125">
                <div class="section-header" style="margin-top:100px">
                    <h2>Payment Received</h2>
                    <p style="text-align:center"> <br> Dear {{ $data['name'] }}, thank you for your donation, please find below the details of your payment. <br>
                    </p>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Name</h3>
                                <p>{{ $data['name'] }}</p>
                            </div>
                        </div>
                        <div class="contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Phone</h3>
                                <p>{{ $data['phone'] }}</p>
                            </div>
                        </div>
                       
                    </div>
                    <div class="col-md-6">
                        <div class="contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Email</h3>
                                <p>{{ $data['email'] }}</p>
                            </div>
                        </div>
                        <div class="contact-info">
                            <div class="contact-icon">
                                <img src="{{ asset('frontend/img/naira.png') }}">
                            </div>
                            <div class="contact-text">
                                <h3>Amount Paid</h3>
                                <p>{{ $data['amount'] }} </p>
                            </div>
                        </div>
                        
                    </div>


                </div>
            </div>
        </div>
        <!-- Contact End -->

@endsection