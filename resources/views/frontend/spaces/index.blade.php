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
                <div class="col-12 col-sm-6 col-md-4 text-center mb-4 mb-md-0 px-lg-4"><img class="img-fluid image-md mb-4" src="{{asset('gsfcom/images/explore.png')}}" alt="explore">
                    <h2 class="h4">Explore</h2>
                    <p>Explore the GSF member database with simple and elegant search features.</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4 text-center mb-4 mb-md-0 px-lg-4"><img class="img-fluid image-md mb-4" src="{{asset('gsfcom/images/connect.png')}}" alt="connect">
                    <h2 class="h4">Connect</h2>
                    <p>Connect with GSFites. Stallites and Alumni. Home and Abroad.</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4 text-center mb-4 mb-md-0 px-lg-4"><img class="img-fluid image-md mb-4" src="{{asset('gsfcom/images/locate.png')}}" alt="locate">
                    <h2 class="h4">Locate</h2>
                    <p>Locate GSF fellowships around you and Nationawide.</p>
                </div>
            </div>
            @if(!empty($officials) && $officials->count() > 0)
            <div class="row mt-6" style="display:none">
                <div class="col-12">
                    <h3 class="h4 mb-5">Meet our Officials</h3>
                </div>
                @foreach($officials as $official)
                @if($official->id = 1421)
                {{-- {{dd($official)}} --}}
                @endif
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-4 mb-4"> <a href="#" class="card img-card fh-250 border-0 outer-bg" data-background-inner="{{ $official->user ? (asset($official->user->passport ?? 'frontend/passports/avatar.jpg')) : asset('frontend/passports/avatar.jpg') }}" alt="passport">
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
                    <p class="lead mt-3">All you’ll need is a name!</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-10 col-lg-6 mx-auto">
                    <div class="nav-wrapper">
                        <ul class="nav nav-pills nav-fill flex-column flex-sm-row mb-lg-4 mb-0" id="tab-32" role="tablist">
                            <li class="nav-item mr-0 mr-sm-2 mr-md-0 mb-3 mb-lg-0"><a class="nav-link flex-sm-fill text-sm-center active" id="tab-find-space" data-toggle="tab" href="#find-space" role="tab" aria-controls="find-space" aria-selected="true"><span class="fa fa-search mr-2"></span>Find a GSFite</a></li>
                            <li class="nav-item"><a class="nav-link flex-sm-fill text-sm-center" id="tab-submit-space" data-toggle="tab" href="#submit-space" role="tab" aria-controls="submit-space" aria-selected="false"><span class="fa fa-upload mr-2"></span>Upload your details</a></li>
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
                                            <h3 class="h5 my-3">1. Type a name</h3>
                                            <p>Type a name in the search box and we will find your GSFite</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-4 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-primary mb-4 rounded-circle"><span class="far fa-calendar-check"></span></div>
                                            <h3 class="h5 my-3">2. Customize your search</h3>
                                            <p>Start typing a name on the Alumi or Members page and we will find your GSFite</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-4 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-primary mb-4 rounded-circle"><span class="fas fa-university"></span></div>
                                            <h3 class="h5 my-3">3. Get Campus Details</h3>
                                            <p>Start typing a school name and we will get all GSFites on that campus</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col mt-lg-6 mt-3 text-center"><a href="all-spaces.html" class="btn btn-primary animate-up-2"><i class="fas fa-search-location mr-2"></i>Find a Location</a></div> --}}
                        </div>
                        <div class="tab-pane fade" id="submit-space" role="tabpanel" aria-labelledby="tab-submit-space">
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-3 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-secondary mb-4 rounded-circle"><span class="fa fa-mouse-pointer "></span></div>
                                            <h3 class="h5 my-3">1. </h3>
                                            <p>Can't find your name, click <a href="{{route('newalumni')}}">HERE</a> to submit details</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-3 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-secondary mb-4 rounded-circle"><span class="fa fa-upload"></span></div>
                                            <h3 class="h5 my-3">2. Upload details</h3>
                                            <p>Fill your correct information on the form that shows up with and click submit.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="card border-light mb-4 mb-lg-0 text-center">
                                        <div class="card-body p-3 px-xl-4 py-xl-6">
                                            <div class="icon icon-shape icon-lg icon-shape-secondary mb-4 rounded-circle"><span class="far fa-money-bill-alt"></span></div>
                                            <h3 class="h5 my-3">3. Get Listed</h3>
                                            <p>Your details will be reviwed and you get listed on the directory. Great work!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col mt-6 text-center"><a href="{{route('newalumni')}}" class="btn btn-secondary animate-up-2"><i class="fas fa-plus mr-2"></i>Start now</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
@endsection

