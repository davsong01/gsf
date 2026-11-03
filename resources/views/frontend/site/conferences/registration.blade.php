@extends('frontend.site.layout')

@php
    $flowImage = $setting->banner ?: $setting->conference_logo ?: 'main/images/events/img-lg1.jpg';
    $flowImageUrl = filter_var($flowImage, FILTER_VALIDATE_URL) ? $flowImage : asset($flowImage);
    $conferenceUrl = $setting->slug ? route('conferences.show', $setting->slug) : route('conferences.index');
@endphp

@section('title', 'Register — '.$setting->conference_theme)
@section('description', 'Register for '.$setting->conference_theme.' with GOFAMINT Students’ Fellowship.')
@section('mainClass', 'public-page conference-flow-page')
@section('activeSection', 'conferences')

@push('head')
    <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
@endpush

@section('content')
<section class="conference-flow-hero">
    <div class="conference-flow-hero__image"><img src="{{ $flowImageUrl }}" alt="" aria-hidden="true"></div>
    <div class="conference-flow-hero__wash"></div>
    <div class="site-container">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ $conferenceUrl }}">{{ $setting->conference_theme }}</a><span>·</span><span>Registration</span></nav>
        <div class="eyebrow eyebrow--light"><span></span> Registration · {{ $setting->start_date ? \Carbon\Carbon::parse($setting->start_date)->format('Y') : 'GSF' }}</div>
        <h1>Take your place.</h1>
        <p>Complete the details below to register for {{ $setting->conference_theme }}.</p>
    </div>
</section>

