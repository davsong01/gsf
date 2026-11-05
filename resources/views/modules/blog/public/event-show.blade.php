@extends('frontend.site.layout')
@section('title', ($post->seo_title ?: $post->title).' — GSF Programs & Events')
@section('description', $post->seo_description ?: $post->excerpt)
@section('activeSection', 'events')
@php
    $eventUrl = route('events.public.show', $post->slug);
    $flyerUrl = \App\Modules\Blog\BlogMedia::url($post->featured_image);
    $socialImageUrl = \App\Modules\Blog\BlogMedia::url($post->social_image ?: $post->featured_image);
    $viewer = auth('stakeholder')->user() ?: auth()->user();
    $canEditEvent = \App\Modules\Blog\BlogPermissions::canEditPost($viewer, $post);
    $editRoute = ($viewer instanceof \App\Models\Stakeholder ? 'stakeholders' : 'admin').'.events.posts.edit';
    $campus = $post->author instanceof \App\Models\Stakeholder ? $post->author->chapter : null;
    $hostName = $campus?->name ?: ($post->author?->name ?: 'GOFAMINT Students’ Fellowship');
    $hostUrl = $campus?->slug ? route('campus.single', $campus->slug) : null;
    $eventDate = $post->event_starts_at;
    $eventStatus = $eventDate?->isPast() ? 'Past event' : 'Upcoming event';
    $eventDescription = $post->seo_description ?: $post->excerpt;
    $eventSchema = ['@context' => 'https://schema.org', '@type' => 'Event', 'name' => $post->title, 'description' => $eventDescription, 'url' => $eventUrl, 'organizer' => ['@type' => 'Organization', 'name' => $hostName]];
    if ($eventDate) $eventSchema['startDate'] = $eventDate->toIso8601String();
    if ($post->event_venue) $eventSchema['location'] = ['@type' => 'Place', 'name' => $post->event_venue];
    if ($flyerUrl) $eventSchema['image'] = $flyerUrl;
    if (!empty($post->event_speakers)) $eventSchema['performer'] = collect($post->event_speakers)->map(fn ($person) => ['@type' => 'Person', 'name' => $person['name']])->all();
