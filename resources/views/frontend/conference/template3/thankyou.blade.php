@extends('frontend.conference.template3.app')
@include('includes.alerts')

@section('content')
<section id="section-hero" class="section-dark text-light jarallax no-top no-bottom relative mh-500">
    <img src="{{ asset('conference_templates/template2/images/hero_area/banner_bg.jpg') }}" class="jarallax-img" alt="">
    <div class="gradient-edge-bottom h-50"></div>
    <div class="sw-overlay op-5"></div>
    <div class="abs w-80 bottom-10 z-2 w-100">
        <div class="container text-center">
            <h1 class="text-uppercase fs-sm-8vw lh-1 mb-2 wow fadeInDown">Thank You!</h1>
            <p class="lead wow fadeInUp" data-wow-delay=".3s">
                Your registration was successful
            </p>
        </div>
    </div>
</section>

<section class="py-5 bg-dark text-light">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h3 class="text-uppercase mb-3">We’re glad to have you onboard</h3>
                <p class="text-muted">
                    Dear <strong>{{ $transaction->name ?? 'Participant' }}</strong>, thank you for registering
                    {{ isset($transaction->edition->conference_theme) ? 'for ' . $transaction->edition->conference_theme : '' }}.
                    <br>
                    A confirmation email has been sent to <strong>{{ $transaction->email }}</strong>
                    with your login credentials and next steps.
                </p>
            </div>
        </div>

        <div class="row justify-content-center text-center mb-5">
            <div class="col-md-8">
                <div class="card bg-transparent border-light rounded-4 shadow-lg p-4">
                    <h5 class="text-uppercase text-warning">Registration Details</h5>
                    <hr class="border-light">
                    <p><strong>Name:</strong> {{ $transaction->name }}</p>
                    <p><strong>Email:</strong> {{ $transaction->email }}</p>
                    <p><strong>Phone:</strong> {{ $transaction->phone }}</p>
                    <p><strong>Gender:</strong> {{ ucfirst($transaction->gender ?? 'N/A') }}</p>

                    @if(!empty($transaction->amount_paid))
                        <hr class="border-light">
                        <p><strong>Total Amount Paid:</strong> ₦{{ number_format($transaction->total_amount, 2) }}</p>
                        <p><strong>Transaction ID:</strong> {{ $transaction->transid ?? 'N/A' }}</p>
                    @endif

                    @if(!empty($transaction->hostel_id))
                        <hr class="border-light">
                        <p><strong>Hostel:</strong> {{ $transaction->hostel->name }}</p>
                        <p><strong>Hostel Allocation Number:</strong> {{ $transaction->hostel_allocation_number }}</p>
                    @endif

                    @if(!empty($transaction->food_id))
                        <hr class="border-light">
                        <p><strong>Service Point:</strong> {{ $transaction->food->name }}</p>
                        <p><strong>Allocation No:</strong> {{ $transaction->service_point_allocation_number }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="row justify-content-center text-center mt-4">
            <div class="col-md-6">
                <div class="p-4 rounded-4 bg-dark text-dark shadow">
                    <h5 class="text-uppercase mb-3">Your Login Details</h5>
                    <p>
                        <strong>Login ID:</strong> {{ $transaction->user->family_id ?? 'N/A' }} <br>
                        <strong>Password:</strong> {{ $transaction->phone ?? 'Your registered phone number' }}
                    </p>

                    <a class="btn-main fx-slide w-100" href="{{ route('conferencemanagement.index', ['login_status' => 'new']) }}"><span>Access Your Account</span></a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
