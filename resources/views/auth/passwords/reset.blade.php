@extends('frontend.main.mainlayout')
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
@endsection