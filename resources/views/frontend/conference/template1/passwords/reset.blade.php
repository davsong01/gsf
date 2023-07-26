@extends('frontend.conference.template1.index')
@section('title', 'Reset your password')
@section('css')
<style>
    .form-control {
        padding-left: 10px !important;
    }

    .select2-selection__rendered {
        line-height: 31px !important;
    }

    .select2-container .select2-selection--single {
        height: 35px !important;
    }

    .select2-selection__arrow {
        height: 34px !important;
    }
    .close{
        background: red !important;
        color: white !important;
        border-radius: unset !important;
        box-shadow: unset !important;
        padding: 6px !important;
        border: unset !important;
        height: 38px !important;
        width: 38px !important;
    }
</style>
@endsection
@section('sec-content')
<section class="bg-100 py-7" id="register">
    <div class="container-lg small-container">
        
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-12 text-center mb-3">
                <h2>Enter new password</h2>
            </div>
        </div>
        @include('includes.alerts')
        <div class="row h-100 justify-content-center">
            <div class="contact-form">
                
                <form method="POST" action="{{ route('password.update') }}">
                @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class='form-group-icon mb15'>
                        <label class="text-bold-600" for="password">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Email" name="email" value="{{ $email ?? old('email') }}" required>
                    </div>
                     <div class="form-group">
                        <label class="text-bold-600" for="password">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="Enter a new password">
                        
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-bold-600" for="password_confirmation">Confirm New Password</label>
                        <input type="password" class="form-control" value="{{ old('password_confirmation') }}" id="password_confirmation" name="password_confirmation" placeholder="Confirm your new password">
                    </div>
                    <input style="margin-top:20px;width: 100%;display: ruby;font-size: 15px;" type="submit" value="Reset my password" class="btn btn-danger hover-top btn-glow rounded-pill border-0">
                </form>
                <div class="text-right"><a href="/login" class="card-link"><small>I remember my password</small></a></div>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>
@endsection
{{-- @extends('frontend.main.mainlayout')
@section('title', 'Reset Password')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Reset your password</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
<div class="container mb70">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="border-card">
                <h3 class="font300 mb0 text-center">Enter new password
                </h3> <hr>
                @include('includes.alerts')     
                <form class="mb-2" method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group">
                        <label class="text-bold-600" for="email">Email</label>
                        <input readonly type="email" class="form-control" name="email" id="email" value="{{ $email ?? old('email') }}" placeholder="Enter your email">
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="text-bold-600" for="password">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="Enter a new password">
                        
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-bold-600" for="password_confirmation">Confirm New Password</label>
                        <input type="password" class="form-control" value="{{ old('password_confirmation') }}" id="password_confirmation" name="password_confirmation" placeholder="Confirm your new password">
                    </div>
                    <button type="submit" class="btn btn-primary glow position-relative w-100">Reset my password<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                </form>
                            
                <div class="text-right"><a href="/login" class="card-link"><small>I remember my password</small></a></div>
            </div>
        </div>
    </div>
</div>
@endsection --}}