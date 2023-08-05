<?php 

    use App\Models\Chapter;
    use App\Models\User;
?>
@extends('frontend.spaces.layouts.app')
@section('content')
    <section class="section section-header section-image bg-primary overlay-primary text-white pb-4 pb-md-7" data-background="{{asset('gsfcom/images/hero-1.jpg')}}">
        <div class="container">
            <div class="row justify-content-center mb-4 mb-xl-5">
                <div class="col-12 col-xl-10 text-center">
                    <h1 class="display-2">Find GSFite...</h1>
                    <p class="lead text-muted mt-4 px-md-6"><span class="font-weight-bold">{{ number_format(User::count()) }}</span>+ stallites, alumni in <span class="font-weight-bold">{{ number_format(Chapter::count())}}</span>+ campuses. Search and reunite today.</p>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card p-md-2">
                        <div class="card-body p-2 p-md-0">
                            <form autocomplete="off" class="row" method="GET" action="{{ route('general.search') }}">
                                <div class="col-12 col-lg-9">
                                    <div class="form-group form-group-lg mb-lg-0">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><span class="fas fa-map-marker-alt"></span></span></div><input id="name" name="name" type="text" class="form-control autocomplete" placeholder="Search for GSFite, GSF Chapter ..." required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3"><button class="btn btn-lg btn-primary btn-block animate-up-2" type="submit">Search</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>
    </section>
    
    <section class="pb-lg-6 pb-5 mb-5">
        <div class="container">
             <div class="row mt-6 mb-5">
                <div class="col-12">
                    <div class="card rounded border border-light">
                        <div class="card-body p-3 p-md-5">
                            <div class="progress-wrapper mb-3 mb-md-5">
                                <div class="progress-info info-xl d-block d-md-flex">
                                    <div class="progress-label">
                                        <h2 class="h4 text-dark">All GSFites, all together, all in one place. All right now!</h2>
                                    </div>
                                </div>
                                <div class="progress progress-lg my-4 my-md-0">
                                    <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="d-block d-lg-flex flex-column flex-lg-row justify-content-between align-items-center">
                                <div class="mb-5 mb-lg-0">
                                    <p class="lead mb-0">
                                        The GSF Directory just got a major upgrade! Update your profile today and start making more meaningful connections in the only place where everyone is a GSFite.
                                        Customizable search and filters make it easier to find and connect with alumni, access your Account, curate your profile, and more.<br class="d-none d-lg-inline">
                                    </p>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4 text-center mb-4 mb-md-0 px-lg-4"><img class="img-fluid image-lg mb-4" src="https://demo.GSF .com/spaces/assets/img/illustrations/easy-transaction.svg" alt="northwestern logo">
                    <h2 class="h4">Explore</h2>
                    <p>Our search makes it verry simple to find your space. And from office match, we are here to help you.</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4 text-center mb-4 mb-md-0 px-lg-4"><img class="img-fluid image-lg mb-4" src="https://demo.GSF .com/spaces/assets/img/illustrations/support.svg" alt="northwestern logo">
                    <h2 class="h4">Connect</h2>
                    <p>We give you all this info, lifting the lid on actual offices, real availability, and accurate pricing.</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4 text-center mb-4 mb-md-0 px-lg-4"><img class="img-fluid image-lg mb-4" src="https://demo.GSF .com/spaces/assets/img/illustrations/payment.svg" alt="northwestern logo">
                    <h2 class="h4">Give Back</h2>
                    <p>Join</p>
                </div>
            </div>
            @if(!empty($officials) && $officials->count() > 0)
            <div class="row mt-6">
                <div class="col-12">
                    <h3 class="h4 mb-5">Meet our Officials</h3>
                </div>
                @foreach($officials as $official)
                @if($official->id = 1421)
                {{-- {{dd($official)}} --}}
                @endif
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-4 mb-4"> <a href="#" class="card img-card fh-250 border-0 outer-bg" data-background-inner="{{ $official->user && !is_null($official->user->passport) ? asset($official->user->passport) : asset('frontend/passports/avatar.jpg') }}" alt="passport">
                        <div class="inner-bg overlay-dark"></div>
                        <div class="card-img-overlay d-flex align-items-center hover-hide-text">
                            <div class="card-body p-3">
                                <span class="text-uppercase text-center" style="display: block;">{{ $official->name }}</span>
                                <p style="text-align: center;">{{ $official->office }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    <section class="section section-lg bg-soft">
        <div class="container">
            <div class="row justify-content-center mb-4 mb-lg-5">
                <div class="col-12 col-md-8 text-center">
                    <h2 class="h1"><span class="font-weight-bold">How</span> it works?</h2>
                    <p class="lead mt-3">All you’ll need are the details of the building and location, the types of space, pricing and some good quality photographs.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-10 col-lg-6 mx-auto">
                    <div class="nav-wrapper">
                        <ul class="nav nav-pills nav-fill flex-column flex-sm-row mb-lg-4 mb-0" id="tab-32" role="tablist">
                            <li class="nav-item mr-0 mr-sm-2 mr-md-0 mb-3 mb-lg-0"><a class="nav-link flex-sm-fill text-sm-center active" id="tab-find-space" data-toggle="tab" href="#find-space" role="tab" aria-controls="find-space" aria-selected="true"><span class="far fa-building mr-2"></span>Find a GSFite</a></li>
                            <li class="nav-item"><a class="nav-link flex-sm-fill text-sm-center" id="tab-submit-space" data-toggle="tab" href="#submit-space" role="tab" aria-controls="submit-space" aria-selected="false"><span class="far fa-money-bill-alt mr-2"></span>Upload your details</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-12">
                    <div class="tab-content mt-lg-5" id="tabcontentexample-3">
                        <div class="tab-pane fade show active" id="find-space" role="tabpanel" aria-labelledby="tab-find-space">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-4 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-primary mb-4 rounded-circle"><span class="fas fa-map-pin"></span></div>
                                            <h3 class="h5 my-3">1. Choose a workspace</h3>
                                            <p>It takes no longer than 15 minutes to list your space on GSF . Our user friendly process.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-4 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-primary mb-4 rounded-circle"><span class="far fa-calendar-check"></span></div>
                                            <h3 class="h5 my-3">2. Schedule a tour</h3>
                                            <p>After you have uploaded your space - our website makes it easy for you to keep the details up to date.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-4 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-primary mb-4 rounded-circle"><span class="fas fa-mouse-pointer"></span></div>
                                            <h3 class="h5 my-3">3. Book your workspace</h3>
                                            <p>Orders coming from GSF  are 100% prepaid. We will bring you not just leads but new clients.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col mt-lg-6 mt-3 text-center"><a href="all-spaces.html" class="btn btn-primary animate-up-2"><i class="fas fa-search-location mr-2"></i>Find a Location</a></div>
                        </div>
                        <div class="tab-pane fade" id="submit-space" role="tabpanel" aria-labelledby="tab-submit-space">
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-3 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-secondary mb-4 rounded-circle"><span class="fas fa-clipboard-list"></span></div>
                                            <h3 class="h5 my-3">1. List your space</h3>
                                            <p>It takes no longer than 15 minutes to list your space on GSF . Our user friendly onboarding process.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-3 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-secondary mb-4 rounded-circle"><span class="far fa-user"></span></div>
                                            <h3 class="h5 my-3">2. Get ready</h3>
                                            <p>After you have uploaded your space - our website makes it easy for you to keep the details up to date.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-3 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-secondary mb-4 rounded-circle"><span class="far fa-money-bill-alt"></span></div>
                                            <h3 class="h5 my-3">3. Earn money</h3>
                                            <p>Orders coming from GSF  are 100% prepaid. We will bring you not just leads but new clients.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col mt-6 text-center"><a href="submit-item.html" class="btn btn-secondary animate-up-2"><i class="fas fa-plus mr-2"></i>List a Space</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
@endsection


