@extends('frontend.conference.template2.app')
@section('css')
<style>
    .divider:after,
    .divider:before {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
    }

    .h-custom {
        height: calc(100% - 73px);
    }

    <blade media|%20(max-width%3A%20450px)%20%7B>.h-custom {
        height: 100%;
    }
    }
</style>
@endsection
@section('content')
<div id="page-banner-area" class="page-banner-area"
    style="background-image:url({{ asset('conference_templates/template2/images/hero_area/banner_bg.jpg') }})">
    <!-- Subpage title start -->
    <div class="page-banner-title">
        <div class="text-center">
            <h2>Login</h2>
        </div>
    </div><!-- Subpage title end -->
</div><!-- Page Banner end -->

<div class="">
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="{{ asset($setting->banner)}}"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4">
                    @include('includes.alerts')
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        {{-- <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0"></p>
                        </div> --}}
                        <!-- Email input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="family_id">Family ID</label>
                            <input required type="text" id="family_id" name="family_id" class="form-control form-control-lg"
                                placeholder="Enter your family ID" value="{{ old('family_id') }}">
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control form-control-lg"
                                placeholder="Enter password" />
   
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Checkbox -->
                            <div class="form-check mb-0">
                                <input class="" type="checkbox" value="" name="" id="check" />
                                <label class="" for="">
                                    <span onclick="newFunction()" style="cursor: pointer;">Show Password</span>
                                </label>
                            </div>
                        </div>
                        <input style="margin-top:20px;width: 100%;" type="submit" value="Login" h
                        class="btn btn-danger hover-top btn-glow rounded-pill border-0">
                    </form>
                </div>
            </div>
        </div>

    </section>
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