@endphp
@push('head')
<link rel="canonical" href="{{ $post->canonical_url ?: $eventUrl }}">
@if($isPreview ?? false)<meta name="robots" content="noindex,nofollow">@endif
<meta property="og:type" content="event"><meta property="og:title" content="{{ $post->seo_title ?: $post->title }}"><meta property="og:description" content="{{ $eventDescription }}"><meta property="og:url" content="{{ $eventUrl }}">@if($socialImageUrl)<meta property="og:image" content="{{ $socialImageUrl }}">@endif
<meta name="twitter:card" content="summary_large_image">
<script type="application/ld+json">{!! json_encode($eventSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@2.47.12/dist/css/suneditor.min.css">
@if($post->allow_comments && !($isPreview ?? false))
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
@endif
<style>
.event-page { background:#f5f7fa; color:#142242; }
.event-page__hero { position:relative; overflow:hidden; background:radial-gradient(circle at 91% 12%,#137996 0,#124f74 27%,transparent 51%),linear-gradient(120deg,#091b3e,#103766 76%); color:#fff; }
.event-page__hero:before { position:absolute; inset:0; background:linear-gradient(120deg,transparent 55%,rgba(255,255,255,.045) 55.2%,transparent 55.5%); content:""; pointer-events:none; }
.event-page__hero-inner { position:relative; display:grid; grid-template-columns:minmax(0,1.2fr) minmax(310px,.8fr); gap:clamp(28px,5vw,90px); align-items:center; min-height:570px; padding-block:62px 92px; }
.event-page__crumbs { display:flex; flex-wrap:wrap; align-items:center; gap:10px; margin-bottom:42px; color:#bfd4e5; font-size:13px; font-weight:700; }
.event-page__crumbs a { color:inherit; text-decoration:none; }
.event-page__crumbs a:hover { color:#fff; }
.event-page__eyebrow { display:inline-flex; align-items:center; gap:9px; margin-bottom:20px; color:#f3cc72; font-size:12px; font-weight:850; letter-spacing:.16em; text-transform:uppercase; }
.event-page__eyebrow:before { width:28px; height:2px; background:currentColor; content:""; }
.event-page__hero h1 { max-width:880px; margin:0; color:#fff; font-size:clamp(42px,5vw,78px); line-height:1.06; letter-spacing:-.045em; overflow-wrap:anywhere; }
.event-page__intro { max-width:700px; margin:25px 0 0; color:#d5e2ef; font-size:clamp(17px,1.5vw,21px); line-height:1.7; }
.event-page__hero-actions { display:flex; flex-wrap:wrap; align-items:center; gap:12px; margin-top:35px; }
.event-page__hero-actions a { display:inline-flex; align-items:center; justify-content:center; min-height:48px; padding:11px 19px; border:1px solid rgba(255,255,255,.5); border-radius:100px; color:#fff; font-size:14px; font-weight:800; text-decoration:none; }
.event-page__hero-actions a:first-child { border-color:#e4bf63; background:#e4bf63; color:#17223a; }
.event-page__hero-actions a:hover { background:#fff; border-color:#fff; color:#142242; }
.event-page__flyer { width:min(100%,440px); justify-self:center; padding:9px; border:1px solid rgba(255,255,255,.27); border-radius:23px; background:rgba(255,255,255,.12); box-shadow:0 24px 65px rgba(0,0,0,.23); transform:rotate(2deg); }
.event-page__flyer img { display:block; width:100%; max-height:590px; border-radius:15px; object-fit:contain; background:#e7ebef; }
.event-page__flyer-placeholder { display:grid; place-items:center; min-height:350px; padding:35px; border-radius:15px; background:linear-gradient(145deg,#19466f,#0a254b); color:#fff; font-size:36px; font-weight:850; text-align:center; }
.event-page__layout { display:grid; grid-template-columns:minmax(0,1.6fr) minmax(290px,.75fr); gap:clamp(25px,5vw,74px); align-items:start; padding-block:62px 85px; }
.event-page__section-label { display:block; color:#0d79a7; font-size:12px; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }
.event-page__section-title { margin:10px 0 24px; color:#112044; font-size:clamp(27px,3vw,40px); line-height:1.2; }
.event-page__body { max-width:860px; color:#465471; font-size:17px; line-height:1.85; overflow-wrap:anywhere; }
.event-page__body img { max-width:100%; height:auto; border-radius:12px; }
.event-page__body iframe { max-width:100%; }
.event-page__body table { display:block; max-width:100%; overflow-x:auto; }
.event-page__body p { margin:0 0 1.2em; }
.event-page__side { position:sticky; top:110px; overflow:hidden; border:1px solid #dce5eb; border-radius:22px; background:#fff; box-shadow:0 16px 40px rgba(12,32,66,.065); }
.event-page__side-head { padding:25px 27px; background:#143962; color:#fff; }
.event-page__side-head span { display:block; color:#a5d6ec; font-size:11px; font-weight:850; letter-spacing:.13em; text-transform:uppercase; }
.event-page__side-head strong { display:block; margin-top:5px; font-size:22px; }
.event-page__fact { display:grid; grid-template-columns:36px minmax(0,1fr); gap:13px; padding:20px 25px; border-bottom:1px solid #e9edf1; }
.event-page__fact i { display:grid; place-items:center; width:36px; height:36px; border-radius:50%; background:#e8f4fa; color:#0877a4; font-size:15px; }
.event-page__fact span { display:block; margin-bottom:5px; color:#69748a; font-size:11px; font-weight:850; letter-spacing:.1em; text-transform:uppercase; }
.event-page__fact strong, .event-page__fact a { display:block; color:#142242; font-size:15px; font-weight:800; line-height:1.5; overflow-wrap:anywhere; }
.event-page__fact a { text-decoration:underline; text-decoration-color:#a9bfcb; text-underline-offset:3px; }
.event-page__side-footer { padding:23px 25px 27px; }
.event-page__side-footer a { display:block; padding:13px 16px; border-radius:100px; background:#e1bd68; color:#18223b; font-size:14px; font-weight:850; text-align:center; text-decoration:none; }
.event-page__people { margin-top:68px; }
.event-page__people-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:15px; }
.event-page__person { min-width:0; padding:21px; border:1px solid #dce5eb; border-radius:18px; background:#fff; }
.event-page__person img { display:block; width:88px; height:88px; margin-bottom:17px; border-radius:50%; object-fit:cover; }
.event-page__person-icon { display:grid; place-items:center; width:88px; height:88px; margin-bottom:17px; border-radius:50%; background:#e8f4fa; color:#1077a5; font-size:27px; }
.event-page__person small { display:block; margin-bottom:5px; color:#1077a5; font-size:11px; font-weight:850; letter-spacing:.09em; text-transform:uppercase; }
.event-page__person strong { display:block; color:#142242; font-size:19px; line-height:1.3; }
.event-page__person span:last-child { display:block; margin-top:6px; color:#67738b; font-size:13px; }
.event-page__share { display:flex; flex-wrap:wrap; align-items:center; gap:9px; margin-top:55px; padding-top:25px; border-top:1px solid #dce5eb; }
.event-page__share span { margin-right:5px; color:#657088; font-size:13px; font-weight:800; }
.event-page__share a, .event-page__share button { padding:10px 14px; border:1px solid #ced9e3; border-radius:100px; background:#fff; color:#123d68; font:inherit; font-size:13px; font-weight:750; text-decoration:none; cursor:pointer; }
.event-page__share a:hover, .event-page__share button:hover { border-color:#0e78a7; color:#0e78a7; }
.event-page__more { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:17px; margin-top:35px; }
.event-page__more a { display:block; padding:22px; border:1px solid #dce5eb; border-radius:17px; background:#fff; color:#142242; text-decoration:none; }
.event-page__more a:hover { border-color:#0e78a7; }
.event-page__more small { display:block; margin-bottom:10px; color:#0e78a7; font-size:11px; font-weight:850; letter-spacing:.08em; text-transform:uppercase; }
.event-page__more strong { display:block; font-size:17px; }
.blog-comments-list { display:grid; gap:13px; margin:0 0 30px; }
.blog-comment { padding:17px 19px; border:1px solid #dce5eb; border-radius:12px; background:#fff; }
.blog-comment__byline { display:flex; flex-wrap:wrap; gap:6px 10px; margin-bottom:8px; color:#142242; font-size:13px; font-weight:800; }
.blog-comment__campus, .blog-comment__date { color:#69748a; font-size:11px; font-weight:600; }
.blog-comment__body { margin:0; color:#465471; font-size:14px; line-height:1.7; white-space:pre-line; }
.blog-comments-empty { margin:0 0 28px; color:#69748a; font-size:14px; }
.blog-comment-form-heading { margin:34px 0 18px; font-size:19px; }
.blog-comment-feedback { max-width:760px; margin:16px 0 0; padding:12px 15px; border-radius:9px; color:#17613b; background:#e8f6ee; font-size:14px; }
.blog-comment-feedback.is-error { color:#9c2938; background:#fff0f1; }
#blog-comment-submit:disabled { opacity:.65; cursor:wait; }
#comments-section { scroll-margin-top:110px; }
@media(max-width:900px) { .event-page__hero-inner, .event-page__layout { grid-template-columns:1fr; } .event-page__hero-inner { padding-block:42px 60px; } .event-page__flyer { width:min(100%,360px); transform:none; } .event-page__side { position:static; order:-1; } }
@media(max-width:600px) { .event-page__hero-inner { gap:38px; } .event-page__crumbs { margin-bottom:30px; } .event-page__hero h1 { font-size:clamp(38px,11vw,56px); } .event-page__layout { padding-block:40px 65px; } .event-page__more { grid-template-columns:1fr; } }
</style>
@endpush
@section('content')
<div class="event-page">
    <header class="event-page__hero">
        <div class="site-container event-page__hero-inner">
            <div>
                <nav class="event-page__crumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route('events.public.index') }}">Programs &amp; Events</a><span>·</span><span>{{ $eventStatus }}</span></nav>
                <span class="event-page__eyebrow">{{ $post->is_featured ? 'Featured event · ' : '' }}{{ $eventStatus }} · GSF programs &amp; events</span>
                <h1>{{ $post->title }}</h1>
                @if($post->excerpt)<p class="event-page__intro">{{ $post->excerpt }}</p>@endif
                <div class="event-page__hero-actions"><a href="#event-details">Event details ↓</a>@if($hostUrl)<a href="{{ $hostUrl }}">Visit the campus ↗</a>@endif</div>
            </div>
            <div class="event-page__flyer">@if($flyerUrl)<img src="{{ $flyerUrl }}" alt="Flyer for {{ $post->title }}">@else<div class="event-page__flyer-placeholder">{{ $post->title }}</div>@endif</div>
        </div>
    </header>
    <div class="site-container event-page__layout">
        <div class="event-page__content">
            @if($isPreview ?? false)<div class="alert alert-info" role="status">Preview mode — this visit does not count toward event views.</div>@endif
            @if($canEditEvent)<p><a href="{{ route($editRoute, $post) }}">Edit event ↗</a></p>@endif
            <section aria-labelledby="event-about-heading"><span class="event-page__section-label">The gathering</span><h2 class="event-page__section-title" id="event-about-heading">About this event</h2><div class="event-page__body sun-editor-editable">{!! \App\Modules\Blog\BlogMedia::htmlWithAvailableImages(str_replace('//storage/', '/storage/', $post->body)) !!}</div></section>
            @if(!empty($post->event_speakers))<section class="event-page__people" aria-labelledby="event-people-heading"><span class="event-page__section-label">The people</span><h2 class="event-page__section-title" id="event-people-heading">Host and Guest Minister</h2><div class="event-page__people-grid">@foreach($post->event_speakers as $person)<div class="event-page__person">@if($personImage = \App\Modules\Blog\BlogMedia::url($person['image'] ?? null))<img src="{{ $personImage }}" alt="Photo of {{ $person['name'] }}" loading="lazy">@else<span class="event-page__person-icon" aria-hidden="true"><i class="fa fa-user"></i></span>@endif<small>{{ ($person['kind'] ?? 'speaker') === 'guest_minister' ? 'Guest Minister' : 'Host' }}</small><strong>{{ $person['name'] }}</strong>@if(filled($person['ministry'] ?? null))<span>{{ $person['ministry'] }}</span>@endif</div>@endforeach</div></section>@endif
            <div class="event-page__share"><span>Share this event</span><a href="https://wa.me/?text={{ urlencode($post->title.' '.$eventUrl) }}" target="_blank" rel="noopener noreferrer">WhatsApp</a><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($eventUrl) }}" target="_blank" rel="noopener noreferrer">Facebook</a><button type="button" id="event-copy-link" data-url="{{ $eventUrl }}">Copy link</button><span id="event-copy-feedback" role="status" aria-live="polite"></span></div>
            @include('modules.blog.public.comments')
            @if(($previousPost ?? null) || ($nextPost ?? null))<nav class="event-page__more" aria-label="More events">@if($previousPost)<a href="{{ route('events.public.show', $previousPost->slug) }}"><small>Past event · {{ $previousPost->event_starts_at->format('j M Y') }}</small><strong>{{ $previousPost->title }}</strong></a>@endif @if($nextPost)<a href="{{ route('events.public.show', $nextPost->slug) }}"><small>Upcoming event · {{ $nextPost->event_starts_at->format('j M Y') }}</small><strong>{{ $nextPost->title }}</strong></a>@endif</nav>@endif
        </div>
        <aside class="event-page__side" id="event-details" aria-label="Event details"><div class="event-page__side-head"><span>Plan your visit</span><strong>Event details</strong></div>@if($eventDate)<div class="event-page__fact"><i class="fa fa-calendar-o" aria-hidden="true"></i><div><span>Date</span><strong>{{ $eventDate->format('l, j F Y') }}</strong></div></div><div class="event-page__fact"><i class="fa fa-clock-o" aria-hidden="true"></i><div><span>Time</span><strong>{{ $eventDate->format('g:i A') }}</strong></div></div>@endif @if($post->event_venue)<div class="event-page__fact"><i class="fa fa-map-marker" aria-hidden="true"></i><div><span>Venue</span><strong>{{ $post->event_venue }}</strong></div></div>@endif<div class="event-page__fact"><i class="fa fa-users" aria-hidden="true"></i><div><span>Hosted by</span>@if($hostUrl)<a href="{{ $hostUrl }}">{{ $hostName }}</a>@else<strong>{{ $hostName }}</strong>@endif</div></div><div class="event-page__side-footer"><a href="{{ route('events.public.index') }}">Browse all programs &amp; events →</a></div></aside>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var button = document.getElementById('event-copy-link');
    if (!button) return;
    button.addEventListener('click', async function () {
        var feedback = document.getElementById('event-copy-feedback');
        try {
            await navigator.clipboard.writeText(button.dataset.url);
            button.textContent = 'Copied!';
            feedback.textContent = 'Event link copied.';
        } catch (error) {
            feedback.textContent = 'Copy unavailable. Use your browser address bar to copy the link.';
        }
    });
});
</script>
@endpush
