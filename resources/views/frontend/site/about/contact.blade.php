@extends('frontend.site.layout')

@section('title', "Contact GSF — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page')
@section('activeSection', 'about')

@section('content')
<section class="inner-hero inner-hero--strategies">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>About GSF</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> Contact Us</div>
            <h1>Let’s stay <em>connected.</em></h1>
            <p>Have a question about the Fellowship, a campus, a conference or your account? Send us a message.</p>
        </div>
    </div>
</section>

<section class="site-info">
    <div class="site-container site-info__grid">
        <div class="site-info__intro">
            <div class="eyebrow"><span></span> Get in Touch</div>
            <h2>We’d love to hear from you.</h2>
            <p>Use the form and the GSF team will receive your message. For campus-specific questions, you can also contact the fellowship through its campus page.</p>
            @php($contactEmail = $siteSettings?->site_contact_email ?: $siteSettings?->official_email)
            @if(filled($contactEmail))
                <a class="site-info__email" href="mailto:{{ $contactEmail }}">{{ $contactEmail }} <span aria-hidden="true">↗</span></a>
            @endif
            <div class="site-info__links">
                <a href="{{ route('people.campuses') }}">Find a campus <span aria-hidden="true">→</span></a>
                <a href="{{ route('conferences.index') }}">Explore conferences <span aria-hidden="true">→</span></a>
                <a href="{{ route('site.privacy') }}">Privacy questions <span aria-hidden="true">→</span></a>
            </div>
        </div>

        <div class="site-contact-card">
            <h2>Send a message</h2>
            <p>Tell us how we can help, and include the details we need to respond.</p>
            @if(session('contact_success'))<div class="site-contact-card__feedback" role="status">{{ session('contact_success') }}</div>@endif
            @if(session('contact_error'))<div class="site-contact-card__feedback site-contact-card__feedback--error" role="alert">{{ session('contact_error') }}</div>@endif
            @if($errors->any())<div class="site-contact-card__feedback site-contact-card__feedback--error" role="alert">Please check the highlighted fields below.</div>@endif
            <form action="{{ route('about.contact.submit') }}" method="POST">
                @csrf
                <div class="site-contact-card__row">
                    <div><label for="contact-name">Your name</label><input id="contact-name" name="name" value="{{ old('name') }}" maxlength="120" autocomplete="name" required>@error('name')<small class="site-contact-card__error">{{ $message }}</small>@enderror</div>
                    <div><label for="contact-email">Email address</label><input id="contact-email" name="email" type="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" required>@error('email')<small class="site-contact-card__error">{{ $message }}</small>@enderror</div>
                </div>
                <label for="contact-subject">Subject</label><input id="contact-subject" name="subject" value="{{ old('subject') }}" maxlength="160" placeholder="How can we help?" required>@error('subject')<small class="site-contact-card__error">{{ $message }}</small>@enderror
                <label for="contact-message">Message</label><textarea id="contact-message" name="message" rows="6" maxlength="5000" required>{{ old('message') }}</textarea>@error('message')<small class="site-contact-card__error">{{ $message }}</small>@enderror
                <div class="site-contact-card__honeypot" aria-hidden="true"><label for="contact-website">Website</label><input id="contact-website" name="website" tabindex="-1" autocomplete="off"></div>
                <button class="button button--blue" type="submit">Send message <span aria-hidden="true">→</span></button>
                <p class="site-contact-card__privacy">Your details are used to respond to this message. <a href="{{ route('site.privacy') }}">Read our privacy policy.</a></p>
            </form>
        </div>
    </div>
</section>
@endsection
