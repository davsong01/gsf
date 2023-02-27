@extends('frontend.conference.template1.index')
@section('css')
<style>
  .close{
    padding: 5px;
    background: red;
    border-radius: 50%;
    height: ;
    color: white;
    border: 1px solid;
    width: 35px; 
  }
</style>
@endsection
@section('sec-content')
<section class="pb-6" id="details" style="margin-top:50px">

  <div class="container">
    <div class="row flex-center">
      <div class="col-lg-6 col-md-5 order-md-1"><img class="img-fluid" src="{{ !empty($conference->banner) ? $conference->banner : asset('conference_templates/template1/assets/img/illustrations/1.png')}}" alt="" /></div>
      <div class="col-md-7 col-lg-6 mt-5 text-center text-md-start">
        <h1 class="fw-medium">{{ $conference->conference_theme }}</span></h1>
        <p class="mt-3 mb-4">{!! $conference->conference_overview !!} </p><a class="btn btn-lg btn-danger hover-top btn-glow" href="#register">Grab your slot </a>
      </div>
    </div>
  </div>
  <!-- end of .container-->
</section>
<section class="py-4">
  <div class="container">
    <div class="card py-5 border-0 shadow-sm">
      <div class="card-body">
        <div class="row">
          <div class="col-4">
            <div class="border-end d-flex justify-content-md-center">
              <div class="mx-2 mx-md-0 me-md-5">
                <div class="badge badge-circle bg-soft-danger">
                  <svg class="bi bi-person-fill" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#F53838" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
                  </svg>
                </div>
              </div>
              <div>
                <p class="fw-bolder text-1000 mb-0">6,000+ </p>
                <p class="mb-0">Participants</p>
              </div>
            </div>
          </div>
          <div class="col-4">
            <div class="border-end d-flex justify-content-md-center">
              <div class="mx-2 mx-md-0 me-md-5">
                <div class="badge badge-circle bg-soft-danger">
                  <svg class="bi bi-geo-alt-fill" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#F53838" viewBox="0 0 16 16">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                  </svg>
                </div>
              </div>
              <div>
                <p class="fw-bolder text-1000 mb-0">30+ </p>
                <p class="mb-0">Ministers </p>
              </div>
            </div>
          </div>
          <div class="col-4">
            <div class="d-flex justify-content-md-center">
              <div class="mx-2 mx-md-0 me-md-5">
                <div class="badge badge-circle bg-soft-danger">
                  <svg class="bi bi-hdd-stack-fill" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#F53838" viewBox="0 0 16 16">
                    <path d="M2 9a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1a2 2 0 0 0-2-2H2zm.5 3a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm2 0a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zM2 2a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2zm.5 3a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm2 0a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1z"></path>
                  </svg>
                </div>
              </div>
              <div>
                <p class="fw-bolder text-1000 mb-0">20+ </p>
                <p class="mb-0">Sessions </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end of .container-->

</section>

<section class="pt-4 pt-md-6" id="expectation">

  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-5 col-lg-7 text-lg-center"><img class="img-fluid mb-5 mb-md-0" src="{{ asset('conference_templates/template1/assets/img/illustrations/2.png')}}" alt="" /></div>
      <div class="col-md-7 col-lg-5 text-center text-md-start">
        <h2>What to expect <br /></h2>
        <p>This edition of the GSF Bienniel Conference is packaged with:</p>
        <div class="d-flex">
          <svg class="bi bi-check-circle-fill" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2FAB73" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path>
          </svg>
          <p class="ms-2">Powerfull online protection.</p>
        </div>
        <div class="d-flex">
          <svg class="bi bi-check-circle-fill" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2FAB73" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path>
          </svg>
          <p class="ms-2">Internet without borders.</p>
        </div>
        <div class="d-flex">
          <svg class="bi bi-check-circle-fill" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2FAB73" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path>
          </svg>
          <p class="ms-2">Supercharged VPN.</p>
        </div>
        <div class="d-flex">
          <svg class="bi bi-check-circle-fill" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2FAB73" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path>
          </svg>
          <p class="ms-2">And more</p>
        </div>
      </div>
    </div>
  </div>
  <!-- end of .container-->

</section>

