<?php
    $main = $alumni->id;
?>
@extends('frontend.spaces.layouts.app')
@section('nav-background', '#0d1b48;')
@section('ogtitle', $alumni->name)
@section('title', $alumni->name)
@section('ogurl', route('user.single', $alumni->slug))
<meta property="og:image" content="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}"/>

@section('content')

<div class="section section-header bg-primary pb-md-6 pb-lg-0 bg-soft">

</div>
<div class="section pt-lg-0" style="padding-bottom:0">
    <div id="spaces-container" class="container">
        <div class="row">
            <aside class="col-12 col-lg-4 d-block z-10">
                <div id="profile-sidebar">
                    <div class="sidebar-inner">
                        <div class="card mt-n7 d-none d-lg-block border-light text-center p-2">
                            <div class="profile-cover rounded-top" style="paddinf-top:50px !important"></div>
                            <div class="card-body p-2">
                                <div class="profile-thumbnail small-thumbnail mt-n6 mx-auto">
                                    <img src="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}"
                                        class="card-img-top rounded-circle border-white" alt="Joseph Portrait">
                                </div>
                                <span class="font-weight-normal mt-4 mb-0">{{ $alumni->name }}</span>
                                @if($alumni->open_to_work)
                                <ul class="list-inline row mx-auto my-1" style="display: inherit;">
                                <span class="h5 btn btn-primary btn-sm font-weight-bold;cursor: none;">Open to work</span>
                                </ul>
                                @endif
                                <ul class="list-inline row mx-auto my-2" style="display: inherit;">
                                    @if($alumni->facebook )
                                    <a href="{{ $alumni->facebook }}" target="_blank" aria-label="facebook social link" class="icon-facebook mr-3"><span class="fab fa-facebook-f"></span></a>
                                    @endif
                                    @if($alumni->twitter )
                                    <a href="{{ $alumni->twitter }}" target="_blank" aria-label="facebook social link" class="icon-twitter mr-3"><span class="fab fa-twitter"></span></a>
                                    @endif
                                </ul>

                                @if($alumni->email)
                                <a style="color: white;" class="btn btn-sm btn-secondary mb-3" data-toggle="modal" data-target="#modal-form"><span class="fas fa-user-plus mr-1"></span>
                                        Send Message</a>
                                </div>
                                @endif
                        </div>
                    </div>
                </div>
                {{-- Mobile --}}
                <div class="card d-lg-none border-light text-center mt-n5 mt-md-n7 p-2">
                    <div class="card-body p-2">
                        <div class="profile-thumbnail small-thumbnail mt-n6 mx-auto"><img
                                src="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}"
                                class="card-img-top rounded-circle border-white" alt=""></div>
                        <h4 class="font-weight-normal mt-4 mb-0">{{ $alumni->name }}</h4>
                        <ul class="list-inline row mx-auto my-4" style="display: block">
                            @if($alumni->facebook )
                                <a href="{{ $alumni->facebook }}" target="_blank" aria-label="facebook social link" class="icon-facebook mr-3"><span class="fab fa-facebook-f"></span>
                                </a>
                            @endif
                            @if($alumni->twitter)
                                <a href="{{ $alumni->twitter }}" target="_blank" aria-label="facebook social link" class="icon-facebook mr-3"><span class="fab fa-twitter"></span>
                                </a>
                            @endif
                        </ul>
                        @if($alumni->email)
                            <a style="color: white;" class="btn btn-sm btn-secondary mb-3" data-toggle="modal" data-target="#modal-form"></span> Send Message</a>
                        @endif
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
                                        <div class="card-body"><h4 class="h5">About</h4>
                                            <ul class="list-group mb-3" style="width:100%">
                                                @if(isset($alumni->campus) && $alumni->campus->id != 86)
                                                <li class="list-group-item p-0"><span class="fa fa-university mr-2" data-toggle="tooltip" data-html="true" title="Chapter" aria-hidden="true"></span><b>GSF</b> - {{ $alumni->campus->name }}
                                                </li>
                                                @endif
                                                @if(!is_null($alumni->course))
                                                <li class="list-group-item p-0">
                                                    <span class="fas fa-book mr-2" data-toggle="tooltip" data-html="true" title="Discipline" aria-hidden="true"></span>
                                                        {{ $alumni->course }}
                                                        @if($alumni->is_graduated == 1 && $alumni->graduation_year != NULL )
                                                            ({{ $alumni->matric_year . ' - ' . $alumni->graduation_year }})
                                                        @endif
                                                </li>
                                                @endif
                                                @if(!is_null($alumni->skills))
                                                 <li class="list-group-item p-0"><span class="fas fa-bullseye mr-2" data-toggle="tooltip" data-html="true" title="Skills" aria-hidden="true"></span>{{ $alumni->skills }}
                                                </li>
                                                @endif
                                                @if(!is_null($alumni->dob))
                                                <li class="list-group-item p-0"><span
                                                        class="fas fa-birthday-cake" data-toggle="tooltip" data-html="true" title="Birthday" aria-hidden="true"></span>
                                                        &nbsp; {{ \Carbon\Carbon::parse($alumni->dob)->format('jS\, F') }} </li>
                                                @endif
                                                @if($alumni->show_email == 1)
                                                <li class="list-group-item p-0"><span
                                                        class="fas fa-envelope" data-toggle="tooltip" data-html="true" title="Email" aria-hidden="true"></span>
                                                       &nbsp; {{ $alumni->email }}</li>
                                                @endif
                                                @if($alumni->show_phone == 1)
                                                <li class="list-group-item p-0"><span
                                                        class="fas fa-phone" data-toggle="tooltip" data-html="true" title="Phone" aria-hidden="true"></span>
                                                       &nbsp; {{ $alumni->phone }}
                                                </li>
                                                @endif

                                            </ul>
                                        </div>
                                        <div class="card-footer bg-soft border-top">
                                            <div class="d-flex justify-content-between">
                                                <div class="col pr-0" style="width:250px">
                                                    @if($alumni->designation)
                                                        <span class="portfolio"></span>{{ $alumni->designation->name ?? 'N/A' }}
                                                    @else
                                                        @if($alumni->is_graduated)
                                                            {{ $alumni->designation->name ?? 'Member' }} <br>
                                                            @if(!empty($alumni->matric_year) && !empty($alumni->graduation_year))
                                                            <span class="sub" style="font-size: small;">
                                                                {{ $alumni->matric_year . ' - ' . $alumni->graduation_year }}</span>
                                                            @endif
                                                            @else
                                                                {{ $alumni->designation->name ?? 'Member'}} <br>
                                                            <span class="sub" style="font-size: small;">
                                                                    {{-- {{ $alumni->portfolio_session }} --}}
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
        <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content"><div class="modal-body p-0">
                    <div class="card shadow-md border-0">
                        <div class="card-body position-relative">
                            <button type="button" class="close mb-2" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <form class="mt-3" action="listing.contact" method="POST">
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-user"></i>
                                            </span>
                                        </div>
                                        <input class="form-control" placeholder="Name" type="text" name="name" required="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input class="form-control" placeholder="Email" type="email" name="email" required="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" placeholder="Write message" name="message" id="message-2" rows="4" required="">
                                        </textarea>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-block btn-primary mt-4">Send</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($related && !empty($related) && $alumni->chapter_id <> 86)
<section class="section" style="padding-top: 0rem !important;padding-bottom: 0rem !important;margin-top:150px">
        <div class="container">
            <hr class="line">
            <div class="row">
                <div class="col-12" style="margin-bottom:100px">
                    <span class="mb-5 mt-3">Attended <strong style="font-style: italic;">{{ $alumni->campus->name }} </strong>together</span>
                </div>
                    @foreach($related as $user)
                        @if($user->id <> $main)
                            @include('frontend.spaces.includes.user_block')
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
@endif
@endsection
