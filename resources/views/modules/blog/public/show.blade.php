@extends('frontend.site.layout')
@section('title', ($post->seo_title ?: $post->title).($post->type === 'event' ? ' — GSF Programs & Events' : ' — GSF News'))
@section('description', $post->seo_description ?: $post->excerpt)
@section('activeSection', $post->type === 'event' ? 'events' : 'news')
@php
    $postViewer = auth('stakeholder')->user() ?: auth()->user();
    $canEditPost = \App\Modules\Blog\BlogPermissions::canEditPost($postViewer, $post);
    $postUrl = route($post->type === 'event' ? 'events.public.show' : 'blog.show', $post->slug);
    $postEditRoute = ($postViewer instanceof \App\Models\Stakeholder ? 'stakeholders' : 'admin').($post->type === 'event' ? '.events.posts.edit' : '.blog.posts.edit');
    $featuredImageUrl = \App\Modules\Blog\BlogMedia::url($post->featured_image);
    $socialImageUrl = \App\Modules\Blog\BlogMedia::url($post->social_image ?: $post->featured_image);
    $isCampusPostAuthor = $post->author instanceof \App\Models\Stakeholder && $post->author->chapter;
    $postAuthorName = $isCampusPostAuthor
        ? $post->author->chapter->name
        : ($post->author?->name ?: $post->author?->email);
    $postAuthorUrl = $isCampusPostAuthor ? route('campus.single', $post->author->chapter->slug) : null;
    $postAuthorAffiliation = $post->author instanceof \App\Models\User
        ? 'National Admin'
        : ($post->author instanceof \App\Models\Stakeholder && !$isCampusPostAuthor ? $post->author->chapter?->name : null);
