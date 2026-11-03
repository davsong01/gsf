@extends('frontend.site.layout')

@php
    $conferenceImage = $edition->banner ?: $edition->conference_logo ?: 'main/images/events/img-lg1.jpg';
    $conferenceImageUrl = filter_var($conferenceImage, FILTER_VALIDATE_URL) ? $conferenceImage : asset($conferenceImage);
    $speakerMap = $speakers->keyBy('id');
    $dateLabel = $edition->start_date ? formatDates($edition->start_date, $edition->end_date) : 'A GSF gathering';
    $conferenceDescription = trim(strip_tags($edition->conference_overview ?: 'A gathering for worship, the Word, fellowship and spiritual growth with GOFAMINT Students’ Fellowship.'));
    $conferenceUrl = route('conferences.show', $edition->slug);
    $shareTitle = rawurlencode($edition->conference_theme.' — GOFAMINT Students’ Fellowship');
    $shareUrl = rawurlencode($conferenceUrl);
    $normalizeSocialUrl = function ($value, $platform) {
        $value = trim((string) $value);
        if ($value === '') return null;

        if (str_starts_with($value, '//')) {
            $value = 'https:'.$value;
        } elseif (!preg_match('/^https?:\/\//i', $value)) {
            $handle = ltrim($value, '@/');
            $value = str_contains($value, '.')
                ? 'https://'.$value
                : 'https://'.$platform.'/'.$handle;
        }

        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
        return in_array($scheme, ['http', 'https'], true) && filter_var($value, FILTER_VALIDATE_URL)
            ? $value
            : null;
    };
    $socialLinks = array_filter([
        'Facebook page' => $normalizeSocialUrl($edition->facebook_page, 'facebook.com'),
        'Facebook event' => $normalizeSocialUrl($edition->facebook_event_page, 'facebook.com'),
        'Instagram' => $normalizeSocialUrl($edition->instagram, 'instagram.com'),
        'Telegram' => $normalizeSocialUrl($edition->telegram, 't.me'),
    ]);
@endphp

@section('title', $edition->conference_theme." — GSF Conferences")
@section('description', \Illuminate\Support\Str::limit($conferenceDescription, 155))
@section('mainClass', 'public-page conference-detail-page')
@section('activeSection', 'conferences')

@push('head')
    <meta property="og:type" content="event">
    <meta property="og:site_name" content="GOFAMINT Students’ Fellowship">
    <meta property="og:title" content="{{ $edition->conference_theme }} | GSF Conferences">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($conferenceDescription, 200) }}">
    <meta property="og:url" content="{{ $conferenceUrl }}">
    <meta property="og:image" content="{{ $conferenceImageUrl }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $edition->conference_theme }} | GSF Conferences">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit($conferenceDescription, 200) }}">
    <meta name="twitter:image" content="{{ $conferenceImageUrl }}">
@endpush

@section('content')
<section class="conference-detail-hero">
    <div class="conference-detail-hero__image"><img src="{{ $conferenceImageUrl }}" alt="{{ $edition->conference_theme }}" fetchpriority="high"></div>
    <div class="conference-detail-hero__wash"></div>
    <div class="site-container conference-detail-hero__content">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route('conferences.index') }}">GSF Conferences</a><span>·</span><span>{{ $edition->conference_theme }}</span></nav>
        <div class="eyebrow eyebrow--light"><span></span> {{ $isRegistrationOpen ? 'Registration is open' : ($isUpcoming ? 'Coming up' : 'A GSF conference edition') }}</div>
        <h1>{{ $edition->conference_theme }}</h1>
        <p class="conference-detail-hero__date">{{ $dateLabel }}</p>
        <p class="conference-detail-hero__lead">{{ \Illuminate\Support\Str::limit($conferenceDescription, 230) }}</p>
        <div class="conference-detail-hero__actions">
            @if($isRegistrationOpen && $edition->conferenceplans->isNotEmpty())
                <a class="button button--gold" href="#conference-registration">Explore registration <span aria-hidden="true">↓</span></a>
            @else
                <a class="button button--gold" href="#conference-story">Explore this edition <span aria-hidden="true">↓</span></a>
            @endif
            <a class="conference-detail-hero__back" href="{{ route('conferences.index') }}">All conferences <span aria-hidden="true">↗</span></a>
        </div>
    </div>
    @if($isUpcoming)
        <div class="conference-countdown" data-conference-countdown="{{ \Carbon\Carbon::parse($edition->start_date)->toIso8601String() }}">
            <span class="conference-countdown__label">Until we gather</span>
            <div><strong data-countdown-days>—</strong><small>Days</small></div>
            <div><strong data-countdown-hours>—</strong><small>Hours</small></div>
            <div><strong data-countdown-minutes>—</strong><small>Minutes</small></div>
            <div><strong data-countdown-seconds>—</strong><small>Seconds</small></div>
        </div>
    @endif
