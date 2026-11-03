@extends('frontend.site.layout')

@section('title', "GOFAMINT Students' Fellowship")
@section('mainClass', 'site-home')
@section('activeSection', 'home')

@section('content')
<section class="hero" aria-roledescription="carousel" aria-label="GSF introduction" data-hero-slider>
    <div class="hero__glow hero__glow--one"></div>
    <div class="hero__glow hero__glow--two"></div>
    <div class="hero-slider__track" aria-live="off">
        <article class="hero-slide is-active" data-hero-slide aria-hidden="false">
            <div class="site-container hero__grid">
                <div class="hero__content">
                    <div class="eyebrow eyebrow--light"><span></span> Faith · Leadership · Service</div>
                    <h1>Forming students for <em>Christ-like influence.</em></h1>
                    <p class="hero__lead">A national fellowship community where students grow in faith, discover purpose, build enduring relationships and become equipped to serve their world.</p>
                    <div class="hero__actions">
                        <a class="button button--gold" href="{{ route('about.foundational-concept') }}">Discover GSF <span aria-hidden="true">→</span></a>
                        <a class="button button--ghost" href="{{ route('people.campuses') }}">Find a campus</a>
                    </div>
                    <div class="hero__proof">
                        <div class="hero__proof-mark" aria-hidden="true">GSF</div>
                        <p><strong>One fellowship, many campuses.</strong><br>Connected nationally. Rooted locally.</p>
                    </div>
                </div>

                <div class="hero__visual" aria-label="GSF fellowship community">
                    <div class="hero__image-wrap">
                        <img src="{{ asset('frontend/img/sliders/slider1.png') }}" alt="Students worshipping together at a GSF gathering">
                        <div class="hero__image-shade"></div>
                        <div class="hero__image-caption"><span>Our community</span><strong>Faith comes alive in fellowship.</strong></div>
                    </div>
                    <a class="hero__directory-card" href="{{ route('directory.index') }}">
                        <span class="hero__directory-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s7-4.35 7-11A7 7 0 1 0 5 10c0 6.65 7 11 7 11Z"/><circle cx="12" cy="10" r="2.4"/></svg></span>
                        <span><small>Explore the network</small><strong>Open GSF Directory</strong></span><b aria-hidden="true">↗</b>
                    </a>
                </div>
            </div>
        </article>

        <article class="hero-slide" data-hero-slide aria-hidden="true">
            <div class="site-container hero__grid">
                <div class="hero__content">
                    <div class="eyebrow eyebrow--light"><span></span> Worship · Word · Community</div>
                    <h1>A place to belong, <em>grow and encounter God.</em></h1>
                    <p class="hero__lead">Campus communities and national gatherings create room for worship, biblical formation, meaningful friendships and a faith that shapes everyday life.</p>
                    <div class="hero__actions">
                        <a class="button button--gold" href="{{ route('about.mission-strategies') }}">Our mission strategies <span aria-hidden="true">→</span></a>
                        <a class="button button--ghost" href="{{ route('people.programs') }}">Explore programs</a>
                    </div>
                    <div class="hero__proof"><div class="hero__proof-mark" aria-hidden="true">01</div><p><strong>Rooted in truth.</strong><br>Growing together in Christ.</p></div>
                </div>
                <div class="hero__visual" aria-label="A worship gathering">
                    <div class="hero__image-wrap hero__image-wrap--landscape">
                        <img src="{{ asset('main/images/bg3.jpg') }}" alt="People gathered in worship">
                        <div class="hero__image-shade"></div>
                        <div class="hero__image-caption"><span>Spiritual formation</span><strong>Gathered around worship and the Word.</strong></div>
                    </div>
                    <a class="hero__directory-card" href="{{ route('people.programs') }}">
                        <span class="hero__directory-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3v18M3 12h18"/></svg></span>
                        <span><small>Grow with the community</small><strong>Explore GSF Programs</strong></span><b aria-hidden="true">↗</b>
                    </a>
                </div>
            </div>
        </article>

        <article class="hero-slide" data-hero-slide aria-hidden="true">
            <div class="site-container hero__grid">
                <div class="hero__content">
                    <div class="eyebrow eyebrow--light"><span></span> Campus · Purpose · Influence</div>
                    <h1>Equipped to lead with <em>clarity and conviction.</em></h1>
                    <p class="hero__lead">GSF helps students develop character, leadership capacity and a lifelong commitment to responsible service—on campus, at work and in society.</p>
                    <div class="hero__actions">
                        <a class="button button--gold" href="#leadership">Meet our leaders <span aria-hidden="true">→</span></a>
                        <a class="button button--ghost" href="{{ route('login') }}">Member portal</a>
                    </div>
                    <div class="hero__proof"><div class="hero__proof-mark" aria-hidden="true">02</div><p><strong>Prepared for influence.</strong><br>Leading through service and example.</p></div>
                </div>
                <div class="hero__visual" aria-label="Leadership and learning">
                    <div class="hero__image-wrap hero__image-wrap--learning">
                        <img src="{{ asset('frontend/img/sliders/slider2.jpg') }}" alt="A participant taking notes during a leadership gathering">
                        <div class="hero__image-shade"></div>
                        <div class="hero__image-caption"><span>Leadership formation</span><strong>Purpose becomes practical through service.</strong></div>
                    </div>
                    <a class="hero__directory-card" href="{{ route('people.campuses') }}">
                        <span class="hero__directory-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 19V8l8-5 8 5v11M8 21v-8h8v8"/></svg></span>
                        <span><small>Find your community</small><strong>Explore GSF Campuses</strong></span><b aria-hidden="true">↗</b>
                    </a>
                </div>
            </div>
        </article>
    </div>

    <div class="site-container hero-slider__controls">
        <div class="hero-slider__pagination" role="tablist" aria-label="Choose hero slide">
            <button class="is-active" type="button" data-hero-dot aria-label="Show slide 1" aria-selected="true"><span>01</span><i></i></button>
            <button type="button" data-hero-dot aria-label="Show slide 2" aria-selected="false"><span>02</span><i></i></button>
            <button type="button" data-hero-dot aria-label="Show slide 3" aria-selected="false"><span>03</span><i></i></button>
        </div>
        <div class="hero-slider__actions">
            <button type="button" data-hero-pause aria-label="Pause automatic slides"><span class="hero-slider__pause-icon" aria-hidden="true"></span></button>
            <button type="button" data-hero-prev aria-label="Previous slide">←</button>
            <button type="button" data-hero-next aria-label="Next slide">→</button>
        </div>
    </div>
