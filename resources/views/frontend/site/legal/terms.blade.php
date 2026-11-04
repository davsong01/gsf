@extends('frontend.site.layout')

@section('title', "Terms & Conditions — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page')

@section('content')
<section class="inner-hero inner-hero--strategies">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid"><div class="inner-hero__copy">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>Terms &amp; Conditions</span></nav>
        <div class="eyebrow eyebrow--light"><span></span> Website Terms</div>
        <h1>Using the GSF <em>website.</em></h1>
        <p>Guidelines for the directory, member accounts, conferences and other services offered here.</p>
    </div></div>
</section>

<section class="site-info site-info--policy"><div class="site-container site-policy">
    <p class="site-policy__updated">Last updated: 3 November 2026</p>
    <p>These terms apply when you use the GOFAMINT Students’ Fellowship (GSF) website. Please use the site and its services responsibly.</p>

    <h2>Accounts and information</h2>
    <p>Provide accurate information when creating an account, joining the directory, applying for an award or registering for an event. Keep your login details private and tell us if you suspect unauthorised access. You are responsible for information submitted through your account.</p>

    <h2>Directory and member profiles</h2>
    <p>Directory content is intended to help GSF members and campus fellowships connect. Do not copy, misuse or publish another person’s information without permission. Where profile details are public, visibility is explained in our <a href="{{ route('site.privacy') }}">Privacy Policy</a>.</p>

    <h2>Conferences, awards and payments</h2>
    <p>Each event or award may have its own eligibility rules, deadlines, fees and instructions shown on its page. A registration or application is subject to those published details and any verification required by GSF. Payment providers may also apply their own checkout terms. If a payment or registration needs review, contact us with your reference before paying again.</p>

    <h2>Acceptable use</h2>
    <p>Do not interfere with the website, submit misleading or unlawful material, try to access another account, or use the directory to send unwanted messages. GSF may restrict access or remove content that undermines these services or the safety of users.</p>

    <h2>Content and availability</h2>
    <p>GSF publishes information to support the Fellowship. Event details, directory listings and other content may change. We work to keep the site available and accurate, but technical interruptions and corrections can occur.</p>

    <h2>Questions</h2>
    <p>If you need help with these terms or a service on this website, please <a href="{{ route('about.contact') }}">contact GSF</a>.</p>
</div></section>
@endsection
