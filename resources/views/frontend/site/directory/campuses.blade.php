@extends('frontend.site.layout')

@section('title', "GSF Campuses — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page directory-page')
@section('activeSection', 'directory')

@section('content')
<section class="inner-hero inner-hero--directory">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route('directory.index') }}">Directory</a><span>·</span><span>Campuses</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> Campus Fellowships</div>
            <h1>Find your place in the <em>GSF campus network.</em></h1>
            <p>Explore campus fellowships by institution, field or zone, and open a campus page to learn more about its community.</p>
        </div>
    </div>
</section>

<section class="directory-listing">
    <div class="site-container">
        <div class="directory-listing__toolbar">
            <div><div class="eyebrow"><span></span> {{ number_format($chapters->total()) }} Campus Fellowships</div><h2>{{ $selectedState !== '' ? 'Campuses in '.$selectedState : 'Browse campuses' }}</h2><p>Search by institution, state, field or zone.</p></div>
            <form class="directory-search" method="GET" action="{{ route('people.campuses') }}">
                @if($selectedState !== '')<input type="hidden" name="state" value="{{ $selectedState }}">@endif
                <label class="sr-only" for="campus-search">Search campuses</label>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4 4"/></svg>
                <input id="campus-search" type="search" name="q" value="{{ $searchTerm }}" placeholder="Search a campus, field or zone">
                <button type="submit">Search</button>
                @if($searchTerm !== '' || $selectedState !== '')<a href="{{ route('people.campuses') }}">Clear</a>@endif
            </form>
        </div>

        @if($chapters->count())
            <div class="campus-directory-grid">
                @foreach($chapters as $campus)
                    <article class="campus-directory-card">
                        <a class="campus-directory-card__image" href="{{ route('campus.single', $campus->id) }}" aria-label="View {{ $campus->name }}">
                            <img src="{{ asset(campusImagePath($campus)) }}" alt="{{ $campus->name }} campus gate or fellowship illustration" loading="lazy">
                            @if($campus->members_count > 0)
                                <span>{{ number_format($campus->members_count) }} {{ \Illuminate\Support\Str::plural('member', $campus->members_count) }}</span>
                            @endif
                        </a>
                        <div class="campus-directory-card__body">
                            <small>{{ $campus->field?->name ?: 'GSF Fellowship' }}</small>
                            <h2><a href="{{ route('campus.single', $campus->id) }}">{{ $campus->name }}</a></h2>
                            <p><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-4.35 7-11A7 7 0 1 0 5 10c0 6.65 7 11 7 11Z"/><circle cx="12" cy="10" r="2.4"/></svg>{{ $campus->state ?: 'Nigeria' }} · {{ $campus->zone?->name ?: 'GSF' }}</p>
                            @if(filled($campus->address))<span class="campus-directory-card__address">{{ $campus->address }}</span>@endif
                            <a class="text-link" href="{{ route('campus.single', $campus->id) }}">Explore campus <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="directory-pagination">{{ $chapters->links() }}</div>
        @else
            <div class="empty-state"><strong>No campuses found.</strong><p>Try another campus name, field or zone.</p></div>
        @endif
    </div>
</section>
@endsection
