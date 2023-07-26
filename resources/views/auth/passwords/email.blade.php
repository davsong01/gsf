@extends('frontend.main.mainlayout')
@section('title', 'Forgot Password')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Forgot Password</h3>
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
                <h3 class="font300 mb0 text-center">Enter your email address and if we can find it in the system, we will send you a password reset link
                </h3> <hr>
                @include('includes.alerts')     
                <form method="POST" action="{{ route('password.email') }}">
                @csrf
                    <div class='form-group-icon mb15'>
                        <i class='fa fa-envelope'></i>
                        <input type="email" class="form-control" id="email" placeholder="Email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <input type="submit" value="SEND
                    PASSWORD RESET LINK" class='btn btn-default btn-lg btn-block'>
                </form>
                <div class="text-right"><a href="/login" class="card-link"><small>I remember my password</small></a></div>
            </div>
        </div>
    </div>
</div>
@endsection