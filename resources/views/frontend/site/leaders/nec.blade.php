@extends('frontend.site.layout')

@section('title', "National Executive Committee — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page leadership-page')
@section('activeSection', 'leaders')

@section('content')
<section class="inner-hero inner-hero--leaders">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route('leaders.fathers') }}">Meet Our Leaders</a><span>·</span><span>NEC</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> National Executive Committee</div>
            <h1>National leaders serving with <em>clarity, accountability and care.</em></h1>
            <p>The National Executive Committee provides coordinated leadership for the Fellowship’s work, programs and campus network.</p>
        </div>
    </div>
</section>

<section class="nec-directory">
    <div class="site-container">
        <div class="nec-directory__toolbar">
            <div>
                <div class="eyebrow"><span></span> National Secretariat</div>
                <h2>Meet the current committee</h2>
                <p><span data-nec-result-count>{{ $nec->count() }}</span> active leadership {{ \Illuminate\Support\Str::plural('profile', $nec->count()) }}</p>
            </div>
            <label class="nec-search" for="nec-search">
                <span aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4 4"/></svg></span>
                <span class="sr-only">Search committee members</span>
                <input id="nec-search" type="search" placeholder="Search by name or office" autocomplete="off" data-nec-search>
            </label>
        </div>

        <div class="nec-grid" data-nec-grid>
            @foreach($nec as $stakeholder)
                @php
                    $displayName = $stakeholder?->name ?? 'Position currently vacant';
                    $roleFallbacks = [
                        1 => 'National Secretariat',
                        2 => 'National Campus Pastor',
                        6 => 'National Portfolio Officer',
                        7 => 'NEC Member',
                        8 => 'National President',
                    ];
                    $office = $stakeholder?->designation?->name
                        ?: ($roleFallbacks[$stakeholder?->role_id] ?? 'National Executive Committee');

                    $tenure = $stakeholder?->designation?->tenure ?? null;
                    $avatar = $stakeholder?->avatar;
                    $initials = avatarInitials($displayName);
                @endphp
                <article class="nec-profile" data-nec-card data-search="{{ \Illuminate\Support\Str::lower($displayName.' '.$office.' '.$tenure) }}">
                    <div class="nec-profile__portrait">
                        @if(filled($avatar))
                            <img src="{{ asset($avatar) }}" alt="{{ $displayName }}">
                        @else
                            <span aria-hidden="true">{{ $initials }}</span>
                        @endif
                        {{-- <small>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</small> --}}
                    </div>
                    <div class="nec-profile__copy">
                        <h2>{{ $displayName }}</h2>
                        <p>{{ $office }}</p>
                        @if(filled($tenure))<span>{{ $tenure }}</span>@endif
                        {{-- @if(filled($stakeholder?->email))
                            <a href="mailto:{{ $stakeholder->email }}" aria-label="Email {{ $displayName }}">Contact <span aria-hidden="true">↗</span></a>
                        @endif --}}
                    </div>
                </article>
            @endforeach
        </div>

        <div class="nec-no-results" data-nec-empty hidden>
            <strong>No committee member matched your search.</strong>
            <p>Try a different name or office.</p>
        </div>
    </div>
</section>

<section class="page-next page-next--leaders">
    <div class="site-container page-next__card">
        <div><span>Leadership heritage</span><h2>Meet the fathers providing spiritual covering and direction to the Fellowship.</h2></div>
        <a class="button button--blue" href="{{ route('leaders.fathers') }}">View Our Fathers <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var search = document.querySelector('[data-nec-search]');
    var cards = Array.prototype.slice.call(document.querySelectorAll('[data-nec-card]'));
    var count = document.querySelector('[data-nec-result-count]');
    var empty = document.querySelector('[data-nec-empty]');

    if (!search) return;

    search.addEventListener('input', function () {
        var term = search.value.trim().toLowerCase();
        var visible = 0;

        cards.forEach(function (card) {
            var matches = card.getAttribute('data-search').indexOf(term) !== -1;
            card.hidden = !matches;
            if (matches) visible += 1;
        });

        if (count) count.textContent = visible;
        if (empty) empty.hidden = visible !== 0;
    });
});
</script>
@endpush
