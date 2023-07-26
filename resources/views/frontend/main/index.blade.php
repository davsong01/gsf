@extends('frontend.main.mainlayout')
@section('title', 'Home')
@section('content')

<!--fullscreen image-->
<div class="fullscreen-parallax bg-parallax" data-jarallax='{"speed": 0.2}' style='background-image: url({{ asset('main/images/bg1.jpg') }})'>
    <div class="fullscreen-inner">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="text-center">
                        <h1>GOFAMINT STUDENTS' FELLOWSHIP</h1>
                        <p class="mb20">Welcome to GSF community</p>
                        <form action="{{ route('general.search') }}" method="POST">
                            @csrf
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control " placeholder="Search for an alumni, student of GSF... " name="name" required>
                               
                                <span class="input-group-btn">
                                    <button class="btn btn-primary btn-lg" type="submit">Search</button>
                                </span>
                            </div> 
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="bg-faded pt80 pb40">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 mb30">
                <div class="icon-center-card">
                    <i class="fa fa-envelope-o"></i>
                    <h3>Full support</h3>
                    <p>
                        Doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                    </p>
                </div>
            </div><!--/col-->
            <div class="col-sm-4 mb30">
                <div class="icon-center-card">
                    <i class="fa fa-map-marker"></i>
                    <h3>More than 1000 places</h3>
                    <p>
                        Doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                    </p>
                </div>
            </div><!--/col-->
            <div class="col-sm-4 mb30">
                <div class="icon-center-card">
                    <i class="fa fa-code"></i>
                    <h3>Free updated</h3>
                    <p>
                        Doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                    </p>
                </div>
            </div><!--/col-->
        </div>
    </div>
</div>
<div class="gray-bg pt80 pb40">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
                <h2>Our Chapters</h2>
                <p>
                    With our over 90 chapters across various institutions of higher learning and numerous alumni at home and abroad, GSF is a big family. Here are some of our chapters
                </p>
            </div>
        </div><!--/row-->
        <div class="row">
            @foreach( Chapter::get()->random(8) as $chapter)
            <div class="col-sm-6 col-md-3 mb30">
                <div class="card-overlay">
                    <img src="{{ asset($chapter->banner ?? 'main/images/chapters/coming-soon.png') }}" style="width:100%;height:170px" class="img-responsive" alt="{{ $chapter->banner }}">
                    <div class="card-hover">
                        <div class="card-content">
                            <h3><a href="{{ route('campus.single', $chapter->id) }}">{{ $chapter->name }}</a></h3>
                            <a class="label view-campus-details label-primary" href="{{ route('campus.single', $chapter->id) }}">View details</a>
                        </div><!--/card-content-->
                    </div>
                </div>
            </div> 
            @endforeach
        </div>
        
        <div class="text-center mb30">
            <a href="{{ route('people.campuses') }}" class="btn btn-lg btn-rounded btn-primary">View All Chapters</a>
        </div>
    </div>
</div>

<div class="dark-bg pt80 pb40 bg-parallax parallax-overlay" data-jarallax='{"speed": 0.2}' style='background-image: url({{ asset('main/images/bg3.jpg') }})'>
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
                <h2>Upcoming GSF Programs</h2>
               
            </div>
        </div>
        <div class="row">
            @foreach($national as $event)
            <div class="col-sm-4 mb40">
                <div class="event-card">
                    <div class="date-icon">
                        <div class="text-center">
                            {{  Carbon\Carbon::parse($event->date)->day }}
                            <span>{{  Carbon\Carbon::parse($event->date)->format('M') }}</span>
                            <span>{{  Carbon\Carbon::parse($event->date)->year }}</span>
                        </div>
                    </div>
                    <span><i class="fa fa-clock-o"></i> {{  Carbon\Carbon::parse($event->time)->format('g:i A') }}</span>
                    <h4 class="mt10"><a href="#">{{ $event->title }}</a></h4>
                    <p>
                        GSF National Program
                    </p>
                    <div class="text-left more-link">
                        <a href="/people/programs/#{{ $event->id }}">View Detail</a>
                    </div>
                </div>
            </div><!--/col-->
            @endforeach
            @foreach($events as $event)
            <div class="col-sm-4 mb40">
                <div class="event-card">
                    <div class="date-icon">
                        <div class="text-center">
                            {{  Carbon\Carbon::parse($event->date)->day }}
                            <span>{{  Carbon\Carbon::parse($event->date)->format('M') }}</span>
                            <span>{{  Carbon\Carbon::parse($event->date)->year }}</span>
                        </div>
                    </div>
                    <span><i class="fa fa-clock-o"></i> {{  Carbon\Carbon::parse($event->time)->format('g:i A') }}</span>
                    <h4 class="mt10"><a href="#">{{ $event->title }}</a></h4>
                    <p>
                        {{ $event->chapter->name }}
                    </p>
                    <div class="text-left more-link">
                        <a href="/people/programs/#{{ $event->id }}">View Detail</a>
                    </div>
                </div>
            </div><!--/col-->
            @endforeach
        </div>
    </div>
</div>

{{-- <div class="gray-bg pt40 pb40 newsletter-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h2>
                    Subscribe and be notified about new locations</h2>
            </div>
            <div class="col-sm-6">
                <div class="newsletter-card">                          
                    <form>
                        <input type="text" class="form-control" placeholder="Enter your Email...">
                        <input type="submit" value="Subscribe" class="newsletter-submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection