@extends('frontend.site.layout')

@section('title', 'Confirm your password — GOFAMINT Students’ Fellowship')
@section('description', 'Confirm your GSF account password to continue to a protected action.')
@section('mainClass', 'public-page auth-page')
@section('activeSection', 'auth')
@section('content')
<section class="auth-stage auth-stage--compact">
    <div class="site-container auth-stage__container">
        <div class="auth-card auth-card--compact">
            <aside class="auth-card__story">
                <span class="auth-card__mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF"></span>
                <div class="eyebrow eyebrow--light"><span></span> Keep your account secure</div>
                <h1>Confirm it’s<br><em>you.</em></h1>
                <p>For your protection, please confirm your password before continuing.</p>
            </aside>
            <div class="auth-card__form-wrap">
                <div class="auth-form-heading"><span>Security check</span><h2>Confirm your password</h2><p>Your password is needed to continue to this protected action.</p></div>
                @if($errors->any())<div class="auth-feedback auth-feedback--error" role="alert">{{ $errors->first() }}</div>@endif
                <form class="auth-form" method="POST" action="{{ route('password.confirm') }}">
                    @csrf
                    <div class="auth-field"><label for="password">Password</label><div class="auth-password"><input id="password" type="password" name="password" autocomplete="current-password" required autofocus><button type="button" data-password-toggle="password" aria-label="Show password">Show</button></div></div>
                    <button class="button button--blue auth-submit" type="submit">Confirm and continue <span aria-hidden="true">→</span></button>
                </form>
                <p class="auth-form__footnote"><a href="{{ route('password.request') }}">Forgot your password?</a></p>
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