</section>

<section class="impact-strip" aria-label="GSF reach">
    <div class="site-container impact-strip__grid">
        <div class="impact-strip__intro"><span>Our national community</span><strong>Present, connected and growing across campuses.</strong></div>
        <div class="impact-stat"><strong>{{ number_format($impact['campuses']) }}</strong><span>Campus fellowships</span></div>
        <div class="impact-stat"><strong>{{ number_format($impact['fields']) }}</strong><span>Ministry fields</span></div>
        <div class="impact-stat"><strong>{{ number_format($impact['leaders']) }}</strong><span>Active leaders</span></div>
    </div>
</section>

<section class="section section--about" id="about">
    <div class="site-container about-grid">
        <div class="about-visual">
            <div class="about-visual__primary">
                <img src="{{ asset('frontend/img/sliders/slider2.jpg') }}" alt="A participant taking notes during a gathering">
            </div>
            <div class="about-visual__note">
                <span class="about-visual__symbol" aria-hidden="true">“</span>
                <p>Building a generation grounded in truth and prepared for meaningful influence.</p>
            </div>
            <span class="about-visual__pattern" aria-hidden="true"></span>
        </div>
        <div class="section-copy">
            <div class="eyebrow"><span></span> GSF Foundational Concept</div>
            <h2>One fellowship. Many campuses. A shared foundation.</h2>
            <p class="section-copy__lead">GOFAMINT Students’ Fellowship brings students together within the GOFAMINT ministry family, creating a connected environment for spiritual formation, leadership, service and lifelong relationships.</p>
            <p>From the local campus fellowship to the national community, GSF provides a place to belong, grow and contribute—during school and beyond.</p>
            <div class="principle-list">
                <div><span>01</span><p><strong>Christ-centred formation</strong>Faith that shapes character, choices and everyday life.</p></div>
                <div><span>02</span><p><strong>Purposeful community</strong>Relationships that strengthen students and endure beyond campus.</p></div>
                <div><span>03</span><p><strong>Leadership through service</strong>Equipping students to lead responsibly wherever they are placed.</p></div>
            </div>
            <a class="text-link" href="{{ route('about.foundational-concept') }}">Read the foundational concept <span aria-hidden="true">→</span></a>
        </div>
    </div>
