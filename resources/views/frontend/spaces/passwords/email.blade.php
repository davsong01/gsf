@extends('frontend.spaces.layouts.app')
@section('title', 'Forgot Password')
@section('ogtitle', 'Forgot Password')
@section('ogdescription')
@section('content')
  <section class="section section-header bg-primary overlay-primary text-white pb-2">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-8 text-center">
            <h1 class="display-2 mb-4">Forgot Password</h1>
          </div>
        </div>
      </div>
    </section>
    <section class="min-vh-80 d-flex align-items-center" style="margin-top:20px">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="text-center text-md-center mb-5 mt-md-0 text-dark">
              <h1 class="mb-0 h3" style="color:black">Reset Your password</h1>
              <span >Enter your email address and if we can find it in the system, we will send you a password reset link </p>
            </div>
          </div>
          <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
            <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                @include('includes.alerts')

                <form method="POST" action="{{ route('password.email') }}">
                @csrf
                    <div class="form-group"><label for="email">Email Address</label>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend"><span class="input-group-text"><span
                            class="fas fa-envelope"></span></span></div>
                            <input class="form-control" required id="email" name="email" value="{{old('email')}}"
                        placeholder="" type="text" aria-label="Email Address">
                    </div>
                    
                    </div>

                    <button type="submit" class="btn btn-block btn-primary">Send Password Reset Link</button>
                </form>
                <div class="d-block d-sm-flex justify-content-center align-items-center mt-4"><span
                    class="font-weight-normal">I remember my password <a href="{{route('login')}}" class="font-weight-bold">Login</a></span>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
@endsection
