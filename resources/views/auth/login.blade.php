@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- login page start -->
            <section id="auth-login" class="row flexbox-container">
                <div class="col-xl-8 col-11">
                    <div class="card bg-authentication mb-0">
                        <div class="row m-0">
                            <!-- left section-login -->
                            <div class="col-md-6 col-12 px-0">
                                <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                                    {{-- <div class="card-header pb-1">
                                        
                                    </div> --}}
                                    <div class="card-content">
                                        <div class="card-body">
                                           <div class="card-title">
                                                <div style="text-align: center;"><img style="width: 50%;" src="{{ asset('frontend/img/logo.png') }}"></div>
                                            </div>
                                            <div class="divider">
                                                <div class="divider-text text-uppercase text-muted"><small>LOGIN</small>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="card-title">
                                                @include('includes.alerts')
                                            
                                                <form method="POST" action="{{ route('login') }}">
                                                @csrf
                                                <div class="form-group mb-50">
                                                    <label class="text-bold-600" for="email">Conference I.D.</label>
                                                    <input type="text" class="form-control" name="conference_number" id="conference_number" value="{{ old('conference_id')  }}" required></div>
                                                <div class="form-group">
                                                    <label class="text-bold-600" for="password">Password</label>
                                                    <input type="password" class="form-control" id="password" value="{{ old('password')  }}" name="password" required>
                                                </div>
                                                <div class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                                                    <div class="text-left">
                                                        <div class="checkbox checkbox-sm">
                                                            <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                                            <label class="checkboxsmall" for="exampleCheck1"><small>Keep me logged
                                                                    in</small></label>
                                                        </div>
                                                    </div>
                                                    <div class="text-right"><a href="password/reset" class="card-link"><small>Forgot Password?</small></a></div>
                                                </div>
                                                <button type="submit" class="btn btn-primary glow w-100 position-relative">Login<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                                            </form>
                                            <hr>
                                            <div class="text-center"><small class="mr-25">Don't have an account?</small><a href="/#register"><small>Register</small></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- right section image -->
                            <div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
                                <div class="card-content">
                                    <img class="img-fluid" src="app-assets/images/pages/login.png" alt="branding logo">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- login page ends -->

        </div>
    </div>
</div>
@endsection