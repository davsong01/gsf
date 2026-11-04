@extends('frontend.site.layout')

@php
    $edition = $transaction->edition;
    $conferenceUrl = $edition && $edition->slug ? route('conferences.show', $edition->slug) : route('conferences.index');
    $isComplete = $transaction->registration_status === 'Complete' || $transaction->status === 'Complete';
    $participantUser = $transaction->user;
    $loginIdentifier = $participantUser?->family_id ?: $participantUser?->email ?: $transaction->email;
@endphp

@section('title', 'Registration received — '.($edition->conference_theme ?? 'GSF Conference'))
@section('description', 'Your conference registration details for '.($edition->conference_theme ?? 'GSF Conference').'.')
@section('mainClass', 'public-page conference-flow-page conference-thankyou-page')
@section('activeSection', 'conferences')

@push('head')
<style>
    .conference-thankyou-page .conference-flow-step { font-size: 12px; }
    .conference-thankyou-page .conference-flow-card__heading p { font-size: 17px; }
    .conference-thankyou-page .conference-summary-grid > div { gap: 9px; padding: 17px; }
    .conference-thankyou-page .conference-summary-grid span { font-size: 13px; }
    .conference-thankyou-page .conference-summary-grid strong { font-size: 16px; line-height: 1.5; }
    .conference-thankyou-page .conference-receipt-status { font-size: 16px; }
    .conference-thankyou-page .conference-account-card > p { font-size: 15px; }
    .conference-thankyou-page .conference-account-card > div span { font-size: 12px; }
    .conference-thankyou-page .conference-account-card > div strong { font-size: 16px; line-height: 1.5; }
    .conference-thankyou-page .conference-account-card > a { font-size: 15px; }
    .conference-thankyou-page .conference-thankyou-back { font-size: 14px; }
</style>
@endpush

@section('content')
<section class="conference-flow-hero conference-flow-hero--compact">
    <div class="site-container">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ $conferenceUrl }}">{{ $edition->conference_theme ?? 'GSF Conference' }}</a><span>·</span><span>Registration received</span></nav>
        <div class="eyebrow eyebrow--light"><span></span> {{ $isComplete ? 'You’re registered' : 'Registration update' }}</div>
        <h1>{{ $isComplete ? 'We’ll see you there.' : 'Thank you, '.($transaction->name ?: 'friend').'.' }}</h1>
        <p>{{ $isComplete ? 'Your place is confirmed. We’re glad you’ll be joining us.' : 'Your registration details have been received. Review your status and next steps below.' }}</p>
    </div>
</section>

<section class="conference-flow-section">
    <div class="site-container conference-thankyou-layout">
        <div class="conference-thankyou-main">
            <div class="conference-success-mark {{ $isComplete ? '' : 'conference-success-mark--pending' }}" aria-hidden="true">{{ $isComplete ? '✓' : '…' }}</div>
            <div class="conference-flow-card">
                <div class="conference-flow-card__heading"><span class="conference-flow-step">REGISTRATION DETAILS</span><h2>{{ $isComplete ? 'You’re part of the gathering.' : 'Your details are being processed.' }}</h2><p>{{ $isComplete ? 'A confirmation email with your account information and next steps has been sent to '.($transaction->email ?: 'your email address').'.' : 'Keep your reference below. If your registration requires an additional step, the conference team will contact you.' }}</p></div>
                <div class="conference-summary-grid conference-summary-grid--receipt">
                    <div><span>Name</span><strong>{{ $transaction->name ?? '—' }}</strong></div>
                    <div><span>Email</span><strong>{{ $transaction->email ?? '—' }}</strong></div>
                    <div><span>Phone</span><strong>{{ $transaction->phone ?? '—' }}</strong></div>
                    <div><span>Registration type</span><strong>{{ $transaction->conferenceplan->title ?? 'Conference registration' }}</strong></div>
                    @if($transaction->gender)<div><span>Gender</span><strong>{{ ucfirst($transaction->gender) }}</strong></div>@endif
                    <div><span>Registration status</span><strong class="conference-receipt-status {{ $isComplete ? 'is-complete' : 'is-pending' }}">{{ $transaction->registration_status ?: $transaction->status }}</strong></div>
                    @if($transaction->transid)<div><span>Reference</span><strong>{{ $transaction->transid }}</strong></div>@endif
                    @if($transaction->amount_paid)<div><span>Amount</span><strong>₦{{ number_format((float) $transaction->total_amount, 2) }}</strong></div>@endif
                    @if($transaction->hostel)<div><span>Accommodation</span><strong>{{ $transaction->hostel->name }}@if($transaction->hostel_allocation_number) · {{ $transaction->hostel_allocation_number }}@endif</strong></div>@endif
                    @if($transaction->food)<div><span>Service point</span><strong>{{ $transaction->food->name }}@if($transaction->service_point_allocation_number) · {{ $transaction->service_point_allocation_number }}@endif</strong></div>@endif
                </div>
            </div>
        </div>

        <aside class="conference-thankyou-aside">
            @if($participantUser)
                <article class="conference-account-card">
                    <div class="eyebrow eyebrow--light"><span></span> YOUR MEMBER ACCOUNT</div>
                    <h2>Keep your details close.</h2>
                    <p>Use your account to access your conference information and member services.</p>
                    <div><span>Login ID</span><strong>{{ $loginIdentifier }}</strong></div>
                    <div><span>Password</span><strong>Your registered phone number</strong></div>
                    @if(auth()->check())
                        <a href="{{ route('account') }}">Go to your account <span aria-hidden="true">→</span></a>
                    @else
                        <a href="{{ route('login', ['id' => $loginIdentifier]) }}">Open member portal <span aria-hidden="true">→</span></a>
                    @endif
                </article>
            @endif
            @if($transaction->remarks)<article class="conference-flow-help"><div><strong>Registration note</strong><p>{{ $transaction->remarks }}</p></div></article>@endif
            <a class="conference-thankyou-back" href="{{ $conferenceUrl }}">Return to conference page <span aria-hidden="true">↗</span></a>
        </aside>
    </div>
</section>
@endsection