<section class="bg-100 py-7" id="register">
  <div class="container-lg">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-5 text-center mb-3">
        <h2>Register</h2>
        <p>Choose registration type</p>
      </div>
    </div>
    <div class="row h-100 justify-content-center">
      <div class="col-md-4 pt-4 px-md-2 px-lg-3">
        <div class="card h-100">
          <div class="card-body d-flex flex-column justify-content-around mx-auto">
            <div class="text-center pt-5">
              <h5 class="my-4">Single Registration</h5>
            </div>
            <ul class="list-unstyled">
              <li class="mb-3"><span class="me-2">
                  <svg class="bi bi-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2FAB73" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                  </svg></span>Undergraduate
              </li>
              <li class="mb-3"><span class="me-2">
                  <svg class="bi bi-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2FAB73" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                  </svg></span>SSS Student
              </li>
              <li class="mb-3"><span class="me-2">
                  <svg class="bi bi-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2FAB73" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                  </svg></span>Youth
              </li>
            </ul>
            <div class="text-center my-5">
              <h2 class="mb-3">&#8358;{{ number_format($setting->registration_fee) }}
              </h2>
              <a href="{{ route('conference.registration',1) }}" h class="btn btn-danger hover-top btn-glow rounded-pill border-0" type="submit">Register</a>
            </div>
          </div>
        </div>
      </div>
      
       <div class="col-md-4 pt-4 px-md-2 px-lg-3">
        <div class="card h-100">
          <div class="card-body d-flex flex-column justify-content-around mx-auto">
            <div class="text-center pt-5">
              <h5 class="my-4">Mass Registration</h5>
            </div>
            <ul class="list-unstyled">
              <li class="mb-3"><span class="me-2">
                  <svg class="bi bi-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2FAB73" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                  </svg></span>2 or more Undergraduates
              </li>
              <li class="mb-3"><span class="me-2">
                  <svg class="bi bi-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2FAB73" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                  </svg></span>2 or more SSS Students
              </li>
              <li class="mb-3"><span class="me-2">
                  <svg class="bi bi-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2FAB73" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                  </svg></span>2 or more Youths
              </li>
            </ul>
            <div class="text-center my-5">
              <h2 class="mb-3">&#8358;{{ number_format($setting->registration_fee) }}
                <span class="text-900"><small>/Participant</small></span>
              </h2>
              <a href="{{ route('conference.registration',2) }}" h class="btn btn-danger hover-top btn-glow rounded-pill border-0" type="submit">Register</a>
            </div>
          </div>
        </div>
      </div>
       <div class="col-md-4 pt-4 px-md-2 px-lg-3">
        <div class="card h-100">
          <div class="card-body d-flex flex-column justify-content-around mx-auto">
            <div class="text-center pt-5">
              <h5 class="my-4">Alumni Registration</h5>
            </div>
            <ul class="list-unstyled">
              <li class="mb-3"><span class="me-2">
                  <svg class="bi bi-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2FAB73" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                  </svg></span>GSF Alumni
              </li>
              <li class="mb-3"><span class="me-2">
                  <svg class="" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2FAB73" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                  </svg></span>Youth Corpers
              </li>
              <li class="mb-3"><span class="me-2">
                  <svg class="bi bi-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2FAB73" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                  </svg></span>Senior Friends
              </li>
            </ul>
            <div class="text-center my-5">
              <h2 class="mb-3">&#8358;{{ number_format($setting->new_alumni_registration_fee) }} - &#8358;{{ number_format($setting->alumni_registration_fee) }}
              </h2>
              <a href="{{ route('conference.registration',3) }}" h class="btn btn-danger hover-top btn-glow rounded-pill border-0" type="submit">Register</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end of .container-->