</section>

<nav class="conference-mini-nav" aria-label="Conference sections">
    <div class="site-container">
        <a href="#conference-story">About</a>
        @if($showSpeakerSection)<a href="#conference-speakers">Speakers</a>@endif
        @if($showScheduleSection)<a href="#conference-schedule">Schedule</a>@endif
        @if($showFaqSection)<a href="#conference-faq">FAQs</a>@endif
        <a href="#conference-social">Connect</a>
        @if($isRegistrationOpen)<a class="conference-mini-nav__cta" href="#conference-registration">Register <span aria-hidden="true">→</span></a>@endif
    </div>
</nav>

<section class="conference-detail-stats">
    <div class="site-container conference-detail-stats__grid">
        @if($edition->registrations_count > 0)<div><strong>{{ number_format($edition->registrations_count) }}+</strong><span>Successful registrations</span></div>@endif
        <div><strong>{{ $edition->start_date ? \Carbon\Carbon::parse($edition->start_date)->format('Y') : 'GSF' }}</strong><span>Conference year</span></div>
        <div><strong>{{ $speakers->count() ?: 'Soon' }}</strong><span>{{ $speakers->count() === 1 ? 'Featured speaker' : 'Featured speakers' }}</span></div>
        <div><strong>{{ $dateLabel }}</strong><span>Gathering dates</span></div>
    </div>
</section>

<section class="conference-detail-content" id="conference-story">
    <div class="site-container conference-detail-main">
        <article class="conference-story-card">
            <div class="eyebrow"><span></span> The heart of this gathering</div>
            <h2>A time to encounter God, build community and grow.</h2>
            <div class="conference-story-copy">
                @if(filled($edition->conference_overview))
                    {!! $edition->conference_overview !!}
                @else
                    <p>This GSF conference brings students and alumni together for worship, biblical teaching, prayer and fellowship. It is an invitation to grow in faith, deepen lasting relationships and return to campus ready to serve.</p>
                @endif
            </div>
        </article>

        @if($showSpeakerSection)
            <section class="conference-detail-section" id="conference-speakers">
                <div class="conference-section-heading"><div><div class="eyebrow"><span></span> Voices for the journey</div><h2>Meet the speakers</h2><p>Ministers and leaders who will share in this gathering.</p></div></div>
                @if($speakers->isNotEmpty())
                    <div class="conference-speakers-grid">
                        @foreach($speakers as $speaker)
                            @php $speakerImage = $speaker->image ? (filter_var($speaker->image, FILTER_VALIDATE_URL) ? $speaker->image : asset($speaker->image)) : asset('frontend/passports/avatar.jpg'); @endphp
                            <article class="conference-speaker-card"><img src="{{ $speakerImage }}" alt="{{ $speaker->name }}" loading="lazy"><div><h3>{{ $speaker->name }}</h3>@if($speaker->title)<p>{{ $speaker->title }}</p>@endif</div></article>
                        @endforeach
                    </div>
                @else
                    <div class="conference-announcement"><span aria-hidden="true">✦</span><div><strong>Speaker announcements are coming soon.</strong><p>Check back as we share the ministers and leaders joining us.</p></div></div>
                @endif
            </section>
        @endif

        @if($showScheduleSection)
            <section class="conference-detail-section" id="conference-schedule">
                <div class="conference-section-heading"><div><div class="eyebrow"><span></span> Make room for every moment</div><h2>Conference schedule</h2><p>Plan your time with us across the gathering.</p></div></div>
                @if($schedule->isNotEmpty())
                    <div class="conference-schedule-list">
                        @foreach($schedule as $day)
                            <article class="conference-schedule-day">
                                <div class="conference-schedule-day__date"><strong>{{ $day->date ? \Carbon\Carbon::parse($day->date)->format('d') : 'GSF' }}</strong><span>{{ $day->date ? \Carbon\Carbon::parse($day->date)->format('M') : $day->day }}</span></div>
                                <div class="conference-schedule-day__content"><h3>{{ $day->day ?: ($day->date ? \Carbon\Carbon::parse($day->date)->format('l') : 'Conference programme') }}</h3>
                                    @foreach(($day->sessions ?: []) as $session)
                                        @php $sessionSpeaker = $speakerMap->get(data_get($session, 'speaker_id')); @endphp
                                        <div class="conference-session"><time>{{ data_get($session, 'time') ? \Carbon\Carbon::createFromFormat('H:i', data_get($session, 'time'))->format('g:i A') : '' }}</time><p><strong>{{ data_get($session, 'description') }}</strong>@if($sessionSpeaker)<span>{{ $sessionSpeaker->name }}</span>@endif</p></div>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="conference-announcement"><span aria-hidden="true">◷</span><div><strong>The full programme will be shared soon.</strong><p>We’re putting the conference schedule together. Check back for session times and details.</p></div></div>
                @endif
            </section>
        @endif

        @if($showFaqSection)
        <section class="conference-detail-section conference-faq-section" id="conference-faq">
            <div class="conference-faq-intro"><div class="eyebrow"><span></span> Helpful details</div><h2>Questions, answered.</h2><p>Find the key information you need before registering for {{ $edition->conference_theme }}.</p>
                @if($edition->official_email)<a href="mailto:{{ $edition->official_email }}">Still have a question? Contact us <span aria-hidden="true">→</span></a>@endif
            </div>
            <div class="conference-faq-card">
                @forelse($faqs as $faq)
                    <details><summary>{{ $faq->question }}</summary><p>{{ $faq->answer }}</p></details>
                @empty
                    <details open><summary>When is the conference?</summary><p>{{ $dateLabel }}. We look forward to welcoming students and alumni from across the GSF network.</p></details>
                    <details><summary>How do I register?</summary><p>Choose the registration option in the final section of this page, then follow the prompts on the registration page.</p></details>
                    <details><summary>Who can I contact for help?</summary><p>@if($edition->official_email)<a href="mailto:{{ $edition->official_email }}">{{ $edition->official_email }}</a>@else Contact your campus fellowship leader for assistance.@endif</p></details>
                @endforelse
            </div>
        </section>
        @endif

        <section class="conference-social-section" id="conference-social">
            <div class="conference-social-section__copy"><div class="eyebrow"><span></span> Stay connected</div><h2>Share the invitation.</h2><p>Help someone else discover this gathering. Follow event updates or share the conference with your community.</p></div>
            <div class="conference-social-section__links">
                @foreach($socialLinks as $label => $link)
                    <a href="{{ $link }}" target="_blank" rel="noopener noreferrer">{{ $label }} <span aria-hidden="true">↗</span></a>
                @endforeach
                <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer">Share on WhatsApp <span aria-hidden="true">↗</span></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer">Share on Facebook <span aria-hidden="true">↗</span></a>
                <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer">Share on Telegram <span aria-hidden="true">↗</span></a>
            </div>
        </section>
    </div>
