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
@extends('frontend.spaces.layouts.app')
@section('title', 'Reset Password')
@section('ogtitle', 'Reset Password')
@section('ogdescription')
@section('content')
  <section class="section section-header bg-primary overlay-primary text-white pb-2">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-8 text-center">
            <h1 class="display-2 mb-4">Reset Password</h1>
          </div>
        </div>
      </div>
    </section>
    <section class="min-vh-80 d-flex align-items-center" style="margin-top:20px">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="text-center text-md-center mb-4 mt-md-0 text-dark">
              <h1 class="mb-0 h3" style="color:black">Enter new password</h1>
            </div>
          </div>
          <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
            <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                @include('includes.alerts')

                <form method="POST" action="{{ route('password.update') }}">
                @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group"><label for="email">Email Address</label>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend"><span class="input-group-text"><span
                            class="fas fa-envelope"></span></span></div>
                            <input class="form-control" required id="email" name="email" value="{{ $email ?? old('email') }}" placeholder="" type="text" aria-label="Email Address">
                        </div>
                    </div>
                    <div class="form-group"><label for="email">New Password</label>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend"><span class="input-group-text"><span
                            class="fas fa-unlock-alt"></span></span></div>
                            <input class="form-control" required id="password" name="password" value="{{old('password')}}" placeholder="" type="password" aria-label="Password">
                        </div>
                    </div>
                    <div class="form-group"><label for="email">Confirm New Password</label>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend"><span class="input-group-text"><span
                            class="fas fa-unlock-alt"></span></span></div>
                            <input class="form-control" required id="password_confirmation" name="password_confirmation" value="{{old('password_confirmation')}}" placeholder="" type="password_confirmation" aria-label="Password Confirmation">
                        </div>
                    </div>

                       
                    <button type="submit" class="btn btn-block btn-primary">Reset my password</button>
                </form>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                    
                    <div><a href="/login" class="small text-right">I remember my password</a></div>
                </div>
                    </div>
                

                
                
            </div>
          </div>
        </div>
      </div>
    </section>
    
@endsection
