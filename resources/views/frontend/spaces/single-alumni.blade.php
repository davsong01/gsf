@extends('frontend.spaces.layouts.app')
@section('nav-background', '#0d1b48;')
@section('ogtitle', $alumni->name)
@section('title', $alumni->name)
@section('ogurl', route('user.single', $alumni->slug))
<meta property="og:image" content="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}"/> 

@section('content')
<div class="section section-header bg-primary pb-md-6 pb-lg-0 bg-soft">
   
</div>
<div class="section pt-lg-0">
    <div id="spaces-container" class="container">
        <div class="row">
            <aside class="col-12 col-lg-4 d-block z-10">
                <div id="profile-sidebar">
                    <div class="sidebar-inner">
                        <div class="card mt-n7 d-none d-lg-block border-light text-center p-2">
                            <div class="profile-cover rounded-top" data-background="../assets/img/coworking.jpg"></div>
                            <div class="card-body p-2">
                                <div class="profile-thumbnail small-thumbnail mt-n6 mx-auto">
                                    <img src="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}"
                                        class="card-img-top rounded-circle border-white" alt="Joseph Portrait">
                                </div>
                                <h4 class="font-weight-normal mt-4 mb-0">{{ $alumni->name }}</h4><span class="icon icon-xs text-success"><i class="fas fa-award"></i></span>
                                <ul class="list-inline row mx-auto my-4" style="display: inherit;">

                                @if($alumni->facebook )
                                <a href="{{ $alumni->facebook }}" target="_blank" aria-label="facebook social link" class="icon-facebook mr-3"><span class="fab fa-facebook-f"></span></a> 
                                @endif
                                @if($alumni->twitter )
                                <a href="{{ $alumni->twitter }}" target="_blank" aria-label="facebook social link" class="icon-twitter mr-3"><span class="fab fa-twitter"></span></a> 
                                @endif
                            </ul><a class="btn btn-sm btn-secondary mb-3" href="#"><span class="fas fa-user-plus mr-1"></span>
                                    Send Message</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Mobile --}}
                <div class="card d-lg-none border-light text-center mt-n5 mt-md-n7 p-2">
                    <div class="card-body p-2">
                        <div class="profile-thumbnail small-thumbnail mt-n6 mx-auto"><img
                                src="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}"
                                class="card-img-top rounded-circle border-white" alt=""></div>
                        <h4 class="font-weight-normal mt-4 mb-0">{{ $alumni->name }}</h4><span class="icon icon-xs text-success"><i class="fas fa-award"></i></span>
                        <ul class="list-inline row mx-auto my-4" style="display: block">
                             @if($alumni->facebook )
                                <a href="{{ $alumni->facebook }}" target="_blank" aria-label="facebook social link" class="icon-facebook mr-3"><span class="fab fa-facebook-f"></span></a> 
                                @endif
                                @if($alumni->twitter )
                                <a href="{{ $alumni->twitter }}" target="_blank" aria-label="facebook social link" class="icon-facebook mr-3"><span class="fab fa-twitter"></span></a> 
                                @endif
                        </ul>
                        <a class="btn btn-sm btn-secondary mb-3" href="#"><span
                                class="fas fa-user-plus mr-1"></span> Send
                            Message</a>
                    </div>
                </div>
            </aside>
            <div class="col-12 col-lg-8">
                <div class="tab-content mt-4">
                    <div class="tab-pane fade show active" id="tab-grid-1" role="tabpanel"
                        aria-labelledby="tab-grid-1-tab">
                        <div class="row justify-content-center">
                            <div class="col-12 col-sm-10 col-md-6 col-lg-12 mb-4">
                                <div class="card border-light mb-4 animate-up-5">
                                    <div class="row no-gutters align-items-center">
                                        <div class="card-body"><a href="single-space.html">
                                                <h4 class="h5">About</h4>
                                            </a>
                                            <div class="d-flex my-4"><span class="star fas fa-star text-warning"></span>
                                                <span class="star fas fa-star text-warning"></span> <span
                                                    class="star fas fa-star text-warning"></span> <span
                                                    class="star fas fa-star text-warning"></span> <span
                                                    class="star fas fa-star text-warning"></span> <span
                                                    class="badge badge-pill badge-primary ml-2">4.7</span>
                                                </div>
                                            <ul class="list-group mb-3">
                                                <li class="list-group-item small p-0"><span
                                                        class="fa fa-university mr-2" data-toggle="tooltip" data-html="true" title="Chapter" aria-hidden="true"></span><b>GSF</b> - {{ $alumni->campus->name }}</li>
                                                @if(!is_null($alumni->course))
                                                <li class="list-group-item small p-0"><span
                                                        class="fas fa-book mr-2" data-toggle="tooltip" data-html="true" title="Discipline" aria-hidden="true"></span>{{ $alumni->course }}  @if($alumni->status == 1 && $alumni->graduation_year != NULL )({{ $alumni->matric_year . ' - ' . $alumni->graduation_year }})@endif</li>
                                                @endif
                                                @if(!is_null($alumni->skills))
                                                <li class="list-group-item small p-0"><span
                                                        class="fas fa-bullseye mr-2" data-toggle="tooltip" data-html="true" title="Skills" aria-hidden="true"></span>@if($alumni->skills)<i class="fa fa-address-card" data-toggle="tooltip" data-html="true" title="Skills" aria-hidden="true"> &nbsp; {{ $alumni->skills }} @endif</li>
                                                @endif
                                                @if(!is_null($alumni->dob))
                                                <li class="list-group-item small p-0"><span
                                                        class="fas fa-birthday-cake" data-toggle="tooltip" data-html="true" title="Birthday" aria-hidden="true"></span>
                                                        &nbsp; {{ \Carbon\Carbon::parse($alumni->dob)->format('jS\, F') }} </li>
                                                @endif

                                                @if($alumni->show_email == 1)
                                                <li class="list-group-item small p-0"><span
                                                        class="fas fa-envelope" data-toggle="tooltip" data-html="true" title="Email" aria-hidden="true"></span>
                                                       &nbsp; {{ $alumni->email }}</li>
                                                @endif
                                                @if($alumni->show_phone == 1)
                                                 <li class="list-group-item small p-0"><span
                                                        class="fas fa-phone" data-toggle="tooltip" data-html="true" title="Phone" aria-hidden="true"></span>
                                                       &nbsp; {{ $alumni->phone }}</li>
                                                @endif
                                                           
                                            </ul>
                                        </div>
                                        <div class="card-footer bg-soft border-top">
                                            <div class="d-flex justify-content-between">
                                                <div class="col pr-0">
                                                    @if($alumni->open_to_work)
                                                    <span class="h5 text-dark font-weight-bold">Open to work</span></div>
                                                    @else
                                                   @if($alumni->rolename == 'Member')
                                                   {{ $alumni->rolename }} <br>
                                                   @if(!is_null($alumni->matric_year))
                                                    <span class="sub" style="font-size: small;">{{ $alumni->matric_year . ' - ' . date('Y')}}</span>
                                                    @endif
                                                    @else
                                                        {{ $alumni->rolename . ', '}} <br>
                                                    <span class="sub" style="font-size: small;">
                                                            {{ $alumni->portfolio_session }}
                                                    </span>
                                                    @endif
                                                    @endif
                                                   
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
</div>
@endsection