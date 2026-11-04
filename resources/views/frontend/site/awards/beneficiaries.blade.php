@extends('frontend.site.layout')

@section('title', $title . ' | GOFAMINT Students’ Fellowship')
@section('description', 'Year-by-year applications, approval status and applicant coverage for ' . $title . '.')
@section('mainClass', 'public-award-page')
@section('activeSection', 'awards')

@push('head')
<style>
    .public-award-page { --award-ink:#172b43; --award-muted:#6e7d90; --award-line:#e4eaf0; --award-gold:#c38d38; background:#f5f7f9; }
    .public-award-hero { position:relative; overflow:hidden; padding:72px 0 64px; color:#fff; background:radial-gradient(circle at 88% 8%,rgba(230,187,105,.2),transparent 26%),linear-gradient(120deg,#102944,#1c5368 62%,#286d6a); }
    .public-award-hero::after { position:absolute; width:360px; height:360px; right:-90px; top:-200px; border:1px solid rgba(255,255,255,.2); border-radius:50%; box-shadow:0 0 0 42px rgba(255,255,255,.035),0 0 0 86px rgba(255,255,255,.025); content:""; }
    .public-award-hero__grid { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); align-items:center; gap:48px; }
    .public-award-hero__copy { position:relative; z-index:1; min-width:0; max-width:850px; }
    .public-award-moments { position:relative; z-index:1; min-width:0; margin:0; overflow:hidden; aspect-ratio:1.45; border:1px solid rgba(255,255,255,.22); border-radius:26px; background:#102944; box-shadow:0 22px 65px rgba(4,22,36,.3); }
    .public-award-moments__group { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:42% center; }
    .public-award-moments__portrait { position:absolute; right:0; top:0; width:43%; height:100%; object-fit:cover; object-position:54% 40%; -webkit-mask-image:linear-gradient(to right,transparent,#000 30%); mask-image:linear-gradient(to right,transparent,#000 30%); }
    .public-award-moments::after { position:absolute; inset:0; pointer-events:none; background:linear-gradient(180deg,rgba(16,41,68,.04) 42%,rgba(8,27,45,.85)); content:""; }
    .public-award-moments figcaption { position:absolute; right:22px; bottom:22px; left:22px; z-index:1; color:#fff; font-size:15px; font-weight:750; line-height:1.5; }
    .public-award-moments figcaption span { display:block; margin-bottom:5px; color:#f1ce8c; font-size:10px; font-weight:850; letter-spacing:.15em; text-transform:uppercase; }
    @media(max-width:900px) { .public-award-hero__grid { grid-template-columns:1fr; gap:30px; } .public-award-moments { width:100%; max-width:680px; aspect-ratio:1.7; } }
    @media(max-width:450px) { .public-award-moments { aspect-ratio:1.25; border-radius:18px; } .public-award-moments figcaption { right:16px; bottom:16px; left:16px; font-size:13px; } }
    .public-award-kicker { color:#f1ce8c; font-size:11px; font-weight:850; letter-spacing:.16em; text-transform:uppercase; }
    .public-award-hero h1 { margin:15px 0 12px; color:#fff; font-size:clamp(34px,5vw,58px); font-weight:800; letter-spacing:-.045em; line-height:1.08; }
    .public-award-hero p { max-width:690px; margin:0; color:rgba(255,255,255,.78); font-size:16px; line-height:1.75; }
    .public-award-years { display:inline-flex; align-items:center; gap:9px; margin-top:23px; padding:9px 14px; border:1px solid rgba(255,255,255,.23); border-radius:999px; color:#fff; background:rgba(255,255,255,.09); font-size:13px; font-weight:700; }
    .public-award-content { padding:38px 0 72px; }
    .public-award-stat-grid { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:14px; margin-bottom:26px; }
    .public-award-stat { min-height:134px; padding:18px; border:1px solid rgba(255,255,255,.85); border-radius:17px; background:linear-gradient(145deg,#fff,#f9fbfd); box-shadow:0 11px 28px rgba(31,55,79,.065); }
    .public-award-stat__label { display:flex; align-items:center; justify-content:space-between; gap:8px; color:var(--award-muted); font-size:11px; font-weight:800; letter-spacing:.07em; text-transform:uppercase; }
    .public-award-stat__icon { display:grid; width:35px; height:35px; flex:0 0 35px; place-items:center; border-radius:11px; color:var(--accent,#426f91); background:var(--soft,#e8f0f6); }
    .public-award-stat strong { display:block; margin-top:15px; color:var(--award-ink); font-size:32px; font-weight:850; line-height:1; }
    .public-award-browser { padding:20px; border:1px solid var(--award-line); border-radius:20px; background:#fff; box-shadow:0 15px 38px rgba(31,55,79,.06); }
    .public-award-browser__toolbar { display:flex; align-items:center; justify-content:space-between; gap:18px; margin-bottom:17px; }
    .public-award-browser__copy h2 { margin:0; color:var(--award-ink); font-size:19px; font-weight:850; }
    .public-award-browser__copy p { margin:3px 0 0; color:var(--award-muted); font-size:12px; }
    .public-award-search { position:relative; width:min(100%,390px); }
    .public-award-search i { position:absolute; top:50%; left:15px; color:#8391a2; transform:translateY(-50%); }
    .public-award-search input { width:100%; height:45px; padding:0 42px; border:1px solid #dce4ec; border-radius:13px; outline:none; color:var(--award-ink); background:#fbfcfd; font-size:14px; transition:border-color .18s ease,box-shadow .18s ease; }
    .public-award-search input:focus { border-color:#397596; box-shadow:0 0 0 3px rgba(57,117,150,.12); }
    .public-award-search__clear { position:absolute; top:50%; right:8px; display:none; width:30px; height:30px; padding:0; border:0; border-radius:9px; color:#617184; background:transparent; transform:translateY(-50%); cursor:pointer; }
    .public-award-search__clear:hover { background:#eef3f7; }
    .public-award-year-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:10px; }
    .public-award-year-card { display:block; min-width:0; padding:13px 14px; border:1px solid #e0e7ee; border-radius:14px; color:inherit; background:linear-gradient(145deg,#fff,#f8fafc); text-align:left; cursor:pointer; transition:border-color .18s ease,transform .18s ease,box-shadow .18s ease; }
    .public-award-year-card[hidden] { display:none; }
    .public-award-year-card:hover { transform:translateY(-2px); border-color:#b8c9d6; box-shadow:0 8px 18px rgba(31,55,79,.08); }
    .public-award-year-card.is-active { border-color:#2d6c8c; background:#eff7fa; box-shadow:0 0 0 2px rgba(45,108,140,.1); }
    .public-award-year-card__top { display:flex; align-items:center; justify-content:space-between; gap:8px; }
    .public-award-year-card__year { color:var(--award-ink); font-size:20px; font-weight:850; line-height:1; }
    .public-award-year-card__arrow { color:#7b8c9d; font-size:11px; }
    .public-award-year-card__counts { display:flex; align-items:center; gap:8px; margin-top:9px; color:var(--award-muted); font-size:11px; }
    .public-award-year-card__awarded { color:#176c50; font-weight:800; }
    .public-award-search-empty { display:none; padding:20px; border:1px dashed #d6dee7; border-radius:14px; color:var(--award-muted); background:#fafbfd; text-align:center; }
    .public-award-loading { padding:32px; color:var(--award-muted); text-align:center; }
    .public-award-results-empty { display:none; padding:20px; border:1px dashed #d6dee7; border-radius:14px; color:var(--award-muted); background:#fafbfd; text-align:center; }
    .public-award-year-panel { margin-top:16px; padding:20px; border:1px solid var(--award-line); border-radius:20px; background:#fff; box-shadow:0 15px 38px rgba(31,55,79,.05); }
    .public-award-year-panel[hidden] { display:none; }
    .public-award-year-panel__header { display:flex; align-items:flex-start; justify-content:space-between; gap:14px; margin-bottom:18px; padding-bottom:15px; border-bottom:1px solid var(--award-line); }
    .public-award-year-panel__header h2 { margin:0; color:var(--award-ink); font-size:22px; font-weight:850; }
    .public-award-year-panel__header p { margin:4px 0 0; color:var(--award-muted); font-size:12px; }
    .public-award-year-metrics { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:7px; }
    .public-award-year-metric { padding:6px 9px; border-radius:9px; color:#536478; background:#eff3f7; font-size:10px; font-weight:800; white-space:nowrap; }
    .public-award-beneficiary-heading { display:flex; align-items:baseline; justify-content:space-between; gap:12px; margin:0 0 13px; }
    .public-award-beneficiary-heading h3 { margin:0; color:var(--award-ink); font-size:17px; font-weight:850; }
    .public-award-beneficiary-heading span { color:var(--award-muted); font-size:12px; }
    .public-award-beneficiary-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:13px; }
    .public-award-beneficiary { display:flex; min-width:0; gap:12px; padding:16px; border:1px solid var(--award-line); border-radius:16px; background:linear-gradient(145deg,#fff,#fbfcfe); transition:transform .18s ease,box-shadow .18s ease; }
    .public-award-beneficiary[hidden] { display:none !important; }
    .public-award-beneficiary:hover { transform:translateY(-2px); box-shadow:0 12px 25px rgba(31,55,79,.08); }
    .public-award-beneficiary__initials { display:grid; width:43px; height:43px; flex:0 0 43px; place-items:center; border:1px solid #f1ddb4; border-radius:14px; color:#765616; background:linear-gradient(145deg,#fff9ec,#f4e4c3); font-size:13px; font-weight:850; }
    .public-award-beneficiary__info { min-width:0; }
    .public-award-beneficiary__name { display:block; overflow:hidden; color:var(--award-ink); font-size:14px; font-weight:850; text-overflow:ellipsis; white-space:nowrap; }
    .public-award-beneficiary__location { margin:4px 0 8px; color:var(--award-muted); font-size:12px; line-height:1.45; }
    .public-award-chip { display:inline-flex; align-items:center; margin:0 4px 4px 0; padding:5px 8px; border-radius:999px; color:#536478; background:#eff3f7; font-size:10px; font-weight:750; }
    .public-award-chip--winner { color:#176c50; background:#e4f5ed; }
    .public-award-recognition { display:flex; flex-wrap:wrap; align-items:center; gap:7px; margin:0 0 8px; }
    .public-award-approved-badge { display:inline-flex; align-items:center; gap:6px; margin:0; padding:6px 9px; border:1px solid #b9e5d2; border-radius:9px; color:#145e45; background:linear-gradient(135deg,#e4f7ed,#f3fbf6); font-size:11px; font-weight:850; }
    .public-award-cgpa { display:inline-flex; align-items:baseline; gap:5px; max-width:100%; padding:6px 9px; border:1px solid #ead7a9; border-radius:9px; color:#725314; background:linear-gradient(135deg,#fff9e9,#f8edcf); }
    .public-award-cgpa__label { font-size:9px; font-weight:850; letter-spacing:.08em; text-transform:uppercase; }
    .public-award-cgpa__value { color:#26364b; font-size:14px; font-weight:900; line-height:1; }
    .public-award-course { margin:10px 0 0; padding-top:9px; border-top:1px solid #edf1f4; color:#536478; font-size:12px; line-height:1.5; }
    .public-award-empty { padding:22px; border:1px dashed #d6dee7; border-radius:15px; color:var(--award-muted); background:#fafbfd; text-align:center; }
    .public-award-note { margin:15px 2px 0; color:#7b8795; font-size:12px; }
    @media(max-width:1050px) { .public-award-stat-grid { grid-template-columns:repeat(3,minmax(0,1fr)); } .public-award-beneficiary-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:650px) { .public-award-hero { padding:53px 0 46px; } .public-award-content { padding:25px 0 48px; } .public-award-stat-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:9px; } .public-award-stat { min-height:112px; padding:13px; } .public-award-stat strong { font-size:27px; } .public-award-browser { padding:14px; } .public-award-browser__toolbar { align-items:stretch; flex-direction:column; gap:12px; } .public-award-search { width:100%; } .public-award-year-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; } .public-award-year-card { padding:11px 12px; } .public-award-year-panel { padding:15px; } .public-award-year-panel__header { flex-direction:column; } .public-award-year-metrics { justify-content:flex-start; } .public-award-beneficiary-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<section class="public-award-hero">
    <div class="site-container public-award-hero__grid">
        <div class="public-award-hero__copy">
            <span class="public-award-kicker">Education Awards</span>
            <h1>{{ $title }}</h1>
            <p>Browse award applications by year and see each applicant’s campus, zone, field and approval status.</p>
            <span class="public-award-years"><i class="fa fa-calendar" aria-hidden="true"></i> {{ number_format($yearGroups->count()) }} {{ \Illuminate\Support\Str::plural('year', $yearGroups->count()) }} represented</span>
        </div>
        <figure class="public-award-moments">
            <img class="public-award-moments__group" src="{{ asset('site/images/awards/award-recipients.jpg') }}" alt="Award recipients standing together with medals and certificates" width="2048" height="1367" fetchpriority="high">
            <img class="public-award-moments__portrait" src="{{ asset('site/images/awards/award-presentation.jpg') }}" alt="A recipient receiving an academic excellence award" width="1639" height="2048">
            <figcaption><span>Education &amp; excellence</span>Celebrating dedication and achievement.</figcaption>
        </figure>
    </div>
</section>

<section class="public-award-content">
    <div class="site-container">
        @if($yearGroups->isNotEmpty())
            <div class="public-award-browser">
                <div class="public-award-browser__toolbar">
                    <div class="public-award-browser__copy">
                        <h2>Applications by year</h2>
                        <p>Select a year to load its applications.</p>
                    </div>
                    <label class="public-award-search">
                        <span class="sr-only">Search applicants in the selected year</span>
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input id="awardYearSearch" type="search" placeholder="Search applicants in selected year" autocomplete="off" {{ $latestYear ? '' : 'disabled' }}>
                        <button class="public-award-search__clear" id="awardYearSearchClear" type="button" aria-label="Clear search"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </label>
                </div>
                <div class="public-award-year-grid" id="awardYearGrid">
                    @foreach($yearGroups as $group)
                        <button
                            class="public-award-year-card {{ (string) $group->year === (string) $latestYear ? 'is-active' : '' }}"
                            type="button"
                            data-award-year-card="{{ $group->year }}"
                            data-url="{{ route('public.awards.year-applicants', ['type' => $type, 'year' => $group->year]) }}"
                            aria-controls="awardYearApplicants"
                            aria-expanded="{{ (string) $group->year === (string) $latestYear ? 'true' : 'false' }}"
                        >
                            <span class="public-award-year-card__top">
                                <span class="public-award-year-card__year">{{ $group->year }}</span>
                                <i class="fa fa-chevron-right public-award-year-card__arrow" aria-hidden="true"></i>
                            </span>
                            <span class="public-award-year-card__counts">
                                <span>{{ number_format($group->applications) }} applications</span>
                                <span aria-hidden="true">·</span>
                                <span class="public-award-year-card__awarded">{{ number_format($group->awarded_count) }} approved</span>
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div id="awardYearApplicants" aria-live="polite" data-initial-year="{{ $latestYear }}">
                @if($latestYear)
                    @include('frontend.site.awards.partials.year-applicants', [
                        'applicants' => $initialApplicants,
                        'year' => $latestYear,
                        'approvedCount' => $initialApprovedCount,
                        'chapterCount' => $initialApplicants->pluck('chapter_id')->filter()->unique()->count(),
                        'zoneCount' => $initialApplicants->pluck('zone_id')->filter()->unique()->count(),
                        'fieldCount' => $initialApplicants->pluck('field_id')->filter()->unique()->count(),
                    ])
                @else
                    <div class="public-award-empty">There are no {{ strtolower($title) }} applications to display yet.</div>
                @endif
            </div>
        @else
            <div class="public-award-empty">There are no {{ strtolower($title) }} applications to display yet.</div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('awardYearSearch');
    const clear = document.getElementById('awardYearSearchClear');
    const cards = Array.from(document.querySelectorAll('[data-award-year-card]'));
    const results = document.getElementById('awardYearApplicants');
    const resultEmpty = document.getElementById('awardYearResultsEmpty');
    let selectedYear = results?.dataset.initialYear || '';
    let loadedYear = selectedYear;
    let requestToken = 0;

    if (!search || !cards.length || !results) return;

    const applySearch = function () {
        const query = search.value.trim().toLocaleLowerCase();
        const rows = Array.from(results.querySelectorAll('[data-applicant-row]'));
        let visible = 0;
        rows.forEach(function (row) {
            const matches = !query || row.dataset.applicantSearch.includes(query);
            row.hidden = !matches;
            if (matches) visible += 1;
        });
        if (resultEmpty) resultEmpty.style.display = visible ? 'none' : 'block';
        clear.style.display = query ? 'grid' : 'none';
    };

    const selectYear = async function (card) {
        const year = card.dataset.awardYearCard;
        if (year === loadedYear) return;
        selectedYear = year;
        const token = ++requestToken;
        cards.forEach(function (card) {
            const active = card.dataset.awardYearCard === year;
            card.classList.toggle('is-active', active);
            card.setAttribute('aria-expanded', active ? 'true' : 'false');
        });
        search.value = '';
        search.disabled = true;
        results.innerHTML = '<div class="public-award-loading"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Loading ' + year + ' applications…</div>';
        try {
            const response = await fetch(card.dataset.url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Unable to load applicants');
            const payload = await response.json();
            if (token !== requestToken) return;
            results.innerHTML = payload.html;
            results.dataset.initialYear = year;
            loadedYear = year;
            search.disabled = false;
            applySearch();
        } catch (error) {
            if (token !== requestToken) return;
            results.innerHTML = '<div class="public-award-empty">Applications could not be loaded. Please select the year to try again.</div>';
            search.disabled = true;
        }
    };

    cards.forEach(function (card) {
        card.addEventListener('click', function () {
            selectYear(card);
        });
    });

    search.addEventListener('input', applySearch);
    applySearch();
    clear.addEventListener('click', function () {
        search.value = '';
        applySearch();
        search.focus();
    });
});
</script>
@endpush
