@extends('frontend.site.layout')

@section('title', $chapter->name." — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page campus-minisite')
@section('activeSection', 'directory')

@section('content')
<section class="campus-hero" style="--campus-image: url('{{ versionedAsset(campusImagePath($chapter)) }}')">
    <div class="campus-hero__shade"></div>
    <div class="site-container campus-hero__content">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route('directory.index') }}">Directory</a><span>·</span><a href="{{ route('people.campuses') }}">Campuses</a></nav>
        <div class="eyebrow eyebrow--light"><span></span> GSF Campus Fellowship</div>
        <h1>{{ $chapter->name }}</h1>
        <p>{{ $chapter->field?->name ?: 'GOFAMINT Students’ Fellowship' }} <span>·</span> {{ $chapter->zone?->name ?: 'Nigeria' }}</p>
        <div class="campus-hero__actions">
            <a class="button button--gold" href="#campus-about">Discover this campus <span aria-hidden="true">↓</span></a>
            <a class="button button--ghost" href="#campus-contact">Get in touch</a>
            @if(auth()->check() && auth()->user()->isAdmin())
                <a class="button button--ghost" href="{{ route('chapters.edit', $chapter->id) }}">Edit campus <span aria-hidden="true">↗</span></a>
            @endif
        </div>
    </div>
</section>

<section class="campus-minisite-stats">
    <div class="site-container campus-minisite-stats__grid">
        <div><strong>{{ number_format($chapter->members_count) }}</strong><span>Current members</span></div>
        <div><strong>{{ number_format($chapter->alumni_count) }}</strong><span>Alumni in the directory</span></div>
        <div><strong>{{ $chapter->zone?->name ?: 'Nigeria' }}</strong><span>Zone</span></div>
        <div><strong>{{ $chapter->field?->name ?: 'GSF' }}</strong><span>Ministry field</span></div>
    </div>
</section>

@php
    $zonalPastor = $chapter->zone?->zonalCord;
    $fieldPastor = $chapter->field?->fieldCord ?? $chapter->zone?->field?->fieldCord;
@endphp
@if($zonalPastor || $fieldPastor)
<section class="campus-pastoral-team" aria-label="Campus pastoral leadership">
    <div class="site-container campus-pastoral-team__layout">
        <div class="campus-pastoral-team__heading">
            <div class="eyebrow"><span></span> Pastoral care</div>
            <h2>Meet our pastors.</h2>
        </div>
        <div class="campus-pastoral-team__grid">
            @if($zonalPastor)
                <article class="campus-pastor-card campus-pastor-card--zone">
                    <div class="campus-pastor-card__portrait">{!! renderAvatar($zonalPastor, 50, 'campus-pastor-card__image') !!}</div>
                    <div class="campus-pastor-card__copy"><span>Zonal Pastor</span><h3>{{ $zonalPastor->name }}</h3></div>
                </article>
            @endif
            @if($fieldPastor)
                <article class="campus-pastor-card campus-pastor-card--field">
                    <div class="campus-pastor-card__portrait">{!! renderAvatar($fieldPastor, 50, 'campus-pastor-card__image') !!}</div>
                    <div class="campus-pastor-card__copy"><span>Field Pastor</span><h3>{{ $fieldPastor->name }}</h3></div>
                </article>
            @endif
        </div>
    </div>
</section>
@endif

