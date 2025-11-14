@extends('frontend.conference.template3.app')
<style>
    .speaker-img {
        height: 400px;
        object-fit: cover;
        object-position: center;
        display: block;
        width: 100%;
        border-radius: 6px;
    }

</style>
@section('content')
<section id="section-hero" class="section-dark no-top no-bottom text-light jarallax relative mh-800">
    <div class="gradient-edge-top op-6 h-50 color"></div>
    <div class="gradient-edge-bottom"></div>
    <div class="sw-overlay op-8"></div>
    <div class="abs abs-centered z-2 w-80">
        <div class="container wow scaleIn" data-wow-duration="3s">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 class="fs-100 text-uppercase fs-sm-12vw mb-4 lh-1">{{ $setting->conference_theme }}</h1>

                    <div class="d-block d-md-flex justify-content-center">
                        <div class="d-flex justify-content-center align-items-center mx-4">
                            <i class="fa fa-calendar id-color me-3"></i>
                            <?php
                                $start = \Carbon\Carbon::parse($setting->start_date);
                                $end = \Carbon\Carbon::parse($setting->end_date);

                                if ($start->month === $end->month && $start->year === $end->year) {
                                    $dateRange = $start->format('F j') . '–' . $end->format('j, Y');
                                } else {
                                    $dateRange = $start->format('F j, Y') . '–' . $end->format('F j, Y');
                                }
                            ?>
                            <h4 class="mb-0">{{ $dateRange }}</h4>
                        </div>
                    </div>

                    <div class="spacer-single"></div>
                    <a class="btn-main mx-2 fx-slide" href="{{ url('/').'#section-tickets' }}"><span>Book slot</span></a>
                    @if(!empty(conferenceSchedule()))
                        <a class="btn-main btn-line mx-2 fx-slide" href="{{ url('/').'#section-schedule' }}"><span>View Schedule</span></a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="abs w-100 start-0 bottom-0 z-3">
        <div class="container">
            <div class="sm-hide border-white-op-3 p-40 py-4 rounded-1 bg-blur relative overflow-hidden wow fadeInUp">
                <div class="gradient-edge-bottom color start-0 h-50 op-5"></div>
                <div class="row g-4 justify-content-between align-items-center relative z-2">
                    <div class="col-lg-3">
                        <h2 class="mb-0">Hurry Up!</h2>
                        <h4 class="mb-0">Reserve Your Spot Today</h4>
                    </div>
                    <div class="col-lg-4">
                      <div id="defaultCountdown" class="pt-2"></div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="d-flex">
                            <i class="fs-60 icofont-google-map id-color"></i>
                            <div class="ms-3">
                                <h4 class="mb-0">Lagos–Ibadan Exp Way,<br> Ogunmakin, Ogun State, Nigeria</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="section-about" class="bg-dark section-dark text-light">
    <div class="container">
        <div class="row  gx-5 align-items-center justify-content-between">
            <div class="col-lg-6">
                  <div class="me-lg-5 pe-lg-5 py-5 my-5">
                      <h2 class="wow fadeInUp" data-wow-delay=".4s">A Divine Gathering for Youths Across Nations</h2>
                      <p class="wow fadeInUp" data-wow-delay=".6s">{!! $setting->conference_overview !!}</p>
                  </div>
            </div>

            <div class="col-lg-5">
                <div class="wow scaleIn">
                    <img src="{{ asset($setting->banner) }}" class="w-100" alt="">
                </div>
            </div>

        </div>
    </div>
</section>

