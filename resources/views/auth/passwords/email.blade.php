@extends('frontend.site.layout')

@section('title', 'Reset your password — GOFAMINT Students’ Fellowship')
@section('description', 'Request a secure password reset link for your GSF member account.')
@section('mainClass', 'public-page auth-page')
@section('activeSection', 'auth')
@section('content')
<section class="auth-stage auth-stage--compact">
    <div class="site-container auth-stage__container">
        <div class="auth-card auth-card--compact">
            <aside class="auth-card__story">
                <span class="auth-card__mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF"></span>
                <div class="eyebrow eyebrow--light"><span></span> Account recovery</div>
                <h1>Let’s get you<br><em>back in.</em></h1>
                <p>Enter the email address linked to your member account and we’ll send you a secure reset link.</p>
            </aside>
            <div class="auth-card__form-wrap">
                <div class="auth-form-heading"><span>Forgot password</span><h2>Reset your password</h2><p>If we find your account, we’ll email a password reset link.</p></div>
                @if(session('status'))<div class="auth-feedback auth-feedback--success" role="status">{{ session('status') }}</div>@endif
                @if($errors->any())<div class="auth-feedback auth-feedback--error" role="alert">{{ $errors->first() }}</div>@endif
                <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="auth-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com" required autofocus></div>
                    <button class="button button--blue auth-submit" type="submit">Send reset link <span aria-hidden="true">→</span></button>
                </form>
                <p class="auth-form__footnote"><a href="{{ route('login') }}">← Return to sign in</a></p>
            </div>
        </div>
        <a class="auth-back-link" href="{{ route('home.index') }}">← Back to the GSF homepage</a>
    </div>
</section>
@endsection
