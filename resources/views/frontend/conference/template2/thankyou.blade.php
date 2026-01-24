@extends('frontend.conference.template2.app')
@section('css')

@endsection
@section('content')
<div id="page-banner-area" class="page-banner-area" style="background-image:url({{ asset('conference_templates/template2/images/hero_area/banner_bg.jpg') }})">
    <!-- Subpage title start -->
    <div class="page-banner-title">
    <div class="text-center">
        <h2>Thank you</h2>
    </div>
    </div><!-- Subpage title end -->
</div><!-- Page Banner end -->
<!-- ts intro start -->
<section class="ts-contact" style="padding: 50px 0;">
    <div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h2 class="section-title text-center" style="margin-bottom: 10px;">
                <span></span>
                Payment Received
            </h2>
            <div class="section-header" style="margin-top:10px">
                    <p style="text-align:center"> <br> Dear {{ $data['name'] }}, please find below the details of your payment for <strong>{{ $data['edition']->conference_theme }} conference</strong>. <br>
                        We have sent a mail to <strong>{{ $data['email'] }} </strong> with your registration details.
                    </p>
                </div>
        </div><!-- col end-->
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="single-intro-text single-contact-feature">
                <h3 class="ts-title">Name</h3>
                <p>{{ $data['name'] }}</p>
                <span class="count-number fa fa-user"></span>
            </div><!-- single intro text end-->
            <div class="border-shap left"></div>
        </div><!-- col end-->
        <div class="col-lg-4">
            <div class="single-intro-text single-contact-feature">
                <h3 class="ts-title">Email</h3>
                <p>
                {{ $data['email'] }}
                </p>
                <span class="count-number fa fa-envelope"></span>
            </div><!-- single intro text end-->
            <div class="border-shap left"></div>
        </div><!-- col end-->
        <div class="col-lg-4">
            <div class="single-intro-text single-contact-feature">
                <h3 class="ts-title">Phone Number</h3>
                <p>
                {{ $data['phone'] }}
                </p>
                <span class="count-number fa fa-phone"></span>
            </div><!-- single intro text end-->
            <div class="border-shap left"></div>
        </div><!-- col end-->
    </div>
    <div class="row" style="margin-top:20px">
        <div class="col-lg-4">
            <div class="single-intro-text single-contact-feature">
                <h3 class="ts-title">Amount Paid</h3>
                <p>
                &#8358;{{ number_format($data['amount']) }}
                </p>
                <p>
                {{ $data['payment_type'] }}
                </p>
                <span class="count-number fa fa-paper-plane"></span>
            </div><!-- single intro text end-->
            <div class="border-shap left"></div>
        </div><!-- col end-->
        <div class="col-lg-4">
            <div class="single-intro-text single-contact-feature">
                <h3 class="ts-title">Hostel Allocation</h3>
                @if(!empty($data['allocated_hostel_data']['hostel_name']))
                <p>
                    <strong>Hostel Name: </strong>{{ $data['allocated_hostel_data']['hostel_name'] }} <br>
                    <strong>Hostel Allocation Number: </strong>{{ $data['allocated_hostel_data']['hostel_allocation_number'] }}
                </p>
                @else
                <p>
                <br>
                <br>
                </p>
                @endif
                <span class="count-number fa fa-paper-plane"></span>
            </div><!-- single intro text end-->
            <div class="border-shap left"></div>
        </div><!-- col end-->
        <div class="col-lg-4">
            <div class="single-intro-text single-contact-feature">
                <h3 class="ts-title">Service Point Allocation</h3>
                @if(!empty($data['allocated_service_point_data']['service_point_allocation_name']))
                <p>
                    <strong>Service Point: </strong>{{ $data['allocated_service_point_data']['service_point_allocation_name'] }} <br>
                    <strong>S.P. Allocation Number: </strong>{{ $data['allocated_service_point_data']['service_point_allocation_number'] }}
                </p>
                @else
                <p>
                <br>
                <br>
                </p>
                @endif
                <span class="count-number fa fa-paper-plane"></span>
            </div><!-- single intro text end-->
            <div class="border-shap left"></div>
        </div><!-- col end-->
    </div><!-- row end-->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center">
                <br> <br>
                <h3> <strong> Your Login Details are</strong></h3>
                <p>Login ID: {{ $data['family_id'] }}  <br>
                Password: {{ $data['phone'] }}
                </p>
                <a style="margin-bottom:20px; background-color:#183187;border: none;" href="{{ route('conferencemanagement.index') }}" data-toggle="tooltip" data-placement="top" title="Click to login" class="btn btn-outline-danger rounded-pill order-0">Access dashboard</a><br><br>
            </div>
        </div>
    </div>
    </div><!-- container end-->
    <div class="speaker-shap">
        <img class="shap2" src="images/shap/home_schedule_memphis1.png" alt="">
    </div>
</section>
@endsection
