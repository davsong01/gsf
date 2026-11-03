@extends('frontend.site.layout')

@section('title', 'Choose a new password — GOFAMINT Students’ Fellowship')
@section('description', 'Choose a new password for your GSF member account.')
@section('mainClass', 'public-page auth-page')
@section('activeSection', 'auth')
@section('content')
<section class="auth-stage auth-stage--compact">
    <div class="site-container auth-stage__container">
        <div class="auth-card auth-card--compact">
            <aside class="auth-card__story">
                <span class="auth-card__mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF"></span>
                <div class="eyebrow eyebrow--light"><span></span> Account recovery</div>
                <h1>A fresh start<br><em>for your account.</em></h1>
                <p>Choose a strong password you haven’t used for this account before.</p>
            </aside>
            <div class="auth-card__form-wrap">
                <div class="auth-form-heading"><span>Secure your account</span><h2>Choose a new password</h2><p>Enter and confirm your new password below.</p></div>
                @if($errors->any())<div class="auth-feedback auth-feedback--error" role="alert">{{ $errors->first() }}</div>@endif
                <form class="auth-form" method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="auth-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" autocomplete="email" readonly required></div>
                    <div class="auth-field"><label for="password">New password</label><div class="auth-password"><input id="password" type="password" name="password" autocomplete="new-password" required autofocus><button type="button" data-password-toggle="password" aria-label="Show password">Show</button></div></div>
                    <div class="auth-field"><label for="password_confirmation">Confirm new password</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
                    <button class="button button--blue auth-submit" type="submit">Save new password <span aria-hidden="true">→</span></button>
                </form>
                <p class="auth-form__footnote"><a href="{{ route('login') }}">← Return to sign in</a></p>
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
