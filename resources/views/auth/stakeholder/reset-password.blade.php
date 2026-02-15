@extends('frontend.spaces.layouts.app')
@section('title', 'Reset Password')
@section('ogtitle', 'Reset Password')
@section('ogdescription')

@section('content')
<section class="section section-header bg-primary overlay-primary text-white pb-2" style="margin-top:-50px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 text-center">
                <h1 class="display-2 mb-4">Reset Password</h1>
                <p class="lead">Enter your new password below</p>
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

                    <form action="{{ route('stakeholders.reset-password.submit') }}" method="POST">
                        @csrf
                        <div class="alert alert-danger" role="alert">
                            DO NOT REFRESH THIS PAGE!!!
                        </div>
                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        {{-- New Password --}}
                        <div class="form-group mb-3">
                            <label for="password">New Password</label>
                            <div class="input-group" id="show_hide_password">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="Enter new password"
                                    required>

                                <span class="input-group-text toggle-password" style="cursor:pointer">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        {{-- <div class="form-group mb-4">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="Confirm new password"
                                    required>
                            </div>
                            @error('password_confirmation')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-block btn-primary">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- jQuery Show/Hide Password --}}
<script>
$(document).ready(function(){
    $('.toggle-password').click(function(){
        let input = $('#password');
        let icon = $(this).find('i');

        if(input.attr('type') === 'password'){
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>
@endsection
