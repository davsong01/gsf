@extends('frontend.conference.template3.app')
@section('content')
<section id="section-hero" class="section-dark no-top no-bottom text-light jarallax relative mh-800" data-video-src="mp4:video/2.mp4">
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
                              $start_date = "2026-04-17 00:00:00";
                              $end_date = "2026-04-20 00:00:00";

                              $start = \Carbon\Carbon::parse($start_date);
                              $end = \Carbon\Carbon::parse($end_date);

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

                    <a class="btn-main mx-2 fx-slide" href="#section-tickets"><span>Book slot</span></a>
                    <a class="btn-main btn-line mx-2 fx-slide" href="#section-schedule"><span>View Schedule</span></a>
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
                        <img src="{{ asset('conference_templates/template3/images/news/s4.webp')}}" class="w-100 hover-scale-1-1" alt="Inspiring Teachings">
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
                        <img src="{{ asset('conference_templates/template3/images/news/s5.webp') }}" class="w-100 hover-scale-1-1" alt="Fellowship">
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
                        <img src="{{ asset('conference_templates/template3/images/news/s1.webp') }}" class="w-100 hover-scale-1-1" alt="Youth Empowerment">
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
                        <img src="{{ asset('conference_templates/template3/images/news/s2.webp')}}" class="w-100 hover-scale-1-1" alt="Divine Encounter">
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
                <div class="col-lg-4">
                    <div class="hover relative rounded-1 overflow-hidden wow fadeIn scale-in-mask">
                        <img src="{{ asset($speaker['image']) }}" class="w-100 hover-scale-1-1" alt="{{ $speaker['name'] }}">
                        <div class="abs w-100 h-100 start-0 top-0 hover-op-1 radial-gradient-color"></div>
                        <div class="abs w-100 start-0 bottom-0 z-3">
                            <div class="bg-blur p-4 m-4 rounded-1 text-light text-center relative z-2">
                                <h3 class="mb-0">{{ $speaker['name'] }}</h3>
                                <span>{{ $speaker['title'] }}</span>
                            </div>
                            <div class="gradient-edge-bottom h-100 op-8"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


