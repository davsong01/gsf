<?php 
    $main = $chapter->id;
?>
@extends('frontend.spaces.layouts.app')
@section('title', 'GSF, ' . $chapter->name)
@section('css')

@endsection
@section('content')
<div class="section section-header section-image bg-primary overlay-primary text-white overflow-hidden pb-6"
    data-background="{{ asset($chapter->banner ?? 'main/images/chapters/coming-soon.png') }}">
    <div class="container z-2">
        <div class="row justify-content-center pt-3">
            <div class="col-12 text-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-transparent justify-content-center mb-4">
                        <li class="breadcrumb-item text-secondary"><a href="/">Home</a></li>
                        <li class="breadcrumb-item text-secondary"><a
                                href="{{ route('people.campuses') }}">Campuses</a></li>
                        {{-- <li class="breadcrumb-item text-white active" aria-current="page"></li> --}}
                    </ol>
                </nav>
                <h1 class="mb-4">{{ $chapter->name }}</h1>

            </div>
        </div>
    </div>
</div>
<div class="section section-lg pt-5" style="padding-bottom: 0rem !important">
    <div class="container">
        @include('includes.alerts')
        <div class="row">
            <div class="col-12 col-lg-8">
                <nav>
                    <div class="nav nav-tabs flex-column flex-md-row border-light mb-3" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-about-tab" data-toggle="tab" href="#nav-about"
                            role="tab" aria-controls="nav-about" aria-selected="true"><span
                                class="far fa-address-card mr-2"></span>About</a>
                        <a class="nav-item nav-link" id="nav-events-tab" data-toggle="tab" href="#nav-events" role="tab"
                            aria-controls="nav-events" aria-selected="false"><span
                                class="far fa fa-calendar mr-2"></span>Events</a>
                        <a class="nav-item nav-link" id="nav-events-tab" data-toggle="tab" href="#members" role="tab"
                            aria-controls="nav-events" aria-selected="false"><span
                                class="fa fa-graduation-cap mr-2"></span>Members</a>
                        <a class="nav-item nav-link" id="nav-events-tab" data-toggle="tab" href="#alumni" role="tab"
                            aria-controls="nav-events" aria-selected="false"><span
                                class="fas fa-user-graduate mr-2"></span>Alumni</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-about" role="tabpanel"
                        aria-labelledby="nav-about-tab">
                        <div class="row mb-5">
                            <div class="col-12">
                                {!! $chapter->about !!}
                            </div>
                            <div class="card border-bottom rounded-0 p-4">
                                <h2 class="h5">Contact</h2>
                                <ul class="list-unstyled mb-0">
                                    @if($chapter->email)
                                        <li class="d-flex py-1"><span class="icon icon-xs icon-primary"><span
                                                    class="fas fa-envelope mr-2"></span>
                                            </span><span>{{ $chapter->email }}</span></li>
                                    @endif
                                    @if($chapter->phone)
                                        <li class="d-flex py-1"><span class="icon icon-xs icon-primary"><span
                                                    class="fas fa-phone mr-2"></span>
                                            </span><span>{{ $chapter->phone }}</span></li>
                                    @endif
                                    @if($chapter->facebook)
                                        <a target="_blank" href="{{ $chapter->facebook }}">
                                            <li class="d-flex py-1"><span class="icon icon-xs icon-primary"><span
                                                        class="fab fa-facebook mr-2"></span>
                                                </span>{{ $chapter->facebook }}<span></span>
                                        </a>
                                        </li>
                                    @endif
                                    @if($chapter->twitter)
                                        <a target="_blank" href="{{ $chapter->twitter }}">
                                            <li class="d-flex py-1"><span class="icon icon-xs icon-primary"><span
                                                        class="fab fa-twitter mr-2"></span>
                                                </span><span>{{ $chapter->twitter }}</span>
                                            </li>
                                        </a>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card border-bottom rounded-0 p-4">
                                    <h2 class="h5">Field</h2>
                                    <p>{{ $chapter->field->name ?? 'N/A' }}</p>
                                    <h2 class="h5">Zone</h2>
                                    <p>{{ $chapter->zone->name ?? 'N/A' }}</p>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="tab-pane fade" id="nav-events" role="tabpanel" aria-labelledby="nav-video-tab">
                        @if($chapter->events->count())
                            @foreach($chapter->events as $event)
                                <div class="card border-light mb-4">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-12 col-lg-6 col-xl-4">
                                            <a class="img-fluid" href="{{ asset('gsfcom/images/events.jpg')}}"><img src="{{ asset('gsfcom/images/events.jpg')}}" alt="private office" class="card-img p-2 rounded-xl">
                                            </a>
                                            </div>
                                            <div class="col-12 col-lg-6 col-xl-8">
                                                <div class="card-body py-lg-0">
                                                    <a href="{{ isset($event->banner) ? $event->banner : asset('gsfcom/images/events.jpg')}}">
                                                        <h2 class="h5"> {{ $event->title }}</h2>
                                                    </a>
                                                    <div class="col d-flex pl-0 pb-1"><span class="text-primary font-small mr-3">
                                                    <span class="fas fa-street-view mr-2"></span>{{ $event->venue }}</span>
                                                    </div>
                                                    <div class="col d-flex pl-0"><span class="text-primary font-small mr-3">
                                                    <span class="font-small mr-3"><span class="fas fa-calendar mr-2"></span>{{  date("F jS, Y", strtotime($event->date)) }}</span> 
                                                    <span class="font-small mr-3"><span class="fa fa-clock mr-2"></span>{{  Carbon\Carbon::parse($event->time)->format('g:i A') }}</span> 
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        </div>
                                </div>
                            @endforeach
                        @else 
                            <div class="col-12 col-lg-6 col-xl-4">
                            No Events
                            </div>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="members" role="tabpanel" aria-labelledby="nav-video-tab">
                        @if($chapter->members()->count() > 0)
                        <div class="row">
                            @foreach($chapter->members()->take(10) as $member)
                                <div class="col-md-6 card mb-4">
                                    <h6 style="float:left">{{ $member->name }}</h3>
                                    <a style="float:right" target="_blank" href="{{ route('user.single', $member->slug) }}" class="btn btn-sm btn-primary animate-up-2" style="color: white;">View details</a>
                                </div>
                               
                            @endforeach
                            <div class="col-md-12">
                                <a target="_blank" href="{{route('members.single.campus', $chapter->id)}}" class="btn btn-info">View all</a>
                            </div>
                        </div>
                        @else 
                            <div class="col-12 col-lg-6 col-xl-4">
                            No Member
                            </div>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="alumni" role="tabpanel" aria-labelledby="nav-video-tab">
                        @if($chapter->alumni()->count() > 0)
                        <div class="row">
                            @foreach($chapter->alumni()->take(10) as $member)
                                <div class="col-md-6 card mb-4">
                                    <h6 style="float:left">{{ $member->name }}</h3>
                                    <a style="float:right" target="_blank" href="{{ route('user.single', $member->slug) }}" class="btn btn-sm btn-primary animate-up-2" style="color: white;">View details</a>
                                </div>
                            @endforeach
                            <div class="col-md-12">
                                <a target="_blank" href="{{route('alumni.single.campus', $chapter->id)}}" class="btn btn-info">View all</a>
                            </div>
                        </div>
                        @else 
                            <div class="col-12 col-lg-6 col-xl-4">
                            No Alumni
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <aside class="col-12 col-lg-4 mt-3 mt-lg-0">

                <div class="card border-light mt-4 p-3">
                    <h5 class="font-weight-normal">Reach out to this GSF Chapter now</h5>
                    <form class="mt-3" action="{{ route('campus.contact') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <div class="input-group mb-4">
                                <div class="input-group-prepend"><span class="input-group-text"><i
                                            class="far fa-user"></i></span>
                                </div><input class="form-control" placeholder="Name"
                                    value="{{ old('name') }}" name="name" type="text" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-4">
                                <div class="input-group-prepend"><span class="input-group-text"><i
                                            class="far fa-envelope"></i></span></div><input class="form-control"
                                    placeholder="Email" type="email" name="email"
                                    value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-4">
                                <div class="input-group-prepend"><span class="input-group-text"><i
                                            class="fas fa-mobile"></i></span>
                                </div><input class="form-control" placeholder="Phone" type="text" name="phone"
                                    value="{{ old('phone') }}" required>
                            </div>
                        </div>
                        <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">

                        <div class="form-group"><textarea class="form-control" placeholder="Message" id="message"
                                rows="4" required name="message"
                                value="{{ old('message') }}"></textarea></div>
                        <div class="text-center"><button type="submit" class="btn btn-block btn-primary mt-4">Send
                                Message</button></div>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</div>
<section class="section" style="padding-top: 0rem !important;
padding-bottom: 0rem !important;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-5 mt-3 font-weight-bold">Related Campuses</h4>
            </div>
            @if($related)
                @foreach($related as $chapter)
                    @if($chapter->id <> $main)
                        @include('frontend.spaces.includes.campus_block')
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</section>
@endsection