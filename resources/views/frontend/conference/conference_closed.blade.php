@extends('frontend.conference.template3.app')

@section('content')

<section id="section-hero"
    class="section-dark no-top no-bottom text-light jarallax relative mh-800">

    <img class="jarallax-img"
        src="{{ asset('conference_templates/template3/images/background/stars.jpg') }}"
        alt="">

    <div class="gradient-edge-top op-6 h-50 color"></div>
    <div class="gradient-edge-bottom"></div>
    <div class="sw-overlay op-8"></div>

    <div class="abs abs-centered z-2 w-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">

                    <h1 class="fs-100 text-uppercase fs-sm-12vw mb-4 lh-1">
                        Conference Registration Closed
                    </h1>

                    <div class="bg-dark-2 p-4 rounded-1 d-inline-block mt-3">
                        <h3 class="text-warning mb-3">Registration is Now Closed</h3>

                        <p class="mb-0">
                            Thank you for your interest in the conference.
                            Registration has officially closed and new registrations
                            are no longer being accepted.
                        </p>

                        <p class="mt-3 mb-0">
                            If you have already registered, we look forward to
                            welcoming you at the event.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

</section>


{{-- <section class="bg-dark section-dark text-light">
    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-8 text-center">

                <h2 class="mb-4">Already Registered?</h2>

                <p class="lead">
                    Participants who have completed their registration can
                    verify their registration status using the verification page.
                </p>

                <div class="spacer-single"></div>

                <a href="{{ route('verify.registration.show') }}"
                    class="btn-main fx-slide">
                    <span>Verify Registration</span>
                </a>

            </div>

        </div>

    </div>
</section> --}}

@endsection
