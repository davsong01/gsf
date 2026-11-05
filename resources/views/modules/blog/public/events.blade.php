@extends('frontend.site.layout')
@section('title', ($campus ? $campus->name.' — ' : '').'Programs & Events — GSF')
@section('activeSection', 'events')
@section('content')
<section class="inner-hero inner-hero--directory"><div class="inner-hero__pattern" aria-hidden="true"></div><div class="site-container inner-hero__grid"><div class="inner-hero__copy"><nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>Programs &amp; Events</span></nav><div class="eyebrow eyebrow--light"><span></span> Gather With Us</div><h1>Programs &amp; Events</h1><p>{{ $campus ? 'Gatherings at '.$campus->name.'.' : 'Explore upcoming gatherings and moments shared across GSF campuses.' }}</p></div></div></section>
<section class="content-section"><div class="site-container">
    @if($campus)<p><a href="{{ route('campus.single', $campus->slug) }}">← Back to {{ $campus->name }}</a></p>@endif
    @if($events->isNotEmpty())
        <div class="public-event-grid">
            @foreach($events as $event)
                @php($eventImage = \App\Modules\Blog\BlogMedia::url($event->featured_image))
                <article class="public-event-card"><a href="{{ route('events.public.show', $event->slug) }}">
                    @if($eventImage)<img src="{{ $eventImage }}" alt="" loading="lazy">@endif
                    <div class="public-event-card__body"><small>{{ $event->is_featured ? 'Featured · ' : '' }}{{ $event->event_starts_at->isPast() ? 'Past event' : 'Upcoming event' }}</small><h2>{{ $event->title }}</h2><p><i class="fa fa-calendar-o" aria-hidden="true"></i> {{ $event->event_starts_at->format('l, j F Y · g:i A') }}</p><p><i class="fa fa-map-marker" aria-hidden="true"></i> {{ $event->event_venue ?: 'Campus Fellowship' }}</p></div>
                </a></article>
            @endforeach
        </div>
        {{ $events->links() }}
    @else
        <div class="empty-state"><strong>No programs or events are listed yet.</strong><p>Check back as campus fellowships share their plans.</p></div>
    @endif
</div></section>
@endsection
@push('head')
<style>
.public-event-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:22px; margin:26px 0 40px; }
.public-event-card { overflow:hidden; border:1px solid #dce4ec; border-radius:17px; background:#fff; box-shadow:0 12px 30px rgba(18,40,73,.06); }
.public-event-card a { display:block; height:100%; color:inherit; text-decoration:none; }
.public-event-card img { display:block; width:100%; height:190px; object-fit:cover; }
.public-event-card__body { padding:20px; }
.public-event-card small { color:#1378a7; font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
.public-event-card h2 { margin:8px 0 16px; color:#17213d; font-size:21px; line-height:1.25; }
.public-event-card p { margin:7px 0; color:#5f6c84; font-size:14px; }
.public-event-card i { width:18px; color:#a97f2c; }
.public-event-card:hover { border-color:#8eacc6; transform:translateY(-2px); }
</style>
@endpush
