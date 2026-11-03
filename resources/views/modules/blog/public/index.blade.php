@extends('frontend.site.layout')
@section('title', 'GSF News — GOFAMINT Students’ Fellowship')
@section('mainClass', 'public-page')
@section('activeSection', 'news')
@section('content')
<section class="inner-hero inner-hero--directory"><div class="inner-hero__pattern" aria-hidden="true"></div><div class="site-container inner-hero__grid"><div class="inner-hero__copy"><nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>News</span></nav><div class="eyebrow eyebrow--light"><span></span> GSF News Network</div><h1>Stories of <em>faith, leadership and service.</em></h1><p>News, teaching, announcements and stories from the GSF community across Nigeria.</p></div></div></section>
<section class="content-section"><div class="site-container"><div class="directory-entry-heading"><div><div class="eyebrow"><span></span> Latest stories</div><h2>From across the Fellowship.</h2></div><a class="button button--blue" href="{{ route('blog.rss') }}">RSS feed <span aria-hidden="true">↗</span></a></div><div class="directory-featured__grid">
@forelse($posts as $post)
<article class="news-card"><a href="{{ route('blog.show', $post->slug) }}" class="news-card__image">@if($post->featured_image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->featured_image) }}" alt="">@endif</a><div class="news-card__copy"><div class="news-card__meta"><span>{{ optional($post->published_at)->format('d M Y') }}</span><span>{{ $post->category?->name ?: 'GSF News' }}</span></div><h3><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3><p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 150) }}</p><a class="text-link" href="{{ route('blog.show', $post->slug) }}">Read story <span aria-hidden="true">→</span></a></div></article>
@empty
<div class="empty-state"><strong>No published stories yet.</strong><p>Check back soon for the latest GSF news.</p></div>
@endforelse
</div><div class="directory-pagination">{{ $posts->links() }}</div></div></section>
@endsection
