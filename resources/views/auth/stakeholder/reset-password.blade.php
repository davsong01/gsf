@extends('frontend.spaces.layouts.app')
@section('title', 'Stakeholder Login')
@section('ogtitle', 'Stakeholder Login')
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
            <div class="col-12 text-center mb-5">
                <h5 style="color:red">ADMINISTRATOR ACCESS ONLY</h5>
                <p style="color:blue">LOGIN AS AN ADMINISTRATOR</p>
                <div class="divider-text text-uppercase text-muted"><small></small></div>
            </div>

            <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
                <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-500">

                    @include('includes.alerts')

                    <form action="{{ route('stakeholders.login') }}" method="POST">
                        @csrf

                        {{-- Email --}}
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="text" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required>
                            </div>
                            @error('email')
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

                        {{-- Forgot Password --}}
                        <div class="d-flex justify-content-start mb-4">
                            <div>
                                <a href="{{ route('stakeholders.forgot-password') }}" class="small text-right">
                                    Forgot Password?
                                </a>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-block btn-primary">Sign in</button>
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
        let input = $(this).closest('#show_hide_password').find('input');
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
