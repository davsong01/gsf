@extends('frontend.site.layout')

@section('title', "GSF Conferences — GOFAMINT Students' Fellowship")
@section('description', 'Explore GSF conference editions, see the stories and gatherings that shaped our community, and find registration details for the next event.')
@section('mainClass', 'public-page conferences-page')
@section('activeSection', 'conferences')

@section('content')
<section class="conferences-intro">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container conferences-intro__inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>GSF Conferences</span></nav>
        <div class="eyebrow eyebrow--light"><span></span> Gather · Grow · Go</div>
        <h1>We gather to grow.<br><em>We leave ready to go.</em></h1>
        <p>Discover the conferences that bring GSF students and alumni together for worship, the Word, friendship and a renewed sense of purpose.</p>
        @if($activeConference)<a class="button button--gold" href="{{ route('conferences.show', $activeConference->slug) }}">Explore the next gathering <span aria-hidden="true">→</span></a>@endif
    </div>
</section>

@if($activeConference)
    @php
        $activeImage = $activeConference->banner ?: $activeConference->conference_logo ?: 'main/images/events/img-lg1.jpg';
        $activeImageUrl = filter_var($activeImage, FILTER_VALIDATE_URL) ? $activeImage : asset($activeImage);
    @endphp
    <section class="conference-featured">
        <div class="site-container">
            <article class="conference-featured__card">
                <div class="conference-featured__image"><img src="{{ $activeImageUrl }}" alt="{{ $activeConference->conference_theme }}" loading="eager"><span>Registration open</span></div>
                <div class="conference-featured__content">
                    <div class="eyebrow"><span></span> The next GSF gathering</div>
                    <h2>{{ $activeConference->conference_theme }}</h2>
                    @if($activeConference->start_date)<p class="conference-date">{{ formatDates($activeConference->start_date, $activeConference->end_date) }}</p>@endif
                    <div class="conference-overview-copy">{!! $activeConference->conference_overview !!}</div>
                    <div class="conference-featured__stats">@if($activeConference->registrations_count > 0)<strong>{{ number_format($activeConference->registrations_count) }}<small> registrations</small></strong>@endif<a href="{{ route('conferences.show', $activeConference->slug) }}">Conference details <span aria-hidden="true">→</span></a></div>
                </div>
            </article>
        </div>
    </section>
@endif

<section class="conference-archive">
    <div class="site-container">
        <div class="conference-archive__heading">
            <div><div class="eyebrow"><span></span> The GSF story, together</div><h2>Conference archive</h2><p>Look back at the moments, themes and communities from previous editions.</p></div>
            <span class="conference-archive__count">{{ number_format($editions->total() + ($activeConference ? 1 : 0)) }} editions</span>
        </div>
        @if($editions->count())
            <div class="conference-grid">
                @foreach($editions as $edition)
                    @php
                        $conferenceImage = $edition->banner ?: $edition->conference_logo ?: 'main/images/events/img-lg2.jpg';
                        $conferenceImageUrl = filter_var($conferenceImage, FILTER_VALIDATE_URL) ? $conferenceImage : asset($conferenceImage);
                        $isEditionUpcoming = $edition->start_date && \Carbon\Carbon::parse($edition->start_date)->isFuture();
                    @endphp
                    <article class="conference-card">
                        <a class="conference-card__image" href="{{ route('conferences.show', $edition->slug) }}">
                            <img src="{{ $conferenceImageUrl }}" alt="{{ $edition->conference_theme }}" loading="lazy">
                            <span class="conference-card__badge">{{ $isEditionUpcoming ? 'Coming soon' : 'Past edition' }}</span>
                            <b aria-hidden="true">↗</b>
                        </a>
                        <div class="conference-card__body">
                            <small>{{ $edition->start_date ? \Carbon\Carbon::parse($edition->start_date)->format('Y') : 'GSF Conference' }}</small>
                            <h3><a href="{{ route('conferences.show', $edition->slug) }}">{{ $edition->conference_theme }}</a></h3>
                            @if($edition->start_date)<p>{{ formatDates($edition->start_date, $edition->end_date) }}</p>@endif
                            <div class="conference-card__footer">@if($edition->registrations_count > 0)<span><strong>{{ number_format($edition->registrations_count) }}</strong> registered</span>@endif<a href="{{ route('conferences.show', $edition->slug) }}">Explore <span aria-hidden="true">→</span></a></div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="directory-pagination">{{ $editions->links() }}</div>
        @else
            <div class="empty-state"><strong>More conference stories are on the way.</strong><p>Past GSF conference editions will be collected here.</p></div>
        @endif
    </div>
</section>
@endsection
