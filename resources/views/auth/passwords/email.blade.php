@extends('layouts.app')
@section('title', 'Forgot Password')
<!-- BEGIN: Body-->
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- forgot password start -->
            <section class="row flexbox-container">
                <div class="col-xl-7 col-md-9 col-10  px-0">
                    <div class="card bg-authentication mb-0">
                        <div class="row m-0">
                            <!-- left section-forgot password -->
                            <div class="col-md-6 col-12 px-0">
                                <div class="card disable-rounded-right mb-0 p-2">
                                    <div class="card-header pb-1">
                                        <div class="card-title">
                                            <div class="card-title mb-2">
                                                <div style="text-align: center;"><img style="width: 50%;"
                                                        src="{{ asset('app-assets/images/logo/logo.png') }}">
                                                </div>
                                            </div>
                                            <h5 class="text-center mb-2">Forgot Password?</h5>
                                        </div>
                                    </div>
                                    <div class="form-group d-flex justify-content-between align-items-center mb-2">
                                        <div class="text-left">
                                            <div class="ml-3 ml-md-2 mr-1"><a href="/login"
                                                    class="card-link btn btn-outline-primary text-nowrap">Sign
                                                    in</a></div>
                                        </div>
                                        <div class="mr-3"><a href="/register"
                                                class="card-link btn btn-outline-primary text-nowrap">Sign
                                                up</a></div>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                                <div class="text-muted text-center mb-2"><small>Enter the email you
                                                        used when you registered and we will send you temporary password
                                                        link</small>
                                                </div>
                                                {{-- @if($errors->any()) --}}
                                                @include('includes.alerts')
                                                <form class="mb-2" method="POST" action="{{ route('password.email') }}">
                                                    @csrf
                                                    <div class="form-group mb-2">
                                                        <label class="text-bold-600" for="email">Email</label>
                                                        <input type="email" class="form-control" id="email" placeholder="Email" name="email" value="{{ old('email') }}" required>
                                                    </div>
                                                    <button type="submit"
                                                        class="btn btn-primary glow position-relative w-100">SEND
                                                        PASSWORD RESET LINK<i id="icon-arrow"
                                                            class="bx bx-right-arrow-alt"></i></button>
                                                </form>
                                                <div class="text-center mb-2"><a href="/login"><small
                                                            class="text-muted">I remembered my password</small></a></div>
                                                {{-- <div class="divider mb-2">
                                                    <div class="divider-text">Or Sign in as</div>
                                                </div>
                                                <div class="d-flex flex-md-row flex-column">
                                                    <a href="#" class="btn btn-social btn-google btn-block font-small-3 mb-1 mb-sm-1 mb-md-0 mr-md-1 text-center">
                                                        <i class="bx bxl-google font-medium-3"></i><span class="pl-1">Google</span></a>
                                                    <a href="#" class="btn btn-social btn-facebook btn-block font-small-3 text-center mt-0">
                                                        <i class="bx bxl-facebook-square font-medium-3"></i><span class="pl-1">Facebook</span></a>
                                                </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- right section image -->
                            <div class="col-md-6 d-md-block d-none text-center align-self-center">
                                <img class="img-fluid"
                                    src="{{ asset('app-assets/images/pages/forgot-password.png') }}"
                                    alt="branding logo" width="300">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- forgot password ends -->
        </div>
    </div>
</div>
@endsection