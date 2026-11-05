@extends('frontend.site.layout')

@section('title', $chapter->name." — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page campus-minisite')
@section('activeSection', 'directory')

@section('content')
<section class="campus-hero" style="--campus-image: url('{{ asset(campusImagePath($chapter)) }}')">
    <div class="campus-hero__shade"></div>
    <div class="site-container campus-hero__content">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route('directory.index') }}">Directory</a><span>·</span><a href="{{ route('people.campuses') }}">Campuses</a></nav>
        <div class="eyebrow eyebrow--light"><span></span> GSF Campus Fellowship</div>
        <h1>{{ $chapter->name }}</h1>
        <p>{{ $chapter->field?->name ?: 'GOFAMINT Students’ Fellowship' }} <span>·</span> {{ $chapter->zone?->name ?: 'Nigeria' }}</p>
        <div class="campus-hero__actions">
            <a class="button button--gold" href="#campus-about">Discover this campus <span aria-hidden="true">↓</span></a>
            <a class="button button--ghost" href="#campus-contact">Get in touch</a>
        </div>
    </div>
</section>

<section class="campus-minisite-stats">
    <div class="site-container campus-minisite-stats__grid">
        <div><strong>{{ number_format($chapter->members_count) }}</strong><span>Current members</span></div>
        <div><strong>{{ number_format($chapter->alumni_count) }}</strong><span>Alumni in the directory</span></div>
        <div><strong>{{ $chapter->field?->name ?: 'GSF' }}</strong><span>Ministry field</span></div>
        <div><strong>{{ $chapter->zone?->name ?: 'Nigeria' }}</strong><span>Zone</span></div>
    </div>
</section>

