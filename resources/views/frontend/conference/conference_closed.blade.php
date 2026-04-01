@extends('frontend.conference.template3.app')

<style>
.speaker-img{
    height:400px;
    object-fit:cover;
    object-position:center;
    display:block;
    width:100%;
    border-radius:6px;
}
</style>

@section('content')

<section id="section-hero"
    class="section-dark no-top no-bottom text-light jarallax relative mh-800"
    style="z-index:0;">

    <img class="jarallax-img"
        src="{{asset('conference_templates/template3/images/background/stars.jpg')}}"
        alt="">

    <div class="gradient-edge-top op-6 h-50 color"></div>
    <div class="gradient-edge-bottom"></div>
    <div class="sw-overlay op-8"></div>

    <div class="abs abs-centered z-2 w-80">
        <div class="container wow scaleIn" data-wow-duration="3s">
            <div class="row">
                <div class="col-lg-12 text-center">

                    <h1 class="fs-100 text-uppercase fs-sm-12vw mb-4 lh-1">
                        {{ $setting->conference_theme }}
                    </h1>

                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <i class="fa fa-calendar id-color me-3"></i>

                        @php
                        $start = \Carbon\Carbon::parse($setting->start_date);
                        $end = \Carbon\Carbon::parse($setting->end_date);

                        if ($start->month === $end->month && $start->year === $end->year) {
                            $dateRange = $start->format('F j') . '–' . $end->format('j, Y');
                        } else {
                            $dateRange = $start->format('F j, Y') . '–' . $end->format('F j, Y');
                        }
                        @endphp

                        <h4 class="mb-0">{{ $dateRange }}</h4>
                    </div>

                    <div class="bg-dark-2 p-4 rounded-1 d-inline-block">
                        <h3 class="text-warning mb-2">Registration Closed</h3>
                        <p class="mb-0">
                            Registration for this conference has officially closed.
                            We look forward to welcoming all registered participants
                            to a powerful and impactful gathering.
                        </p>
                    </div>

                    @if(!empty(conferenceSchedule()))
                    <div class="spacer-single"></div>

                    <a class="btn-main btn-line mx-2 fx-slide"
                        href="{{ url('/').'#section-schedule' }}">
                        <span>View Schedule</span>
                    </a>
                    @endif

                </div>
            </div>
        </div>
    </div>

</section>


<section id="section-about" class="bg-dark section-dark text-light">
<div class="container">
<div class="row gx-5 align-items-center justify-content-between">

<div class="col-lg-6">
<div class="me-lg-5 pe-lg-5 py-5 my-5">

<h2 class="wow fadeInUp">A Divine Gathering for Youths Across Nations</h2>

<p class="wow fadeInUp">
{!! $setting->conference_overview !!}
</p>

</div>
</div>

<div class="col-lg-5">
<div class="wow scaleIn">
<img src="{{ asset($setting->banner) }}" class="w-100">
</div>
</div>

</div>
</div>
</section>


@if($speakers->count() && $setting->speaker_section_status)

<section id="section-speakers" class="bg-dark section-dark text-light">
<div class="container">

<div class="row g-4 justify-content-center">
<div class="col-lg-6 text-center">

<h2 class="wow fadeInUp">Meet the Ministers</h2>

<p class="lead wow fadeInUp">
Anointed men and women of God inspired by the Holy Spirit
to equip and empower believers.
</p>

</div>
</div>

<div class="row g-4">

@foreach ($speakers as $speaker)

<div class="col-lg-4 col-md-6 mb-4">

<div class="hover relative rounded-1 overflow-hidden wow fadeIn scale-in-mask">

<img
src="{{ asset($speaker->image) }}"
class="w-100 hover-scale-1-1 speaker-img"
alt="{{ $speaker->name }}"
>

<div class="abs w-100 start-0 bottom-0 z-3">

<div class="bg-blur p-4 m-4 rounded-1 text-light text-center">

<h3 class="mb-0">{{ $speaker->name }}</h3>

<span>{{ $speaker->title }}</span>

</div>

</div>

</div>

</div>

@endforeach

</div>

</div>
</section>

@endif


@if($schedule->count())

<section id="section-schedule" class="bg-dark section-dark text-light">

<div class="container">

<div class="row g-4 justify-content-center">

<div class="col-lg-6 text-center">

<h2>{{ $schedule->count() }} Days of Spiritual Illumination</h2>

</div>

</div>

<div class="row g-4 gx-5 justify-content-center wow fadeInUp">

<div class="col-lg-12">

<div class="de-tab plain">

<ul class="d-tab-nav mb-4 pb-4 d-flex flex-wrap justify-content-center gap-3">

@foreach($schedule as $day)

<li class="{{ $loop->first ? 'active-tab' : '' }}">

<h3>{{ $day->day }}</h3>

<small>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</small>

</li>

@endforeach

</ul>


<ul class="d-tab-content pt-3">

@foreach($schedule as $day)

<li>

@foreach($day->sessions ?? [] as $session)

@php
$speaker = \App\Models\ConferenceSpeaker::find($session['speaker_id']);
@endphp

<div class="border-white-bottom-op-2 pb-5 mb-5">

<div class="row g-4 align-items-center">

<div class="col-md-2 col-4 text-warning fw-bold">
{{ $session['time'] ?? '' }}
</div>

<div class="col-md-4 col-8">

@if($speaker)

<div class="d-flex align-items-center">

<img src="{{ asset($speaker->image) }}"
class="rounded-1 me-3"
style="height:80px;width:80px;object-fit:cover;">

<div>

<h5 class="mb-1">{{ $speaker->name }}</h5>

<small class="text-muted">{{ $speaker->title }}</small>

</div>

</div>

@endif

</div>

<div class="col-md-6">

<p class="fs-15 mb-0">
{{ $session['description'] ?? '' }}
</p>

</div>

</div>

</div>

@endforeach

</li>

@endforeach

</ul>

</div>

</div>

</div>

</div>

</section>

@endif



<section id="section-tickets"
class="bg-dark section-dark text-light pt-40 relative jarallax">

<img src="{{ asset('conference_templates/template3/images/background/7.webp') }}"
class="jarallax-img">

<div class="sw-overlay op-7"></div>

<div class="container relative z-2">

<div class="row justify-content-center text-center">

<div class="col-lg-6">

<h2>Registration Closed</h2>

<p class="lead">
Registration for this conference has ended.
If you have already registered, we look forward
to seeing you at the event.
</p>

</div>

</div>

<div class="row g-4 justify-content-center mt-4">

@foreach($plans as $plan)

<div class="col-md-4">

<div class="d-ticket p-4 bg-dark-2 rounded-1 text-center h-100">

<img
style="border-radius:50%"
src="{{ asset($setting->conference_logo) }}"
class="w-80px mb-4">

<h2 class="mb-2">{{ $plan->title }}</h2>

<h4 class="mb-4">
{!! currency_symbol() !!}
{{ number_format($plan->price,2) }}
</h4>

<ul class="ul-check mb-4 text-start">

@foreach($plan->items ?? [] as $item)

<li>{{ $item }}</li>

@endforeach

</ul>

<button class="btn-main w-100" disabled
style="opacity:.6;cursor:not-allowed;">
Registration Closed
</button>

</div>

</div>

@endforeach

</div>

</div>

</section>

@endsection
