@extends('frontend.site.layout')

@section('title', "Privacy Policy — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page')

@section('content')
<section class="inner-hero inner-hero--strategies">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid"><div class="inner-hero__copy">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>Privacy</span></nav>
        <div class="eyebrow eyebrow--light"><span></span> Your Information</div>
        <h1>Privacy at <em>GSF.</em></h1>
        <p>How the GOFAMINT Students’ Fellowship uses information shared through this website and member services.</p>
    </div></div>
</section>

<section class="site-info site-info--policy"><div class="site-container site-policy">
    <p class="site-policy__updated">Last updated: 3 November 2026</p>
    <p>GOFAMINT Students’ Fellowship (GSF) operates this website and its directory, member, award and conference services. This notice explains what we collect, why we use it and how to contact us about your information.</p>

    <h2>Information we collect</h2>
    <p>Depending on the service you use, we may collect your name, contact details, campus and fellowship details, profile information, conference registration details, award application information, messages you send us, and payment references and status. The website also processes technical information needed for sessions, security and operation.</p>

    <h2>How we use it</h2>
    <p>We use information to provide accounts and directory profiles, manage fellowships and events, process registrations and applications, respond to enquiries, keep records, and protect the website. Where relevant, processing may rely on providing a requested service, a legal obligation, a legitimate operational need, or your consent for an optional use.</p>

    <h2>Public directory information</h2>
    <p>Some profile and campus details may be visible on public directory pages. Please review the visibility options in your profile before submitting information you do not want displayed publicly. You can ask us to correct or review a public listing through the <a href="{{ route('about.contact') }}">Contact Us page</a>.</p>

    <h2>Payments and service providers</h2>
    <p>Online conference payments are handled through payment providers. Those providers may process payment details under their own privacy notices; GSF receives transaction information needed to verify and manage registrations. We may also use hosting and email services to operate the website and deliver messages.</p>

    <h2>Cookies and local storage</h2>
    <p>Essential cookies and similar storage support sign-in, security and form sessions. This site also remembers when you dismiss the privacy notice. A payment provider may use its own technology during checkout. You can manage cookies through your browser, though blocking essential storage may prevent account or form features from working.</p>

    <h2>Retention and security</h2>
    <p>We keep information while it is needed to provide the service, maintain relevant records or meet applicable obligations. We use access controls and other reasonable measures to protect it. No online system can guarantee absolute security.</p>

    <h2>Your choices and rights</h2>
    <p>You may request access to, correction or deletion of your personal information, subject to applicable law and record-keeping needs. Where a use depends on consent, you can withdraw it. You may also raise a concern with an applicable data protection authority. These rights may apply under the Nigeria Data Protection Act and, where relevant, the GDPR.</p>

    <h2>Contact us</h2>
    @php($contactEmail = $siteSettings?->site_contact_email ?: $siteSettings?->official_email)
    <p>For privacy requests, use our <a href="{{ route('about.contact') }}">Contact Us page</a>@if(filled($contactEmail)) or email <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif. We may need to verify your identity before acting on an account request.</p>
</div></section>
@endsection
