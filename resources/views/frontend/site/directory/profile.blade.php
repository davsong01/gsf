@extends('frontend.site.layout')

@php
    $campus = $alumni->campus && $alumni->campus->id != 86 ? $alumni->campus : null;
    $designation = $alumni->designation?->name ?: $alumni->stakeholder?->designation?->name;
    $position = $designation ?: ($alumni->stakeholder?->role?->name ?: $alumni->rolename);
    $memberStatus = $alumni->is_graduated ? 'GSF Alumni' : 'GSF Member';
    $studyPeriod = $alumni->matric_year && $alumni->is_graduated && $alumni->graduation_year
        ? $alumni->matric_year.'–'.$alumni->graduation_year
        : null;
    $shareDescription = collect([
        $memberStatus.'.',
        $position && $position !== 'Unknown Role' ? $position.'.' : null,
        $campus ? 'Connected with '.$campus->name.'.' : null,
        $alumni->course ? 'Studied '.$alumni->course.'.' : null,
        $alumni->motto ? 'Motto: “'.$alumni->motto.'”' : null,
    ])->filter()->implode(' ');
    $shareImage = $alumni->passport ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg');
@endphp

@section('title', $alumni->name." — GOFAMINT Students' Fellowship Directory")
@section('description', $shareDescription.' View '.$alumni->name.'’s GSF directory profile.')
@section('mainClass', 'public-page directory-profile-page')
@section('activeSection', 'directory')

@push('head')
    <link rel="canonical" href="{{ route('user.single', $alumni->slug) }}">
    <meta property="og:type" content="profile">
    <meta property="og:site_name" content="GOFAMINT Students’ Fellowship">
    <meta property="og:title" content="{{ $alumni->name }} | GSF Directory">
    <meta property="og:description" content="{{ $shareDescription }}">
    <meta property="og:url" content="{{ route('user.single', $alumni->slug) }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:image:alt" content="{{ $alumni->name }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $alumni->name }} | GSF Directory">
    <meta name="twitter:description" content="{{ $shareDescription }}">
    <meta name="twitter:image" content="{{ $shareImage }}">
@endpush

@section('content')
<section class="profile-hero">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container profile-hero__inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home.index') }}">Home</a><span>·</span>
            <a href="{{ route('directory.index') }}">Directory</a><span>·</span>
            @if($campus)<a href="{{ route('campus.single', $campus->id) }}">{{ $campus->name }}</a><span>·</span>@endif
            <span>{{ $memberStatus }}</span>
        </nav>
        <div class="profile-hero__card">
            <div class="profile-hero__portrait">{!! renderAvatar($alumni, 156, 'profile-hero__avatar') !!}</div>
            <div class="profile-hero__identity">
                <span class="profile-status">{{ $memberStatus }}</span>
                <h1>{{ $alumni->name }}</h1>
                @if($position && $position !== 'Unknown Role')<p class="profile-hero__designation">{{ $position }}</p>@endif
                @if($campus)
                    <a class="profile-hero__campus" href="{{ route('campus.single', $campus->id) }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6"/></svg>
                        {{ $campus->name }}
                    </a>
                @endif
                @if($alumni->open_to_work)<span class="profile-open-work">Open to work</span>@endif
            </div>
            <div class="profile-hero__actions">
                @if($alumni->email)<a class="button button--gold" href="#profile-contact">Contact {{ explode(' ', trim($alumni->name))[0] }} <span aria-hidden="true">→</span></a>@endif
                <a class="profile-directory-back" href="{{ $alumni->is_graduated ? route('people.alumni') : route('people.students') }}">Back to {{ $alumni->is_graduated ? 'alumni' : 'members' }}</a>
            </div>
        </div>
    </div>
</section>

