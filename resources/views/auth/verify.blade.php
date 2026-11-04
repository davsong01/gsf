@extends('frontend.site.layout')

@section('title', 'Verify your email — GOFAMINT Students’ Fellowship')
@section('description', 'Verify the email address connected to your GSF account.')
@section('mainClass', 'public-page auth-page')
@section('activeSection', 'auth')
@section('content')
<section class="auth-stage auth-stage--compact">
    <div class="site-container auth-stage__container">
        <div class="auth-card auth-card--compact">
            <aside class="auth-card__story">
                <span class="auth-card__mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF"></span>
                <div class="eyebrow eyebrow--light"><span></span> One connected fellowship</div>
                <h1>One last step<br><em>to stay connected.</em></h1>
                <p>Confirming your email helps keep your GSF account secure and your member details up to date.</p>
            </aside>
            <div class="auth-card__form-wrap auth-verification-card">
                <div class="auth-verification-icon" aria-hidden="true">✉</div>
                <div class="auth-form-heading"><span>Email verification</span><h2>Check your inbox</h2><p>Before continuing, open the verification link we sent to your email.</p></div>
                @if(session('resent'))<div class="auth-feedback auth-feedback--success" role="status">A fresh verification link has been sent to your email address.</div>@endif
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button class="button button--blue auth-submit" type="submit">Send a new verification link <span aria-hidden="true">→</span></button>
                </form>
            </div>
        </div>
        <a class="auth-back-link" href="{{ route('home.index') }}">← Back to the GSF homepage</a>
    </div>
</section>
@endsection