<section id="section-why-attend" class="bg-dark section-dark text-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6 offset-lg-3 text-center">
                <h2 class="wow fadeInUp" data-wow-delay=".2s">Why You Should Attend</h2>
                <p class="lead mb-0 wow fadeInUp">Experience divine transformation, spiritual renewal, and fellowship that ignites your light to shine brighter for Christ.</p>
            </div>
        </div>

        <div class="spacer-single"></div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="hover">
                    <div class="bg-dark-2 relative rounded-1 overflow-hidden hover-bg-color hover-text-light wow scale-in-mask">
                        <div class="abs p-40 bottom-0 z-2">
                            <div class="relative wow fadeInUp">
                                <h4>Spiritual Renewal</h4>
                                <p class="mb-0">Be refreshed through powerful worship, impactful sermons, and life-transforming encounters with God.</p>
                            </div>
                        </div>
                        <div class="gradient-edge-bottom h-100"></div>
                        <img src="{{ asset('conference_templates/template3/images/news/s3.webp') }}" class="w-100 hover-scale-1-1" alt="Spiritual Renewal">
                        <div class="abs w-100 h-100 start-0 top-0 hover-op-1 radial-gradient-color"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="hover">
                    <div class="bg-dark-2 relative rounded-1 overflow-hidden hover-bg-color hover-text-light wow scale-in-mask">
                        <div class="abs p-40 bottom-0 z-2">
                            <div class="relative wow fadeInUp">
                                <h4>Inspiring Teachings</h4>
                                <p class="mb-0">Learn from seasoned ministers and leaders who will equip you to shine as a light in your generation.</p>
                            </div>
                        </div>
                        <div class="gradient-edge-bottom h-100"></div>
                        <img src="{{ asset('conference_templates/template3/images/news/s3.webp')}}" class="w-100 hover-scale-1-1" alt="Inspiring Teachings">
                        <div class="abs w-100 h-100 start-0 top-0 hover-op-1 radial-gradient-color"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="hover">
                    <div class="bg-dark-2 relative rounded-1 overflow-hidden hover-bg-color hover-text-light wow scale-in-mask">
                        <div class="abs p-40 bottom-0 z-2">
                            <div class="relative wow fadeInUp">
                                <h4>Life-Changing Fellowship</h4>
                                <p class="mb-0">Connect with believers from across Nigeria and beyond in a spirit-filled atmosphere of unity and love.</p>
                            </div>
                        </div>
                        <div class="gradient-edge-bottom h-100"></div>
                        <img src="{{ asset('conference_templates/template3/images/news/s3.webp') }}" class="w-100 hover-scale-1-1" alt="Fellowship">
                        <div class="abs w-100 h-100 start-0 top-0 hover-op-1 radial-gradient-color"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="hover">
                    <div class="bg-dark-2 relative rounded-1 overflow-hidden hover-bg-color hover-text-light wow scale-in-mask">
                        <div class="abs p-40 bottom-0 z-2">
                            <div class="relative wow fadeInUp">
                                <h4>Youth Empowerment</h4>
                                <p class="mb-0">Discover your purpose and be empowered to lead with excellence, integrity, and the light of Christ.</p>
                            </div>
                        </div>
                        <div class="gradient-edge-bottom h-100"></div>
                        <img src="{{ asset('conference_templates/template3/images/news/s3.webp') }}" class="w-100 hover-scale-1-1" alt="Youth Empowerment">
                        <div class="abs w-100 h-100 start-0 top-0 hover-op-1 radial-gradient-color"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="hover">
                    <div class="bg-dark-2 relative rounded-1 overflow-hidden hover-bg-color hover-text-light wow scale-in-mask">
                        <div class="abs p-40 bottom-0 z-2">
                            <div class="relative wow fadeInUp">
                                <h4>Divine Encounters</h4>
                                <p class="mb-0">Experience the presence of God in worship, prayers, and sessions designed to reignite your spiritual fire.</p>
                            </div>
                        </div>
                        <div class="gradient-edge-bottom h-100"></div>
                        <img src="{{ asset('conference_templates/template3/images/news/s3.webp')}}" class="w-100 hover-scale-1-1" alt="Divine Encounter">
                        <div class="abs w-100 h-100 start-0 top-0 hover-op-1 radial-gradient-color"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="hover">
                    <div class="bg-dark-2 relative rounded-1 overflow-hidden hover-bg-color hover-text-light wow scale-in-mask">
                        <div class="abs p-40 bottom-0 z-2">
                            <div class="relative wow fadeInUp">
                                <h4>The Shining Lights Mandate</h4>
                                <p class="mb-0">Be part of a movement to shine God’s light in every sphere of life—academics, ministry, business, and leadership.</p>
                            </div>
                        </div>
                        <div class="gradient-edge-bottom h-100"></div>
                        <img src="{{ asset('conference_templates/template3/images/news/s3.webp') }}" class="w-100 hover-scale-1-1" alt="Shining Lights Mandate">
                        <div class="abs w-100 h-100 start-0 top-0 hover-op-1 radial-gradient-color"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@if($speakers->count() && $setting->speaker_section_status)
