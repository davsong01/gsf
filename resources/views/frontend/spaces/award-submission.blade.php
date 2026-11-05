@extends('frontend.site.layout')

@section('title', ($type === 'go' ? 'G.O. Award Application' : 'ETF Application').' · GOFAMINT Students’ Fellowship')
@section('description', $type === 'go' ? 'Apply for the G.O. Award from GOFAMINT Students’ Fellowship.' : 'Apply for support from the Education Trust Fund.')
@section('mainClass', 'public-page award-application')
@section('activeSection', 'awards')
@section('hideDirectoryNetwork', '1')

@push('head')
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
<style>
    .award-application-hero { position:relative; overflow:hidden; color:#fff; background:linear-gradient(125deg,var(--forest-950),#123f7a 62%,#1177ad); }
    .award-application-hero::after { position:absolute; top:-230px; right:-110px; width:560px; height:560px; border:1px solid rgba(255,255,255,.13); border-radius:50%; box-shadow:0 0 0 45px rgba(255,255,255,.035),0 0 0 95px rgba(255,255,255,.025); content:""; }
    .award-application-hero__inner { position:relative; z-index:1; padding-block:48px 61px; }
    .award-application-hero .breadcrumbs { margin-bottom:34px; }
    .award-application-hero__grid { display:grid; grid-template-columns:minmax(0,1fr) minmax(250px,.55fr); align-items:end; gap:50px; }
    .award-application-hero h1 { max-width:780px; margin:16px 0 12px; color:#fff; font-size:clamp(38px,5.1vw,62px); font-weight:750; line-height:1.04; letter-spacing:-.045em; }
    .award-application-hero__copy > p { max-width:650px; margin:0; color:rgba(255,255,255,.75); font-size:16px; line-height:1.75; }
    .award-application-verse { padding:22px 24px; border:1px solid rgba(255,255,255,.17); border-radius:16px; color:rgba(255,255,255,.78); background:rgba(255,255,255,.07); font-family:Georgia,serif; font-size:15px; font-style:italic; line-height:1.65; }
    .award-application-verse strong { display:block; margin-bottom:8px; color:var(--gold); font-family:inherit; font-size:11px; font-style:normal; letter-spacing:.12em; text-transform:uppercase; }
    .award-application-section { padding:58px 0 90px; background:var(--paper); }
    .award-application-layout { display:grid; grid-template-columns:minmax(230px,.65fr) minmax(0,1.35fr); align-items:start; gap:clamp(30px,6vw,76px); }
    .award-application-aside { position:sticky; top:110px; }
    .award-application-aside h2 { margin:13px 0 12px; color:var(--ink); font-size:clamp(28px,3vw,38px); line-height:1.1; letter-spacing:-.04em; }
    .award-application-aside > p { margin:0; color:var(--ink-soft); font-size:14px; line-height:1.8; }
    .award-application-steps { display:grid; gap:15px; margin-top:28px; }
    .award-application-steps > div { display:flex; align-items:center; gap:12px; color:var(--ink-soft); font-size:12px; font-weight:700; }
    .award-application-steps span { display:grid; width:30px; height:30px; flex:0 0 30px; place-items:center; border-radius:50%; color:var(--forest-950); background:var(--gold); font-size:10px; font-weight:900; }
    .award-application-form-card { min-width:0; padding:clamp(22px,4vw,42px); border:1px solid var(--line); border-radius:20px; background:#fff; box-shadow:0 22px 65px rgba(7,20,47,.08); }
    .award-application-form-card__heading { margin-bottom:27px; padding-bottom:21px; border-bottom:1px solid var(--line); }
    .award-application-form-card__heading h2 { margin:9px 0 5px; color:var(--ink); font-size:25px; line-height:1.2; letter-spacing:-.03em; }
    .award-application-form-card__heading p { margin:0; color:var(--ink-soft); font-size:13px; }
    .award-application .row.g-4 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:19px 16px; margin:0; }
    .award-application .row.g-4 > .col-md-6 { width:auto; max-width:none; padding:0; }
    .award-application .col-md-6 > .mb-3 { display:grid; align-content:start; gap:8px; margin:0 !important; }
    .award-application .form-label { margin:0; color:var(--ink); font-size:10px; font-weight:850; letter-spacing:.09em; text-transform:uppercase; }
    .award-application .form-control,.award-application .form-select { display:block; width:100%; min-height:48px; padding:10px 13px; border:1px solid #d8deea; border-radius:8px; outline:none; color:var(--ink); background:#fbfcfe; font:inherit; font-size:14px; box-shadow:none; }
    .award-application textarea.form-control { min-height:112px; resize:vertical; }
    .award-application .form-control:focus,.award-application .form-select:focus { border-color:#167ba4; outline:2px solid rgba(18,110,159,.17); }
    .award-application input[type="file"].form-control { height:auto; padding:9px; font-size:12px; }
    .award-application .border.rounded-3.p-3.bg-light { border:1px solid #e4eaf0 !important; border-radius:10px !important; background:#f8fafc !important; }
    .award-application .form-text,.award-application .text-muted { color:var(--ink-soft) !important; font-size:11px; line-height:1.5; }
    .award-application .select2-container { width:100% !important; }
    .award-application .select2-container--default .select2-selection--single { display:flex; align-items:center; min-height:48px; border:1px solid #d8deea; border-radius:8px; background:#fbfcfe; }
    .award-application .select2-container--default .select2-selection--single .select2-selection__rendered { padding:0 34px 0 13px; color:var(--ink); font:inherit; font-size:14px; line-height:1.4; }
    .award-application .select2-container--default .select2-selection--single .select2-selection__arrow { top:10px; right:8px; }
    .award-application .select2-container--focus .select2-selection--single,.award-application .select2-container--open .select2-selection--single { border-color:#167ba4; outline:2px solid rgba(18,110,159,.17); }
    .award-application .select2-dropdown { overflow:hidden; border:1px solid #d8deea; border-radius:8px; box-shadow:0 12px 30px rgba(7,20,47,.13); }
    .award-application .select2-search--dropdown .select2-search__field { min-height:38px; padding:7px 10px; border:1px solid #d8deea; border-radius:6px; }
    .award-application-alert { margin-bottom:22px; padding:13px 15px; border-radius:9px; color:#165d3d; background:#e8f5ed; font-size:13px; }
    .award-application-alert--error { color:#8b3030; background:#fbeaea; }
    .award-application-submit { display:flex; align-items:center; justify-content:space-between; gap:15px; margin-top:30px; padding-top:22px; border-top:1px solid var(--line); }
    .award-application-submit p { max-width:360px; margin:0; color:var(--ink-soft); font-size:11px; line-height:1.6; }
    .award-application-submit .button { flex:0 0 auto; border:0; cursor:pointer; font:inherit; }
    @media(max-width:800px) { .award-application-hero__grid,.award-application-layout { grid-template-columns:1fr; }.award-application-verse { max-width:560px; }.award-application-aside { position:static; }.award-application-steps { grid-template-columns:repeat(3,minmax(0,1fr)); }.award-application-steps > div { align-items:flex-start; flex-direction:column; } }
    @media(max-width:560px) { .award-application-hero__inner { padding-block:34px 42px; }.award-application-hero .breadcrumbs { margin-bottom:25px; }.award-application-hero__grid { gap:24px; }.award-application-section { padding-block:38px 60px; }.award-application .row.g-4 { grid-template-columns:1fr; }.award-application-steps { grid-template-columns:1fr; }.award-application-steps > div { align-items:center; flex-direction:row; }.award-application-submit { align-items:stretch; flex-direction:column; }.award-application-submit .button { width:100%; } }
</style>
@endpush

@section('content')
@php
    $awardMeta = [
        'go' => [
            'title' => 'G.O. Award Application',
            'subtitle' => 'Celebrating academic excellence, diligence and godly character.',
            'verse' => 'Daniel 1:20',
            'quote' => 'In every matter of wisdom and understanding, the king found them ten times better.',
        ],
        'etf' => [
            'title' => 'Education Trust Fund Application',
            'subtitle' => 'Supporting students in their pursuit of learning and academic excellence.',
            'verse' => 'Proverbs 4:7',
            'quote' => 'Wisdom is the principal thing; therefore get wisdom.',
        ],
    ];
    $meta = $awardMeta[$type];
@endphp

<section class="award-application-hero">
    <div class="site-container award-application-hero__inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route($type === 'go' ? 'public.awards.go' : 'public.awards.etf') }}">Education awards</a><span>·</span><span>{{ $type === 'go' ? 'G.O. Award' : 'ETF' }}</span></nav>
        <div class="award-application-hero__grid">
            <div class="award-application-hero__copy">
                <div class="eyebrow eyebrow--light"><span></span> Education awards</div>
                <h1>{{ $meta['title'] }}</h1>
                <p>{{ $meta['subtitle'] }}</p>
            </div>
            <div class="award-application-verse"><strong>{{ $meta['verse'] }}</strong>“{{ $meta['quote'] }}”</div>
        </div>
    </div>
</section>

<section class="award-application-section">
    <div class="site-container award-application-layout">
        <aside class="award-application-aside">
            <div class="eyebrow"><span></span> Before you begin</div>
            <h2>Tell us about your application.</h2>
            <p>Complete each required field accurately. The award committee will review the information and supporting documents you provide.</p>
            <div class="award-application-steps" aria-label="Application steps">
                <div><span>01</span><b>Complete your details</b></div>
                <div><span>02</span><b>Attach requested documents</b></div>
                <div><span>03</span><b>Review and submit</b></div>
            </div>
        </aside>

        <div class="award-application-form-card">
            @if(session('message'))<div class="award-application-alert" role="status">{{ session('message') }}</div>@endif
            @if(session('error'))<div class="award-application-alert award-application-alert--error" role="alert">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="award-application-alert award-application-alert--error" role="alert">Please check the details below and correct any highlighted fields before submitting.</div>@endif

            <div class="award-application-form-card__heading">
                <div class="eyebrow"><span></span> Application form</div>
                <h2>Your information</h2>
                <p>Fields marked with an asterisk are required.</p>
            </div>

            <form method="POST" enctype="multipart/form-data" action="{{ route('award.submit', $type) }}">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="award-application__fields">
                    @include('admin.awards.award_form', [
                        'fields' => $fields,
                        'award' => null,
                        'canEdit' => true,
                        'isAdmin' => false,
                        'required' => true,
                        'type' => $type,
                    ])
                </div>
                <div class="award-application-submit">
                    <p>Your information will be used to process this award application and will be reviewed by the appropriate committee.</p>
                    <button type="submit" class="button button--gold">Submit application <span aria-hidden="true">→</span></button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('app-assets/js/core/libraries/jquery.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script>
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.award-application select').each(function () {
            if (this.options.length > 1) {
                jQuery(this).select2({ width: '100%' });
            }
        });
    }
</script>
@endpush
