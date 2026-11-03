@extends('frontend.site.layout')

@section('title', 'Recover stakeholder access — GOFAMINT Students’ Fellowship')
@section('description', 'Request a one-time password to reset your GSF stakeholder account.')
@section('mainClass', 'public-page auth-page stakeholder-auth-page')
@section('activeSection', 'auth')
@section('content')
<section class="auth-stage auth-stage--compact">
    <div class="site-container auth-stage__container">
        <div class="auth-card auth-card--compact">
            <aside class="auth-card__story auth-card__story--stakeholder">
                <span class="auth-card__mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF"></span>
                <div class="eyebrow eyebrow--light"><span></span> Stakeholder portal</div>
                <h1>Access can be<br><em>restored securely.</em></h1>
                <p>We’ll send a one-time verification code to the email address registered to your stakeholder account.</p>
            </aside>
            <div class="auth-card__form-wrap">
                <div class="auth-form-heading"><span>Account recovery</span><h2>Reset stakeholder password</h2><p>Enter your registered email to receive a secure reset code.</p></div>
                @if(session('message'))<div class="auth-feedback auth-feedback--success" role="status">{{ session('message') }}</div>@endif
                @if(session('error'))<div class="auth-feedback auth-feedback--error" role="alert">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="auth-feedback auth-feedback--error" role="alert">{{ $errors->first() }}</div>@endif
                <form class="auth-form" action="{{ route('stakeholders.forgot-password.send') }}" method="POST">
                    @csrf
                    <div class="auth-field"><label for="email">Registered email</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="name@example.com" required autofocus></div>
                    <input type="hidden" name="user_type" value="stakeholder">
                    <button class="button button--blue auth-submit" type="submit">Send reset code <span aria-hidden="true">→</span></button>
                </form>
                <p class="auth-form__footnote"><a href="{{ route('stakeholders.loginpage') }}">← Return to stakeholder sign in</a></p>
            </div>
        </div>
        <a class="auth-back-link" href="{{ route('home.index') }}">← Back to the GSF homepage</a>
    </div>
</section>
@endsection
