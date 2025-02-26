@extends('frontend.conference.template2.app')
@section('content')
      <!-- banner start-->
      <section class="hero-area">
         <div class="banner-item" style="background-image:url('{{ asset('conference_templates/template2/images/hero_area/banner_bg.jpg')}}')">
            <div class="container">
               <div class="row">
      <div class="col-lg-8">
          <div class="banner-content-wrap">

            <p class="banner-info wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="500ms">{{$setting->slug }}</p>
            <h1 class="banner-title wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="700ms" style="margin-bottom: 0px;">{{ $setting->conference_theme }}</h1>
            <p class="banner-info wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="500ms" style="margin-bottom: 50px;color:yellow">{{ formatDates($setting->start_date, $setting->end_date) }}</p>
            
            <div class="countdown wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="800ms">
                <div class="counter-item">
                  <i class="icon icon-ring-1Asset-1"></i>
                  <span class="days">00</span>
                  <div class="smalltext">Days</div>

                </div>
                <div class="counter-item">
                  <i class="icon icon-ring-4Asset-3"></i>
                  <span class="hours">00</span>
                  <div class="smalltext">Hours</div>
                </div>
                <div class="counter-item">
                  <i class="icon icon-ring-3Asset-2"></i>
                  <span class="minutes">00</span>
                  <div class="smalltext">Minutes</div>
                </div>
                <div class="counter-item">
                  <i class="icon icon-ring-4Asset-3"></i>
                  <span class="seconds">00</span>
                  <div class="smalltext">Seconds</div>
                </div>
            </div>
            <!-- Countdown end -->
            <div class="banner-btn wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="800ms">
                <a href="#register" class="btn">Register now</a>
            </div>

          </div>
          <!-- Banner content wrap end -->
      </div><!-- col end-->
      <div class="col-lg-4 align-self-end">
          <div class="banner-img">
            {{-- <img src="{{ asset('conference_templates/template2/images/hero_area/banner_img.png') }}" alt=""> --}}
          </div>
      </div>
    </div><!-- row end-->
</div>
<!-- Container end -->
</div>
<!-- banner slice image-->
<div class="tiles">
<div class="tile" data-scale="1.1" data-image="{{ asset('conference_templates/template2/images/hero_area/banner_slices.png')}}"></div>
</div>
</section>
<!-- banner end-->

<!-- ts intro start -->
    
<section id="details" class="ts-intro-item section-bg">
    <div class="container">
      <div class="row">
          <div class="col-lg-4 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="300ms">
            <div class="intro-left-content">
                <h2 class="column-title">
                  <span>Unlock the Future</span>
                  Why Attend The {{ $setting->conference_theme }} Conference
                </h2>
                <p>
                  Experience transformative spiritual growth, powerful prayer sessions, marital, professional, academic, business and leadership insights that will distinguish you as a student or young adult with an aim of deepening your faith in Christ, connect you to a vital and supportive community, gain soft skills which will in turn foster holistic development.
                </p>
                <a href="#register" class="btn">Register Now</a>
            </div>
          </div><!-- col end-->
          <div class="col-lg-8">
            <div class="row">
                <div class="col-lg-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="400ms">
                  <div class="single-intro-text mb-30">
                      <i class="icon icon-speaker"></i>
                      <h3 class="ts-title">Great Speakers</h3>
                      <p>
                        Experience profound teachings and guidance from esteemed spiritual and quality ministers at the Oracle Conference. <br><br>
                      </p>
                      <span class="count-number">01</span>
                  </div><!-- single intro text end-->
                </div><!-- col end-->
                <div class="col-lg-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="500ms">
                  <div class="single-intro-text mb-30">
                      <i class="icon icon-netwrorking"></i>
                      <h3 class="ts-title">Spiritual discipline and discipleship</h3>
                      <p>
                        Connect with like-minded individuals and share your values at the Oracle Conference. Build lasting friendships and a supportive community.
                      </p>
                      <span class="count-number">02</span>
                  </div><!-- single intro text end-->

                </div><!-- col end-->
                <div class="col-lg-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="600ms">
                  <div class="single-intro-text mb-30">
                      <i class="icon icon-people"></i>
                      <h3 class="ts-title">Networking (which will house having fun, meeting new people)</h3>
                      <p>
                        Network with global peers and industry leaders at the Oracle Conference.
                      </p>
                      <span class="count-number">03</span>
                  </div><!-- single intro text end-->
                </div><!-- col end-->
                <div class="col-lg-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="700ms">
                  <div class="single-intro-text mb-30">
                      <i class="icon icon-fun"></i>
                      <h3 class="ts-title">Unforgettable experiences and encounters</h3>
                      <p>
                        Experience unparalleled fun and excitement at the Oracle Conference <br><br>
                      </p>
                      <span class="count-number">04</span>
                  </div><!-- single intro text end-->
                </div><!-- col end-->
            </div>
          </div><!-- col end-->

      </div><!-- row end-->
    </div><!-- container end-->