</section>

<section class="conference-detail-endnote"><div class="site-container"><span>One fellowship. Many gatherings. A lifetime of impact.</span><a href="{{ route('conferences.index') }}">Back to GSF Conferences <span aria-hidden="true">→</span></a></div></section>

@if($isRegistrationOpen && $edition->conferenceplans->isNotEmpty())
    <section class="conference-registration-section" id="conference-registration">
        <div class="site-container">
            <div class="conference-section-heading conference-registration-section__heading">
                <div><div class="eyebrow"><span></span> Your place is waiting</div><h2>Choose your registration</h2><p>Pick the pass that fits you and complete your registration online.</p></div>
                @if($edition->close_registration)<span class="conference-registration-deadline">Registration closes {{ \Carbon\Carbon::parse($edition->close_registration)->format('M j, Y') }}</span>@endif
            </div>
            <div class="conference-registration-options">
                @foreach($edition->conferenceplans as $plan)
                    <article class="conference-pass-card">
                        <div class="conference-pass-card__top"><span>{{ $plan->level ?: 'Conference pass' }}</span><strong>{{ $plan->price > 0 ? '₦'.number_format((float) $plan->price) : 'Details on registration' }}</strong></div>
                        <h3>{{ trim($plan->title) }}</h3>
                        @if($plan->items)<ul>@foreach($plan->items as $item)<li>{{ $item }}</li>@endforeach</ul>@endif
                        <a href="{{ route('conference.registration', $plan->id) }}">Continue to registration <span aria-hidden="true">→</span></a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection

@if($isUpcoming)
    @push('scripts')
        <script>
            (() => {
                const timer = document.querySelector('[data-conference-countdown]');
                if (!timer) return;
                const target = new Date(timer.dataset.conferenceCountdown).getTime();
                const update = () => {
                    const remaining = Math.max(0, target - Date.now());
                    const values = {
                        days: Math.floor(remaining / 86400000),
                        hours: Math.floor((remaining % 86400000) / 3600000),
                        minutes: Math.floor((remaining % 3600000) / 60000),
                        seconds: Math.floor((remaining % 60000) / 1000)
                    };
                    Object.entries(values).forEach(([unit, value]) => {
                        const output = timer.querySelector(`[data-countdown-${unit}]`);
                        if (output) output.textContent = String(value).padStart(2, '0');
                    });
                };
                update();
                window.setInterval(update, 1000);
            })();
        </script>
    @endpush
@endif
