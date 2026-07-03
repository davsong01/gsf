@php
    $title = $type == 'go'
        ? 'First Class Award Application'
        : 'Educational Trust Fund (ETF) Application'
@endphp
@extends('frontend.spaces.layouts.app')

@section('title', $title . ' Registration Closed')

@section('content')
<section
    class="section section-header text-white position-relative overflow-hidden"
    style="
        background: linear-gradient(135deg, #0f172a, #1e293b, #1e40af);
        min-height: 420px;
        padding: 120px 0 90px;
    ">
    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-8 text-center">

                <div
                    class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-10 mb-4"
                    style="width:110px;height:110px"
                >
                    <i class="fas fa-lock fa-3x text-warning"></i>
                </div>

                <span class="badge bg-warning text-dark px-4 py-2 mb-4">
                    Registration Closed
                </span>

                <h1 class="display-4 fw-bold mb-3">
                    {{ $title }}
                </h1>

                <p class="lead text-light opacity-75 mb-0">
                    Applications for this application have officially closed.
                    We appreciate everyone who took the time to submit
                    their entries.
                </p>

            </div>

        </div>

    </div>
</section>

<section class="py-6 bg-light">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-9">

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                    <div class="card-body p-5 p-lg-6">

                        <div class="row g-5 align-items-center">

                            <div class="col-md-5 text-center">

                                <img
                                    src="{{ asset('images/closed-registration.svg') }}"
                                    alt="Registration Closed"
                                    class="img-fluid"
                                    style="max-height:260px"
                                >

                            </div>

                            <div class="col-md-7">

                                <h2 class="fw-bold mb-4">
                                    Thank You For Your Interest
                                </h2>

                                <p class="text-muted mb-4">
                                    The application window for this award
                                    has now ended. Submitted entries are
                                    currently undergoing review by the
                                    appropriate committees.
                                </p>

                                <div class="bg-light rounded-4 p-4 mb-4">

                                    <h6 class="fw-bold mb-3">
                                        What happens next?
                                    </h6>

                                    <ul class="mb-0 text-muted">
                                        <li class="mb-2">
                                            Applications will be screened and verified.
                                        </li>

                                        <li class="mb-2">
                                            Successful candidates may be shortlisted
                                            for further review.
                                        </li>

                                        <li>
                                            Final announcements will be communicated
                                            through official GSF channels.
                                        </li>
                                    </ul>

                                </div>

                                <div class="d-flex flex-wrap gap-3">

                                    <a
                                        href="{{ route('home') }}"
                                        class="btn btn-primary px-4"
                                    >
                                        <i class="fas fa-home me-2"></i>
                                        Return Home
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="text-center mt-5 text-muted">

                    <p class="mb-1">
                        “To everything there is a season, and a time to every purpose under heaven.”
                    </p>

                    <small>
                        Ecclesiastes 3:1
                    </small>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