<section class="campus-minisite-content" id="campus-about">
    <div class="site-container campus-minisite-layout">
        <div class="campus-minisite-main">
            <section class="campus-about-block">
                <div class="eyebrow"><span></span> About the Fellowship</div>
                <h2>A community growing in faith, friendship and service.</h2>
                @if(filled($chapter->about))
                    <div class="campus-about-block__text">{!! $chapter->about !!}</div>
                @else
                    <p class="campus-about-block__text">The {{ $chapter->name }} campus fellowship is part of the GOFAMINT Students’ Fellowship network, helping students grow in Christ, build meaningful relationships and serve their campus community.</p>
                @endif
                <div class="campus-location-strip">
                    <span aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s7-4.35 7-11A7 7 0 1 0 5 10c0 6.65 7 11 7 11Z"/><circle cx="12" cy="10" r="2.4"/></svg></span>
                    <div><small>Campus location</small><strong>{{ $chapter->address ?: $chapter->name }}</strong></div>
                </div>
            </section>

            <section class="campus-people-section">
                <div class="campus-section-heading"><div><div class="eyebrow"><span></span> The Community</div><h2>Current members</h2></div><span>{{ number_format($chapter->members_count) }} listed</span></div>
                @if($members->isNotEmpty())
                    <div class="campus-people-list">
                        @foreach($members as $member)
                            <article><span class="campus-people-list__avatar">{{ avatarInitials($member->name) }}</span><div><strong>{{ $member->name }}</strong><small>GSF Member</small></div>@if(filled($member->slug))<a href="{{ route('user.single', $member->slug) }}" aria-label="View {{ $member->name }}">↗</a>@endif</article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state empty-state--compact"><strong>Member profiles are being updated.</strong><p>Contact the fellowship to learn more about the campus community.</p></div>
                @endif
            </section>

            <section class="campus-people-section">
                <div class="campus-section-heading"><div><div class="eyebrow"><span></span> Connected for Life</div><h2>Our alumni</h2></div><span>{{ number_format($chapter->alumni_count) }} listed</span></div>
                @if($alumni->isNotEmpty())
                    <div class="campus-people-list">
                        @foreach($alumni as $graduate)
                            <article><span class="campus-people-list__avatar campus-people-list__avatar--gold">{{ avatarInitials($graduate->name) }}</span><div><strong>{{ $graduate->name }}</strong><small>GSF Alumni</small></div>@if(filled($graduate->slug))<a href="{{ route('user.single', $graduate->slug) }}" aria-label="View {{ $graduate->name }}">↗</a>@endif</article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state empty-state--compact"><strong>Alumni profiles will appear here.</strong><p>GSF connections continue well beyond graduation.</p></div>
                @endif
            </section>

            <section class="campus-events-section">
                <div class="campus-section-heading"><div><div class="eyebrow"><span></span> Gather With Us</div><h2>Upcoming events</h2></div></div>
                @forelse($events as $event)
                    <article class="campus-event-row"><div class="campus-event-date"><strong>{{ \Carbon\Carbon::parse($event->date)->format('d') }}</strong><span>{{ \Carbon\Carbon::parse($event->date)->format('M') }}</span></div><div><small>{{ $event->venue ?: 'Campus Fellowship' }}</small><h3>{{ $event->title }}</h3><p>{{ \Carbon\Carbon::parse($event->date)->format('l, j F Y') }}@if(filled($event->time)) · {{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}@endif</p></div></article>
                @empty
                    <div class="empty-state empty-state--compact"><strong>No upcoming events are listed.</strong><p>Reach out to the campus fellowship for current meeting details.</p></div>
                @endforelse
            </section>
        </div>

        <aside class="campus-minisite-aside" id="campus-contact">
            <div class="campus-contact-card">
                <div class="eyebrow eyebrow--light"><span></span> Connect Locally</div>
                <h2>Say hello to this fellowship.</h2>
                <p>Ask a question or request more information about the campus community.</p>
                @if(session('message'))<div class="campus-contact-feedback" role="status">{{ session('message') }}</div>@endif
                @if($errors->any())<div class="campus-contact-feedback campus-contact-feedback--error" role="alert">{{ $errors->first() }}</div>@endif
                <form action="{{ route('campus.contact') }}" method="POST">
                    @csrf
                    <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                    <label for="campus-contact-name">Your name</label><input id="campus-contact-name" name="name" type="text" value="{{ old('name') }}" required>
                    <label for="campus-contact-email">Email address</label><input id="campus-contact-email" name="email" type="email" value="{{ old('email') }}" required>
                    <label for="campus-contact-phone">Phone number</label><input id="campus-contact-phone" name="phone" type="tel" value="{{ old('phone') }}" required>
                    <label for="campus-contact-message">Message</label><textarea id="campus-contact-message" name="message" rows="4" required>{{ old('message') }}</textarea>
                    <button type="submit">Send message <span aria-hidden="true">→</span></button>
                </form>
                @if(filled($chapter->email))<a class="campus-contact-card__direct" href="mailto:{{ $chapter->email }}">{{ $chapter->email }}</a>@endif
                @if(filled($chapter->phone))<a class="campus-contact-card__direct" href="tel:{{ $chapter->phone }}">{{ $chapter->phone }}</a>@endif
            </div>
        </aside>
    </div>
</section>

@if($related->isNotEmpty())
<section class="campus-related-section">
    <div class="site-container">
        <div class="directory-entry-heading"><div><div class="eyebrow"><span></span> Nearby in the Network</div><h2>Related campus fellowships.</h2></div><a class="text-link" href="{{ route('people.campuses') }}">Explore all campuses <span aria-hidden="true">→</span></a></div>
        <div class="campus-related-grid">
            @foreach($related as $relatedCampus)
                <a href="{{ route('campus.single', $relatedCampus->slug) }}"><span>{{ $relatedCampus->field?->name ?: 'GSF Campus' }}</span><strong>{{ $relatedCampus->name }}</strong><small>{{ $relatedCampus->zone?->name ?: 'Nigeria' }} · {{ number_format($relatedCampus->members_count) }} members</small><b aria-hidden="true">↗</b></a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