</section>
<!-- ts intro end-->

<!-- ts speaker start-->
{{-- <section id="ts-speakers" class="ts-speakers" style="background-image:url(images/speakers/speaker_bg.png)">
    <div class="container">
      <div class="row">
          <div class="col-lg-8 mx-auto">
            <h2 class="section-title text-center">
                <span>Key</span>Speakers
            </h2>
          </div><!-- col end-->
      </div><!-- row end-->
      <div class="row">
          <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="400ms">
            <div class="ts-speaker">
                <div class="speaker-img">
                  <img class="img-fluid" src="{{ asset('conference_templates/template2/images/speakers/speaker1.jpg')}}" alt="">
                  <a href="#popup_1" class="view-speaker ts-image-popup" data-effect="mfp-zoom-in">
                      <i class="icon icon-plus"></i>
                  </a>
                </div>
                <div class="ts-speaker-info">
                  <h3 class="ts-title"><a href="#">David Robert</a></h3>
                  <p>
                      Founder, Btech Ltd
                  </p>
                </div>
            </div>
            <!-- popup start-->
            <div id="popup_1" class="container ts-speaker-popup mfp-hide">
                <div class="row">
                  <div class="col-lg-6">
                      <div class="ts-speaker-popup-img">
                        <img src="{{ asset('conference_templates/template2/images/speakers/speaker1.jpg')}}" alt="">
                      </div>
                  </div><!-- col end-->
                  <div class="col-lg-6">
                      <div class="ts-speaker-popup-content">
                        <h3 class="ts-title">David Robert</h3>
                        <span class="speakder-designation">Cheif Architecture</span>
                        <p>
                            Some biography extract... World is committed to making participation in the event a harass ment free experience
                            for everyone, regardless of level experience gender, gender identity and expression
                        </p>
                        <h4 class="session-name">
                            Sessions by David
                        </h4>
                        <div class="row">
                            <div class="col-lg-12">
                              <div class="speaker-session-info">
                                
                                  <span>Go in this thy Might </span>
                                  <p>
                                    (Judges 6:14)
                                  </p>
                              </div>
                            </div>
                            
                        </div>
                        <div class="ts-speakers-social">
                            <a target="_blank" href="#"><i class="fa fa-facebook"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-twitter"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-instagram"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-google-plus"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-linkedin"></i></a>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
          </div> <!-- col end-->
          <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="500ms">
            <div class="ts-speaker">
                <div class="speaker-img">
                  <img class="img-fluid" src="{{ asset('conference_templates/template2/images/speakers/speaker2.jpg')}}" alt="">
                  <a href="#popup_2"  class="view-speaker ts-image-popup" data-effect="mfp-zoom-in"><i class="icon icon-plus"></i></a>
                </div>
                <div class="ts-speaker-info">
                  <h3 class="ts-title"><a href="#">David Roberts</a></h3>
                  <p>
                      Lead Designer, Payol
                  </p>
                </div>
            </div>
            <!-- popup start-->
            <div id="popup_2" class="container ts-speaker-popup mfp-hide">
                <div class="row">
                  <div class="col-lg-6">
                      <div class="ts-speaker-popup-img">
                        <img src="{{ asset('conference_templates/template2/images/speakers/speaker2.jpg')}}" alt="">
                      </div>
                  </div><!-- col end-->
                  <div class="col-lg-6">
                      <div class="ts-speaker-popup-content">
                        <h3 class="ts-title">David Robert</h3>
                        <span class="speakder-designation">Cheif Architecture</span>
                        <p>
                            Some biography extract... World is committed to making participation in the event a harass ment free experience
                            for everyone, regardless of level experience gender, gender identity and expression
                        </p>
                        <h4 class="session-name">
                            Sessions by David
                        </h4>
                        <div class="row">
                            <div class="col-lg-12">
                              <div class="speaker-session-info">
                                
                                  <span>Go in this thy Might </span>
                                  <p>
                                    (Judges 6:14)
                                  </p>
                              </div>
                            </div>
                            
                        </div>
                        <div class="ts-speakers-social">
                            <a target="_blank" href="#"><i class="fa fa-facebook"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-twitter"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-instagram"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-google-plus"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-linkedin"></i></a>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
          </div> <!-- col end-->
          <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="600ms">
            <div class="ts-speaker">
                <div class="speaker-img">
                  <img class="img-fluid" src="{{ asset('conference_templates/template2/images/speakers/speaker3.jpg')}}" alt="">
                  <a href="#popup_3" class="view-speaker ts-image-popup" data-effect="mfp-zoom-in">
                  <i class="icon icon-plus"></i></a>
                </div>
                <div class="ts-speaker-info">
                  <h3 class="ts-title"><a href="#">Sewanu Oriyomi</a></h3>
                  <p>
                      Developer Expert
                  </p>
                </div>
            </div>
            <!-- popup start-->
            <div id="popup_3" class="container ts-speaker-popup mfp-hide">
                <div class="row">
                  <div class="col-lg-6">
                      <div class="ts-speaker-popup-img">
                        <img src="{{ asset('conference_templates/template2/images/speakers/speaker3.jpg')}}" alt="">
                      </div>
                  </div><!-- col end-->
                  <div class="col-lg-6">
                      <div class="ts-speaker-popup-content">
                        <h3 class="ts-title">David Robert</h3>
                        <span class="speakder-designation">Cheif Architecture</span>
                        <p>
                            Some biography extract... World is committed to making participation in the event a harass ment free experience
                            for everyone, regardless of level experience gender, gender identity and expression
                        </p>
                        <h4 class="session-name">
                            Sessions by David
                        </h4>
                        <div class="row">
                            <div class="col-lg-12">
                              <div class="speaker-session-info">
                                
                                  <span>Go in this thy Might </span>
                                  <p>
                                    (Judges 6:14)
                                  </p>
                              </div>
                            </div>
                            
                        </div>
                        <div class="ts-speakers-social">
                            <a target="_blank" href="#"><i class="fa fa-facebook"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-twitter"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-instagram"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-google-plus"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-linkedin"></i></a>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
          </div> <!-- col end-->
          <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="700ms">
            <div class="ts-speaker">
                <div class="speaker-img">
                  <img class="img-fluid" src="{{ asset('conference_templates/template2/images/speakers/speaker1.jpg')}}" alt="">
                  <a href="#popup_4" class="view-speaker ts-image-popup" data-effect="mfp-zoom-in">
                              <i class="icon icon-plus"></i>
                          </a>
                </div>
                <div class="ts-speaker-info">
                  <h3 class="ts-title"><a href="#">Semedoh Henriken</a></h3>
                  <p>
                      Founder, Cards
                  </p>
                </div>
            </div>
            <!-- popup start-->
            <div id="popup_4" class="container ts-speaker-popup mfp-hide">
                <div class="row">
                  <div class="col-lg-6">
                      <div class="ts-speaker-popup-img">
                        <img src="{{ asset('conference_templates/template2/images/speakers/speaker1.jpg')}}" alt="">
                      </div>
                  </div><!-- col end-->
                  <div class="col-lg-6">
                      <div class="ts-speaker-popup-content">
                        <h3 class="ts-title">David Robert</h3>
                        <span class="speakder-designation">Cheif Architecture</span>
                        <p>
                            Some biography extract... World is committed to making participation in the event a harass ment free experience
                            for everyone, regardless of level experience gender, gender identity and expression
                        </p>
                        <h4 class="session-name">
                            Sessions by David
                        </h4>
                        <div class="row">
                            <div class="col-lg-12">
                              <div class="speaker-session-info">
                                  <span>Go in this thy Might </span>
                                  <p>
                                    (Judges 6:14)
                                  </p>
                              </div>
                            </div>
                        </div>
                        <div class="ts-speakers-social">
                            <a target="_blank" href="#"><i class="fa fa-facebook"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-twitter"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-instagram"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-google-plus"></i></a>
                            <a target="_blank" href="#"><i class="fa fa-linkedin"></i></a>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
          </div>

          
      </div>
    </div>

    <!-- shap img-->
    <div class="speaker-shap">
      <img class="shap1" src="{{ asset('conference_templates/template2/images/shap/home_speaker_memphis1.png')}}" alt="">
      <img class="shap2" src="{{ asset('conference_templates/template2/images/shap/home_speaker_memphis2.png')}}" alt="">
      <img class="shap3" src="{{ asset('conference_templates/template2/images/shap/home_speaker_memphis3.png')}}" alt="">
    </div>
    <!-- shap img end-->
</section> --}}
<!-- ts speaker end-->