</section>

<section class="section section--strategies" id="strategies">
    <div class="site-container">
        <div class="section-heading section-heading--split">
            <div><div class="eyebrow eyebrow--light"><span></span> GSF Mission Strategies</div><h2>How we turn conviction into action.</h2></div>
            <p>Our work is expressed through a connected journey that reaches students, develops disciples, equips leaders and mobilises service.</p>
        </div>
        <div class="strategy-grid">
            <article class="strategy-card strategy-card--featured">
                <span class="strategy-card__number">01</span>
                <div class="strategy-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3v18M3 12h18"/></svg></div>
                <h3>Reach &amp; establish</h3>
                <p>Helping students discover a faith community and become firmly rooted in Christ and fellowship.</p>
            </article>
            <article class="strategy-card">
                <span class="strategy-card__number">02</span>
                <div class="strategy-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 19V8l8-5 8 5v11M8 21v-8h8v8"/></svg></div>
                <h3>Disciple &amp; nurture</h3>
                <p>Creating consistent environments for biblical growth, character formation and mutual care.</p>
            </article>
            <article class="strategy-card">
                <span class="strategy-card__number">03</span>
                <div class="strategy-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c.7-4.3 3.4-7 8-7s7.3 2.7 8 7"/></svg></div>
                <h3>Equip &amp; empower</h3>
                <p>Developing capable leaders who serve with competence, conviction and accountability.</p>
            </article>
            <article class="strategy-card">
                <span class="strategy-card__number">04</span>
                <div class="strategy-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m3 12 7-7v4c7 0 10 4 11 10-3-3-6-4-11-4v4l-7-7Z"/></svg></div>
                <h3>Mobilise &amp; influence</h3>
                <p>Sending students into their communities and professions as responsible agents of godly influence.</p>
            </article>
        </div>
        <div class="strategy-more"><a class="text-link text-link--light" href="{{ route('about.mission-strategies') }}">Read the full mission strategies <span aria-hidden="true">→</span></a></div>
    </div>
</section>

<section class="section section--programs">
    <div class="site-container">
        <div class="section-heading section-heading--split section-heading--dark">
            <div><div class="eyebrow"><span></span> Programs &amp; Initiatives</div><h2>Spaces designed for growth and connection.</h2></div>
            <a class="text-link" href="{{ route('people.programs') }}">View all programs <span aria-hidden="true">→</span></a>
        </div>
        <div class="program-grid">
            <article class="program-card program-card--large">
                <span class="program-card__tag">Formation</span>
                <div><h3>Spiritual formation</h3><p>Gatherings and resources that help students deepen their walk with Christ and live out their faith.</p></div>
                <span class="program-card__arrow" aria-hidden="true">↗</span>
            </article>
            <article class="program-card"><span class="program-card__tag">Leadership</span><div><h3>Leadership development</h3><p>Practical formation for responsible service on campus and beyond.</p></div><span class="program-card__arrow" aria-hidden="true">↗</span></article>
            <article class="program-card"><span class="program-card__tag">Community</span><div><h3>Campus fellowship</h3><p>Local communities where students worship, learn, serve and belong.</p></div><span class="program-card__arrow" aria-hidden="true">↗</span></article>
            <article class="program-card program-card--accent"><span class="program-card__tag">Gatherings</span><div><h3>National programs</h3><p>Shared moments that unite the wider GSF family across campuses and generations.</p></div><span class="program-card__arrow" aria-hidden="true">↗</span></article>
        </div>
    </div>