@if(filled($chapter->email) || filled($chapter->phone) || filter_var($chapter->facebook, FILTER_VALIDATE_URL) || filter_var($chapter->twitter, FILTER_VALIDATE_URL))
<section class="campus-connect-section" aria-labelledby="campus-connect-heading">
    <div class="site-container campus-connect-panel">
        <div class="campus-connect-panel__heading">
            <div class="eyebrow"><span></span> Fellowship contacts</div>
            <h2 id="campus-connect-heading">Connect with us.</h2>
        </div>
        <div class="campus-connect-panel__links">
            @if(filled($chapter->email))
                <a href="mailto:{{ $chapter->email }}"><span>Email</span><strong>{{ $chapter->email }}</strong><b aria-hidden="true">↗</b></a>
            @endif
            @if(filled($chapter->phone))
                <a href="tel:{{ preg_replace('/[^+\d]/', '', $chapter->phone) }}"><span>Phone</span><strong>{{ $chapter->phone }}</strong><b aria-hidden="true">↗</b></a>
            @endif
            @if(filter_var($chapter->facebook, FILTER_VALIDATE_URL) || filter_var($chapter->twitter, FILTER_VALIDATE_URL))
                <div class="campus-connect-panel__social">
                    <span>Social media</span>
                    <div class="campus-connect-panel__social-icons">
                        @if(filter_var($chapter->facebook, FILTER_VALIDATE_URL))
                            <a class="campus-connect-panel__social-icon campus-connect-panel__social-icon--facebook" href="{{ $chapter->facebook }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $chapter->name }} on Facebook" title="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 21v-8.2h2.8l.4-3.2h-3.2v-2c0-.9.3-1.6 1.6-1.6H17V3.1c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.3H7.5v3.2h2.8V21h3.2Z"/></svg></a>
                        @endif
                        @if(filter_var($chapter->twitter, FILTER_VALIDATE_URL))
                            <a class="campus-connect-panel__social-icon campus-connect-panel__social-icon--x" href="{{ $chapter->twitter }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $chapter->name }} on X" title="X / Twitter"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.9 2H22l-6.8 7.8L23.2 22h-6.3L12 14.6 5.6 22H2.4l7.3-8.4L1.9 2h6.5l4.5 6.8L18.9 2Zm-1.1 18h1.7L7.4 3.9H5.6L17.8 20Z"/></svg></a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endif

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
                @if($chapter->meetings->isNotEmpty())
                    <div class="campus-meeting-schedule"><h3>When we meet</h3><div class="campus-meeting-schedule__list">@foreach($chapter->meetings as $meeting)<div><strong>{{ $meeting->title ?: 'Fellowship meeting' }}</strong><span>{{ $meeting->day_of_week }} · {{ \Carbon\Carbon::parse($meeting->time)->format('g:i A') }}</span><small>{{ $meeting->venue }}</small></div>@endforeach</div></div>
                @endif
            </section>

            <section class="campus-people-section">
                <div class="campus-section-heading"><div><div class="eyebrow"><span></span> The Community</div><h2>Current members</h2></div><div class="campus-section-heading__meta"><span>{{ number_format($chapter->members_count) }} listed</span><a href="{{ route('members.campus', $chapter->slug) }}">View all <span aria-hidden="true">→</span></a></div></div>
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
                <div class="campus-section-heading"><div><div class="eyebrow"><span></span> Connected for Life</div><h2>Our alumni</h2></div><div class="campus-section-heading__meta"><span>{{ number_format($chapter->alumni_count) }} listed</span><a href="{{ route('alumni.campus', $chapter->slug) }}">View all <span aria-hidden="true">→</span></a></div></div>
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

            @if($events->isNotEmpty())
                <section class="campus-events-section">
                    <div class="campus-section-heading"><div><div class="eyebrow"><span></span> Gather With Us</div><h2>Programs &amp; events</h2></div><div class="campus-section-heading__meta"><a href="{{ route('events.public.index', ['campus_id' => $chapter->id]) }}">View all <span aria-hidden="true">→</span></a></div></div>
                    @foreach($events as $event)
                        <article class="campus-event-row">
                            <div class="campus-event-date"><strong>{{ $event->event_starts_at->format('d') }}</strong><span>{{ $event->event_starts_at->format('M') }}</span></div>
                            <div><small>{{ $event->is_featured ? 'Featured · ' : '' }}{{ $event->event_starts_at->isPast() ? 'Past event' : 'Upcoming event' }} · {{ $event->event_venue ?: 'Campus Fellowship' }}</small><h3><a href="{{ route('events.public.show', $event->slug) }}">{{ $event->title }}</a></h3><p>{{ $event->event_starts_at->format('l, j F Y · g:i A') }}</p>@if(!empty($event->event_speakers))<p><strong>Host and Guest Minister:</strong> @foreach($event->event_speakers as $person){{ ($person['kind'] ?? 'speaker') === 'guest_minister' ? 'Guest Minister' : 'Host' }}: {{ $person['name'] }}@if(!$loop->last) · @endif @endforeach</p>@endif</div>
                            @if($eventImage = \App\Modules\Blog\BlogMedia::url($event->featured_image))<a class="campus-event-row__image" href="{{ route('events.public.show', $event->slug) }}" aria-label="View {{ $event->title }}"><img src="{{ $eventImage }}" alt="" loading="lazy"></a>@endif
                        </article>
                    @endforeach
                </section>
            @endif
        </div>

        <aside class="campus-minisite-aside">
            @if($campusPosts->isNotEmpty())
            <div class="campus-news-panel">
                <div class="campus-news-panel__heading">
                    <div><div class="eyebrow"><span></span> Campus news</div><h2>Latest stories.</h2></div>
                    <a href="{{ route('blog.index', ['campus_id' => $chapter->id]) }}">View all <span aria-hidden="true">→</span></a>
                </div>
                <div class="campus-news-list">
                    @foreach($campusPosts as $campusPost)
                        @php($campusPostImage = \App\Modules\Blog\BlogMedia::url($campusPost->featured_image))
                        <a class="campus-news-item" href="{{ route('blog.show', $campusPost->slug) }}">
                            @if($campusPostImage)
                                <img src="{{ $campusPostImage }}" alt="" loading="lazy">
                            @else
                                <span class="campus-news-item__icon" aria-hidden="true">G</span>
                            @endif
                            <span class="campus-news-item__copy">
                                <small>{{ $campusPost->published_at?->format('d M Y') }}</small>
                                <strong>{{ $campusPost->title }}</strong>
                                <span>{{ \Illuminate\Support\Str::limit(strip_tags($campusPost->excerpt ?: $campusPost->body), 82) }}</span>
                            </span>
                            <b aria-hidden="true">↗</b>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="campus-contact-card" id="campus-contact">
                <div class="eyebrow eyebrow--light"><span></span> Connect Locally</div>
                <h2>Say hello.</h2>
                <p>Ask a question or request more information about us.</p>
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
