@extends('frontend.conference.template1.index')
@section('title', 'Forgot Password')
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
            <div class="col-md-8 col-lg-5 text-center mb-3">
                <h2>Login</h2>
            </div>
        </div>
        @include('includes.alerts')
        <div class="row h-100 justify-content-center">
            <div class="contact-form">
                <h5 style="margin-bottom:10px" class="font300 mb text-center">Enter your email address and if we can find it in the system, we will send you a password reset link</h5>
                
                <form method="POST" action="{{ route('password.email') }}">
                @csrf
                    <div class='form-group-icon mb15'>
                        <input type="email" class="form-control" id="email" placeholder="Email" name="email" value="{{ old('email') }}" required>
                    </div>
                    
                    <input style="margin-top:20px;width: 100%;display: ruby;font-size: 15px;" type="submit" value="SEND PASSWORD RESET LINK" class="btn btn-danger hover-top btn-glow rounded-pill border-0">
                </form>
                <div class="text-right"><a href="/login" class="card-link"><small>I remember my password</small></a></div>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>
@endsection