@extends('layouts.dashboard')

@section('title', 'Member Dashboard')
@section('active')
<li class="breadcrumb-item">Dashboard</li>
@endsection

@section('content')
@php
    $completedRegistrations = $transactions->where('registration_status', 'Complete')->count();
    $conferenceCount = $transactions->pluck('conference_edition_id')->filter()->unique()->count();
    $profileFields = collect([$user->name, $user->email, $user->phone, $user->gender, $user->dob, $user->chapter_id, $user->course, $user->matric_year]);
    $profileCompletion = (int) round(($profileFields->filter()->count() / $profileFields->count()) * 100);
@endphp
<style>
    .member-dashboard { max-width: 1500px; margin: 0 auto; color: #24314a; }
    .member-dashboard__hero { position: relative; display: grid; grid-template-columns: minmax(0,1fr) auto; align-items: center; gap: 24px; overflow: hidden; padding: 34px; border-radius: 22px; color: #fff; background: linear-gradient(120deg,#091a3d 0%,#153f78 68%,#2366b1 100%); box-shadow: 0 18px 44px rgba(15,39,77,.18); }
    .member-dashboard__hero::after { content:''; position:absolute; right:-90px; bottom:-135px; width:330px; height:330px; border:1px solid rgba(255,255,255,.13); border-radius:50%; box-shadow:0 0 0 48px rgba(255,255,255,.035),0 0 0 96px rgba(255,255,255,.025); }
    .member-dashboard__hero > * { position:relative; z-index:1; }
    .member-dashboard__eyebrow { margin-bottom:8px; color:#e2c46f; font-size:10px; font-weight:850; letter-spacing:.16em; text-transform:uppercase; }
    .member-dashboard__hero h1 { margin:0 0 9px; color:#fff; font-family:Georgia,serif; font-size:clamp(29px,4vw,42px); font-weight:500; letter-spacing:-.035em; }
    .member-dashboard__hero p { max-width:650px; margin:0; color:rgba(255,255,255,.72); font-size:14px; line-height:1.7; }
    .member-dashboard__identity { min-width:205px; padding:15px 18px; border:1px solid rgba(255,255,255,.16); border-radius:14px; background:rgba(255,255,255,.08); backdrop-filter:blur(8px); }
    .member-dashboard__identity span { display:block; color:rgba(255,255,255,.58); font-size:9px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
    .member-dashboard__identity strong { display:block; margin-top:5px; color:#fff; font-size:16px; }
    .member-dashboard__stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; margin:20px 0; }
    .member-stat { padding:20px 22px; border:1px solid #e2e8f0; border-radius:16px; background:#fff; box-shadow:0 9px 26px rgba(15,39,77,.055); }
    .member-stat span { display:block; color:#7a879b; font-size:10px; font-weight:850; letter-spacing:.08em; text-transform:uppercase; }
    .member-stat strong { display:block; margin-top:8px; color:#153f78; font-family:Georgia,serif; font-size:27px; font-weight:600; }
    .member-dashboard__grid { display:grid; grid-template-columns:minmax(0,1.25fr) minmax(310px,.75fr); gap:20px; align-items:start; }
    .member-panel { overflow:hidden; border:1px solid #e2e8f0; border-radius:18px; background:#fff; box-shadow:0 11px 30px rgba(15,39,77,.06); }
    .member-panel__head { display:flex; align-items:center; justify-content:space-between; gap:15px; padding:20px 22px; border-bottom:1px solid #edf1f5; }
    .member-panel__head h2 { margin:0 0 3px; color:#12274d; font-family:Georgia,serif; font-size:21px; font-weight:600; }
    .member-panel__head p { margin:0; color:#7a879b; font-size:11px; }
    .member-panel__head a { color:#175792; font-size:11px; font-weight:800; }
    .member-registration { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:16px; padding:17px 22px; border-bottom:1px solid #edf1f5; }
    .member-registration:last-child { border-bottom:0; }
    .member-registration h3 { margin:0 0 5px; color:#172641; font-size:14px; }
    .member-registration p { margin:0; color:#7a879b; font-size:11px; line-height:1.6; }
    .member-registration__status { align-self:center; padding:5px 9px; border-radius:999px; color:#8a4d14; background:#fff3dc; font-size:9px; font-weight:850; text-transform:uppercase; }
    .member-registration__status.is-complete { color:#087052; background:#e5f6ef; }
    .member-dashboard__actions { display:grid; gap:11px; padding:18px; }
    .member-action { display:grid; grid-template-columns:42px minmax(0,1fr) auto; align-items:center; gap:12px; padding:13px; border:1px solid #e4eaf1; border-radius:13px; color:#24314a; background:#fbfcfe; }
    .member-action:hover { color:#153f78; border-color:#bed0e5; background:#f5f9fd; }
    .member-action i { display:grid; place-items:center; width:42px; height:42px; border-radius:11px; color:#fff; background:#153f78; font-size:16px; }
    .member-action strong { display:block; color:#172641; font-size:13px; }
    .member-action small { display:block; margin-top:3px; color:#7a879b; font-size:10px; }
    .member-action b { color:#b38c2e; font-size:16px; }
    .member-dashboard__empty { padding:34px 22px; color:#7a879b; text-align:center; font-size:12px; }
    @media(max-width:850px){ .member-dashboard__grid{grid-template-columns:1fr}.member-dashboard__hero{grid-template-columns:1fr}.member-dashboard__identity{min-width:0}.member-dashboard__stats{grid-template-columns:1fr 1fr} }
    @media(max-width:520px){ .member-dashboard__hero{padding:25px 22px;border-radius:16px}.member-dashboard__stats{grid-template-columns:1fr}.member-registration{grid-template-columns:1fr}.member-registration__status{justify-self:start} }
</style>

<div class="content-body member-dashboard">
    @include('includes.alerts')
    <section class="member-dashboard__hero">
        <div><div class="member-dashboard__eyebrow">Your GSF member portal</div><h1>Welcome, {{ $user->name }}.</h1><p>Manage your profile, review your conference registrations and discover what is happening across the GSF community.</p></div>
        <div class="member-dashboard__identity"><span>Member ID</span><strong>{{ $user->family_id ?: 'Pending assignment' }}</strong></div>
    </section>

    <section class="member-dashboard__stats">
        <article class="member-stat"><span>Conferences</span><strong>{{ $conferenceCount }}</strong></article>
        <article class="member-stat"><span>Completed registrations</span><strong>{{ $completedRegistrations }}</strong></article>
        <article class="member-stat"><span>Profile completion</span><strong>{{ $profileCompletion }}%</strong></article>
    </section>

    <div class="member-dashboard__grid">
        <section class="member-panel">
            <header class="member-panel__head"><div><h2>Recent registrations</h2><p>Your latest GSF conference activity.</p></div>@if($transactions->isNotEmpty())<a href="{{ route('conferencemanagement.index') }}">View all →</a>@endif</header>
            @forelse($transactions->take(4) as $transaction)
                <a class="member-registration" href="{{ route('conferencemanagement.show', ['conferencemanagement' => $transaction->id, 'edition' => $transaction->conference_edition_id]) }}">
                    <div><h3>{{ $transaction->edition->conference_theme ?? 'GSF Conference' }}</h3><p>{{ $transaction->conferenceplan->title ?? 'Conference registration' }} · {{ $transaction->created_at?->format('d M Y') }}</p></div>
                    <span class="member-registration__status {{ $transaction->registration_status === 'Complete' ? 'is-complete' : '' }}">{{ $transaction->registration_status ?: $transaction->status }}</span>
                </a>
            @empty
                <div class="member-dashboard__empty">You have no conference registrations yet.</div>
            @endforelse
        </section>

        <aside class="member-panel">
            <header class="member-panel__head"><div><h2>Quick access</h2><p>Choose where you want to go.</p></div></header>
            <div class="member-dashboard__actions">
                <a class="member-action" href="{{ route('member.profile') }}"><i class="fa fa-user-circle-o"></i><span><strong>My profile</strong><small>Update your member and directory details</small></span><b>→</b></a>
                <a class="member-action" href="{{ route('conferencemanagement.index') }}"><i class="fa fa-calendar"></i><span><strong>My conferences</strong><small>Open registrations, badges and allocations</small></span><b>→</b></a>
                <a class="member-action" href="{{ route('conferences.index') }}"><i class="fa fa-compass"></i><span><strong>Explore conferences</strong><small>Discover upcoming GSF gatherings</small></span><b>↗</b></a>
                @if($user->slug)<a class="member-action" href="{{ route('user.single', $user->slug) }}" target="_blank"><i class="fa fa-address-card-o"></i><span><strong>Public profile</strong><small>See how your directory profile appears</small></span><b>↗</b></a>@endif
            </div>
        </aside>
    </div>
</div>
@endsection
