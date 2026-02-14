@extends('frontend.spaces.layouts.app')
@section('title', 'Forgot Password')
@section('ogtitle', 'Forgot Password')
@section('ogdescription')

@section('content')
<section class="section section-header bg-primary overlay-primary text-white pb-2" style="margin-top:-50px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 text-center">
                <h1 class="display-2 mb-4">Forgot Password</h1>
                <p class="lead">Enter your email address and we’ll send you a reset code.</p>
            </div>
        </div>
    </div>
</section>

<section class="min-vh-80 d-flex align-items-center" style="margin-top:20px">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
                <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-500">

                    @include('includes.alerts')

                    <form action="{{ route('stakeholders.forgot-password.send') }}" method="POST">
                        @csrf

                        {{-- Email --}}
                        <div class="form-group mb-4">
                            <label for="email">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    placeholder="Enter your registered email"
                                    value="{{ old('email') }}"
                                    required
                                >
                            </div>

                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-block btn-primary">
                            Send Reset Code
                        </button>
                        <input type="hidden" name="user_type" value="stakeholder">
                        {{-- Back to login --}}
                        <div class="text-center mt-3">
                            <a href="{{ route('stakeholders.login') }}" class="small">
                                ← Back to Login
                            </a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
