@extends('frontend.site.layout')

@section('title', "GSF Directory — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page directory-page')
@section('activeSection', 'directory')

@section('content')
<section class="inner-hero inner-hero--directory">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>Directory</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> Find Your GSF Community</div>
            <h1>One fellowship. <em>Connected across campuses and generations.</em></h1>
            <p>Find a campus fellowship, connect with current members, or reconnect with GSF alumni across Nigeria.</p>
            <a class="button button--gold directory-hero__action" href="{{ route('people.campuses') }}">Explore campuses <span aria-hidden="true">→</span></a>
        </div>
    </div>
</section>

<section class="directory-overview">
    <div class="site-container">
        <div class="directory-overview__stats" aria-label="Directory at a glance">
            <div><span>Campus fellowships</span><strong>{{ number_format($directoryStats['campuses']) }}</strong></div>
            <div><span>Current members</span><strong>{{ number_format($directoryStats['members']) }}</strong></div>
            <div><span>Alumni</span><strong>{{ number_format($directoryStats['alumni']) }}</strong></div>
        </div>

        <div class="directory-entry-heading">
            <div><div class="eyebrow"><span></span> Explore the Directory</div><h2>Where would you like to begin?</h2></div>
            <p>Browse the national network or search directly for people and campus fellowships.</p>
        </div>

        <div class="directory-entry-grid">
            <a class="directory-entry-card directory-entry-card--campuses" href="{{ route('people.campuses') }}">
                <span class="directory-entry-card__number">01</span><span class="directory-entry-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6M8 10h.01M16 10h.01"/></svg></span>
                <span class="directory-entry-card__copy"><small>Find a fellowship</small><strong>Campuses</strong><span>Explore GSF communities by institution, field and zone.</span></span>
                <b aria-hidden="true">↗</b>
            </a>
            <a class="directory-entry-card directory-entry-card--members" href="{{ route('people.students') }}">
                <span class="directory-entry-card__number">02</span><span class="directory-entry-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c.5-4.2 2.8-6.5 7-6.5s6.5 2.3 7 6.5M17 4.5a3.5 3.5 0 0 1 0 7M18 15c2.4.7 3.7 2.7 4 6"/></svg></span>
                <span class="directory-entry-card__copy"><small>Meet the community</small><strong>Members</strong><span>Find current students and connect with their campus fellowship.</span></span>
                <b aria-hidden="true">↗</b>
            </a>
            <a class="directory-entry-card directory-entry-card--alumni" href="{{ route('people.alumni') }}">
                <span class="directory-entry-card__number">03</span><span class="directory-entry-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6M8 10h.01M16 10h.01M4 4l2 2M20 4l-2 2"/></svg></span>
                <span class="directory-entry-card__copy"><small>Stay connected</small><strong>Alumni</strong><span>Reconnect with GSF graduates and the wider fellowship family.</span></span>
                <b aria-hidden="true">↗</b>
            </a>
            <a class="directory-entry-card directory-entry-card--tracker" href="{{ route('campus.tracker') }}">
                <span class="directory-entry-card__number">04</span><span class="directory-entry-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 21h18M5 18l5-5 3 2 6-8M16 7h3v3"/><path d="M5 4v14"/></svg></span>
                <span class="directory-entry-card__copy"><small>See the network</small><strong>Campus Tracker</strong><span>Explore campus activity and fellowship coverage.</span></span>
                <b aria-hidden="true">↗</b>
            </a>
        </div>
    </div>
</section>

<section class="directory-featured">
    <div class="site-container">
        <div class="directory-entry-heading">
            <div><div class="eyebrow eyebrow--light"><span></span> Across the Network</div><h2>Explore campus fellowships.</h2></div>
            <a class="text-link text-link--light" href="{{ route('people.campuses') }}">View all campuses <span aria-hidden="true">→</span></a>
        </div>

        <div class="directory-featured__grid">
            @foreach($featuredCampuses as $campus)
                <a class="directory-campus-mini" href="{{ route('campus.single', $campus->id) }}">
                    <span class="directory-campus-mini__image"><img src="{{ asset(campusImagePath($campus)) }}" alt="{{ $campus->name }} campus gate or fellowship illustration" loading="lazy"></span>
                    <span class="directory-campus-mini__copy"><small>{{ $campus->field?->name ?: 'GSF Campus' }}</small><strong>{{ $campus->name }}</strong><span>{{ $campus->zone?->name ?: 'Nigeria' }} · {{ number_format($campus->members_count) }} {{ \Illuminate\Support\Str::plural('member', $campus->members_count) }}</span></span>
                    <b aria-hidden="true">↗</b>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