<!-- ts experience start-->
<section id="ts-experiences" class="ts-experiences">
    <div class="container-fluid">
      <div class="row">
          <div class="col-lg-6 no-padding">


            <div class="exp-img image-container">
              <img class="img-fluid" src="{{ asset($setting->banner)}}" alt=""> 
            </div>
          </div><!-- col end-->
          <div class="col-lg-6 no-padding align-self-center wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="500ms">
            <div class="ts-exp-wrap">
                <div class="ts-exp-content">
                  <h2 class="column-title">
                      
                      <span>Get Experience</span>
                      Shift your perspective on
                      life perspectives
                  </h2>
                  <p>
                      How  you transform your business as technology, consumer, habits industry dynamic s change? Find out from those leading the charge.
                  </p>
                </div>
            </div>

          </div><!-- col end-->
      </div><!-- row end-->
    </div><!-- container fluid end-->
</section>
<!-- ts experience end-->

<!-- ts experience start-->
  <section class="ts-schedule">
    <div class="container">
      <div class="row">
          <div class="col-lg-8 mx-auto">
            <h2 class="section-title">
                <span>Schedule Details</span>
                Event Schedules
            </h2>
            <div class="ts-schedule-nav">
                <ul class="nav nav-tabs justify-content-center" role="tablist">
                  <li class="nav-item">
                      <a class="active" title="Click Me" href="#day1" role="tab" data-toggle="tab">
                        <h3>17th April</h3>
                        <span>Thursday</span>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="" href="#date2" title="Click Me" role="tab" data-toggle="tab">        
                        <h3>18th April</h3>
                        <span>Friday</span>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="" href="#date3" title="Click Me" role="tab" data-toggle="tab">
                        <h3>19th April</h3>
                        <span>Saturday</span>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="" href="#date3" title="Click Me" role="tab" data-toggle="tab">
                        <h3>20th April</h3>
                        <span>Sunday</span>
                      </a>
                  </li>
                </ul>
                <!-- Tab panes -->
            </div>
          </div><!-- col end-->

      </div><!-- row end-->
      <div class="row">
          <div class="col-lg-12">
            <div class="tab-content schedule-tabs schedule-tabs-item">
                <div role="tabpanel" class="tab-pane active" id="day1">
                  <div class="row">
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-left">
                            {{-- <img class="schedule-slot-speakers" src="images/speakers/speaker1.jpg" alt=""> --}}
                            {{-- <span class="schedule-slot-time">10.30 - 11.30 AM</span> --}}
                            <h3 class="schedule-slot-title">Opening Intercession</h3>
                            {{-- <h4 class="schedule-slot-name">@ Henrikon Rebecca</h4> --}}
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-right">
                              {{-- <img class="schedule-slot-speakers" src="images/speakers/speaker2.jpg" alt=""> --}}
                            {{-- <span class="schedule-slot-time">11.30 - 12.30 PM</span> --}}
                            <h3 class="schedule-slot-title">Prophetic Worship</h3>
                            {{-- <h4 class="schedule-slot-name">@ Johnsson Agaton</h4> --}}
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-left">
                              {{-- <img class="schedule-slot-speakers" src="images/speakers/speaker3.jpg" alt=""> --}}
                            {{-- <span class="schedule-slot-time">12.30 - 01.30 PM</span> --}}
                            <h3 class="schedule-slot-title">Drama</h3>
                            {{-- <h4 class="schedule-slot-name">@ Lundryn Melisa</h4> --}}
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-right">
                              {{-- <img class="schedule-slot-speakers" src="images/speakers/speaker4.jpg" alt=""> --}}
                            {{-- <span class="schedule-slot-time">01.30 - 02.30 PM</span> --}}
                            <h3 class="schedule-slot-title">Power from on High</h3>
                            {{-- <h4 class="schedule-slot-name">@ Fredric Martinsson</h4> --}}
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                  </div><!-- row end-->
                  
                </div><!-- tab pane end-->

                <div role="tabpanel" class="tab-pane" id="date2">
                  <div class="row">
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-left">
                            <h3 class="schedule-slot-title">Prayer Walk</h3>
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-right">
                            <h3 class="schedule-slot-title">Teaching</h3>
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-left">
                            <h3 class="schedule-slot-title">AI and your Career!</h3>
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-right">
                            <h3 class="schedule-slot-title">From Babylon to Zion</h3>
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                  </div><!-- row end-->
                </div>
                <div role="tabpanel" class="tab-pane" id="date3">
                  <div class="row">
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-left">
                            <h3 class="schedule-slot-title">Business Strategy catchups</h3>
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-right">
                            <h3 class="schedule-slot-title">Variety Night</h3>
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-left">
                            <h3 class="schedule-slot-title">Teaching</h3>
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                      <div class="col-lg-6">
                        <div class="schedule-listing-item schedule-right">
                            <h3 class="schedule-slot-title">Handing over ceremony</h3>
                            <p>
                              How you transform your business technolog consumer habits industry dynamics change
                              Find out from those leading the charge How you
                            </p>
                        </div>
                      </div><!-- col end-->
                  </div><!-- row end-->
                </div>
            </div>

          </div>
      </div>
    </div><!-- container end-->