<section class="profile-content">
    <div class="site-container profile-content__layout">
        <div class="profile-content__main">
            <section class="profile-panel profile-about">
                <div class="eyebrow"><span></span> Member profile</div>
                <h2>A little about {{ explode(' ', trim($alumni->name))[0] }}.</h2>
                @if($alumni->motto)<blockquote class="profile-motto">“{{ $alumni->motto }}”</blockquote>@endif
                <p class="profile-about__intro">{{ $alumni->skills ? $alumni->skills : 'Part of the GOFAMINT Students’ Fellowship community, growing in faith, leadership and service.' }}</p>
                <div class="profile-facts">
                    @if($campus)
                        <div><span>Campus fellowship</span><strong><a href="{{ route('campus.single', $campus->id) }}">{{ $campus->name }}</a></strong></div>
                    @endif
                    @if($alumni->course)<div><span>Course of study</span><strong>{{ $alumni->course }}</strong></div>@endif
                    @if($studyPeriod)<div><span>Study years</span><strong>{{ $studyPeriod }}</strong></div>@elseif(!$alumni->is_graduated && $alumni->matric_year)<div><span>Joined campus</span><strong>{{ $alumni->matric_year }}</strong></div>@endif
                    @if($campus?->field)<div><span>Field</span><strong>{{ $campus->field->name }}</strong></div>@endif
                    @if($campus?->zone)<div><span>Zone</span><strong>{{ $campus->zone->name }}</strong></div>@endif
                    @if($alumni->show_email && $alumni->email)<div><span>Email</span><strong><a href="mailto:{{ $alumni->email }}">{{ $alumni->email }}</a></strong></div>@endif
                    @if($alumni->show_phone && $alumni->phone)<div><span>Phone</span><strong><a href="tel:{{ $alumni->phone }}">{{ $alumni->phone }}</a></strong></div>@endif
                </div>
                @if($alumni->facebook || $alumni->twitter)
                    <div class="profile-socials">
                        <span>Connect</span>
                        @if($alumni->facebook)<a href="{{ $alumni->facebook }}" target="_blank" rel="noopener noreferrer">Facebook <span aria-hidden="true">↗</span></a>@endif
                        @if($alumni->twitter)<a href="{{ $alumni->twitter }}" target="_blank" rel="noopener noreferrer">X / Twitter <span aria-hidden="true">↗</span></a>@endif
                    </div>
                @endif
            </section>
        </div>

        <aside class="profile-sidebar" id="profile-contact">
            <div class="profile-contact-card">
                <span class="profile-contact-card__eyebrow">Stay connected</span>
                <h2>Send a message</h2>
                <p>Your message will be shared privately with {{ explode(' ', trim($alumni->name))[0] }}.</p>
                @if(session('message'))<div class="profile-form-feedback" role="status">{{ session('message') }}</div>@endif
                @if(session('error'))<div class="profile-form-feedback profile-form-feedback--error" role="alert">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="profile-form-feedback profile-form-feedback--error" role="alert">{{ $errors->first() }}</div>@endif
                @if($alumni->email)
                    <form action="{{ route('listing.contact') }}" method="POST">
                        @csrf
                        <input type="hidden" name="alumni_id" value="{{ $alumni->id }}">
                        <label for="profile-contact-name">Your name</label><input id="profile-contact-name" name="name" value="{{ old('name') }}" autocomplete="name" required>
                        <label for="profile-contact-email">Your email</label><input id="profile-contact-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                        <label for="profile-contact-phone">Phone</label><input id="profile-contact-phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" required>
                        <label for="profile-contact-message">Message</label><textarea id="profile-contact-message" name="message" rows="4" required>{{ old('message') }}</textarea>
                        <button type="submit">Send message <span aria-hidden="true">→</span></button>
                    </form>
                @else
                    <p class="profile-contact-card__private">This member has not added a contact email.</p>
                @endif
            </div>
        </aside>
    </div>
</section>

@if($related->isNotEmpty())
<section class="profile-peers">
    <div class="site-container">
        <div class="profile-peers__heading">
            <div><div class="eyebrow"><span></span> Shared campus</div><h2>Fellowship friends</h2><p>Other GSF members connected with {{ $campus?->name ?: 'this campus' }}.</p></div>
            @if($campus)<a href="{{ route('campus.single', $campus->id) }}">Explore campus <span aria-hidden="true">→</span></a>@endif
        </div>
        <div class="profile-peers__grid">
            @foreach($related as $friend)
                <a class="profile-peer-card" href="{{ route('user.single', $friend->slug) }}">
                    {!! renderAvatar($friend, 48, 'profile-peer-card__avatar') !!}
                    <span class="profile-peer-card__copy"><strong>{{ $friend->name }}</strong><small>{{ $friend->designation?->name ?: ($friend->is_graduated ? 'GSF Alumni' : 'GSF Member') }}</small></span>
                    <b aria-hidden="true">↗</b>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
