@extends('frontend.site.layout')

@section('title', 'Stakeholder sign in — GOFAMINT Students’ Fellowship')
@section('description', 'Secure stakeholder access to GSF administration, appraisals and reports.')
@section('mainClass', 'public-page auth-page stakeholder-auth-page')
@section('activeSection', 'auth')
@section('content')
<section class="auth-stage">
    <div class="site-container auth-stage__container">
        <div class="auth-card">
            <aside class="auth-card__story auth-card__story--stakeholder">
                <span class="auth-card__mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF"></span>
                <div class="eyebrow eyebrow--light"><span></span> National Secretariat</div>
                <h1>Lead with care.<br><em>Serve with purpose.</em></h1>
                <p>Access the GSF stakeholder workspace for leadership tools, appraisals, reports and member services.</p>
                <div class="auth-security-note"><span aria-hidden="true">◈</span><span><strong>Secure access</strong><small>For authorised GSF stakeholders</small></span></div>
            </aside>
            <div class="auth-card__form-wrap">
                <div class="auth-form-heading"><span>Stakeholder portal</span><h2>Sign in to continue</h2><p>Use the email address and password connected to your stakeholder account.</p></div>
                @if(session('message'))<div class="auth-feedback auth-feedback--success" role="status">{{ session('message') }}</div>@endif
                @if(session('error'))<div class="auth-feedback auth-feedback--error" role="alert">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="auth-feedback auth-feedback--error" role="alert">{{ $errors->first() }}</div>@endif
                <form class="auth-form" action="{{ route('stakeholders.login') }}" method="POST">
                    @csrf
                    <div class="auth-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="name@example.com" required autofocus></div>
                    <div class="auth-field"><label for="stakeholder-password">Password</label><div class="auth-password"><input id="stakeholder-password" type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required><button type="button" data-password-toggle="stakeholder-password" aria-label="Show password">Show</button></div></div>
                    <div class="auth-form__options auth-form__options--end"><a href="{{ route('stakeholders.forgot-password') }}">Forgot password?</a></div>
                    <button class="button button--blue auth-submit" type="submit">Stakeholder sign in <span aria-hidden="true">→</span></button>
                </form>
                <p class="auth-form__footnote">Looking for the member portal? <a href="{{ route('login') }}">Member sign in</a>.</p>
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
