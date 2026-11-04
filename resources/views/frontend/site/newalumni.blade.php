@extends('frontend.site.layout')

@section('title', 'Join the GSF Directory — GOFAMINT Students’ Fellowship')
@section('mainClass', 'public-page join-directory-page')
@section('activeSection', 'directory')

@push('head')
<style>
    .join-hero { background: linear-gradient(125deg, var(--forest-950), #0d366b 58%, #167ba4); }
    .join-hero__grid { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(260px, .9fr); gap: 60px; align-items: end; }
    .join-hero__note { justify-self: end; max-width: 330px; padding: 24px; border: 1px solid rgba(255,255,255,.18); border-radius: 16px; color: rgba(255,255,255,.75); background: rgba(255,255,255,.07); font-size: 13px; line-height: 1.7; }
    .join-hero__note strong { display: block; margin-bottom: 8px; color: var(--gold); font-size: 10px; letter-spacing: .15em; text-transform: uppercase; }
    .join-section { padding: 76px 0 110px; background: var(--paper); }
    .join-layout { display: grid; grid-template-columns: .72fr 1.28fr; gap: clamp(36px, 7vw, 100px); align-items: start; }
    .join-intro { position: sticky; top: 28px; }
    .join-intro h2 { margin: 16px 0; font-size: clamp(34px, 4vw, 52px); line-height: 1.05; letter-spacing: -.045em; }
    .join-intro p { max-width: 390px; color: var(--ink-soft); font-size: 15px; line-height: 1.8; }
    .join-intro__steps { display: grid; gap: 14px; margin-top: 32px; }
    .join-intro__steps div { display: flex; gap: 13px; align-items: center; color: var(--ink-soft); font-size: 12px; }
    .join-intro__steps span { display: grid; place-items: center; width: 28px; height: 28px; flex: 0 0 28px; border-radius: 50%; color: var(--forest-950); background: var(--gold); font-size: 10px; font-weight: 850; }
    .join-form-card { padding: clamp(24px, 4vw, 46px); border: 1px solid var(--line); border-radius: 20px; background: var(--white); box-shadow: 0 25px 70px rgba(7,20,47,.08); }
    .join-form-card__heading { margin-bottom: 28px; padding-bottom: 22px; border-bottom: 1px solid var(--line); }
    .join-form-card__heading h2 { margin: 10px 0 0; font-size: 26px; letter-spacing: -.03em; }
    .join-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px 18px; }
    .join-field { display: grid; gap: 8px; }
    .join-field--full { grid-column: 1 / -1; }
    .join-field label { color: var(--ink); font-size: 10px; font-weight: 850; letter-spacing: .1em; text-transform: uppercase; }
    .join-field input, .join-field select { width: 100%; min-height: 48px; padding: 0 14px; border: 1px solid #d8deea; border-radius: 7px; color: var(--ink); background: #fbfcfe; font: inherit; font-size: 14px; }
    .join-field input:focus, .join-field select:focus { outline: 2px solid rgba(18,110,159,.22); border-color: #167ba4; }
    .join-field .select2-container { width: 100% !important; }
    .join-field .select2-container .select2-selection--single { display: flex; align-items: center; min-height: 48px; border: 1px solid #d8deea; border-radius: 7px; background: #fbfcfe; }
    .join-field .select2-container .select2-selection--single .select2-selection__rendered { padding-left: 14px; color: var(--ink); font: inherit; font-size: 14px; line-height: 1.4; }
    .join-field .select2-container .select2-selection--single .select2-selection__arrow { top: 11px; right: 8px; }
    .join-field .select2-container--focus .select2-selection--single, .join-field .select2-container--open .select2-selection--single { outline: 2px solid rgba(18,110,159,.22); border-color: #167ba4; }
    .select2-dropdown { border-color: #d8deea; }
    .select2-search--dropdown .select2-search__field { padding: 8px 10px; border: 1px solid #d8deea; border-radius: 5px; }
    .join-form-card .button { margin-top: 28px; border: 0; cursor: pointer; font: inherit; }
    .join-alert { margin-bottom: 22px; padding: 13px 15px; border-radius: 8px; color: #155c37; background: #e6f5eb; font-size: 13px; }
    .join-alert--error { color: #8b2d2d; background: #fbeaea; }
    @media (max-width: 760px) {
        .join-hero__grid, .join-layout { grid-template-columns: 1fr; }
        .join-hero__note { justify-self: start; }
        .join-intro { position: static; }
        .join-form-grid { grid-template-columns: 1fr; }
        .join-field--full { grid-column: auto; }
    }
</style>
@endpush

@section('content')
<section class="inner-hero join-hero">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid join-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ route('directory.index') }}">Directory</a><span>·</span><span>Join</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> Join the GSF Directory</div>
            <h1>Keep your place in the <em>GSF family.</em></h1>
            <p>Share your details so fellow GSFites can find you, reconnect with you and celebrate the community we are building together.</p>
        </div>
        <div class="join-hero__note"><strong>One fellowship, many campuses</strong>Your details help us keep the national GSF network connected across generations.</div>
    </div>
</section>

<section class="join-section">
    <div class="site-container join-layout">
        <div class="join-intro">
            <div class="eyebrow"><span></span> Add your details</div>
            <h2>Let’s keep in touch.</h2>
            <p>Complete the short form and our team will review your submission before adding it to the directory.</p>
            <div class="join-intro__steps" aria-label="What happens next">
                <div><span>01</span><b>Submit your details</b></div>
                <div><span>02</span><b>GSF reviews your listing</b></div>
                <div><span>03</span><b>Stay connected nationally</b></div>
            </div>
        </div>

        <div class="join-form-card">
            @if(session('message'))<div class="join-alert">{{ session('message') }}</div>@endif
            @if(session('error'))<div class="join-alert join-alert--error">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="join-alert join-alert--error">Please check the highlighted details and try again.</div>@endif

            <div class="join-form-card__heading">
                <div class="eyebrow"><span></span> Your profile</div>
                <h2>Tell us about yourself</h2>
            </div>

            <form method="POST" action="{{ route('newalumni.save') }}">
                @csrf
                <div class="join-form-grid">
                    <div class="join-field join-field--full"><label for="name">Full name</label><input id="name" name="name" type="text" value="{{ old('name') }}" required></div>
                    <div class="join-field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" required></div>
                    <div class="join-field"><label for="phone">Phone number</label><input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required></div>
                    <div class="join-field"><label for="gender">Gender</label><select class="join-search-select" id="gender" name="gender" data-placeholder="Select gender" required><option value="">Select gender</option><option value="Male" @selected(old('gender') === 'Male')>Male</option><option value="Female" @selected(old('gender') === 'Female')>Female</option></select></div>
                    <div class="join-field"><label for="campus">Campus fellowship</label><select class="join-search-select" id="campus" name="campus" data-placeholder="Search campuses..." required><option value="">Search campuses...</option>@foreach($chapters as $chapter)<option value="{{ $chapter->id }}" @selected(old('campus') == $chapter->id)>{{ $chapter->name }}</option>@endforeach</select></div>
                    <div class="join-field"><label for="portfolio">Portfolio</label><select class="join-search-select" id="portfolio" name="portfolio" data-placeholder="Search chapter executive designations..."><option value="">Search chapter executive designations...</option>@foreach($portfolios as $portfolio)<option value="{{ $portfolio->name }}" @selected(old('portfolio') == $portfolio->name)>{{ $portfolio->name }}</option>@endforeach</select></div>
                    <div class="join-field"><label for="matriculation_year">Year of matriculation</label><select class="join-search-select" id="matriculation_year" name="matriculation_year" data-placeholder="Select year"><option value="">Select year</option>@foreach($sessions as $value)<option value="{{ $value . '/' . ($value + 1) }}" @selected(old('matriculation_year') == $value . '/' . ($value + 1))>{{ $value . '/' . ($value + 1) }}</option>@endforeach</select></div>
                    <div class="join-field"><label for="graduation_year">Year of graduation</label><select class="join-search-select" id="graduation_year" name="graduation_year" data-placeholder="Select year"><option value="">Select year</option>@foreach($sessions as $value)<option value="{{ $value . '/' . ($value + 1) }}" @selected(old('graduation_year') == $value . '/' . ($value + 1))>{{ $value . '/' . ($value + 1) }}</option>@endforeach</select></div>
                    <div class="join-field join-field--full"><label for="office">Office or role <span style="font-weight:500;text-transform:none;letter-spacing:0">(optional)</span></label><input id="office" name="office" type="text" value="{{ old('office') }}" placeholder="e.g. Publicity Secretary"></div>
                </div>
                <button class="button button--gold" type="submit">Submit my details <span aria-hidden="true">→</span></button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('head')
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('app-assets/js/core/libraries/jquery.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script>
    jQuery(function ($) {
        $('.join-search-select').select2({
            width: '100%',
            minimumResultsForSearch: 0,
            placeholder: function () { return $(this).data('placeholder'); },
            allowClear: false
        });
    });
</script>
@endpush
