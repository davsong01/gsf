@extends('frontend.site.layout')
@section('title', 'GSF News — GOFAMINT Students’ Fellowship')
@section('description', 'Latest news, stories and updates from GOFAMINT Students’ Fellowship campuses across Nigeria.')
@section('mainClass', 'public-page newsroom-page')
@section('activeSection', 'news')
@section('content')
<section class="inner-hero inner-hero--directory">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>News</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> GSF News Network</div>
            <h1>Stories of <em>faith, leadership and service.</em></h1>
            <p>News, teaching and stories from GSF chapters, zones and fields across Nigeria.</p>
        </div>
    </div>
</section>

<section class="newsroom-section">
    <div class="site-container">
        <div class="newsroom-toolbar">
            <div class="newsroom-categories" aria-label="Filter stories by category">
                <a class="newsroom-chip {{ $categorySlug === '' ? 'is-active' : '' }}" href="{{ route('blog.index', array_filter(['search' => $searchTerm])) }}">All stories</a>
                @foreach($categories as $category)
                    @if($category->posts_count)
                        <a class="newsroom-chip {{ $categorySlug === $category->slug ? 'is-active' : '' }}" href="{{ route('blog.index', array_filter(['category' => $category->slug, 'search' => $searchTerm])) }}">{{ $category->name }}</a>
                    @endif
                @endforeach
            </div>
            <form class="newsroom-search" method="GET" action="{{ route('blog.index') }}" role="search">
                @if($categorySlug)<input type="hidden" name="category" value="{{ $categorySlug }}">@endif
                <label class="sr-only" for="newsroom-search-input">Search GSF news</label>
                <input id="newsroom-search-input" type="search" name="search" value="{{ $searchTerm }}" placeholder="Search stories…">
                <button type="submit" aria-label="Search stories"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg></button>
            </form>
        </div>

        @if($featuredPost)
            @php($featuredImageUrl = \App\Modules\Blog\BlogMedia::url($featuredPost->featured_image))
            <section class="newsroom-lead {{ $featuredImageUrl ? '' : 'newsroom-lead--text' }}" aria-label="Featured story">
                @if($featuredImageUrl)
                    <a class="newsroom-lead__image" href="{{ route('blog.show', $featuredPost->slug) }}"><img src="{{ $featuredImageUrl }}" alt=""></a>
                @endif
                <div class="newsroom-lead__copy">
                    <div class="newsroom-label"><span></span> Featured story</div>
                    <div class="newsroom-meta"><span>{{ $featuredPost->categories->pluck('name')->join(', ') ?: 'GSF News' }}</span><span>{{ optional($featuredPost->published_at)->format('d M Y') }}</span></div>
                    <h2><a href="{{ route('blog.show', $featuredPost->slug) }}">{{ $featuredPost->title }}</a></h2>
                    <p>{{ $featuredPost->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($featuredPost->body), 250) }}</p>
                    <a class="newsroom-read-link" href="{{ route('blog.show', $featuredPost->slug) }}">Read the story <span aria-hidden="true">↗</span></a>
                </div>
            </section>
        @endif

        <div class="newsroom-content-layout">
            <div class="newsroom-content-main">
                <div class="newsroom-section-heading">
                    <div><div class="eyebrow"><span></span> Across the Fellowship</div><h2>{{ $searchTerm ? 'Search results' : ($categorySlug ? 'Stories in this category' : 'Latest stories') }}</h2></div>
                    <a href="{{ route('blog.rss') }}" class="newsroom-rss">RSS feed <span aria-hidden="true">↗</span></a>
                </div>

                <div class="newsroom-grid">
                    @forelse($listingPosts as $post)
                        @php($imageUrl = \App\Modules\Blog\BlogMedia::url($post->featured_image))
                        <article class="newsroom-card {{ $imageUrl ? '' : 'newsroom-card--text' }}">
                            @if($imageUrl)<a class="newsroom-card__image" href="{{ route('blog.show', $post->slug) }}"><img src="{{ $imageUrl }}" alt="" loading="lazy"></a>@endif
                            <div class="newsroom-card__content">
                                <div class="newsroom-meta"><span>{{ $post->categories->pluck('name')->join(', ') ?: 'GSF News' }}</span><span>{{ optional($post->published_at)->format('d M Y') }}</span></div>
                                <h3><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                                <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 145) }}</p>
                                <a class="newsroom-read-link" href="{{ route('blog.show', $post->slug) }}">Read story <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    @empty
                        <div class="newsroom-empty"><strong>No stories found.</strong><p>Try another search or choose a different category.</p><a href="{{ route('blog.index') }}">Browse all stories</a></div>
                    @endforelse
                </div>

                @if($posts->hasPages())<div class="directory-pagination">{{ $posts->links() }}</div>@endif
            </div>

            <aside class="newsroom-most-read" aria-labelledby="newsroom-most-read-title">
                <h2 id="newsroom-most-read-title">Most read</h2>
                <div class="newsroom-most-read__list">
                    @forelse($mostRead as $popularPost)
                        @php($popularImageUrl = \App\Modules\Blog\BlogMedia::url($popularPost->featured_image))
                        <a class="newsroom-popular {{ $popularImageUrl ? '' : 'newsroom-popular--text' }}" href="{{ route('blog.show', $popularPost->slug) }}">
                            <span class="newsroom-popular__copy">
                                <span class="newsroom-popular__category">{{ $popularPost->categories->pluck('name')->join(', ') ?: 'GSF News' }}</span>
                                <span class="newsroom-popular__title">{{ $popularPost->title }}</span>
                                <span class="newsroom-popular__meta">{{ optional($popularPost->published_at)->format('d M') }} <i aria-hidden="true">·</i> {{ number_format($popularPost->view_count) }} {{ \Illuminate\Support\Str::plural('view', $popularPost->view_count) }}</span>
                            </span>
                            @if($popularImageUrl)<span class="newsroom-popular__image"><img src="{{ $popularImageUrl }}" alt="" loading="lazy"></span>@endif
                        </a>
                    @empty
                        <p class="newsroom-most-read__empty">Most read stories will appear here as readers explore GSF news.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
