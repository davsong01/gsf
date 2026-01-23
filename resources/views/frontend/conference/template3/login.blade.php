@extends('frontend.conference.template3.app')

@section('content')
<section id="section-hero" class="section-dark no-top no-bottom text-light jarallax relative mh-300">
    <img src="{{ asset('conference_templates/template2/images/hero_area/banner_bg.jpg') }}" class="jarallax-img" alt="">
    <div class="gradient-edge-bottom h-50"></div>
    <div class="sw-overlay op-5"></div>
    <div class="abs w-80 bottom-10 z-2 w-100">
        <div class="container">
            <div class="row align-items-center justify-content-between gx-5">
                <div class="col-lg-6">
                    <div class="relative wow mask-right">
                        <div class="text-start">
                            <h1 class="fs-80 text-uppercase fs-sm-10vw mb-0 lh-1">Login</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="relative">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <div class="wow scaleIn">
                    <img src="{{ asset($setting->banner) }}" class="w-100" alt="">
                </div>
            </div>
            <!-- Right Column: Registration Form -->
            <div class="col-lg-6">
                <div class="bg-dark-2 rounded-1 p-20 relative">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                {{-- <h3>{{ $title }}</h3> --}}
                                <p>Login below to access your dashboard.</p>

                                @include('includes.bootstrap5alerts')
                                <div class="field-set" style="background-size: cover; background-repeat: no-repeat;">
                                    <label>Registration ID</label>
                                    <input value="{{ old('family_id') }}" type="text" name="family_id" id="family_id" class="form-underline" placeholder="Enter Registration ID" required>
                                </div>

                                <div class="field-set" style="background-size: cover; background-repeat: no-repeat;">
                                    <label>Password</label>
                                    <input value="{{ old('password') }}" type="password" name="password" id="password" class="form-underline" placeholder="Enter Password" required>
                                </div>

                                <div class="field-set" style="background-size: cover; background-repeat: no-repeat;">
                                    <input class="" type="checkbox" value="" name="" id="check" />
                                    <label class="" for="">
                                        <span onclick="newFunction()" style="cursor: pointer;">Show Password</span>
                                    </label>
                                </div>

                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn-main w-100">Login</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script>
    function newFunction() {
        var x = document.getElementById("password");
        var c = document.getElementById("check");

        if (x.type === "password") {
            x.type = "text";
            c.checked = "checked";
        } else {
            x.type = "password";
            c.checked = "";
        }

    }
</script>
@endsection
