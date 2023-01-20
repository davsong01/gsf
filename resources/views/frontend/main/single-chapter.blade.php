@extends('frontend.main.mainlayout')
@section('title', 'GSF, ' . $chapter->name)
@section('content')

<div class="listing-detail-header mb20">
    <div class="container">
        <h2 class="font300">{{ 'GSF - ' . $chapter->name }}</h2>
        @if($chapter->address)
        <p><i class="fa fa-map-marker"></i> {{ $chapter->address }}</p>
        @endif
        @if($chapter->phone)
        <p><i class="fa fa-phone"></i> {{ $chapter->phone }}</p>
        @endif
        @if($chapter->facebook && $chapter->twitter)
        <p class="social-inline">
        <a target="_blank" href="{{ $chapter->facebook }}"><i class="fa fa-facebook-square" style="font-size: 25px;"></i></a>
        <a target="_blank" href="{{ $chapter->twitter }}"><i class="fa fa-twitter-square" style="font-size: 25px;"></i></a>
        </p>
        @endif
    </div>
</div>

<div class="container mb30">
    <div class="row">
        
        <div class="col-sm-8 mb40">
            <h3 class="left-title mb25">About us</h3>
            <img src="{{ asset($chapter->banner ?? 'main/images/chapters/coming-soon.png') }}" alt="{{ $chapter->name . 'banner' }}" class="mb10 responsive-image">
            {!! $chapter->about !!}
            </p>
            @if($chapter->events->count() > 0)
            <h3 class="left-title mt10 mb25" style="margin-top: 30px !important">Our Programs</h3>
            <div class="row">
                @foreach($chapter->events as $event)
                <div id="{{ $event->id }}"></div>
                <div class="col-md-6 col-sm-12 mb40">
                    <div class="working-hours">
                        <div class="day clearfix">
                            <div class="event-details">
                                <span class="name"> <b class="event-title"> <i class="fa fa-window-maximize" aria-hidden="true"></i>
                                    {{ $event->title }} </b><br>
                                    <i class="fa fa-calendar" aria-hidden="true"></i> {{  date("F jS, Y", strtotime($event->date)) }} |  
                                    <i class="fa fa-clock-o" aria-hidden="true"></i> {{  Carbon\Carbon::parse($event->time)->format('g:i A') }} |
                                    <i class="fa fa-street-view" aria-hidden="true"></i> {{ $event->venue }}          
                                </span>
                            </div>
                            <a href="{{ asset($event->banners) }}" target="_blank" data-lightbox="{{ $event->id }}" data-title="My caption">
                                <img src="{{ asset($event->banners) }}" alt="{{ $chapter->name . 'banner' }}" class="mb10 events-image">
                            </a>
                        </div>
                    </div>
                   
                </div>
                @endforeach
            </div>
            @else
            <h3 class="left-title mt10 mb25" style="margin-top: 30px !important">Programs</h3>
            <div class="row">
                @foreach($nationalevents as $event)
            <div id="{{ $event->id }}" class="col-md-6 col-sm-12 mb40">
                <div class="working-hours">
                    <div class="day clearfix">
                        <div class="event-details">
                            <span class="name"> <b class="event-title"> <i class="fa fa-window-maximize" aria-hidden="true"></i>
                                {{ $event->title }} </b><br>
                                <i class="fa fa-calendar" aria-hidden="true"></i> {{  date("F jS, Y", strtotime($event->date)) }} |  
                                <i class="fa fa-clock-o" aria-hidden="true"></i> {{  Carbon\Carbon::parse($event->time)->format('g:i A') }} |
                                <i class="fa fa-street-view" aria-hidden="true"></i> {{ $event->venue }} 
                                     
                                </span>
                        </div>
                        <a href="{{ asset($event->banners) }}" target="_blank" data-lightbox="{{ $event->id }}" data-title="My caption">
                            <img src="{{ asset($event->banners) }}" alt="{{ $chapter->name . 'banner' }}" class="mb10 events-image">
                        </a>
                    </div>
                </div>
               
            </div>
            @endforeach
            </div>
            
            @endif
        </div>
        <div class="col-sm-4">
            @if($chapter->email)
            <div class="mb40">
                <h3 class="left-title mb25">Leave a message for us</h3>
                <form action="{{ route('campus.contact') }}" method="POST" class="finder-contact">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12"> 
                            <div class="row">
                                <div class="col-sm-12 mb15">
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Your Full Name...." required/>
                                </div>
                                <div class="col-sm-6 mb15">
                                    <input type="emai;" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email Address...." required/>
                                </div>
                                <div class="col-sm-6 mb15">
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="Phone number...." required/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mb15">
                            <textarea name="message" value="{{ old('message') }}" class="form-control" rows="5" placeholder="Message...." required></textarea><input type="hidden" name="chapter_id" value="{{ $chapter->id }}">          
                        </div>
                    </div>
                    <div class="row mb15">
                        <div class="col-sm-12 text-center">
                            <div class="data-status"></div> <!-- data submit status -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="submit" name="submit" class="btn btn-primary btn-lg" style="width:100%">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
