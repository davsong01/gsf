@extends('frontend.spaces.layouts.app')
@section('title', 'Login')
@section('ogtitle', 'Login')
@section('ogdescription')

@section('content')
<section class="section section-header bg-primary overlay-primary text-white pb-2" style="margin-top:-50px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 text-center">
                <h1 class="display-2 mb-4">Login</h1>
            </div>
        </div>
    </div>
</section>

<section class="min-vh-80 d-flex align-items-center" style="margin-top:20px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h1 class="mb-0 h3" style="color:black">Login to your account</h1>
                </div>
            </div>

            <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
                <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-500">

                    {{-- Alerts --}}
                    @include('includes.falerts')

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        {{-- Family ID --}}
                        <div class="form-group mb-3">
                            <label for="family_id">Login ID</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-users"></i></span>
                                <input type="text" class="form-control @error('family_id') is-invalid @enderror"
                                    id="family_id" name="family_id" placeholder="GSF-******"
                                    value="{{ old('family_id') }}" required>
                            </div>
                            @error('family_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <div class="input-group" id="show_hide_password">
                                <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Password" required>
                                <span class="input-group-text toggle-password" style="cursor:pointer">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember & Forgot --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me">
                                <label class="form-check-label" for="remember_me">Remember me</label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}" class="small text-right">Forgot password?</a>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
