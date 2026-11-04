@extends('frontend.site.layout')

@section('title', "GSF Mission Strategies — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page')
@section('activeSection', 'about')

@section('content')
<section class="inner-hero inner-hero--strategies">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>About GSF</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> Our Strategies</div>
            <h1>Constitutional direction for <em>gospel, growth and service.</em></h1>
            <p>Our strategies are rooted in the aims and objectives of GSF as established in Article IV of the Fellowship’s Constitution.</p>
        </div>
    </div>
</section>

<section class="content-section">
    <div class="site-container content-intro">
        <div><div class="eyebrow"><span></span> Article IV</div><h2>The constitutional foundation of our mission.</h2></div>
        <div class="content-intro__body"><p>Our Foundational Concept is rooted in the aims and objectives of the Fellowship as enshrined in Article IV of the GSF Constitution.</p><p>These objectives establish GSF’s identity within GOFAMINT and define our responsibility to the gospel, our members, the church and the nation.</p></div>
    </div>
</section>

<section class="constitution-statement">
    <div class="site-container constitution-statement__card">
        <span class="constitution-statement__quote" aria-hidden="true">“</span>
        <div><small>The Constitution states:</small><blockquote>GSF shall be the student wing of The Gospel Faith Mission International, herein referred to as GOFAMINT.</blockquote></div>
        <img src="{{ asset('frontend/img/logo.png') }}" alt="GSF logo">
    </div>
</section>

<section class="mission-pathway mission-pathway--constitution">
    <div class="site-container">
        <div class="constitutional-heading"><div class="eyebrow"><span></span> Our Aims &amp; Objectives</div><h2>Seven constitutional commitments</h2><p>The Fellowship exists to pursue the following aims and objectives:</p></div>
        <div class="mission-pathway__list">
            <article><span class="mission-pathway__number">01</span><div class="mission-pathway__title"><small>The Gospel</small><h2>Propagate Christ</h2></div><p>To propagate the Gospel of our Lord Jesus Christ in higher institutions of learning and secondary schools, and to cooperate on spiritual matters with other Christian bodies that share the same biblical beliefs and doctrines.</p></article>
            <article><span class="mission-pathway__number">02</span><div class="mission-pathway__title"><small>Holy living</small><h2>Nurture spiritual maturity</h2></div><p>To uphold members in living holy lives and nurture them in the Word of God unto spiritual maturity.</p></article>
            <article><span class="mission-pathway__number">03</span><div class="mission-pathway__title"><small>Unity</small><h2>Build the spirit of oneness</h2></div><p>To inculcate in members the spirit of oneness in obedience to the will of Christ Jesus.</p></article>
            <article><span class="mission-pathway__number">04</span><div class="mission-pathway__title"><small>Mutual care</small><h2>Aid one another</h2></div><p>To support one another in areas of need—materially, spiritually and academically.</p></article>
            <article><span class="mission-pathway__number">05</span><div class="mission-pathway__title"><small>Alignment</small><h2>Operate within GOFAMINT</h2></div><p>To live and operate within the aims and objectives of The Gospel Faith Mission International.</p></article>
            <article><span class="mission-pathway__number">06</span><div class="mission-pathway__title"><small>Responsible action</small><h2>Pursue lawful objectives</h2></div><p>To undertake all other lawful activities that are incidental or conducive to the attainment of any or all of the Fellowship’s objectives.</p></article>
            <article><span class="mission-pathway__number">07</span><div class="mission-pathway__title"><small>Peace &amp; loyalty</small><h2>Honour God, church and nation</h2></div><p>Above all, to inculcate in members the spirit of peacemaking and loyalty—first to God, then to GOFAMINT and to the nation.</p></article>
        </div>
    </div>
</section>

<section class="strategy-outcome">
    <div class="site-container strategy-outcome__grid">
        <div><div class="eyebrow eyebrow--light"><span></span> From Constitution to Campus</div><h2>Objectives expressed through everyday fellowship life.</h2></div>
        <div class="strategy-outcome__points"><p><span>01</span>Proclaiming the gospel in schools and institutions</p><p><span>02</span>Nurturing holy living and biblical maturity</p><p><span>03</span>Building unity, care and academic support</p><p><span>04</span>Serving GOFAMINT, society and the nation responsibly</p></div>
    </div>
</section>

@if(filled($siteSettings?->gsf_family_song_lyrics) || filled($siteSettings?->gsf_family_song_audio_path))
<section class="family-song-section">
    <div class="site-container family-song-section__grid">
        <div class="family-song-section__heading">
            <div class="eyebrow"><span></span> Our Fellowship Song</div>
            <h2>The GSF Family Song</h2>
            <p>A song that brings our fellowship family together in faith and shared purpose.</p>
            @if(filled($siteSettings?->gsf_family_song_audio_path))
                <audio class="family-song-section__audio" controls preload="none" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->gsf_family_song_audio_path) }}">Your browser does not support audio playback.</audio>
            @endif
        </div>
        @if(filled($siteSettings?->gsf_family_song_lyrics))
            <div class="family-song-section__lyrics">{!! nl2br(e($siteSettings->gsf_family_song_lyrics)) !!}</div>
        @endif
    </div>
</section>
@endif

<section class="document-section">
    <div class="site-container">
        <div class="document-section__heading"><div class="eyebrow"><span></span> Governance Documents</div><h2>Read the documents that guide the Fellowship.</h2></div>
        <div class="document-grid">
            @if(filled($siteSettings?->gsf_constitution_path))
            <a class="document-card" href="{{ route('site.documents.show', 'constitution') }}" target="_blank" rel="noopener">
                <span class="document-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3h7l4 4v14H7z"/><path d="M14 3v5h5M9.5 13h6M9.5 17h6"/></svg></span>
                <span><small>PDF document</small><strong>Download GSF Constitution</strong></span><b aria-hidden="true">↓</b>
            </a>
            @else
            <div class="document-card document-card--unavailable" aria-disabled="true">
                <span class="document-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3h7l4 4v14H7z"/><path d="M14 3v5h5M9.5 13h6M9.5 17h6"/></svg></span>
                <span><small>Upload pending</small><strong>GSF Constitution</strong></span>
            </div>
            @endif

            @if(filled($siteSettings?->gsf_code_of_conduct_path))
            <a class="document-card document-card--gold" href="{{ route('site.documents.show', 'code-of-conduct') }}" target="_blank" rel="noopener">
                <span class="document-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3h7l4 4v14H7z"/><path d="M14 3v5h5M9.5 13h6M9.5 17h6"/></svg></span>
                <span><small>PDF document</small><strong>Download Code of Conduct</strong></span><b aria-hidden="true">↓</b>
            </a>
            @else
            <div class="document-card document-card--gold document-card--unavailable" aria-disabled="true">
                <span class="document-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3h7l4 4v14H7z"/><path d="M14 3v5h5M9.5 13h6M9.5 17h6"/></svg></span>
                <span><small>Upload pending</small><strong>GSF Code of Conduct</strong></span>
            </div>
            @endif
        </div>
    </div>
</section>

@endsection
