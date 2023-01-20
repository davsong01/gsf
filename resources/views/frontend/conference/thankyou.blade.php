@extends('layouts.index')
@section('body')

<!-- Contact Start -->
        <div class="contact">
            <div class="container mt-125">
                <div class="section-header">
                    <h2>Payment Received</h2>
                    <p style="text-align:center"> <br> Dear {{ $data['name'] }}, please find below the details of your payment. <br>
                     We have sent a mail to <strong>{{ $data['email'] }} </strong> with your registration details.
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
                                <i class="fa fa-phone-alt"></i>
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

                    <div class="col-md-12">
                        <div class="contact-text">
                            <a class="btn submitregistration" href="/account" data-toggle="tooltip" data-placement="top" title="Click to login" style="width:100%; margin-bottom:30px">Login to complete registration</a><br><br>
                            <h3> <strong> Your Login Details are</strong>
                            </h3>
                            <p>Conference Number: {{ $data['conference_number'] }}  <br>
                            Password: {{ $data['phone'] }} 
                            </p>
                        </div>                       
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->

@endsection