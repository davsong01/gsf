@extends('frontend.site.layout')

@php
    $isAlumni = $directoryType === 'alumni';
    $pageTitle = $isAlumni ? 'GSF Alumni' : 'GSF Members';
    $pageIntro = $isAlumni
        ? 'Reconnect with graduates and friends of the Fellowship across campuses and generations.'
        : 'Find current students and connect with the GSF fellowship in their institution.';
    $pageEyebrow = $isAlumni ? 'The Alumni Community' : 'The Campus Community';
    $campusContext = $campus ?? null;
    if ($campusContext) {
        $pageTitle = ($isAlumni ? 'Alumni · ' : 'Members · ').$campusContext->name;
        $pageIntro = ($isAlumni ? 'Reconnect with GSF graduates from ' : 'Meet current members of ').$campusContext->name.'.';
    }
@endphp

@section('title', $pageTitle." — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page directory-page')
@section('activeSection', 'directory')

@section('content')
<section class="inner-hero inner-hero--directory">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route('directory.index') }}">Directory</a><span>·</span>@if($campusContext)<a href="{{ route('campus.single', $campusContext->id) }}">{{ $campusContext->name }}</a><span>·</span>@endif<span>{{ $isAlumni ? 'Alumni' : 'Members' }}</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> {{ $pageEyebrow }}</div>
            <h1>{{ $isAlumni ? 'Stay connected to the' : 'Find your people in the' }} <em>{{ $isAlumni ? 'GSF family.' : 'GSF community.' }}</em></h1>
            <p>{{ $pageIntro }}</p>
            <div class="directory-hero__links">
                <a href="{{ route('people.students') }}" class="{{ !$isAlumni ? 'is-current' : '' }}">Members</a>
                <a href="{{ route('people.alumni') }}" class="{{ $isAlumni ? 'is-current' : '' }}">Alumni</a>
                <a href="{{ route('people.campuses') }}">Campuses</a>
            </div>
        </div>
    </div>
</section>

<section class="directory-listing">
    <div class="site-container">
        <div class="directory-listing__toolbar">
            <div>
                <div class="eyebrow"><span></span> {{ number_format($people->total()) }} {{ $isAlumni ? 'Alumni' : 'Members' }}</div>
                <h2>{{ $isAlumni ? 'Browse GSF alumni' : 'Browse current members' }}</h2>
                <p>Search by name{{ $campusContext ? '' : ' or campus' }} to find the person you’re looking for.</p>
            </div>
            <form class="directory-search directory-search--people {{ $campusContext ? 'directory-search--campus' : '' }}" method="GET" action="{{ $campusContext ? ($isAlumni ? route('alumni.campus', $campusContext->id) : route('members.campus', $campusContext->id)) : ($isAlumni ? route('people.alumni') : route('people.students')) }}">
                <label class="sr-only" for="person-search">Search by name</label>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4 4"/></svg>
                <input id="person-search" type="search" name="name" value="{{ $searchName }}" placeholder="Name">
                @unless($campusContext)
                <label class="sr-only" for="person-campus">Filter by campus</label>
                <input id="person-campus" type="search" name="school" value="{{ $searchSchool }}" placeholder="Campus">
                @endunless
                <button type="submit">Find people</button>
                @if($searchName !== '' || $searchSchool !== '')<a href="{{ $campusContext ? ($isAlumni ? route('alumni.campus', $campusContext->id) : route('members.campus', $campusContext->id)) : ($isAlumni ? route('people.alumni') : route('people.students')) }}">Clear</a>@endif
            </form>
        </div>

        @if($people->count())
            <div class="directory-people-grid">
                @foreach($people as $person)
                    @php
                        $designation = $person->stakeholder?->designation?->name;
                        $role = $person->stakeholder?->role?->name;
                        $profileHref = filled($person->slug) ? route('user.single', $person->slug) : null;
                    @endphp
                    <article class="directory-person-card">
                        <div class="directory-person-card__top">
                            {!! renderAvatar($person, 88, 'directory-person-card__avatar') !!}
                            <span class="directory-person-card__status">{{ $isAlumni ? 'Alumni' : 'Member' }}</span>
                        </div>
                        <div class="directory-person-card__body">
                            <small>{{ $designation ?: ($role ?: ($isAlumni ? 'GSF Alumni' : 'GSF Member')) }}</small>
                            <h3>@if($profileHref)<a href="{{ $profileHref }}">{{ $person->name }}</a>@else{{ $person->name }}@endif</h3>
                            @if($person->campus && $person->campus->id !== 86)
                                <p><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6"/></svg><a href="{{ route('campus.single', $person->campus->id) }}">{{ $person->campus->name }}</a></p>
                            @endif
                            @if($isAlumni && $person->matric_year && $person->graduation_year)
                                <span class="directory-person-card__years">{{ $person->matric_year }}–{{ $person->graduation_year }}</span>
                            @elseif(!$isAlumni && $person->matric_year)
                                <span class="directory-person-card__years">Class of {{ $person->matric_year }}</span>
                            @endif
                            @if($profileHref)<a class="directory-person-card__link" href="{{ $profileHref }}">View profile <span aria-hidden="true">→</span></a>@endif
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="directory-pagination">{{ $people->links() }}</div>
        @else
            <div class="empty-state"><strong>No {{ strtolower($pageTitle) }} matched your search.</strong><p>Try a different name or campus.</p></div>
        @endif
    </div>
</section>

<section class="directory-join">
    <div class="site-container directory-join__card">
        <div><span>{{ $isAlumni ? 'Keep your details current' : 'Not listed yet?' }}</span><h2>{{ $isAlumni ? 'Help fellow GSFites stay in touch with you.' : 'Add your details to the GSF directory.' }}</h2></div>
        <a class="button button--gold" href="{{ route('newalumni') }}">{{ $isAlumni ? 'Update my details' : 'Join the directory' }} <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection
