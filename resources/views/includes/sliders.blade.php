<div class="carousel">

    <div class="container-fluid">
        <div class="owl-carousel">
            
                <div class="carousel-item">
                <div class="carousel-img">
                    <img src="{{ asset('frontend/img/sliders/slider1.jpg') }}" alt="slider">
                    <img src="{{ asset('frontend/img/sliders/slider2.jpg') }}" alt="slider">
                </div>
                <div class="carousel-text">
                    <div class="banner_text">
                        <h2 style="color:white">19TH BIENNIAL NATIONAL CONFERENCE, {{ $conference_year}}</h2><br>
                        <h3 style="color:white"> <strong> "{{ $setting->conference_theme }}"</strong></h3>
                        <p style="color: yellow !important; font-weight: normal;"> <b>DATE:</b> {{ $setting->start_date }} to  {{ $setting->end_date }}
                        </p>
                        
                        <p>
                            @if($setting->close_registration >= date('Y-m-d'))
                                <a class="btn" href="#register"><i class="fa fa-link"></i>REGISTER NOW</a>
                            @else
                                <a class="btn" disabled><i class="fa fa-link"></i>REGISTER HAS CLOSED!</a>
                            @endif
                            
                           
                        </p>
            
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

