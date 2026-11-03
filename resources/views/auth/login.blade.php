@extends('frontend.site.layout')

@section('title', 'Member sign in — GOFAMINT Students’ Fellowship')
@section('description', 'Sign in to your GOFAMINT Students’ Fellowship member account.')
@section('mainClass', 'public-page auth-page')
@section('activeSection', 'auth')
@section('content')
@php
    $prefilledLoginId = old('family_id', request()->query('id', request()->query('family_id', '')));
@endphp
<section class="auth-stage">
    <div class="site-container auth-stage__container">
        <div class="auth-card">
            <aside class="auth-card__story">
                <span class="auth-card__mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF"></span>
                <div class="eyebrow eyebrow--light"><span></span> GSF Member Portal</div>
                <h1>Your fellowship.<br><em>Always within reach.</em></h1>
                <p>Stay connected to your campus community and access the tools and services that support your GSF journey.</p>
                <div class="auth-card__values"><span>Faith</span><i></i><span>Leadership</span><i></i><span>Service</span></div>
            </aside>
            <div class="auth-card__form-wrap">
                <div class="auth-form-heading"><span>Welcome back</span><h2>Sign in to your account</h2><p>Use your family ID or email address to continue.</p></div>
                @if(session('message'))<div class="auth-feedback auth-feedback--success" role="status">{{ session('message') }}</div>@endif
                @if(session('error'))<div class="auth-feedback auth-feedback--error" role="alert">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="auth-feedback auth-feedback--error" role="alert">{{ $errors->first() }}</div>@endif

                <form class="auth-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="auth-field"><label for="family_id">Family ID or email</label><input id="family_id" type="text" name="family_id" value="{{ $prefilledLoginId }}" autocomplete="username" placeholder="Enter your family ID or email" required @if(!$prefilledLoginId) autofocus @endif></div>
                    <div class="auth-field"><label for="password">Password</label><div class="auth-password"><input id="password" type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required @if($prefilledLoginId) autofocus @endif><button type="button" data-password-toggle="password" aria-label="Show password">Show</button></div></div>
                    <div class="auth-form__options"><label class="auth-checkbox"><input type="checkbox" name="remember" value="1"><span>Remember me</span></label><a href="{{ route('password.request') }}">Forgot password?</a></div>
                    <button class="button button--blue auth-submit" type="submit">Sign in <span aria-hidden="true">→</span></button>
                </form>
                <p class="auth-form__footnote">Need access to the stakeholder administration portal? <a href="{{ route('stakeholders.loginpage') }}">Stakeholder sign in</a>.</p>
            </div>
        </div>
        <a class="auth-back-link" href="{{ route('home.index') }}">← Back to the GSF homepage</a>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        const showing = input.type === 'password';
        input.type = showing ? 'text' : 'password';
        button.textContent = showing ? 'Hide' : 'Show';
        button.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
    });
});
</script>
@endpush