<section class="conference-flow-section">
    <div class="site-container conference-flow-layout">
        <div class="conference-flow-main">
            @if(session('message') || session('error') || session('warning') || $errors->any())
                <div class="conference-flow-alert {{ session('error') || session('warning') || $errors->any() ? 'conference-flow-alert--error' : '' }}" role="status">
                    @if(session('message'))<p>{{ session('message') }}</p>@endif
                    @if(session('error'))<p>{{ session('error') }}</p>@endif
                    @if(session('warning'))<p>{{ session('warning') }}</p>@endif
                    @if($errors->any())<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
                </div>
            @endif

            <div class="conference-flow-card">
                <div class="conference-flow-card__heading">
                    <span class="conference-flow-step">01 <i></i> YOUR DETAILS</span>
                    <h2>Begin your registration</h2>
                    <p>Fields marked required must be completed. Your information will be used to prepare your conference registration.</p>
                </div>

                <form action="{{ route('checkout') }}" method="POST" class="conference-registration-form">
                    @csrf
                    <input type="hidden" name="conference_plan_id" value="{{ $plan->id }}">
                    <div class="conference-form-grid">
                        @foreach($registrationFields as $field)
                            @if(!empty($field['status']))
                                @php
                                    $name = $field['name'] ?? '';
                                    $label = $field['label'] ?? $field['name'] ?? 'Information';
                                    $inputType = $field['type'] ?? 'text';
                                    $oldValue = old($name);
                                    $isRequired = !empty($field['required']);
                                    $isWide = in_array($inputType, ['textarea'], true);
                                    $isSearchableChapter = $inputType === 'select' && in_array(strtolower($name), ['chapter', 'chapter_id'], true);
                                    $choices = collect($field['options'] ?? [])->mapWithKeys(function ($value, $key) {
                                        $optionValue = is_array($value) ? ($value['id'] ?? $key) : (is_object($value) ? ($value->id ?? $key) : $key);
                                        $optionLabel = is_array($value) ? ($value['name'] ?? ($value['title'] ?? $optionValue)) : (is_object($value) ? ($value->name ?? $optionValue) : $value);
                                        return [$optionValue => $optionLabel];
                                    });
                                @endphp
                                <div class="conference-form-field {{ $isWide ? 'conference-form-field--wide' : '' }} {{ $errors->has($name) ? 'has-error' : '' }}">
                                    <label for="{{ $name }}">{{ $label }}@if($isRequired)<span aria-hidden="true">*</span>@endif</label>
                                    @if($inputType === 'select')
                                        @if($isSearchableChapter)
                                            <select id="{{ $name }}" name="{{ $name }}" class="conference-chapter-select" data-placeholder="Search GSF campuses..." @if($isRequired) required @endif @if(!empty($field['has_onchange'])) onchange="{{ $field['has_onchange'] }}" @endif>
                                                <option value="">Search GSF campuses...</option>
                                                @foreach($choices as $optionValue => $optionLabel)
                                                    <option value="{{ $optionValue }}" @selected((string) $oldValue === (string) $optionValue)>{{ $optionLabel }}</option>
                                                @endforeach
                                                @if(!empty($field['has_other_option']))<option value="other" @selected($oldValue === 'other')>Other</option>@endif
                                            </select>
                                        @else
                                        <select id="{{ $name }}" name="{{ $name }}" @if($isRequired) required @endif @if(!empty($field['has_onchange'])) onchange="{{ $field['has_onchange'] }}" @endif>
                                            <option value="">Choose {{ strtolower($label) }}</option>
                                            @foreach($choices as $optionValue => $optionLabel)
                                                <option value="{{ $optionValue }}" @selected((string) $oldValue === (string) $optionValue)>{{ $optionLabel }}</option>
                                            @endforeach
                                            @if(!empty($field['has_other_option']))<option value="other" @selected($oldValue === 'other')>Other</option>@endif
                                        </select>
                                        @endif
                                    @elseif($inputType === 'textarea')
                                        <textarea id="{{ $name }}" name="{{ $name }}" rows="4" placeholder="Enter {{ strtolower($label) }}" @if($isRequired) required @endif>{{ $oldValue }}</textarea>
                                    @elseif($inputType === 'radio')
                                        <div class="conference-choice-list">
                                            @foreach($choices as $optionValue => $optionLabel)
                                                <label class="conference-choice"><input type="radio" name="{{ $name }}" value="{{ $optionValue }}" @checked((string) $oldValue === (string) $optionValue) @if($isRequired && $loop->first) required @endif><span>{{ $optionLabel }}</span></label>
                                            @endforeach
                                        </div>
                                    @elseif($inputType === 'checkbox' && $choices->isNotEmpty())
                                        @php $selectedChoices = (array) $oldValue; @endphp
                                        <div class="conference-choice-list">
                                            @foreach($choices as $optionValue => $optionLabel)
                                                <label class="conference-choice"><input type="checkbox" name="{{ $name }}[]" value="{{ $optionValue }}" @checked(in_array((string) $optionValue, array_map('strval', $selectedChoices), true)) @if($isRequired && $loop->first) required @endif><span>{{ $optionLabel }}</span></label>
                                            @endforeach
                                        </div>
                                    @else
                                        <input id="{{ $name }}" type="{{ in_array($inputType, ['text','email','tel','number','date','url','checkbox'], true) ? $inputType : 'text' }}" name="{{ $name }}" value="{{ $inputType === 'checkbox' ? '1' : $oldValue }}" placeholder="Enter {{ strtolower($label) }}" @if($inputType === 'checkbox') @checked($oldValue) @endif @if($isRequired) required @endif>
                                    @endif
                                    @error($name)<small class="conference-form-error">{{ $message }}</small>@enderror
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="conference-form-submit">
                        <p>By continuing, you confirm these details are accurate.</p>
                        <button class="conference-flow-button" type="submit">Continue <span aria-hidden="true">→</span></button>
                    </div>
                </form>
            </div>
        </div>

        <aside class="conference-flow-aside">
            <article class="conference-flow-pass">
                <div class="eyebrow"><span></span> YOUR SELECTED PASS</div>
                <span class="conference-flow-pass__level">{{ $plan->level ?: 'Conference registration' }}</span>
                <h2>{{ trim($plan->title) }}</h2>
                <strong class="conference-flow-pass__price">{{ $plan->price > 0 ? '₦'.number_format((float) $plan->price) : 'Registration details' }}</strong>
                @if($plan->items)<ul>@foreach($plan->items as $item)<li>{{ $item }}</li>@endforeach</ul>@endif
                <a href="{{ $conferenceUrl }}#conference-registration">Change registration option <span aria-hidden="true">↗</span></a>
            </article>
            <article class="conference-flow-help">
                <span class="conference-flow-help__icon" aria-hidden="true">?</span>
                <div><strong>Need a hand?</strong><p>Our conference team can help with your registration.</p>
                    @if($setting->official_email)<a href="mailto:{{ $setting->official_email }}">{{ $setting->official_email }}</a>@endif
                    @if($setting->official_phone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $setting->official_phone) }}">{{ $setting->official_phone }}</a>@endif
                </div>
            </article>
        </aside>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('app-assets/js/core/libraries/jquery.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script>
    jQuery(function ($) {
        $('.conference-chapter-select').select2({
            width: '100%',
            minimumResultsForSearch: 0,
            placeholder: 'Search GSF campuses...',
            allowClear: false
        });
    });
</script>
@endpush