</section>
<!-- ts experience end-->

<!-- ts pricing start-->
<section id="register" class="ts-pricing gradient" style="background-image: url({{ asset('conference_templates/template2/images/pricing/pricing_img.jpg')}})">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
            <h2 class="section-title white">
                <span>Register</span>
                Choose registration type
            </h2>
          </div><!-- section title end-->
      </div><!-- col end-->
      <!-- row end-->
      <div class="row">
          <div class="col-lg-4 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="400ms">
            <div class="pricing-item">
                <img class="pricing-dot " src="{{ asset('conference_templates/template2/images/pricing/dot.png')}}" alt="">
                <div class="ts-pricing-box">
                  <div class="ts-pricing-header">
                      <h2 class="ts-pricing-name">Single Registration</h2>
                      <h3 class="ts-pricing-price">
                        <span class="currency">&#8358;</span>{{ number_format($setting->registration_fee) }}
                        <span class="text-900"><small><br></small><br></span>
                      </h3>
                  </div>
                  <div class="ts-pricing-progress">
                      <p class="amount-progres-text">
                        Undergraduate <br>
                        SSS Student <br>
                        Youth <br>
                      </p>
                      <div class="ts-progress">
                        <div class="ts-progress-inner" style="width: 100%"></div>
                      </div>
                  </div>
                  <div class="promotional-code">
                      <a href="{{ route('conference.registration',1) }}" class="btn pricing-btn">Register Now</a>
                      <p class="vate-text"><small>Free Feeding, Accomodation</small></p>
                  </div>
                </div><!-- ts pricing box-->
                <img class="pricing-dot1 " src="{{ asset('conference_templates/template2/images/pricing/dot.png')}}" alt="">
            </div>
          </div><!-- col end-->
          @if(isset($setting->lock_online_payment) && $setting->lock_online_payment == 'no')
          <div class="col-lg-4 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="500ms">
            <div class="pricing-item">
                <img class="pricing-dot " src="{{ asset('conference_templates/template2/images/pricing/dot.png')}}" alt="">
                <div class="ts-pricing-box">
                  <span class="big-dot"></span>
                  <div class="ts-pricing-header">
                      <h2 class="ts-pricing-name">Mass Registration</h2>
                      <h3 class="ts-pricing-price">
                        <span class="currency">&#8358;</span>{{ number_format($setting->registration_fee) }}
                        <span class="text-900"><small>/Participant</small></span>
                      </h3>
                  </div>
                  <div class="ts-pricing-progress">
                      <p class="amount-progres-text">
                        2 or more Undergraduates <br>
                        2 or more SSS Students <br>
                        2 or more Youths <br>
                      </p>
                      <div class="ts-progress">
                        <div class="ts-progress-inner" style="width: 100%"></div>
                      </div>
                  </div>
                  <div class="promotional-code">
                      <a href="{{ route('conference.registration',2) }}" class="btn pricing-btn">Register Now</a>
                      <p class="vate-text"><small>Free Feeding, Accomodation</small></p>
                  </div>
                </div><!-- ts pricing box-->
                <img class="pricing-dot1" src="{{ asset('conference_templates/template2/images/pricing/dot.png')}}" alt="">
            </div>
          </div><!-- col end-->
          @endif

          <div class="col-lg-4 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="600ms">
            <div class="pricing-item">
                <img class="pricing-dot " src="{{ asset('conference_templates/template2/images/pricing/dot.png')}}" alt="">
                <div class="ts-pricing-box">
                  <span class="big-dot"></span>
                  <div class="ts-pricing-header">
                      <h2 class="ts-pricing-name">Alumni Registration</h2>
                      <h3 class="ts-pricing-price">
                        <span class="currency">&#8358;</span>{{ number_format($setting->new_alumni_registration_fee) }} - &#8358;{{ number_format($setting->alumni_registration_fee) }}
                      </h3>
                  </div>
                  <div class="ts-pricing-progress">
                      <p class="amount-progres-text">
                        GSF Alumni <br>
                        Youth Corpers <br>
                        Senior Friends<br>
                      </p>
                      <div class="ts-progress">
                        <div class="ts-progress-inner" style="width: 100%"></div>
                      </div>
                  </div>
                  <div class="promotional-code">
                      <a href="{{ route('conference.registration',3) }}" class="btn pricing-btn">Register Now</a>
                      <p class="vate-text"><small>Free Feeding, Accomodation</small></p>
                  </div>
                </div><!-- ts pricing box-->
                <img class="pricing-dot1 " src="{{ asset('conference_templates/template2/images/pricing/dot.png')}}" alt="">
            </div>
          </div><!-- col end-->
      </div>
    </div><!-- container end-->
    <div class="speaker-shap wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="400ms">
      <img class="shap2" src="{{ asset('conference_templates/template2/images/shap/pricing_memphis1.png')}}" alt="">
    </div>
