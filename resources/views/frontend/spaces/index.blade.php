@extends('frontend.spaces.layouts.app')
@section('content')
    <section class="section section-header section-image bg-primary overlay-primary text-white pb-4 pb-md-7" data-background="{{asset('gsfcom/images/hero-1.jpg')}}">
        <div class="container">
            <div class="row justify-content-center mb-4 mb-xl-5">
                <div class="col-12 col-xl-10 text-center">
                    <h1 class="display-2">Find your space.</h1>
                    <p class="lead text-muted mt-4 px-md-6"><span class="font-weight-bold">12,000+</span> coworking spaces with desks, offices & meeting rooms in <span class="font-weight-bold">165+</span> countries. Discover and reserve space today.</p>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card p-md-2">
                        <div class="card-body p-2 p-md-0">
                            <form autocomplete="off" class="row" method="get" action="https://demo.themesberg.com/spaces/html/all-spaces.html">
                                <div class="col-12 col-lg-5">
                                    <div class="form-group form-group-lg mb-lg-0">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><span class="fas fa-map-marker-alt"></span></span></div><input id="search-location" type="text" class="form-control autocomplete" placeholder="Search location" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="input-group input-group-lg mb-3 mb-lg-0">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input class="form-control datepicker" placeholder="Select date" type="text" required>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3"><button class="btn btn-lg btn-primary btn-block animate-up-2" type="submit">Find a desk</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col">
                    <ul class="d-flex flex-wrap justify-content-center list-unstyled mb-0">
                        <li class="mx-xl-4 mx-2 mb-5 mb-md-0"><img class="img-fluid image-xs" src="https://demo.themesberg.com/spaces/assets/img/clients/northwestern.svg" alt="northwestern logo"></li>
                        <li class="mx-xl-4 mx-2 mb-5 mb-md-0"><img class="img-fluid image-xs" src="https://demo.themesberg.com/spaces/assets/img/clients/google.svg" alt="google logo"></li>
                        <li class="mx-xl-4 mx-2 mb-5 mb-md-0"><img class="img-fluid image-xs" src="https://demo.themesberg.com/spaces/assets/img/clients/university-of-chicago.svg" alt="university logo"></li>
                        <li class="mx-xl-4 mx-2 mb-5 mb-md-0"><img class="img-fluid image-xs" src="https://demo.themesberg.com/spaces/assets/img/clients/corsair.svg" alt="corsair logo"></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="section section-lg pb-lg-6 pb-5">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4 text-center mb-4 mb-md-0 px-lg-4"><img class="img-fluid image-lg mb-4" src="https://demo.themesberg.com/spaces/assets/img/illustrations/easy-transaction.svg" alt="northwestern logo">
                    <h2 class="h4">Extraordinarily easy</h2>
                    <p>Our search makes it verry simple to find your space. And from office match, we are here to help you.</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4 text-center mb-4 mb-md-0 px-lg-4"><img class="img-fluid image-lg mb-4" src="https://demo.themesberg.com/spaces/assets/img/illustrations/support.svg" alt="northwestern logo">
                    <h2 class="h4">Truly transparent</h2>
                    <p>We give you all this info, lifting the lid on actual offices, real availability, and accurate pricing.</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4 text-center mb-4 mb-md-0 px-lg-4"><img class="img-fluid image-lg mb-4" src="https://demo.themesberg.com/spaces/assets/img/illustrations/payment.svg" alt="northwestern logo">
                    <h2 class="h4">Best prices</h2>
                    <p>Choose Spaces and our experts will save you around 15% off the list price. What are you waiting for?</p>
                </div>
            </div>
            <div class="row mt-6">
                <div class="col-12">
                    <div class="card rounded border border-light">
                        <div class="card-body p-3 p-md-5">
                            <div class="progress-wrapper mb-3 mb-md-5">
                                <div class="progress-info info-xl d-block d-md-flex">
                                    <div class="progress-label">
                                        <h2 class="h4 text-dark">Space occupancy level</h2>
                                    </div>
                                    <div><span class="text-gray h4">85%</span></div>
                                </div>
                                <div class="progress progress-lg my-4 my-md-0">
                                    <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 85%;"></div>
                                </div>
                            </div>
                            <div class="d-block d-lg-flex flex-column flex-lg-row justify-content-between align-items-center">
                                <div class="mb-5 mb-lg-0">
                                    <h4 class="font-weight-normal">Book your tour experience today!</h4>
                                    <p class="lead mb-0">Schedule a tour, make an appointment to rent space<br class="d-none d-lg-inline">at Themesberg, or ask for more information.</p>
                                </div>
                                <div class="align-content-end"> <button type="button" class="btn btn-primary animate-up-2" data-toggle="modal" data-target="#modal-form">Schedule a tour</button></div>
                                <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body p-0">
                                                <div class="card bg-soft shadow-md border-0">
                                                    <div class="card-header bg-white py-4"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                        <div class="text-muted text-center mb-3">
                                                            <h3>Interested?</h3>
                                                            <p>We would love to show you Spaces. Please let us know when you are available and we will make our best to receive you on that date and time.</p>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <form class="mt-3">
                                                            <div class="form-group">
                                                                <div class="input-group mb-4">
                                                                    <div class="input-group-prepend"><span class="input-group-text"><span class="far fa-user"></span></span></div><input class="form-control" placeholder="Name" type="text" required>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group mb-4">
                                                                    <div class="input-group-prepend"><span class="input-group-text"><span class="far fa-envelope"></span></span></div><input class="form-control" placeholder="Email" type="email" required>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group mb-4">
                                                                    <div class="input-group-prepend"><span class="input-group-text"><span class="fas fa-mobile"></span></span></div><input class="form-control" placeholder="Phone" type="number" required>
                                                                </div>
                                                            </div>
                                                            <div class="input-group mb-lg-0">
                                                                <div class="input-group-prepend"><span class="input-group-text"><span class="far fa-calendar-alt"></span></span></div><input class="form-control datepicker" placeholder="Select date" type="text" data-position="top">
                                                            </div>
                                                            <div class="text-center"><button type="submit" class="btn btn-block btn-primary mt-4">Send Request Quote</button></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-6">
                <div class="col-12">
                    <h3 class="h4 mb-5">Top Cities</h3>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0"> <a href="all-spaces.html" class="card img-card fh-400 border-0 outer-bg" data-background-inner="https://demo.themesberg.com/spaces/assets/img/newyork.jpg">
                        <div class="inner-bg overlay-dark"></div>
                        <div class="card-img-overlay d-flex align-items-center">
                            <div class="card-body text-white p-3">
                                <h5 class="text-uppercase text-center">New York</h5>
                            </div>
                        </div>
                    </a></div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0"> <a href="all-spaces.html" class="card img-card fh-400 border-0 outer-bg" data-background-inner="https://demo.themesberg.com/spaces/assets/img/paris.jpg">
                        <div class="inner-bg overlay-dark"></div>
                        <div class="card-img-overlay d-flex align-items-center">
                            <div class="card-body text-white p-3">
                                <h5 class="text-uppercase text-center">Paris</h5>
                            </div>
                        </div>
                    </a></div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0"> <a href="all-spaces.html" class="card img-card fh-400 border-0 outer-bg" data-background-inner="https://demo.themesberg.com/spaces/assets/img/london.jpg">
                        <div class="inner-bg overlay-dark"></div>
                        <div class="card-img-overlay d-flex align-items-center">
                            <div class="card-body text-white p-3">
                                <h5 class="text-uppercase text-center">London</h5>
                            </div>
                        </div>
                    </a></div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0"> <a href="all-spaces.html" class="card img-card fh-400 border-0 outer-bg" data-background-inner="https://demo.themesberg.com/spaces/assets/img/tokyo.jpg">
                        <div class="inner-bg overlay-dark"></div>
                        <div class="card-img-overlay d-flex align-items-center">
                            <div class="card-body text-white p-3">
                                <h5 class="font-weight-normal text-uppercase text-center">Tokyo</h5>
                            </div>
                        </div>
                    </a></div>
            </div>
            <div class="row mt-6">
                <div class="col-12">
                    <h3 class="h4 mb-5">Trending Spaces</h3>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-light mb-4 animate-up-5"><a href="single-space.html" class="position-relative"><img src="https://demo.themesberg.com/spaces/assets/img/image-office.jpg" class="card-img-top p-2 rounded-xl" alt="themesberg office"></a>
                        <div class="card-body"><a href="single-space.html">
                                <h4 class="h5">Collaborative Workspace</h4>
                            </a>
                            <div class="d-flex my-4"><span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="badge badge-pill badge-primary ml-2">5.0</span></div>
                            <ul class="list-group mb-3">
                                <li class="list-group-item small p-0"><span class="fas fa-map-marker-alt mr-2"></span>New York, Manhattan, USA</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Old Street (2 mins walk)</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Shoreditch High Street (10 mins walk)</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-soft border-top">
                            <div class="d-flex justify-content-between">
                                <div class="col pl-0"><span class="text-muted font-small d-block mb-2">Monthly</span> <span class="h5 text-dark font-weight-bold">2100$</span></div>
                                <div class="col"><span class="text-muted font-small d-block mb-2">People</span> <span class="h5 text-dark font-weight-bold">12</span></div>
                                <div class="col pr-0"><span class="text-muted font-small d-block mb-2">Sq.Ft</span> <span class="h5 text-dark font-weight-bold">1200</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-light mb-4 animate-up-5"><a href="single-space.html" class="position-relative"><img src="https://demo.themesberg.com/spaces/assets/img/cowork-office.jpg" class="card-img-top p-2 rounded-xl" alt="developer desk"></a>
                        <div class="card-body"><a href="single-space.html">
                                <h4 class="h5">Coworking Workspace</h4>
                            </a>
                            <div class="d-flex my-4"><span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-light"></span> <span class="star fas fa-star text-light"></span> <span class="badge badge-pill badge-primary ml-2">3.0</span></div>
                            <ul class="list-group mb-3">
                                <li class="list-group-item small p-0"><span class="fas fa-map-marker-alt mr-2"></span>California, USA</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Penny Market Street (15 mins walk)</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Museum Street (20 mins walk)</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-soft border-top">
                            <div class="d-flex justify-content-between">
                                <div class="col pl-0"><span class="text-muted font-small d-block mb-2">Monthly</span> <span class="h5 text-dark font-weight-bold">300$</span></div>
                                <div class="col"><span class="text-muted font-small d-block mb-2">People</span> <span class="h5 text-dark font-weight-bold">24</span></div>
                                <div class="col pr-0"><span class="text-muted font-small d-block mb-2">Sq.Ft</span> <span class="h5 text-dark font-weight-bold">2000</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-light mb-4 animate-up-5"><a href="single-space.html" class="position-relative"><img src="https://demo.themesberg.com/spaces/assets/img/meeting-office.jpg" class="card-img-top p-2 rounded-xl" alt="wood office"></a>
                        <div class="card-body"><a href="single-space.html">
                                <h4 class="h5">Meeting Office Space</h4>
                            </a>
                            <div class="d-flex my-4"><span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-light"></span> <span class="badge badge-pill badge-primary ml-2">4.0</span></div>
                            <ul class="list-group mb-3">
                                <li class="list-group-item small p-0"><span class="fas fa-map-marker-alt mr-2"></span>London, Canary Wharf, UK</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Stamford Bridge Stadium (5 mins walk)</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Bluebird Chelsea Pub (15 mins walk)</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-soft border-top">
                            <div class="d-flex justify-content-between">
                                <div class="col pl-0"><span class="text-muted font-small d-block mb-2">Monthly</span> <span class="h5 text-dark font-weight-bold">50$</span></div>
                                <div class="col"><span class="text-muted font-small d-block mb-2">People</span> <span class="h5 text-dark font-weight-bold">2-4</span></div>
                                <div class="col pr-0"><span class="text-muted font-small d-block mb-2">Sq.Ft</span> <span class="h5 text-dark font-weight-bold">400</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-light mb-4 animate-up-5"><a href="single-space.html" class="position-relative"><img src="https://demo.themesberg.com/spaces/assets/img/conference-office.jpg" class="card-img-top p-2 rounded-xl" alt="pixel room"></a>
                        <div class="card-body"><a href="single-space.html">
                                <h4 class="h5">Conference Room</h4>
                            </a>
                            <div class="d-flex my-4"><span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="fas fa-star-half text-warning"></span> <span class="badge badge-pill badge-primary ml-2">4.7</span></div>
                            <ul class="list-group mb-3">
                                <li class="list-group-item small p-0"><span class="fas fa-map-marker-alt mr-2"></span>Paris, Île-de-France, France</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>LE BHV MARAIS (5 mins walk)</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Shakespeare & Company (15 mins walk)</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-soft border-top">
                            <div class="d-flex justify-content-between">
                                <div class="col pl-0"><span class="text-muted font-small d-block mb-2">Monthly</span> <span class="h5 text-dark font-weight-bold">150$</span></div>
                                <div class="col"><span class="text-muted font-small d-block mb-2">People</span> <span class="h5 text-dark font-weight-bold">2-10</span></div>
                                <div class="col pr-0"><span class="text-muted font-small d-block mb-2">Sq.Ft</span> <span class="h5 text-dark font-weight-bold">200</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-light mb-4 animate-up-5"><a href="single-space.html" class="position-relative"><img src="https://demo.themesberg.com/spaces/assets/img/lifestyle-office.jpg" class="card-img-top p-2 rounded-xl" alt="modern desk"></a>
                        <div class="card-body"><a href="single-space.html">
                                <h4 class="h5">Lifestyle Space</h4>
                            </a>
                            <div class="d-flex my-4"><span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="badge badge-pill badge-primary ml-2">4.7</span></div>
                            <ul class="list-group mb-3">
                                <li class="list-group-item small p-0"><span class="fas fa-map-marker-alt mr-2"></span>Madrid, Hortaleza, Spain</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Plaza Mayor (2 mins walk)</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>eal Casa de Correos (15 mins walk)</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-soft border-top">
                            <div class="d-flex justify-content-between">
                                <div class="col pl-0"><span class="text-muted font-small d-block mb-2">Monthly</span> <span class="h5 text-dark font-weight-bold">200$</span></div>
                                <div class="col"><span class="text-muted font-small d-block mb-2">People</span> <span class="h5 text-dark font-weight-bold">10-30</span></div>
                                <div class="col pr-0"><span class="text-muted font-small d-block mb-2">Sq.Ft</span> <span class="h5 text-dark font-weight-bold">500</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-light mb-4 animate-up-5"><a href="single-space.html" class="position-relative"><img src="https://demo.themesberg.com/spaces/assets/img/private-office.jpg" class="card-img-top p-2 rounded-xl" alt="office"></a>
                        <div class="card-body"><a href="single-space.html">
                                <h4 class="h5">Private Space</h4>
                            </a>
                            <div class="d-flex my-4"><span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="badge badge-pill badge-primary ml-2">5.0</span></div>
                            <ul class="list-group mb-3">
                                <li class="list-group-item small p-0"><span class="fas fa-map-marker-alt mr-2"></span>New York, Manhattan, USA</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Old Street (2 mins walk)</li>
                                <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Shoreditch High Street (10 mins walk)</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-soft border-top">
                            <div class="d-flex justify-content-between">
                                <div class="col pl-0"><span class="text-muted font-small d-block mb-2">Monthly</span> <span class="h5 text-dark font-weight-bold">100$</span></div>
                                <div class="col"><span class="text-muted font-small d-block mb-2">People</span> <span class="h5 text-dark font-weight-bold">1</span></div>
                                <div class="col pr-0"><span class="text-muted font-small d-block mb-2">Sq.Ft</span> <span class="h5 text-dark font-weight-bold">10</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mt-lg-6 mt-3 d-flex flex-column text-center">
                    <div><a href="all-spaces.html" class="btn btn-primary animate-up-2 mb-2">Browse All</a></div><span class="small">1422 spaces in 34 countries</span>
                </div>
            </div>
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
                            <li class="nav-item mr-0 mr-sm-2 mr-md-0 mb-3 mb-lg-0"><a class="nav-link flex-sm-fill text-sm-center active" id="tab-find-space" data-toggle="tab" href="#find-space" role="tab" aria-controls="find-space" aria-selected="true"><span class="far fa-building mr-2"></span>Find your Space</a></li>
                            <li class="nav-item"><a class="nav-link flex-sm-fill text-sm-center" id="tab-submit-space" data-toggle="tab" href="#submit-space" role="tab" aria-controls="submit-space" aria-selected="false"><span class="far fa-money-bill-alt mr-2"></span>Submit your Space</a></li>
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
                                            <p>It takes no longer than 15 minutes to list your space on themesberg. Our user friendly process.</p>
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
                                            <p>Orders coming from themesberg are 100% prepaid. We will bring you not just leads but new clients.</p>
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
                                            <p>It takes no longer than 15 minutes to list your space on themesberg. Our user friendly onboarding process.</p>
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
                                            <p>Orders coming from themesberg are 100% prepaid. We will bring you not just leads but new clients.</p>
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
    <div class="section bg-white">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-12 col-lg-9 text-center">
                    <div class="nav-wrapper">
                        <ul class="nav nav-pills nav-pill-circle flex-sm-row justify-content-center" id="tab-34" role="tablist">
                            <li class="nav-item"><a class="nav-link bg-white text-sm-center avatar-link active" id="tab-link-example-13" data-toggle="tab" href="#link-example-13" role="tab" aria-controls="link-example-13" aria-selected="true"><img class="rounded-circle" src="https://demo.themesberg.com/spaces/assets/img/team/profile-picture-3.jpg" alt="Bonnie avatar"></a></li>
                            <li class="nav-item"><a class="nav-link bg-white text-sm-center avatar-link" id="tab-link-example-14" data-toggle="tab" href="#link-example-14" role="tab" aria-controls="link-example-14" aria-selected="false"><img class="rounded-circle" src="https://demo.themesberg.com/spaces/assets/img/team/profile-picture-1.jpg" alt="Neil avatar"></a></li>
                            <li class="nav-item"><a class="nav-link bg-white text-sm-center avatar-link" id="tab-link-example-15" data-toggle="tab" href="#link-example-15" role="tab" aria-controls="link-example-15" aria-selected="false"><img class="rounded-circle" src="https://demo.themesberg.com/spaces/assets/img/team/profile-picture-4.jpg" alt="Christopher avatar"></a></li>
                        </ul>
                    </div>
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="tab-content" id="tabcontentexample-5">
                                <div class="tab-pane fade show active" id="link-example-13" role="tabpanel" aria-labelledby="tab-link-example-13"><span class="d-block my-3"><span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span></span>
                                    <blockquote class="blockquote bg-white p-0 p-md-4">I used Themesberg's logo creation services along with their website development services. They have been a pleasure to work with and have been responsive to all questions asked.<footer class="blockquote-footer mt-3 text-primary">Bonnie Green</footer>
                                    </blockquote>
                                </div>
                                <div class="tab-pane fade" id="link-example-14" role="tabpanel" aria-labelledby="tab-link-example-14"><span class="d-block my-3"><span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span></span>
                                    <blockquote class="blockquote bg-white p-0 p-md-4">I have worked with Themesberg over the years on a number of projects. I've always found them to be responsive, friendly and up-to-date with all the technology - which everyone knows is constantly changing.<footer class="blockquote-footer mt-3 text-primary">Neil Sims</footer>
                                    </blockquote>
                                </div>
                                <div class="tab-pane fade" id="link-example-15" role="tabpanel" aria-labelledby="tab-link-example-15"><span class="d-block my-3"><span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span> <span class="star fas fa-star text-warning"></span></span>
                                    <blockquote class="blockquote bg-white p-0 p-md-4">Themesberg are the best in the business for website design and building. They worked hard to give us exactly what we wanted and more for our website and delivered on time. We would definitely use them again.<footer class="blockquote-footer mt-3 text-primary">Christopher Wood</footer>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