@endphp
@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@2.47.12/dist/css/suneditor.min.css">
<style>
.blog-article-body { text-align: left; overflow-wrap: anywhere; }
.blog-article-body img { max-width: 100%; height: auto; }
.blog-article-body > p { margin: 0 0 1.2em; }
.blog-article-body > p:last-child { margin-bottom: 0; }
.blog-article-body blockquote { margin: 1.5em 0; padding: .3em 1.25em; border-left: 4px solid var(--gold-dark); color: var(--ink); font-style: italic; }
.blog-article-body figure { max-width: 100%; margin: 1.5em auto; }
.blog-article-body iframe { max-width: 100%; }
.blog-article-body table { display: block; max-width: 100%; overflow-x: auto; }
.blog-post-event-details { display:flex; flex-wrap:wrap; align-items:center; gap:10px 18px; margin:0 0 18px; padding:18px 20px; border:1px solid #dbe5ef; border-radius:14px; background:#f4f8fc; color:#23446b; }
.blog-post-event-details strong { color:#0f5790; text-transform:uppercase; font-size:12px; letter-spacing:.08em; }
.blog-post-event-details span { font-size:14px; font-weight:700; }
.blog-post-event-details i { margin-right:5px; color:#a07822; }
.blog-event-people { margin:34px 0; }
.blog-event-people h2 { margin:0 0 15px; color:var(--ink); font-size:22px; }
.blog-event-people__grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; }
.blog-event-people__person { padding:17px 19px; border:1px solid #dbe5ef; border-radius:12px; background:#f7f9fc; }
.blog-event-people__person img { display:block; width:72px; height:72px; margin-bottom:12px; border-radius:50%; object-fit:cover; }
.blog-event-people__person small { display:block; margin-bottom:5px; color:#2272a8; font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
.blog-event-people__person strong { display:block; color:var(--ink); font-size:17px; }
.blog-event-people__person span { display:block; margin-top:3px; color:var(--ink-soft); font-size:13px; }
.blog-post-meta { display:flex; flex-wrap:wrap; align-items:center; gap:10px; margin:24px 0 8px; }
.blog-post-meta__item { display:inline-flex; align-items:center; gap:8px; padding:9px 12px; border:1px solid #e3e8f0; border-radius:999px; background:#f7f9fc; color:#30415f; font-size:13px; font-weight:700; letter-spacing:.02em; text-decoration:none; }
.blog-post-meta__item svg, .blog-post-author svg { width:17px; height:17px; flex:0 0 17px; stroke:currentColor; fill:none; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
a.blog-post-meta__item:hover { background:#eaf0f9; color:#173e79; }
.blog-post-author { display:flex; align-items:center; gap:8px; margin:10px 0 24px; color:#52617b; font-size:14px; font-weight:600; }
.blog-post-author a { color:inherit; text-decoration:underline; text-decoration-color:#aebbd0; text-underline-offset:3px; }
#comments-section, #comment-body { scroll-margin-top:110px; }
.blog-post-extras { margin-top:38px; }
.blog-post-extras__heading { margin:0 0 18px; color:var(--ink); font-size:23px; line-height:1.2; }
.blog-post-tags { display:flex; flex-wrap:wrap; gap:9px; }
.blog-post-tag { display:inline-flex; padding:7px 12px; border:1px solid var(--line); border-radius:999px; color:var(--forest-800); background:#fff; font-size:12px; font-weight:750; text-decoration:none; }
.blog-share { display:flex; flex-wrap:wrap; align-items:center; gap:9px; margin:30px 0 0; }
.blog-share__label { margin-right:4px; color:var(--ink-soft); font-size:13px; font-weight:750; }
.blog-share__link, .blog-share__copy { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:38px; padding:8px 13px; border:1px solid var(--line); border-radius:999px; color:var(--forest-800); background:#fff; font:inherit; font-size:12px; font-weight:750; text-decoration:none; cursor:pointer; }
.blog-share__link:hover, .blog-share__copy:hover { border-color:var(--forest-700); color:var(--forest-700); background:#f5f8fc; }
.blog-share__icon { width:17px; height:17px; flex:0 0 17px; fill:currentColor; }
.blog-share__icon--facebook { color:#1877f2; }
.blog-share__icon--x { color:#111; }
.blog-share__icon--whatsapp { color:#25d366; }
.blog-share__icon--copy { color:#60718a; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
.blog-comment-feedback { max-width:760px; margin:16px 0 0; padding:12px 15px; border-radius:9px; color:#17613b; background:#e8f6ee; font-size:14px; }
.blog-comment-feedback.is-error { color:#9c2938; background:#fff0f1; }
#blog-comment-submit:disabled { opacity:.65; cursor:wait; }
.blog-related-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
.blog-related-card { min-width:0; overflow:hidden; border:1px solid var(--line); border-radius:13px; background:#fff; text-decoration:none; }
.blog-related-card__image { display:block; aspect-ratio:1.7; overflow:hidden; background:#edf1f5; }
.blog-related-card__image img { width:100%; height:100%; object-fit:cover; }
.blog-related-card__copy { display:block; padding:14px 15px 16px; }
.blog-related-card__category { display:block; margin-bottom:7px; color:var(--forest-700); font-size:9px; font-weight:850; letter-spacing:.08em; text-transform:uppercase; }
.blog-related-card__title { display:-webkit-box; overflow:hidden; color:var(--ink); font-size:15px; font-weight:800; line-height:1.35; -webkit-box-orient:vertical; -webkit-line-clamp:3; }
.blog-related-card:hover .blog-related-card__title { color:var(--forest-700); }
.blog-related-card--text .blog-related-card__copy { padding-top:19px; }
.blog-comments-list { display:grid; gap:13px; margin:0 0 30px; }
.blog-comment { padding:17px 19px; border:1px solid var(--line); border-radius:12px; background:#fff; }
.blog-comment__byline { display:flex; flex-wrap:wrap; gap:6px 10px; margin-bottom:8px; color:var(--ink); font-size:13px; font-weight:800; }
.blog-comment__campus, .blog-comment__date { color:var(--ink-soft); font-size:11px; font-weight:600; }
.blog-comment__body { margin:0; color:var(--ink-soft); font-size:14px; line-height:1.7; white-space:pre-line; }
.blog-comments-empty { margin:0 0 28px; color:var(--ink-soft); font-size:14px; }
.blog-comment-form-heading { margin:34px 0 18px; font-size:19px; }
.blog-post-navigation { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin:0 auto 110px; }
.blog-post-navigation__link { position:relative; display:flex; min-height:138px; flex-direction:column; justify-content:space-between; gap:18px; overflow:hidden; padding:21px 23px; border:1px solid #dbe3ee; border-radius:16px; color:var(--ink); background:linear-gradient(145deg,#fff,#f5f8fc); box-shadow:0 8px 22px rgba(15,35,65,.045); text-decoration:none; transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease; }
.blog-post-navigation__link::before { position:absolute; inset:0 auto 0 0; width:3px; background:var(--gold-dark); content:""; }
.blog-post-navigation__link:hover { transform:translateY(-3px); border-color:#aebfd5; color:var(--forest-700); box-shadow:0 14px 30px rgba(15,35,65,.1); }
.blog-post-navigation__top { display:flex; align-items:center; justify-content:space-between; gap:12px; }
.blog-post-navigation__link--next .blog-post-navigation__top { flex-direction:row-reverse; }
.blog-post-navigation__link--next { text-align:right; }
.blog-post-navigation__label { color:var(--ink-soft); font-size:10px; font-weight:850; letter-spacing:.1em; text-transform:uppercase; }
.blog-post-navigation__arrow { display:grid; place-items:center; width:34px; height:34px; flex:0 0 34px; border:1px solid #dbe3ee; border-radius:50%; color:var(--forest-800); background:#fff; font-size:17px; transition:color .2s ease,background .2s ease; }
.blog-post-navigation__link:hover .blog-post-navigation__arrow { border-color:var(--forest-800); color:#fff; background:var(--forest-800); }
.blog-post-navigation__title { display:-webkit-box; overflow:hidden; font-size:16px; font-weight:800; line-height:1.38; -webkit-box-orient:vertical; -webkit-line-clamp:2; }
.blog-post-navigation__date { color:var(--ink-soft); font-size:12px; font-weight:700; }
@media (max-width:700px) { .blog-related-grid { grid-template-columns:1fr; } .blog-related-card { display:grid; grid-template-columns:110px minmax(0,1fr); align-items:center; } .blog-related-card__image { height:100%; min-height:95px; aspect-ratio:auto; } .blog-related-card__copy { padding:12px; } .blog-related-card__title { font-size:13px; } }
@media (max-width:520px) { .blog-post-navigation { grid-template-columns:1fr; gap:12px; margin-bottom:70px; } .blog-post-navigation__link { min-height:112px; padding:17px 19px; } }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
<link rel="canonical" href="{{ $post->canonical_url ?: $postUrl }}">
@if($isPreview ?? false)<meta name="robots" content="noindex,nofollow">@endif
<meta property="og:type" content="{{ $post->type === 'event' ? 'event' : 'article' }}"><meta property="og:title" content="{{ $post->seo_title ?: $post->title }}"><meta property="og:description" content="{{ $post->seo_description ?: $post->excerpt }}"><meta property="og:url" content="{{ $postUrl }}">@if($socialImageUrl)<meta property="og:image" content="{{ $socialImageUrl }}">@endif
<meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="{{ $post->seo_title ?: $post->title }}"><meta name="twitter:description" content="{{ $post->seo_description ?: $post->excerpt }}">@if($socialImageUrl)<meta name="twitter:image" content="{{ $socialImageUrl }}">@endif
@php
    $structuredPost = $post->type === 'event' && $post->event_starts_at
        ? ['@context' => 'https://schema.org', '@type' => 'Event', 'name' => $post->title, 'description' => $post->seo_description ?: $post->excerpt, 'startDate' => $post->event_starts_at->toIso8601String(), 'url' => $postUrl, 'location' => ['@type' => 'Place', 'name' => $post->event_venue ?: 'Campus Fellowship']]
        : ['@context' => 'https://schema.org', '@type' => $post->type === 'video' ? 'VideoObject' : 'Article', 'headline' => $post->title, 'description' => $post->seo_description ?: $post->excerpt, 'datePublished' => optional($post->published_at)->toIso8601String(), 'dateModified' => optional($post->updated_at)->toIso8601String(), 'url' => $postUrl, 'author' => ['@type' => 'Person', 'name' => $post->author?->name ?: $post->author?->email ?: 'GSF']];
    if ($post->type === 'event' && !empty($post->event_speakers)) {
        $structuredPost['performer'] = collect($post->event_speakers)->map(fn ($person) => ['@type' => 'Person', 'name' => $person['name']])->all();
    }
@endphp
<script type="application/ld+json">{!! json_encode($structuredPost, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
@endpush
@section('content')
<section class="inner-hero inner-hero--directory"><div class="inner-hero__pattern" aria-hidden="true"></div><div class="site-container inner-hero__grid"><div class="inner-hero__copy"><nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ $post->type === 'event' ? route('events.public.index') : route('blog.index') }}">{{ $post->type === 'event' ? 'Programs & Events' : 'News' }}</a><span>·</span><span>{{ $post->type === 'event' ? 'Event' : ($post->categories->pluck('name')->join(', ') ?: 'Story') }}</span></nav><div class="eyebrow eyebrow--light"><span></span> {{ $post->type === 'event' ? 'GSF Programs & Events' : ($post->categories->pluck('name')->join(', ') ?: 'GSF News') }}</div><h1>{{ $post->title }}</h1><p>{{ $post->excerpt }}</p></div></div></section>
<article class="content-section"><div class="site-container" style="max-width:900px">@if($isPreview ?? false)<div class="alert alert-info" role="status">Preview mode — this visit does not count toward post views.</div>@endif @if($canEditPost)<div style="display:flex;justify-content:flex-end;margin-bottom:20px"><a class="button button--blue" href="{{ route($postEditRoute, $post) }}">Edit post</a></div>@endif
@if($post->type === 'event' && $post->event_starts_at)
    <div class="blog-post-event-details"><strong>{{ $post->event_starts_at->isPast() ? 'Past event' : 'Upcoming event' }}</strong><span><i class="fa fa-calendar-o" aria-hidden="true"></i> {{ $post->event_starts_at->format('l, j F Y · g:i A') }}</span>@if($post->event_venue)<span><i class="fa fa-map-marker" aria-hidden="true"></i> {{ $post->event_venue }}</span>@endif</div>
@endif
<div class="blog-post-meta" aria-label="Post details">
    <span class="blog-post-meta__item"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>{{ optional($post->published_at)->format('d M Y') }}</span>
    <span class="blog-post-meta__item"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>{{ $post->reading_time }} min read</span>
    <span class="blog-post-meta__item"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>{{ number_format($post->view_count + ((($isPreview ?? false) || !($countsAsView ?? true)) ? 0 : 1)) }} views</span>
    @if($post->allow_comments)<a class="blog-post-meta__item blog-comments-link" href="#comment-body"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 9 9 0 0 1-4-.9L3 21l1.9-5.5a9 9 0 0 1-.9-4A8.5 8.5 0 0 1 12.5 3H13a8.5 8.5 0 0 1 8 8v.5Z"/></svg><span data-comment-count>{{ number_format($post->approved_comments_count ?? 0) }}</span> comments</a>@endif
</div>
@if($postAuthorName)<div class="blog-post-author"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><span>By: @if($postAuthorUrl)<a href="{{ $postAuthorUrl }}">{{ $postAuthorName }}</a>@else{{ $postAuthorName }}@endif @if($postAuthorAffiliation)- {{ $postAuthorAffiliation }}@endif</span></div>@endif
@if($featuredImageUrl)<img style="width:100%;max-height:520px;object-fit:cover;border-radius:18px;margin:28px 0 38px" src="{{ $featuredImageUrl }}" alt="{{ $post->title }}">@endif<div class="blog-article-body sun-editor-editable" style="font-size:17px;line-height:1.9;color:var(--ink-soft)">{!! \App\Modules\Blog\BlogMedia::htmlWithAvailableImages(str_replace('//storage/', '/storage/', $post->body)) !!}</div>
@if($post->type === 'event' && !empty($post->event_speakers))
    <section class="blog-event-people" aria-label="Speakers and guest ministers"><h2>Speakers &amp; guest ministers</h2><div class="blog-event-people__grid">@foreach($post->event_speakers as $person)<div class="blog-event-people__person">@if($speakerPhoto = \App\Modules\Blog\BlogMedia::url($person['image'] ?? null))<img src="{{ $speakerPhoto }}" alt="Photo of {{ $person['name'] }}" loading="lazy">@endif<small>{{ ($person['kind'] ?? 'speaker') === 'guest_minister' ? 'Guest minister' : 'Speaker' }}</small><strong>{{ $person['name'] }}</strong>@if(filled($person['ministry'] ?? null))<span>{{ $person['ministry'] }}</span>@endif</div>@endforeach</div></section>
@endif
<div class="blog-share" aria-label="Share this post">
    <span class="blog-share__label">Share this {{ $post->type === 'event' ? 'event' : 'story' }}</span>
    <a class="blog-share__link" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($postUrl) }}" target="_blank" rel="noopener noreferrer"><svg class="blog-share__icon blog-share__icon--facebook" viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 21v-8.2h2.8l.4-3.2h-3.2v-2c0-.9.3-1.6 1.6-1.6H17V3.1c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.3H7.5v3.2h2.8V21h3.2Z"/></svg>Facebook</a>
    <a class="blog-share__link" href="https://twitter.com/intent/tweet?url={{ urlencode($postUrl) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener noreferrer"><svg class="blog-share__icon blog-share__icon--x" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.9 2H22l-6.8 7.8L23.2 22h-6.3L12 14.6 5.6 22H2.4l7.3-8.4L1.9 2h6.5l4.5 6.8L18.9 2Zm-1.1 18h1.7L7.4 3.9H5.6L17.8 20Z"/></svg>X</a>
    <a class="blog-share__link" href="https://wa.me/?text={{ urlencode($post->title.' '.$postUrl) }}" target="_blank" rel="noopener noreferrer"><svg class="blog-share__icon blog-share__icon--whatsapp" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.5 0 .1 5.4.1 12c0 2.1.5 4.1 1.6 5.9L0 24l6.3-1.7a12 12 0 0 0 5.8 1.5h.1c6.6 0 12-5.4 12-12 0-3.2-1.3-6.1-3.7-8.3Zm-8.4 18.3h-.1c-1.8 0-3.5-.5-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.8 9.8 0 0 1 2.2 12c0-5.4 4.4-9.8 9.9-9.8 2.6 0 5.1 1 7 2.9a9.7 9.7 0 0 1 2.9 7c0 5.4-4.4 9.7-9.9 9.7Zm5.4-7.3c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.2-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.4-2.3-1.4-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6l.5-.6.3-.5c.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.9 4.3.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 1.9-1.3.2-.7.2-1.2.2-1.3-.1-.2-.3-.3-.5-.4Z"/></svg>WhatsApp</a>
    <button class="blog-share__copy" type="button" id="blog-copy-link" data-url="{{ $postUrl }}"><svg class="blog-share__icon blog-share__icon--copy" viewBox="0 0 24 24" aria-hidden="true"><rect x="8" y="8" width="12" height="13" rx="2"/><path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3"/></svg>Copy link</button>
    <span id="blog-copy-feedback" class="sr-only" aria-live="polite"></span>
</div>
@if($post->tags->isNotEmpty())
<section class="blog-post-extras" aria-labelledby="blog-tags-heading">
    <h2 class="blog-post-extras__heading" id="blog-tags-heading">Topics</h2>
    <div class="blog-post-tags">@foreach($post->tags as $tag)<span class="blog-post-tag">#{{ $tag->name }}</span>@endforeach</div>
</section>
@endif
@if($relatedPosts->isNotEmpty())
<section class="blog-post-extras" aria-labelledby="blog-related-heading">
    <h2 class="blog-post-extras__heading" id="blog-related-heading">Related stories</h2>
    <div class="blog-related-grid">
        @foreach($relatedPosts as $relatedPost)
            @php($relatedImageUrl = \App\Modules\Blog\BlogMedia::url($relatedPost->featured_image))
            <a class="blog-related-card {{ $relatedImageUrl ? '' : 'blog-related-card--text' }}" href="{{ route($post->type === 'event' ? 'events.public.show' : 'blog.show', $relatedPost->slug) }}">
                @if($relatedImageUrl)<span class="blog-related-card__image"><img src="{{ $relatedImageUrl }}" alt="" loading="lazy"></span>@endif
                <span class="blog-related-card__copy"><span class="blog-related-card__category">{{ $relatedPost->categories->pluck('name')->join(', ') ?: 'GSF News' }}</span><span class="blog-related-card__title">{{ $relatedPost->title }}</span></span>
            </a>
        @endforeach
    </div>
</section>
@endif
@include('modules.blog.public.comments')</div></article>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var copyButton = document.getElementById('blog-copy-link');
    if (!copyButton) return;
    copyButton.addEventListener('click', async function () {
        var feedback = document.getElementById('blog-copy-feedback');
        try {
            await navigator.clipboard.writeText(copyButton.dataset.url);
            feedback.textContent = 'Link copied.';
            copyButton.textContent = 'Link copied';
        } catch (error) {
            feedback.textContent = 'Copy unavailable. Use your browser address bar to copy the link.';
        }
    });
});
</script>
@endpush
@if(!($isPreview ?? false))
<nav class="site-container blog-post-navigation" aria-label="{{ $post->type === 'event' ? 'Navigate between events' : 'Navigate between posts' }}" style="max-width:900px">
    @if($previousPost)<a class="blog-post-navigation__link" href="{{ route($post->type === 'event' ? 'events.public.show' : 'blog.show', $previousPost->slug) }}"><span class="blog-post-navigation__top"><span class="blog-post-navigation__label">{{ $post->type === 'event' ? 'Past events' : 'Previous story' }}</span><span class="blog-post-navigation__arrow" aria-hidden="true">←</span></span><span class="blog-post-navigation__title">{{ $previousPost->title }}</span>@if($post->type === 'event')<span class="blog-post-navigation__date">{{ $previousPost->event_starts_at->format('j M Y') }}</span>@endif</a>@else<span></span>@endif
    @if($nextPost)<a class="blog-post-navigation__link blog-post-navigation__link--next" href="{{ route($post->type === 'event' ? 'events.public.show' : 'blog.show', $nextPost->slug) }}"><span class="blog-post-navigation__top"><span class="blog-post-navigation__label">{{ $post->type === 'event' ? 'Upcoming event' : 'Next story' }}</span><span class="blog-post-navigation__arrow" aria-hidden="true">→</span></span><span class="blog-post-navigation__title">{{ $nextPost->title }}</span>@if($post->type === 'event')<span class="blog-post-navigation__date">{{ $nextPost->event_starts_at->format('j M Y') }}</span>@endif</a>@endif
</nav>
@endif
@endsection
