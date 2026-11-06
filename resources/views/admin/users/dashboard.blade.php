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
    .member-dashboard { max-width: 1440px; margin: 0 auto; color: #1b2940; }
    .member-dashboard__hero { position:relative; display:flex; align-items:center; justify-content:space-between; gap:28px; overflow:hidden; padding:clamp(25px,4vw,46px); border-radius:24px; color:#fff; background:linear-gradient(116deg,#10264a 0%,#173f70 57%,#28748a 100%); box-shadow:0 20px 48px rgba(19,48,83,.16); }
    .member-dashboard__hero::after { content:''; position:absolute; top:-145px; right:13%; width:370px; height:370px; border:1px solid rgba(255,255,255,.12); border-radius:50%; box-shadow:0 0 0 55px rgba(255,255,255,.035),0 0 0 110px rgba(255,255,255,.025); }
    .member-dashboard__hero > * { position:relative; z-index:1; }
    .member-dashboard__intro { max-width:700px; }
    .member-dashboard__eyebrow { display:flex; align-items:center; gap:9px; margin-bottom:12px; color:#f1cf79; font-size:10px; font-weight:850; letter-spacing:.15em; text-transform:uppercase; }
    .member-dashboard__eyebrow::before { content:''; width:22px; height:2px; background:#f1cf79; }
    .member-dashboard__hero h1 { margin:0 0 10px; color:#fff; font-family:Georgia,serif; font-size:clamp(30px,4vw,46px); font-weight:500; letter-spacing:-.04em; }
    .member-dashboard__hero p { max-width:600px; margin:0; color:rgba(255,255,255,.76); font-size:14px; line-height:1.75; }
    .member-dashboard__hero-actions { display:flex; flex-wrap:wrap; gap:10px; margin-top:24px; }
    .member-dashboard__button { display:inline-flex; align-items:center; gap:9px; padding:11px 15px; border:1px solid rgba(255,255,255,.24); border-radius:10px; color:#fff!important; background:rgba(255,255,255,.09); font-size:11px; font-weight:800; transition:background .2s,transform .2s; }
    .member-dashboard__button:hover { background:rgba(255,255,255,.17); transform:translateY(-1px); }
    .member-dashboard__button--primary { border-color:#f0ca69; color:#172d4b!important; background:#f0ca69; }
    .member-dashboard__button--primary:hover { background:#ffda7e; }
    .member-dashboard__identity { flex:0 0 210px; padding:18px; border:1px solid rgba(255,255,255,.17); border-radius:16px; background:rgba(255,255,255,.09); backdrop-filter:blur(8px); }
    .member-dashboard__identity span { display:block; color:rgba(255,255,255,.65); font-size:9px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .member-dashboard__identity strong { display:block; margin-top:7px; color:#fff; font-size:16px; overflow-wrap:anywhere; }
    .member-dashboard__stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; margin:18px 0 22px; }
    .member-stat { display:flex; align-items:center; gap:15px; min-height:93px; padding:17px 19px; border:1px solid #e6ebf1; border-radius:16px; background:#fff; box-shadow:0 7px 22px rgba(22,43,72,.045); }
    .member-stat__icon { display:grid; place-items:center; flex:0 0 42px; width:42px; height:42px; border-radius:13px; color:#20558a; background:#edf5fb; font-size:16px; }
    .member-stat:nth-child(2) .member-stat__icon { color:#14775d; background:#e9f6f0; }
    .member-stat:nth-child(3) .member-stat__icon { color:#98711c; background:#fcf4df; }
    .member-stat span { display:block; color:#718096; font-size:10px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
    .member-stat strong { display:block; margin-top:4px; color:#162d4e; font-size:25px; line-height:1.1; font-weight:750; }
    .member-dashboard__grid { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(290px,.65fr); gap:18px; align-items:start; }
    .member-panel { overflow:hidden; border:1px solid #e5eaf0; border-radius:17px; background:#fff; box-shadow:0 8px 26px rgba(22,43,72,.045); }
    .member-panel__head { display:flex; align-items:center; justify-content:space-between; gap:15px; padding:19px 21px; border-bottom:1px solid #edf1f5; }
    .member-panel__head h2 { margin:0 0 4px; color:#172c4c; font-family:Georgia,serif; font-size:20px; font-weight:600; }
    .member-panel__head p { margin:0; color:#8490a1; font-size:11px; }
    .member-panel__head a { color:#1d6294; font-size:11px; font-weight:800; white-space:nowrap; }
    .member-registration { display:grid; grid-template-columns:minmax(0,1fr) auto; align-items:center; gap:16px; padding:17px 21px; border-bottom:1px solid #edf1f5; transition:background .15s; }
    .member-registration:hover { color:inherit; background:#f8fbfd; }
    .member-registration:last-child { border-bottom:0; }
    .member-registration h3 { margin:0 0 5px; color:#1c2d46; font-size:13px; font-weight:750; }
    .member-registration p { margin:0; color:#7d899a; font-size:10px; line-height:1.6; }
    .member-registration__status { align-self:center; padding:6px 10px; border-radius:999px; color:#8a5718; background:#fff4dd; font-size:9px; font-weight:850; text-transform:uppercase; white-space:nowrap; }
    .member-registration__status.is-complete { color:#147052; background:#e7f6ee; }
    .member-dashboard__actions { display:grid; gap:9px; padding:14px; }
    .member-action { display:grid; grid-template-columns:39px minmax(0,1fr) auto; align-items:center; gap:11px; padding:12px; border:1px solid transparent; border-radius:12px; color:#24314a; background:#f7f9fb; transition:border .15s,background .15s,transform .15s; }
    .member-action:hover { color:#153f78; border-color:#dce8f2; background:#f1f7fb; transform:translateX(2px); }
    .member-action i { display:grid; place-items:center; width:39px; height:39px; border-radius:11px; color:#215784; background:#e6f0f7; font-size:15px; }
    .member-action strong { display:block; color:#1b2e49; font-size:12px; }
    .member-action small { display:block; margin-top:3px; color:#7d899a; font-size:9px; line-height:1.4; }
    .member-action b { color:#ae852a; font-size:15px; }
    .member-dashboard__empty { padding:34px 22px; color:#7a879b; text-align:center; font-size:12px; }
    .member-dashboard__empty i { display:block; margin-bottom:10px; color:#a9bacb; font-size:25px; }
    @media(max-width:850px){ .member-dashboard__grid{grid-template-columns:1fr}.member-dashboard__hero{align-items:flex-start;flex-direction:column}.member-dashboard__identity{flex-basis:auto;width:min(100%,280px)} }
    @media(max-width:520px){ .member-dashboard__hero{padding:24px 21px;border-radius:17px}.member-dashboard__stats{grid-template-columns:1fr;gap:9px;margin:13px 0 17px}.member-stat{min-height:75px;padding:14px 16px}.member-registration{grid-template-columns:1fr;gap:9px}.member-registration__status{justify-self:start}.member-panel__head{padding:17px}.member-dashboard__actions{padding:11px} }
</style>

<div class="content-body member-dashboard">
    @include('includes.alerts')
    <section class="member-dashboard__hero">
        <div class="member-dashboard__intro"><div class="member-dashboard__eyebrow">Your GSF member portal</div><h1>Welcome, {{ $user->name }}.</h1><p>Everything you need for your GSF membership, in one place. Update your details or pick up where you left off with your conferences.</p><div class="member-dashboard__hero-actions"><a class="member-dashboard__button member-dashboard__button--primary" href="{{ route('conferencemanagement.index') }}"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> My conferences</a><a class="member-dashboard__button" href="{{ route('member.profile') }}"><i class="fa fa-user-o" aria-hidden="true"></i> Update profile</a></div></div>
        <div class="member-dashboard__identity"><span>Member ID</span><strong>{{ $user->family_id ?: 'Pending assignment' }}</strong></div>
    </section>

    <section class="member-dashboard__stats">
        <article class="member-stat"><span class="member-stat__icon"><i class="fa fa-calendar" aria-hidden="true"></i></span><div><span>Conferences</span><strong>{{ $conferenceCount }}</strong></div></article>
        <article class="member-stat"><span class="member-stat__icon"><i class="fa fa-check-circle-o" aria-hidden="true"></i></span><div><span>Completed registrations</span><strong>{{ $completedRegistrations }}</strong></div></article>
        <article class="member-stat"><span class="member-stat__icon"><i class="fa fa-id-card-o" aria-hidden="true"></i></span><div><span>Profile completion</span><strong>{{ $profileCompletion }}%</strong></div></article>
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
                <div class="member-dashboard__empty"><i class="fa fa-calendar-o" aria-hidden="true"></i>You have no conference registrations yet.</div>
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
