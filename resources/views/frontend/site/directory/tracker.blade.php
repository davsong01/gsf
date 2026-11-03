@extends('frontend.site.layout')

@section('title', "GSF Campus Tracker — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page directory-page')
@section('activeSection', 'directory')

@section('content')
<section class="inner-hero inner-hero--directory">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route('directory.index') }}">Directory</a><span>·</span><span>Campus Tracker</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> Campus Tracker</div>
            <h1>Follow the GSF network <em>campus by campus.</em></h1>
            <p>Search the national campus list and open a fellowship’s minisite for its local information, community and upcoming events.</p>
        </div>
    </div>
</section>

<section class="directory-listing">
    <div class="site-container">
        <div class="directory-listing__toolbar">
            <div><div class="eyebrow"><span></span> {{ number_format($chapters->count()) }} Campus Locations</div><h2>Choose a campus</h2><p>Search by institution, field or zone.</p></div>
            <label class="directory-search directory-search--tracker" for="tracker-search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4 4"/></svg>
                <input id="tracker-search" type="search" placeholder="Search campus network" autocomplete="off" data-campus-tracker-search>
            </label>
        </div>

        <div class="campus-directory-grid" data-campus-tracker-grid>
            @foreach($chapters as $campus)
                <article class="campus-directory-card" data-tracker-campus data-search="{{ \Illuminate\Support\Str::lower($campus->name.' '.($campus->field?->name ?? '').' '.($campus->zone?->name ?? '')) }}">
                    <a class="campus-directory-card__image" href="{{ route('campus.single', $campus->id) }}" aria-label="View {{ $campus->name }}">
                        <img src="{{ asset($campus->banner ?: 'gsfcom/images/image-office.jpg') }}" alt="" loading="lazy">
                        <span>{{ number_format($campus->members_count) }} {{ \Illuminate\Support\Str::plural('member', $campus->members_count) }}</span>
                    </a>
                    <div class="campus-directory-card__body">
                        <small>{{ $campus->field?->name ?: 'GSF Campus' }}</small>
                        <h2><a href="{{ route('campus.single', $campus->id) }}">{{ $campus->name }}</a></h2>
                        <p><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-4.35 7-11A7 7 0 1 0 5 10c0 6.65 7 11 7 11Z"/><circle cx="12" cy="10" r="2.4"/></svg>{{ $campus->zone?->name ?: 'Nigeria' }}</p>
                        <a class="text-link" href="{{ route('campus.single', $campus->id) }}">Open campus minisite <span aria-hidden="true">→</span></a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="empty-state mt-4" data-tracker-empty hidden><strong>No campus matched your search.</strong><p>Try another institution, field or zone.</p></div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.querySelector('[data-campus-tracker-search]');
    var cards = Array.prototype.slice.call(document.querySelectorAll('[data-tracker-campus]'));
    var empty = document.querySelector('[data-tracker-empty]');

    if (!input) return;

    input.addEventListener('input', function () {
        var term = input.value.trim().toLowerCase();
        var visible = 0;

        cards.forEach(function (card) {
            var matches = card.getAttribute('data-search').indexOf(term) !== -1;
            card.hidden = !matches;
            if (matches) visible += 1;
        });

        if (empty) empty.hidden = visible !== 0;
    });
});
</script>
@endpush
