@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('active', ' Dashboard')

@section('content')
@php
    $isAdminDashboard = auth()->user()->isAdmin();
@endphp
<style>
    .gsf-admin-dashboard { max-width:1440px; margin:0 auto; color:#1b2940; }
    .gsf-admin-dashboard__hero { position:relative; display:flex; align-items:center; justify-content:space-between; gap:24px; overflow:hidden; padding:clamp(26px,4vw,42px); border-radius:23px; color:#fff; background:linear-gradient(116deg,#10264a 0%,#173f70 58%,#28748a 100%); box-shadow:0 20px 46px rgba(19,48,83,.15); }
    .gsf-admin-dashboard__hero::after { content:''; position:absolute; right:8%; top:-170px; width:370px; height:370px; border:1px solid rgba(255,255,255,.13); border-radius:50%; box-shadow:0 0 0 55px rgba(255,255,255,.035),0 0 0 110px rgba(255,255,255,.025); }
    .gsf-admin-dashboard__hero > * { position:relative; z-index:1; }
    .gsf-admin-dashboard__eyebrow { display:flex; align-items:center; gap:9px; margin-bottom:12px; color:#f1cf79; font-size:10px; font-weight:850; letter-spacing:.15em; text-transform:uppercase; }
    .gsf-admin-dashboard__eyebrow::before { content:''; width:22px; height:2px; background:#f1cf79; }
    .gsf-admin-dashboard__hero h1 { margin:0 0 9px; color:#fff; font-family:Georgia,serif; font-size:clamp(30px,4vw,43px); font-weight:500; letter-spacing:-.04em; }
    .gsf-admin-dashboard__hero p { max-width:660px; margin:0; color:rgba(255,255,255,.76); font-size:13px; line-height:1.75; }
    .gsf-admin-dashboard__hero-note { flex:0 0 auto; display:flex; align-items:center; gap:11px; padding:13px 16px; border:1px solid rgba(255,255,255,.18); border-radius:13px; background:rgba(255,255,255,.09); color:#fff; font-size:11px; font-weight:750; }
    .gsf-admin-dashboard__hero-note i { color:#f1cf79; font-size:17px; }
    .gsf-admin-dashboard__section-heading { display:flex; align-items:end; justify-content:space-between; gap:15px; margin:27px 2px 13px; }
    .gsf-admin-dashboard__section-heading h2 { margin:0 0 4px; color:#172c4c; font-family:Georgia,serif; font-size:20px; font-weight:600; }
    .gsf-admin-dashboard__section-heading p { margin:0; color:#8490a1; font-size:11px; }
    .gsf-admin-dashboard__grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; }
    .gsf-admin-dashboard__card { display:flex; align-items:center; gap:15px; min-height:112px; padding:19px; border:1px solid #e5eaf0; border-radius:16px; color:#1b2940; background:#fff; box-shadow:0 8px 24px rgba(22,43,72,.045); transition:transform .18s,border-color .18s,box-shadow .18s; }
    .gsf-admin-dashboard__card:hover { color:#1b2940; border-color:#cbdce9; box-shadow:0 12px 28px rgba(22,43,72,.08); transform:translateY(-2px); }
    .gsf-admin-dashboard__icon { display:grid; place-items:center; flex:0 0 46px; width:46px; height:46px; border-radius:14px; color:#20558a; background:#edf5fb; font-size:17px; }
    .gsf-admin-dashboard__card--members .gsf-admin-dashboard__icon { color:#14775d; background:#e9f6f0; }
    .gsf-admin-dashboard__card--alumni .gsf-admin-dashboard__icon { color:#98711c; background:#fcf4df; }
    .gsf-admin-dashboard__card--chapters .gsf-admin-dashboard__icon { color:#6753a2; background:#f0edfa; }
    .gsf-admin-dashboard__card-copy { min-width:0; flex:1; }
    .gsf-admin-dashboard__card-label { display:block; color:#728096; font-size:10px; font-weight:800; letter-spacing:.055em; text-transform:uppercase; }
    .gsf-admin-dashboard__card-count { display:block; margin-top:5px; color:#172c4c; font-size:26px; font-weight:750; line-height:1; }
    .gsf-admin-dashboard__arrow { color:#b18a35; font-size:16px; }
    .gsf-admin-dashboard__footer { display:flex; align-items:center; gap:9px; margin-top:17px; padding:13px 15px; border:1px solid #e7edf2; border-radius:12px; color:#68788d; background:#f8fafc; font-size:11px; }
    .gsf-admin-dashboard__footer i { color:#56806e; }
    @media(max-width:900px){ .gsf-admin-dashboard__grid{grid-template-columns:repeat(2,minmax(0,1fr))} }
    @media(max-width:600px){ .gsf-admin-dashboard__hero{align-items:flex-start;flex-direction:column;padding:25px 21px;border-radius:17px}.gsf-admin-dashboard__grid{grid-template-columns:1fr;gap:9px}.gsf-admin-dashboard__card{min-height:86px;padding:15px}.gsf-admin-dashboard__section-heading{margin-top:22px} }
</style>

<div class="content-body gsf-admin-dashboard">
    @include('includes.alerts')
    <section class="gsf-admin-dashboard__hero" aria-labelledby="admin-dashboard-title">
        <div>
            <div class="gsf-admin-dashboard__eyebrow">GSF administration</div>
            <h1 id="admin-dashboard-title">Welcome, {{ auth()->user()->name }}.</h1>
            <p>{{ $isAdminDashboard ? 'A clear overview of the GSF community. Open a directory to review records and manage the network.' : 'A quick overview of your campus community. Open a directory to manage members and alumni.' }}</p>
        </div>
        <div class="gsf-admin-dashboard__hero-note"><i class="fa fa-shield" aria-hidden="true"></i><span>{{ $isAdminDashboard ? 'National administration' : 'Campus administration' }}</span></div>
    </section>

    <div class="gsf-admin-dashboard__section-heading">
        <div><h2>Community overview</h2><p>Select a count to open its directory.</p></div>
    </div>

    <section class="gsf-admin-dashboard__grid" aria-label="Community directories">
        @if($isAdminDashboard)
            <a class="gsf-admin-dashboard__card" href="{{ route('fields.index') }}">
                <span class="gsf-admin-dashboard__icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span><span class="gsf-admin-dashboard__card-copy"><span class="gsf-admin-dashboard__card-label">Fields</span><strong class="gsf-admin-dashboard__card-count">{{ $fields->count() }}</strong></span><i class="fa fa-arrow-right gsf-admin-dashboard__arrow" aria-hidden="true"></i>
            </a>
            <a class="gsf-admin-dashboard__card" href="{{ route('zones.index') }}">
                <span class="gsf-admin-dashboard__icon"><i class="fa fa-map-o" aria-hidden="true"></i></span><span class="gsf-admin-dashboard__card-copy"><span class="gsf-admin-dashboard__card-label">Zones</span><strong class="gsf-admin-dashboard__card-count">{{ $zones->count() }}</strong></span><i class="fa fa-arrow-right gsf-admin-dashboard__arrow" aria-hidden="true"></i>
            </a>
        @endif
        <a class="gsf-admin-dashboard__card gsf-admin-dashboard__card--members" href="{{ route('users.index') }}">
            <span class="gsf-admin-dashboard__icon"><i class="fa fa-users" aria-hidden="true"></i></span><span class="gsf-admin-dashboard__card-copy"><span class="gsf-admin-dashboard__card-label">Active members</span><strong class="gsf-admin-dashboard__card-count">{{ $users->count() }}</strong></span><i class="fa fa-arrow-right gsf-admin-dashboard__arrow" aria-hidden="true"></i>
        </a>
        <a class="gsf-admin-dashboard__card gsf-admin-dashboard__card--alumni" href="{{ route('users.index') }}">
            <span class="gsf-admin-dashboard__icon"><i class="fa fa-graduation-cap" aria-hidden="true"></i></span><span class="gsf-admin-dashboard__card-copy"><span class="gsf-admin-dashboard__card-label">Alumni</span><strong class="gsf-admin-dashboard__card-count">{{ $alumnis->count() }}</strong></span><i class="fa fa-arrow-right gsf-admin-dashboard__arrow" aria-hidden="true"></i>
        </a>
        @if($isAdminDashboard)
            <a class="gsf-admin-dashboard__card gsf-admin-dashboard__card--chapters" href="{{ route('chapters.index') }}">
                <span class="gsf-admin-dashboard__icon"><i class="fa fa-university" aria-hidden="true"></i></span><span class="gsf-admin-dashboard__card-copy"><span class="gsf-admin-dashboard__card-label">GSF chapters</span><strong class="gsf-admin-dashboard__card-count">{{ $chapters->count() }}</strong></span><i class="fa fa-arrow-right gsf-admin-dashboard__arrow" aria-hidden="true"></i>
            </a>
        @endif
    </section>

    <div class="gsf-admin-dashboard__footer"><i class="fa fa-info-circle" aria-hidden="true"></i><span>Counts reflect the records currently available to your account.</span></div>
</div>
@endsection