</section>
<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="py-7" id="testimonies">

  <div class="container">
    <div class="row flex-center">
      <div class="col-md-8 col-lg-5 text-center">
        <h2>Testimonies from previous conferences</h2>
        <p>These are the stories of our customers who have joined us with great pleasure when using this crazy feature.</p>
      </div>
    </div>
    <div class="carousel slide pt-6" id="carouselExampleDark" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active" data-bs-interval="10000">
          <div class="row h-100">
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="flex-1 ms-0">
                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Viezh Robert</h6>
                        <p class="fs--2 fw-normal mb-0">arsaw, Poland</p>
                      </div>
                    </div>
                    
                  </div>
                  <p class="card-text pt-3">“Wow...I am very happy to use this VPN, it turned out to be more than my expectations and so far there have been no problems..</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="flex-1 ms-0">
                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Kim Young Jou</h6>
                        <p class="fs--2 fw-normal mb-0">Seoul, South Korea</p>
                      </div>
                    </div>
                    
                  </div>
                  <p class="card-text pt-3">“I like it because I like to travel far and still can connect with high speed.”</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="flex-1 ms-0">
                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Viezh Robert</h6>
                        <p class="fs--2 fw-normal mb-0">Shanxi, China</p>
                      </div>
                    </div>
                    
                  </div>
                  <p class="card-text pt-3">like it because I like to travel far and still can connect with high speed”.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item" data-bs-interval="2000">
          <div class="row h-100">
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="flex-1 ms-0">
                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Viezh Robert</h6>
                        <p class="fs--2 fw-normal mb-0">arsaw, Poland</p>
                      </div>
                    </div>
                    
                  </div>
                  <p class="card-text pt-3">“Wow...I am very happy to use this VPN, it turned out to be more than my expectations and so far there have been no problems.”.</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="flex-1 ms-0">
                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Kim Young Jou</h6>
                        <p class="fs--2 fw-normal mb-0">Seoul, South Korea</p>
                      </div>
                    </div>
                    
                  </div>
                  <p class="card-text pt-3">“I like it because I like to travel far and still can connect with high speed.”</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="flex-1 ms-0">
                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Viezh Robert</h6>
                        <p class="fs--2 fw-normal mb-0">Shanxi, China</p>
                      </div>
                    </div>
                  </div>
                  <p class="card-text pt-3">like it because I like to travel far and still can connect with high speed”.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="row h-100">
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="flex-1 ms-0">
                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Viezh Robert</h6>
                        <p class="fs--2 fw-normal mb-0">arsaw, Poland</p>
                      </div>
                    </div>
                    
                  </div>
                  <p class="card-text pt-3">“Wow...I am very happy to use this VPN, it turned out to be more than my expectations and so far there have been no problems.”.</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="flex-1 ms-0">
                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Kim Young Jou</h6>
                        <p class="fs--2 fw-normal mb-0">Seoul, South Korea</p>
                      </div>
                    </div>
                    
                  </div>
                  <p class="card-text pt-3">“I like it because I like to travel far and still can connect with high speed.”</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="flex-1 ms-0">
                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Viezh Robert</h6>
                        <p class="fs--2 fw-normal mb-0">Shanxi, China</p>
                      </div>
                    </div>
                    
                  </div>
                  <p class="card-text pt-3">like it because I like to travel far and still can connect with high speed”.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row px-3 px-md-0 mt-4">
        <div class="col-6 position-relative">
          <ol class="carousel-indicators">
            <li class="active" data-bs-target="#carouselExampleDark" data-bs-slide-to="0"></li>
            <li data-bs-target="#carouselExampleDark" data-bs-slide-to="1"></li>
            <li data-bs-target="#carouselExampleDark" data-bs-slide-to="2"></li>
          </ol>
        </div>
        <div class="col-6 position-relative"><a class="carousel-control-prev carousel-icon z-index-2" href="#carouselExampleDark" role="button" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></a><a class="carousel-control-next carousel-icon z-index-2" href="#carouselExampleDark" role="button" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></a></div>
      </div>
    </div>
  </div>
  <!-- end of .container-->

</section>
<!-- <section> close ============================-->
<!-- ============================================-->

<!-- ============================================-->
<!-- <section> begin ============================-->
<section class="py-5 z-index-1" style="margin-bottom: 1rem">

  <div class="container" id="donate">
    <div class="card py-5 px-5 border-0 shadow-sm" style="box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px !important;">
      <div class="card-body">
        <div class="row flex-center">
          <div class="col-12 col-lg-6 text-lg-start">
            <h2>Special <br />Support</h2>
            <p class="mb-lg-0">You can support the conference online</p>
          </div>
          <div class="col-12 col-lg-6 text-lg-end"><a class="btn btn-lg btn-danger hover-top btn-glow text-end" href="{{route('conference.registration',5) }}"type="submit">Donate Now</a></div>
        </div>
      </div>
    </div>
  </div>
  <!-- end of .container-->

</section>

@endsection 
@section('script')
    <script>
      // In your Javascript (external .js resource or <script> tag)
       $(document).ready(function() {
          $('.js-example-basic-single').select2();
      });
    $('#chapterind').select2({
        dropdownParent: $('#singleModal')
    });
    
    </script>
@endsection