</section>
<section id="faq" class="ts-faq-sec">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
            <div class="faq-content">
                  <h2 class="column-title">
                      Frequently asked Questions
                  </h2>
                  <div class="panel-group faq-item" id="accordion1" role="tablist" aria-multiselectable="true">

                      <div class="panel faq-list panel-default">
                        <div class="panel-heading" role="tab" id="heading5">
                            <h4 class="panel-title">
                              <a role="button" class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                        1. When will the Conference start?
                              </a>
                            </h4>
                        </div>
                        <div id="collapse5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading5">
                            <div class="panel-body">
                                  The Conference will commence on Thursday night with a opening session which is often characterized with the power of the Spirit with all-encompassing display of joy.
                            </div>
                        </div>
                      </div>

                      <div class="panel faq-list panel-default">
                        <div class="panel-heading" role="tab" id="heading6">
                            <h4 class="panel-title">
                              <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                        2. Where does the conference take place?
                                    </a>
                            </h4>
                        </div>
                        <div id="collapse6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading6">
                            <div class="panel-body">
                                  The Conference will hold at the International Gospel City of The Gospel Faith Mission International located at Ogunmakin, Ogun State along Lagos-lbadan Express Way.
                            </div>
                        </div>
                      </div>

                      <div class="panel faq-list panel-default">
                        <div class="panel-heading" role="tab" id="heading7">
                            <h4 class="panel-title">
                              <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                  3. How can I get the latest news?
                              </a>
                            </h4>
                        </div>
                        <div id="collapse7" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading7">
                            <div class="panel-body">
                              Please stay connected to our social media handles on Facebook, Instagram and X: gsfnational
                            </div>
                        </div>
                      </div>
                      <div class="panel faq-list panel-default">
                        <div class="panel-heading" role="tab" id="heading8">
                            <h4 class="panel-title">
                              <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                    4. How can my Church sponsor this event?
                              </a>
                            </h4>
                        </div>
                        <div id="collapse8" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading8">
                            <div class="panel-body">
                                For individual or corporate sponsorship of event, please reach out to the following contacts, +234 805 263 8670, +234 816 447 8392, +234 7013 530858
                            </div>
                        </div>
                      </div>
                  </div><!-- panel-group -->
                </div>
          </div><!-- col end -->
      </div><!-- row end-->
    </div><!-- .container end -->
</section><!-- End faq section -->

@endsection