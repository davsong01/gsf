@extends('frontend.main.mainlayout')
@section('title', 'Programs')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>GSF Community - Programs & Events</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
<div class="container mb0">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
            <h2>Upcoming programs</h2>
            <p>
                List most recent places are submitted by our users. It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.
            </p>
                <div class="form-group">
                </div>
        </div>
    </div><!--/row-->
</div>

<div class="container mb30">
    {{-- <div class="row">
            <h3 class="left-title mt10 mb25" style="margin-top: 30px !important">Programs</h3>
            <div class="row">
            @foreach($programs->sortby('date') as $event)
            <div id="{{ $event->id }}" class="col-md-6 col-sm-12 mb40">
                <div class="working-hours">
                    <div class="day clearfix">
                        <div class="event-details">
                            <span class="name"> <b class="event-title"> 
                                @if(isset($event->chapter))
                                {{ $event->chapter->name }}
                                @else
                                GSF National Program
                                @endif
                                <br>
                                
                                <i class="fa fa-window-maximize" aria-hidden="true"></i>
                                {{ $event->title }} </b><br>
                                <i class="fa fa-calendar" aria-hidden="true"></i> {{  date("F jS, Y", strtotime($event->date)) }} |  
                                <i class="fa fa-clock-o" aria-hidden="true"></i> {{  Carbon\Carbon::parse($event->time)->format('g:i A') }} |
                                <i class="fa fa-street-view" aria-hidden="true"></i> {{ $event->venue }} 
                                     
                                </span>
                        </div>
                        <a href="{{ asset($event->banners) }}" target="_blank" data-lightbox="{{ $event->id }}" data-title="banner">
                            <img src="{{ asset($event->banners) }}" alt="{{ $event->banner }}" class="mb10 events-image">
                        </a>
                    </div>
                </div>
               
            </div>
            @endforeach
            </div>
        </div>
    </div> --}}
    <div class="row mt-120">
        <div class="row">
            <div class="col-sm-12">
                @foreach($programs as $event)
                <div class="row listing-row" id="{{ $event->id }}">
                    <div class="col-sm-5">
                    <a href="{{ asset($event->banners) }}" target="_blank" data-lightbox="{{ $event->id }}" data-title="banner"><img src="{{ asset($event->banners) }}" alt="{{ asset($event->banners) }}" class="img-responsive"></a>
                    </div>
                    <div class="col-sm-7">
                        <h4><a href="#">{{ $event->title }}</a></h4>
                        <p><strong>Campus:</strong> <span>
                            @if(isset($event->chapter))
                            {{ $event->chapter->name }}
                            @else
                            GSF National Program
                            @endif</span>
                        </p>
                        <p><strong>Date:</strong> <span>{{  date("F jS, Y", strtotime($event->date)) }}</span></p>
                        <p><strong>Time:</strong> <span>{{  Carbon\Carbon::parse($event->time)->format('g:i A') }}</span></p>
                        <p><strong>Venue:</strong> <span>{{ $event->venue }}</span></p>
                        
                    </div>
                </div><hr>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
