@extends('frontend.site.layout')

@section('title', 'Choose a new stakeholder password — GOFAMINT Students’ Fellowship')
@section('description', 'Choose a new password for your GSF stakeholder account.')
@section('mainClass', 'public-page auth-page stakeholder-auth-page')
@section('activeSection', 'auth')
@section('content')
<section class="auth-stage auth-stage--compact">
    <div class="site-container auth-stage__container">
        <div class="auth-card auth-card--compact">
            <aside class="auth-card__story auth-card__story--stakeholder">
                <span class="auth-card__mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF"></span>
                <div class="eyebrow eyebrow--light"><span></span> Secure your access</div>
                <h1>Choose a new<br><em>password.</em></h1>
                <p>Your new password will protect access to stakeholder reports, appraisals and administration tools.</p>
            </aside>
            <div class="auth-card__form-wrap">
                <div class="auth-form-heading"><span>Final step</span><h2>Set a new password</h2><p>Choose a password with at least six characters.</p></div>
                @if(session('error'))<div class="auth-feedback auth-feedback--error" role="alert">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="auth-feedback auth-feedback--error" role="alert">{{ $errors->first() }}</div>@endif
                <form class="auth-form" action="{{ route('stakeholders.reset-password.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="auth-field"><label for="stakeholder-new-password">New password</label><div class="auth-password"><input id="stakeholder-new-password" type="password" name="password" autocomplete="new-password" minlength="6" required autofocus><button type="button" data-password-toggle="stakeholder-new-password" aria-label="Show password">Show</button></div></div>
                    <button class="button button--blue auth-submit" type="submit">Save new password <span aria-hidden="true">→</span></button>
                </form>
                <p class="auth-form__footnote"><a href="{{ route('stakeholders.loginpage') }}">← Return to stakeholder sign in</a></p>
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