<section id="section-schedule" class="bg-dark section-dark text-light">
    <div class="container">
        <div class="row g-4 gx-5 justify-content-center">
            <div class="col-lg-6 text-center">
                {{-- <div class="subtitle s2 mb-3 wow fadeInUp" data-wow-delay=".0s">Event Schedule</div> --}}
                <h2 class="wow fadeInUp" data-wow-delay=".2s">4 Days of Spiritual Illumination</h2>
            </div>
        </div>

        <div class="row g-4 gx-5 justify-content-center wow fadeInUp">
            <div class="col-lg-12">
                <div class="de-tab plain">
                    {{-- Days Navigation --}}
                    <ul class="d-tab-nav mb-4 pb-4 d-flex justify-content-between">
                        @foreach($schedule as $index => $day)
                            <li class="{{ $loop->first ? 'active-tab' : '' }}">
                                <h3>Day {{ $index + 1 }}</h3>
                                {{ $day['date'] }}
                            </li>
                        @endforeach
                    </ul>

                    {{-- Schedule Content --}}
                    <ul class="d-tab-content pt-3 wow fadeInUp">
                        @foreach($schedule as $day)
                            <li>
                                @foreach($day['sessions'] as $session)
                                    <div class="border-white-bottom-op-2 pb-5 mb-5 {{ $loop->last ? 'pb-5 mb-5' : '' }}">
                                        <div class="row g-4 align-items-center">
                                            <div class="col-md-2">
                                                {{ $session['time'] }}
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset($session['image']) }}" class="w-100px rounded-1 me-4" alt="">
                                                    <div>
                                                        <h5 class="mb-0">{{ $session['speaker'] }}</h5>
                                                        {{ $session['title'] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>{{ $session['title'] }}</h3>
                                                <p class="fs-15 mb-0">{{ $session['description'] }}</p>
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

<section id="section-tickets" class="bg-dark section-dark text-light pt-40 relative jarallax" aria-label="section">
    <img src="images/background/7.webp" class="jarallax-img" alt="">
    <div class="gradient-edge-top"></div>
    <div class="gradient-edge-bottom"></div>
    <div class="sw-overlay op-7"></div>

    <div class="container relative z-2">
        <div class="row g-4 gx-5 justify-content-center">
          <div class="col-lg-6 text-center">
              <h2 class="wow fadeInUp" data-wow-delay=".2s">Secure Your Nayoco 2026 Pass</h2>
              <p class="lead wow fadeInUp" data-wow-delay=".4s">
                  Join us for four days of divine encounters, worship, and transformation. Choose your pass and be part of The Shining Lights experience.
              </p>
          </div>
      </div>


        <div class="row g-4 justify-content-center">
            <div class="col-lg-12">
                <div class="owl-carousel owl-theme owl-3-dots wow mask-right">
                    <!-- ticket item begin -->
                    <div class="item">
                        <div class="d-ticket">
                            <img src="{{ asset('conference_templates/templat3/images/logo.webp') }}" class="w-80px mb-4" alt="">
                            <img src="{{ asset('conference_templates/templat3/images/misc/barcode.webp') }}" class="w-20 p-2 abs abs-middle end-0 me-2" alt="">
                            <img src="{{ asset('conference_templates/templat3/images/logo-big-white.webp') }}" class="w-40 abs abs-centered me-4 op-2" alt="">
                            <h2>Standard</h2>
                            <h4 class="mb-4">$299</h4>
                            <div class="fs-14">October 1 to 5 - 10:00 AM</div>
                        </div>

                        <div class="relative overflow-hidden">
                            <div class="py-4 z-2">
                                <ul class="ul-check mb-4">
                                    <li>Access to keynotes and sessions.</li>
                                    <li>Admission to exhibitions and demos.</li>
                                    <li>Networking opportunities.</li>
                                    <li>Digital materials and session recordings.</li>
                                </ul>
                            </div>

                            <a class="btn-main fx-slide w-100" href="tickets.html"><span>Buy Ticket</span></a>
                            
                        </div>
                    </div>
                    <!-- ticket item end -->

                    <!-- ticket item begin -->
                    <div class="item">
                        <div class="d-ticket">
                            <img src="images/logo.webp" class="w-80px mb-4" alt="">
                            <img src="images/misc/barcode.webp" class="w-20 p-2 abs abs-middle end-0 me-2" alt="">
                            <img src="images/logo-big-white.webp" class="w-40 abs abs-centered me-4 op-2" alt="">
                            <h2>VIP</h2>
                            <h4 class="mb-4">$699</h4>
                            <div class="fs-14">October 1 to 5 - 10:00 AM</div>
                        </div>
                        <div class="relative">
                            <div class="py-4 z-2">
                                <ul class="ul-check mb-4">
                                    <li>All Standard benefits.</li>
                                    <li>VIP lounge access and exclusive events.</li>
                                    <li>Front-row seating and priority workshop access.</li>
                                    <li>VIP swag bag and exclusive content.</li>
                                </ul>
                            </div>
                        </div>

                        <a class="btn-main fx-slide w-100" href="tickets.html"><span>Buy Ticket</span></a>
                    </div>
                    <!-- ticket item end -->

                    <!-- ticket item begin -->
                    <div class="item">
                        <div class="d-ticket s2">
                            <img src="images/logo.webp" class="w-80px mb-4" alt="">
                            <img src="images/misc/barcode.webp" class="w-20 p-2 abs abs-middle end-0 me-2" alt="">
                            <img src="images/logo-big-white.webp" class="w-40 abs abs-centered me-4 op-2" alt="">
                            <h2>Full Access</h2>
                            <h4 class="mb-4">$1199</h4>
                            <div class="fs-14">October 1 to 5 - 10:00 AM</div>
                        </div>
                        <div class="relative">
                            <div class="py-4 z-2">
                                <ul class="ul-check mb-4">
                                    <li>All VIP benefits.</li>
                                    <li>Access to all workshops and breakout sessions.</li>
                                    <li>Personalized session scheduling.</li>
                                    <li>Speaker meet-and-greet and after-party access.</li>
                                </ul>
                            </div>
                        </div>

                        <a class="btn-main fx-slide w-100" href="tickets.html"><span>Buy Ticket</span></a>
                    </div>
                    <!-- ticket item end -->

                    <!-- ticket item begin -->
                    <div class="item">
                        <div class="d-ticket s2">
                            <img src="images/logo.webp" class="w-80px mb-4" alt="">
                            <img src="images/misc/barcode.webp" class="w-20 p-2 abs abs-middle end-0 me-2" alt="">
                            <img src="images/logo-big-white.webp" class="w-40 abs abs-centered me-4 op-2" alt="">
                            <h2>Exclusive Access</h2>
                            <h4 class="mb-4">$2499</h4>
                            <div class="fs-14">October 1 to 5 - 10:00 AM</div>
                        </div>
                        <div class="relative">
                            <div class="py-4 z-2">
                                <ul class="ul-check mb-4">
                                    <li>All Full Access Pass benefits.</li>
                                    <li>Private one-on-one sessions with speakers.</li>
                                    <li>Priority access to all events and workshops.</li>
                                    <li>Exclusive VIP gala and after-party invitations.</li>
                                </ul>
                            </div>
                        </div>

                        <a class="btn-main fx-slide w-100" href="tickets.html"><span>Buy Ticket</span></a>
                    </div>
                    <!-- ticket item end -->

                    <!-- ticket item begin -->
                    <div class="item">
                        <div class="d-ticket s3">
                            <img src="images/logo.webp" class="w-80px mb-4" alt="">
                            <img src="images/misc/barcode.webp" class="w-20 p-2 abs abs-middle end-0 me-2" alt="">
                            <img src="images/logo-big-white.webp" class="w-40 abs abs-centered me-4 op-2" alt="">
                            <h2>Student</h2>
                            <h4 class="mb-4">$149</h4>
                            <div class="fs-14">October 1 to 5 - 10:00 AM</div>
                        </div>
                        <div class="relative">
                            <div class="py-4 z-2">
                                <ul class="ul-check mb-4">
                                    <li>Access to keynotes and workshops.</li>
                                    <li>Student-specific networking events.</li>
                                    <li>Discounted online resources post-event.</li>
                                    <li>Special student meetups for networking.</li>
                                </ul>
                            </div>
                        </div>

                        <a class="btn-main fx-slide w-100" href="tickets.html"><span>Buy Ticket</span></a>
                    </div>
                    <!-- ticket item end -->

                    <!-- ticket item begin -->
                    <div class="item">
                        <div class="d-ticket s3">
                            <img src="images/logo.webp" class="w-80px mb-4" alt="">
                            <img src="images/misc/barcode.webp" class="w-20 p-2 abs abs-middle end-0 me-2" alt="">
                            <img src="images/logo-big-white.webp" class="w-40 abs abs-centered me-4 op-2" alt="">
                            <h2>Virtual</h2>
                            <h4 class="mb-4">$99</h4>
                            <div class="fs-14">October 1 to 5 - 10:00 AM</div>
                        </div>
                        <div class="relative">
                            <div class="py-4 z-2">
                                <ul class="ul-check mb-4">
                                    <li>Live-streamed keynotes and workshops.</li>
                                    <li>On-demand access to recorded sessions.</li>
                                    <li>Interactive Q&A with speakers.</li>
                                    <li>Virtual networking and digital swag.</li>
                                </ul>
                            </div>
                        </div>

                        <a class="btn-main fx-slide w-100" href="tickets.html"><span>Buy Ticket</span></a>
                    </div>
                    <!-- ticket item end -->
                </div>
            </div>
        </div>
    </div>
</section>

<section id="section-venue" class="bg-dark section-dark text-light pt-40 relative jarallax" aria-label="section">
  <div class="container relative z-2">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-6 text-center">
            <h2 class="wow fadeInUp" data-wow-delay=".2s">Location & Venue</h2>
            <p class="lead wow fadeInUp" data-wow-delay=".6s">Join us at Gospel CIty - Lagos–Ibadan Exp Way, Ogunmakin, Ogun State, Nigeria</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-sm-6">
            <img src="{{ asset('conference_templates/template3/images/misc/l1.webp')}}" class="w-100 rounded-1 wow scale-in-mask" alt="">
        </div>

        <div class="col-sm-6">
            <img src="{{ asset('conference_templates/template3/images/misc/l2.webp')}} " class="w-100 rounded-1 wow scale-in-mask" alt="">
        </div>

        <div class="clearfix"></div>

        <div class="col-lg-4 col-md-6 mb-sm-30">
            <div class="d-flex justify-content-center wow fadeInUp" data-wow-delay=".2s">
                <i class="fs-60 id-color icofont-google-map"></i>
                <div class="ms-3">
                    <h4 class="mb-0">Address</h4>
                    <p>Gospel City. Lagos Ibadan Expressway. Ogunmakin. Ogun state.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-sm-30">
            <div class="d-flex justify-content-center wow fadeInUp" data-wow-delay=".4s">
                <i class="fs-60 id-color icofont-phone"></i>
                <div class="ms-3">
                    <h4 class="mb-0">Phone</h4>
                    <p>Call: {{ $setting->official_phone}}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-sm-30">
            <div class="d-flex justify-content-center wow fadeInUp" data-wow-delay=".6s">
                <i class="fs-60 id-color icofont-envelope"></i>
                <div class="ms-3">
                    <h4 class="mb-0">Email</h4>
                    <p>{{ $setting->official_email }}</p>
                </div>
            </div>
        </div>
    </div>

  </div>
</section>

@if(!empty($faqs))
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