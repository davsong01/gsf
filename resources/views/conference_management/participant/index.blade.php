@extends('layouts.participant_single_edition')

@section('title', 'My Conferences')

@section('item')
<li class="breadcrumb-item"><a href="{{ route('account') }}">Dashboard</a></li>
@endsection

@section('active')
<li class="breadcrumb-item">My Conferences</li>
@endsection

@section('single')
@php
    $myConferenceRegistrations = auth()->user()->transactions->sortByDesc('created_at');
@endphp
<style>
    .my-conferences { max-width: 1500px; margin: 0 auto; color: #24314a; }
    .my-conferences__hero { position: relative; display: flex; align-items: center; justify-content: space-between; gap: 22px; overflow: hidden; margin-bottom: 22px; padding: 28px 32px; border-radius: 19px; color: #fff; background: linear-gradient(120deg,#091a3d 0%,#153f78 68%,#2366b1 100%); box-shadow: 0 17px 38px rgba(15,39,77,.16); }
    .my-conferences__hero::after { content:''; position:absolute; right:-70px; bottom:-115px; width:270px; height:270px; border:1px solid rgba(255,255,255,.14); border-radius:50%; box-shadow:0 0 0 42px rgba(255,255,255,.035),0 0 0 84px rgba(255,255,255,.025); }
    .my-conferences__hero > * { position:relative; z-index:1; }
    .my-conferences__eyebrow { margin-bottom:7px; color:#e2c46f; font-size:10px; font-weight:850; letter-spacing:.16em; text-transform:uppercase; }
    .my-conferences__hero h1 { margin:0 0 7px; color:#fff; font-family:Georgia,serif; font-size:31px; font-weight:500; letter-spacing:-.03em; }
    .my-conferences__hero p { max-width:620px; margin:0; color:rgba(255,255,255,.74); font-size:13px; line-height:1.7; }
    .my-conferences__dashboard-link { flex:0 0 auto; padding:10px 15px; border:1px solid rgba(255,255,255,.45); border-radius:9px; color:#fff; background:rgba(255,255,255,.08); font-size:12px; font-weight:750; }
    .my-conferences__dashboard-link:hover { color:#12274d; background:#fff; }
    .my-conferences__section-head { display:flex; align-items:end; justify-content:space-between; gap:15px; margin:0 0 14px; }
    .my-conferences__section-head h2 { margin:0; color:#12274d; font-family:Georgia,serif; font-size:22px; font-weight:600; }
    .my-conferences__section-head p { margin:4px 0 0; color:#7a879b; font-size:12px; }
    .my-conferences__count { padding:6px 10px; border-radius:999px; color:#174a83; background:#eaf2fa; font-size:10px; font-weight:850; }
    .my-conferences__grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,430px),1fr)); gap:17px; }
    .my-conference-card { position:relative; display:flex; flex-direction:column; min-width:0; overflow:hidden; border:1px solid #e0e7ef; border-radius:16px; background:#fff; box-shadow:0 10px 28px rgba(15,39,77,.065); transition:transform .18s ease,box-shadow .18s ease; }
    .my-conference-card:hover { transform:translateY(-2px); box-shadow:0 16px 34px rgba(15,39,77,.11); }
    .my-conference-card__top { position:relative; overflow:hidden; padding:20px 21px 17px; color:#fff; background:linear-gradient(115deg,#10264c,#194d81); }
    .my-conference-card__top::after { content:''; position:absolute; right:-26px; top:-60px; width:155px; height:155px; border:1px solid rgba(255,255,255,.12); border-radius:50%; box-shadow:0 0 0 26px rgba(255,255,255,.035); }
    .my-conference-card__top > * { position:relative; z-index:1; }
    .my-conference-card__topline { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
    .my-conference-card__tag { display:inline-flex; align-items:center; gap:6px; margin-bottom:9px; color:#e2c46f; font-size:9px; font-weight:850; letter-spacing:.13em; text-transform:uppercase; }
    .my-conference-card__top h3 { max-width:560px; margin:0; color:#fff; font-family:Georgia,serif; font-size:22px; font-weight:500; line-height:1.2; }
    .my-conference-card__date { flex:0 0 auto; padding-top:3px; color:rgba(255,255,255,.78); font-size:11px; font-weight:700; text-align:right; }
    .my-conference-card__badges { display:flex; flex-wrap:wrap; gap:7px; margin-top:13px; }
    .my-conference-card__badge { display:inline-flex; align-items:center; padding:5px 9px; border:1px solid rgba(255,255,255,.16); border-radius:999px; color:#fff; background:rgba(255,255,255,.1); font-size:9px; font-weight:800; text-transform:uppercase; }
    .my-conference-card__badge.is-active { border-color:rgba(111,220,173,.25); color:#b9f4d8; background:rgba(20,143,94,.2); }
    .my-conference-card__body { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:13px 17px; padding:18px 21px; }
    .my-conference-card__detail { min-width:0; }
    .my-conference-card__detail span { display:block; margin-bottom:4px; color:#7d899b; font-size:9px; font-weight:850; letter-spacing:.07em; text-transform:uppercase; }
    .my-conference-card__detail strong { display:block; overflow-wrap:anywhere; color:#23334f; font-size:12px; font-weight:750; }
    .my-conference-card__footer { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:auto; padding:13px 21px; border-top:1px solid #edf1f5; background:#fbfcfe; }
    .my-conference-card__payment { color:#68768d; font-size:10px; }
    .my-conference-card__payment strong { color:#24314a; }
    .my-conference-card__open { flex:0 0 auto; padding:9px 12px; border-radius:8px; color:#fff; background:#174a83; font-size:10px; font-weight:800; }
    .my-conference-card__open:hover { color:#fff; background:#102f5b; }
    .my-conferences__empty { padding:46px 22px; border:1px dashed #ccd7e4; border-radius:16px; background:#fff; text-align:center; }
    .my-conferences__empty i { display:grid; place-items:center; width:48px; height:48px; margin:0 auto 12px; border-radius:14px; color:#174a83; background:#edf4fb; font-size:19px; }
    .my-conferences__empty h3 { margin:0 0 5px; color:#172641; font-family:Georgia,serif; font-size:19px; }
    .my-conferences__empty p { margin:0 auto 14px; color:#7a879b; font-size:12px; }
    .my-conferences__empty a { display:inline-block; padding:9px 13px; border-radius:8px; color:#fff; background:#174a83; font-size:11px; font-weight:800; }
    @media(max-width:767px) {
        .my-conferences__hero { align-items:flex-start; flex-direction:column; padding:23px; border-radius:15px; }
        .my-conferences__hero h1 { font-size:26px; }
        .my-conferences__dashboard-link { width:100%; text-align:center; }
        .my-conference-card__topline { flex-direction:column; gap:8px; }
        .my-conference-card__date { text-align:left; }
    }
    @media(max-width:480px) {
        .my-conferences__section-head { align-items:flex-start; flex-direction:column; }
        .my-conference-card__top { padding:17px; }
        .my-conference-card__body { grid-template-columns:1fr; gap:11px; padding:16px 17px; }
        .my-conference-card__footer { align-items:flex-start; flex-direction:column; padding:13px 17px; }
        .my-conference-card__open { width:100%; text-align:center; }
    }
</style>

<main class="my-conferences">
    <section class="my-conferences__hero">
        <div><div class="my-conferences__eyebrow">Your GSF member portal</div><h1>My Conferences</h1><p>Your conference registrations, access details and participation information in one place.</p></div>
        <a class="my-conferences__dashboard-link" href="{{ route('account') }}"><i class="fa fa-arrow-left mr-1"></i> Member dashboard</a>
    </section>

    <div class="my-conferences__section-head">
        <div><h2>Your registrations</h2><p>Open a registration to review its details and available conference services.</p></div>
        <span class="my-conferences__count">{{ $myConferenceRegistrations->count() }} {{ \Illuminate\Support\Str::plural('registration', $myConferenceRegistrations->count()) }}</span>
    </div>

    @if($myConferenceRegistrations->isNotEmpty())
        <section class="my-conferences__grid" aria-label="Your conference registrations">
            @foreach($myConferenceRegistrations as $payment)
                @php
                    $conference = $payment->edition;
                    $registrationStatus = $payment->registration_status ?: $payment->status;
                    $isRegistrationComplete = $registrationStatus === 'Complete';
                @endphp
                @continue(!$conference)
                <article class="my-conference-card">
                    <header class="my-conference-card__top">
                        <div class="my-conference-card__topline">
                            <div><div class="my-conference-card__tag"><i class="fa fa-star-o"></i> GSF Conference</div><h3>{{ $conference->conference_theme }}</h3></div>
                            <div class="my-conference-card__date">{{ $conference->start_date ? formatDates($conference->start_date, $conference->end_date) : 'Dates to be announced' }}</div>
                        </div>
                        <div class="my-conference-card__badges">
                            <span class="my-conference-card__badge {{ $conference->status === 'active' ? 'is-active' : '' }}">{{ ucfirst($conference->status) }}</span>
                            <span class="my-conference-card__badge">{{ $payment->level ?: 'Participant' }}@if($payment->registration_user_type && strtolower($payment->registration_user_type) !== 'participant') · {{ ucfirst($payment->registration_user_type) }}@endif</span>
                        </div>
                    </header>
                    <div class="my-conference-card__body">
                        <div class="my-conference-card__detail"><span>Member login ID</span><strong>{{ $payment->user->family_id ?? auth()->user()->family_id ?? '—' }}</strong></div>
                        <div class="my-conference-card__detail"><span>Registration plan</span><strong>{{ $payment->conferenceplan->title ?? 'Conference registration' }}</strong></div>
                        <div class="my-conference-card__detail"><span>Transaction reference</span><strong>{{ $payment->transid ?: '—' }}</strong></div>
                        <div class="my-conference-card__detail"><span>Registered on</span><strong>{{ $payment->created_at?->format('d M Y') ?: '—' }}</strong></div>
                    </div>
                    <footer class="my-conference-card__footer">
                        <span class="my-conference-card__payment">Registration: <strong>{{ $registrationStatus }}</strong> · Payment: <strong>{{ $payment->status }}</strong></span>
                        <a class="my-conference-card__open" href="{{ route('conferencemanagement.show', ['conferencemanagement' => $payment->id, 'edition' => $payment->conference_edition_id]) }}">View registration <span aria-hidden="true">→</span></a>
                    </footer>
                </article>
            @endforeach
        </section>
    @else
        <section class="my-conferences__empty">
            <i class="fa fa-calendar-o" aria-hidden="true"></i>
            <h3>Your conference journey starts here.</h3>
            <p>You have no conference registrations yet. Explore upcoming GSF conferences to find your next gathering.</p>
            <a href="{{ route('conferences.index') }}">Explore GSF Conferences <span aria-hidden="true">→</span></a>
        </section>
    @endif
</main>
@endsection