</section>

<section class="section section--leaders" id="leadership">
    <div class="site-container">
        <div class="section-heading section-heading--split section-heading--dark">
            <div><div class="eyebrow"><span></span> Meet Our Leaders</div><h2>Serving the fellowship with conviction and care.</h2></div>
            <a class="text-link" href="{{ route('leaders.fathers') }}">Meet the leadership team <span aria-hidden="true">→</span></a>
        </div>

        @if($leaders->isNotEmpty())
            <div class="leader-grid">
                @foreach($leaders as $leader)
                    @php
                        $initials = collect(preg_split('/\s+/', trim($leader->name)))->filter()->take(2)->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                        $position = $leader->designation?->name ?: ($leader->role?->name ?: 'GSF Leader');
                    @endphp
                    <article class="leader-card">
                        <div class="leader-card__portrait"><span>{{ $initials }}</span><i aria-hidden="true"></i></div>
                        <div class="leader-card__copy"><h3>{{ $leader->name }}</h3><p>{{ $position }}</p></div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state"><strong>Leadership profiles are being prepared.</strong><p>Visit the directory to explore the wider GSF leadership network.</p></div>
        @endif
    </div>
</section>

<section class="section section--news" id="news-events">
    <div class="site-container">
        <div class="section-heading section-heading--split section-heading--dark">
            <div><div class="eyebrow"><span></span> News &amp; Events</div><h2>What’s happening across GSF.</h2></div>
            <a class="text-link" href="{{ route('people.programs') }}">Explore all events <span aria-hidden="true">→</span></a>
        </div>

        <div class="updates-grid">
            <div class="events-panel">
                <div class="panel-label"><span>Upcoming</span><strong>Programs &amp; events</strong></div>
                @forelse($upcomingEvents->take(4) as $event)
                    <article class="event-row">
                        <div class="event-date"><strong>{{ \Carbon\Carbon::parse($event->date)->format('d') }}</strong><span>{{ \Carbon\Carbon::parse($event->date)->format('M') }}</span></div>
                        <div class="event-row__copy">
                            <span>{{ $event->chapter?->name ?: 'GSF National' }}</span>
                            <h3>{{ $event->title }}</h3>
                            <p>{{ $event->venue ?: 'Venue to be announced' }}@if($event->time) · {{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}@endif</p>
                        </div>
                        <a href="{{ route('people.programs') }}#{{ $event->id }}" aria-label="View {{ $event->title }}">→</a>
                    </article>
                @empty
                    <div class="empty-state empty-state--compact"><strong>New programs will appear here.</strong><p>Check back for the next national and campus gathering.</p></div>
                @endforelse
            </div>

            <div class="news-panel">
                <div class="panel-label"><span>Latest</span><strong>From our community</strong></div>
                @forelse($latestPosts as $post)
                    <article class="news-card">
                        <div class="news-card__meta"><span>{{ optional($post->published_at)->format('d M Y') }}</span><span>{{ $post->category?->name ?: 'GSF News' }}</span></div>
                        <h3><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                        <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(trim(strip_tags($post->body)), 150) }}</p>
                    </article>
                @empty
                    <article class="news-card news-card--placeholder">
                        <div class="news-card__meta"><span>GSF Newsroom</span></div>
                        <h3>Stories from across the fellowship will live here.</h3>
                        <p>Approved news, updates and insights from national and campus communities will appear in this section.</p>
                    </article>
                @endforelse
            </div>
        </div>
    </div>
</section>

<section class="section section--cta">
    <div class="site-container cta-card">
        <div class="cta-card__mark" aria-hidden="true">GSF</div>
        <div><span>There is a place for you here.</span><h2>Find your campus. Join the community. Grow with purpose.</h2></div>
        <div class="cta-card__actions">
            <a class="button button--gold" href="{{ route('directory.index') }}">Explore directory <span aria-hidden="true">→</span></a>
            <a class="button button--ghost" href="{{ route('login') }}">Member portal</a>
        </div>
    </div>
</section>
@endsection