<section id="section-speakers" class="bg-dark section-dark text-light">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-6 relative z-3">
                <div class="text-center">
                    <h2 class="wow fadeInUp" data-wow-delay=".2s">Meet the Ministers</h2>
                    <p class="lead wow fadeInUp">Anointed men and women of God inspired by the Holy Spirit to equip, empower, and ignite your light for Kingdom impact.</p>
                </div>
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
                        <div class="abs w-100 h-100 start-0 top-0 hover-op-1 radial-gradient-color"></div>
                        <div class="abs w-100 start-0 bottom-0 z-3">
                            <div class="bg-blur p-4 m-4 rounded-1 text-light text-center relative z-2">
                                <h3 class="mb-0">{{ $speaker->name }}</h3>
                                <span>{{ $speaker->title }}</span>
                            </div>
                            <div class="gradient-edge-bottom h-100 op-8"></div>
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
        <div class="row g-4 gx-5 justify-content-center">
            <div class="col-lg-6 text-center">
                <h2 class="wow fadeInUp" data-wow-delay=".2s">{{ $schedule->count() }} Days of Spiritual Illumination</h2>
            </div>
        </div>

        <div class="row g-4 gx-5 justify-content-center wow fadeInUp">
            <div class="col-lg-12">
                <div class="de-tab plain">
                    {{-- Days Navigation --}}
                    <ul class="d-tab-nav mb-4 pb-4 d-flex flex-wrap justify-content-center gap-3">
                        @foreach($schedule as $index => $day)
                            <li class="{{ $loop->first ? 'active-tab' : '' }}">
                                <h3>{{ $day->day }}</h3>
                                <small>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</small>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Schedule Content --}}
                    <ul class="d-tab-content pt-3 wow fadeInUp">
                        @foreach($schedule as $day)
                            @php
                                $sessions = $day->sessions ?? [];
                            @endphp

                            <li>
                                @forelse($sessions as $session)
                                    @php
                                        $speaker = \App\Models\ConferenceSpeaker::find($session['speaker_id']);
                                    @endphp

                                    <div class="border-white-bottom-op-2 pb-5 mb-5 {{ $loop->last ? 'pb-5 mb-5' : '' }}">
                                        <div class="row g-4 align-items-center">
                                            <div class="col-md-2 col-4 text-warning fw-bold">
                                                {{ $session['time'] ?? '' }}
                                            </div>

                                            <div class="col-md-4 col-8">
                                                @if($speaker)
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset($speaker->image) }}" 
                                                            alt="{{ $speaker->name }}" 
                                                            class="rounded-1 me-3" 
                                                            style="height:80px; width:80px; object-fit:cover;">
                                                        <div>
                                                            <h5 class="mb-1">{{ $speaker->name }}</h5>
                                                            <small class="text-muted">{{ $speaker->title }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <em>No speaker info</em>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <p class="fs-15 mb-0">{{ $session['description'] ?? '' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center">No sessions scheduled for this day.</p>
                                @endforelse
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
{{-- @if($plans->count()) --}}
<section id="section-tickets" class="bg-dark section-dark text-light pt-40 relative jarallax" aria-label="section">
    <img src="{{ asset('conference_templates/template3/images/background/7.webp') }}" class="jarallax-img" alt="">
    <div class="gradient-edge-top"></div>
    <div class="gradient-edge-bottom"></div>
    <div class="sw-overlay op-7"></div>

    <div class="container relative z-2">
        <div class="row g-4 gx-5 justify-content-center">
            <div class="col-lg-6 text-center">
                <h2 class="wow fadeInUp" data-wow-delay=".2s">Secure Your {{$setting->conference_theme}} Pass</h2>
                <p class="lead wow fadeInUp" data-wow-delay=".4s">
                    Join us for divine encounters, worship, and transformation. Choose your pass and be part of The Shining Lights experience.
                </p>
            </div>
        </div>

        <div class="row g-4 justify-content-center mt-4">
            @foreach($plans as $plan)
                @php
                    $items = $plan->items ?? [];
                @endphp

                <div class="col-md-4">
                    <div class="d-ticket p-4 bg-dark-2 rounded-1 text-center h-100 position-relative wow fadeInUp">
                        <img style="border-radius: 50%;" 
                            src="{{ asset($setting->conference_logo) }}" 
                            class="w-80px mb-4" 
                            alt="">

                        <h2 class="mb-2">{{ $plan->title }}</h2>
                        <h4 class="mb-4">&#8358;{{ number_format($plan->price, 2) }}</h4>

                        <ul class="ul-check mb-4 text-start">
                            @foreach($items ?? [] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                        
                        <a class="btn-main fx-slide w-100 mt-auto" href="{{ route('conference.registration', $plan->id) }}">
                            <span>Get Started</span>
                        </a>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section>
{{-- @endif --}}

@if(!empty($faqs) && $setting->faq_section_status)
<section  id="section-faq" class="bg-dark section-dark text-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <h2 class="wow fadeInUp" data-wow-delay=".2s">Frequently Asked Questions</h2>
            </div>

            <div class="col-lg-7">
              <div class="accordion s2 wow fadeInUp">
                <div class="accordion-section">
                  @foreach ($faqs as $faq)
                      <div class="accordion-section-title" data-tab="#{{ $faq['id'] }}">
                          {{ $faq['question'] }}
                      </div>
                      <div class="accordion-section-content" id="{{ $faq['id'] }}">
                          {!! $faq['answer'] !!}
                      </div>
                  @endforeach
              </div>                      
            </div>
        </div>
    </div>
</section>
@endif
@endsection
@section('script')
@php
    use Carbon\Carbon;
    $start = Carbon::parse($setting->start_date);
@endphp

<script>
jQuery(document).ready(function($) {
    $('#defaultCountdown').countdown({
        until: new Date(
            {{ $start->year }},     
            {{ $start->month - 1 }},
            {{ $start->day }},      
            {{ $start->hour }},     
            {{ $start->minute }},   
            {{ $start->second }}    
        ),
        onExpiry: function() {
            $('#defaultCountdown')
              .removeClass('pt-2')
              .html('<h5 class="text-light mt-3 text-center">Event Started</h5>');
        }
    });
});
</script>
@endsection
@section('facebook', $setting->facebook)
@section('facebook_event_page', $setting->facebook_event_page)
@section('instagram', $setting->instagram)
@section('telegram', $setting->telegram)