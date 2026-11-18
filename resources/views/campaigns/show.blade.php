@extends('frontend.site.layout')

@section('title', $campaign->title.' — GOFAMINT Students’ Fellowship')
@section('description', $campaign->description ?: $campaign->title.' campaign by GOFAMINT Students’ Fellowship.')
@section('mainClass', 'public-page campaign-public-page')
@section('activeSection', 'events')

@push('head')
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
<style>
    .campaign-public { padding: clamp(28px, 4vw, 48px) 0 64px; background: var(--cream); }
    .campaign-public__heading { position: relative; margin-bottom: 22px; padding: 24px clamp(22px, 4vw, 38px); overflow: hidden; border-radius: var(--radius-md); color: var(--white); background: linear-gradient(125deg, var(--forest-950), var(--forest-800) 75%, #3159a5); box-shadow: var(--shadow); }
    .campaign-public__heading::after { position: absolute; right: -55px; bottom: -150px; width: 310px; height: 310px; border: 1px solid rgba(255,255,255,.16); border-radius: 50%; content: ""; box-shadow: 0 0 0 34px rgba(255,255,255,.035), 0 0 0 68px rgba(255,255,255,.025); }
    .campaign-public__topline { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 12px; }
    .campaign-public__edit { display: inline-flex; align-items: center; gap: 6px; padding: 7px 11px; border: 1px solid rgba(255,255,255,.38); border-radius: 8px; color: var(--white); background: rgba(255,255,255,.1); font-size: 12px; font-weight: 700; white-space: nowrap; }
    .campaign-public__edit:hover { background: rgba(255,255,255,.2); }
    .campaign-public__eyebrow { position: relative; z-index: 1; margin: 0 0 6px; color: var(--gold); font-size: 10px; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; }
    .campaign-public__heading h1 { position: relative; z-index: 1; max-width: 760px; margin: 0; color: var(--white); font-size: clamp(28px, 4vw, 42px); font-weight: 800; letter-spacing: -.035em; line-height: 1.08; }
    .campaign-countdown { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 14px; margin-top: 14px; padding: 9px 12px; border: 1px solid rgba(255,255,255,.2); border-radius: 12px; background: rgba(255,255,255,.09); }
    .campaign-countdown__label { color: rgba(255,255,255,.8); font-size: 10px; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; }
    .campaign-countdown__units { display: flex; gap: 10px; }
    .campaign-countdown__unit { min-width: 38px; text-align: center; }
    .campaign-countdown__unit strong { display: block; color: var(--white); font-size: 18px; font-variant-numeric: tabular-nums; line-height: 1.1; }
    .campaign-countdown__unit span { display: block; margin-top: 3px; color: rgba(255,255,255,.74); font-size: 8px; font-weight: 750; letter-spacing: .07em; text-transform: uppercase; }
    .campaign-card { width: 100%; margin: 0; padding: clamp(22px, 4vw, 42px); border: 1px solid var(--line); border-radius: var(--radius-md); background: var(--paper); box-shadow: 0 14px 42px rgba(7,20,47,.08); }
    .campaign-card__description { margin: 0 0 22px; color: var(--ink-soft); font-size: 14px; line-height: 1.7; }
    .campaign-card__intro { margin: 0 0 22px; color: var(--ink-soft); font-size: 14px; }
    .campaign-instructions { margin: 0 0 25px; padding: 16px 18px; border-left: 4px solid var(--gold); border-radius: 0 10px 10px 0; color: var(--ink-soft); background: var(--cream); font-size: 13px; line-height: 1.7; }
    .campaign-instructions strong { display: block; margin-bottom: 4px; color: var(--ink); }
    .campaign-question { display: flex; flex-direction: column; gap: 8px; margin: 0; }
    .campaign-questions-grid { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 24px 20px; margin-bottom: 24px; }
    .campaign-question-item { grid-column: span var(--campaign-column-span, 6); min-width: 0; }
    .campaign-question > label, .campaign-question legend { display: block; margin: 0; color: var(--ink); font-size: 13px; font-weight: 750; }
    .campaign-question fieldset { display: flex; flex-direction: column; gap: 8px; }
    .campaign-question .required { color: #bd5144; }
    .campaign-control { display: block; width: 100%; min-height: 46px; padding: 10px 12px; border: 1px solid var(--line); border-radius: 9px; color: var(--ink); background: var(--paper); font: inherit; font-size: 14px; }
    .campaign-control:focus { border-color: var(--forest-700); outline: 3px solid rgba(17,119,173,.13); }
    .campaign-card .select2-container { width:100%!important; }
    .campaign-card .select2-container .select2-selection--single { display:flex; align-items:center; height:46px; border:1px solid var(--line); border-radius:9px; background:var(--paper); }
    .campaign-card .select2-container .select2-selection--single .select2-selection__rendered { padding-left:12px; color:var(--ink); font:inherit; font-size:14px; line-height:1.4; }
    .campaign-card .select2-container .select2-selection--single .select2-selection__arrow { top:10px; right:8px; }
    .campaign-card .select2-container--focus .select2-selection--single,.campaign-card .select2-container--open .select2-selection--single { border-color:var(--forest-700); outline:3px solid rgba(17,119,173,.13); }
    .campaign-card .select2-dropdown { border-color:var(--line); color:var(--ink); background:var(--paper); }
    .campaign-card .select2-search__field { min-height:36px; padding:6px 9px; border:1px solid var(--line); border-radius:6px; }
    textarea.campaign-control { min-height: 112px; resize: vertical; }
    .campaign-option { display: flex; align-items: center; gap: 9px; margin: 0; color: var(--ink-soft); font-size: 13px; }
    .campaign-option input { accent-color: var(--forest-700); }
    .campaign-error { display: block; margin: 0; color: #ae443c; font-size: 12px; }
    .campaign-alert { margin-bottom: 20px; padding: 13px 15px; border: 1px solid #b8dfc7; border-radius: 9px; color: #246343; background: #eef9f1; font-size: 13px; }
    .campaign-alert--error { border-color: #e6b7b1; color: #8f3932; background: #fff1ef; }
    .campaign-closed { margin: 5px 0; padding: 18px; border: 1px solid #e8d7b7; border-radius: 10px; color: #755626; background: #fff8e8; text-align: center; font-size: 14px; line-height: 1.6; }
    .campaign-submit { margin-top: 16px; }
    @media(max-width:767px) { .campaign-questions-grid { grid-template-columns: 1fr; } .campaign-question-item { grid-column: 1 / -1; } }
    @media(max-width:600px) { .campaign-public { padding: 25px 0 48px; } .campaign-public__topline { align-items: flex-start; } .campaign-public__heading { border-radius: 18px; } .campaign-countdown { align-items: flex-start; flex-direction: column; gap: 8px; } .campaign-card { border-radius: 18px; } }
</style>
@endpush

@section('content')
<section class="campaign-public">
    <div class="site-container">
        <header class="campaign-public__heading">
            @php
                $campaignUserPermissions = auth()->user()?->permissions ?? [];
                $canEditCampaign = (isAdmin()['status'] ?? false)
                    || in_array('campaigns.index', $campaignUserPermissions, true)
                    || in_array('stakeholder.questions.index', $campaignUserPermissions, true);
            @endphp
            @if($canEditCampaign)
                <div class="campaign-public__topline">
                    <a class="campaign-public__edit" href="{{ route('campaigns.edit', $campaign) }}"><span aria-hidden="true">✎</span> Edit campaign</a>
                </div>
            @endif
            <p class="campaign-public__eyebrow">GOFAMINT Students’ Fellowship · Campaign</p>
            <h1>{{ $campaign->title }}</h1>
            <div class="campaign-countdown" id="campaign-countdown" data-event-at="{{ $campaign->event_at?->toIso8601String() }}">
                <span class="campaign-countdown__label" id="campaign-countdown-label">Countdown to event</span>
                <span class="campaign-countdown__units" id="campaign-countdown-units">
                    <span class="campaign-countdown__unit"><strong data-unit="days">--</strong><span>Days</span></span>
                    <span class="campaign-countdown__unit"><strong data-unit="hours">--</strong><span>Hours</span></span>
                    <span class="campaign-countdown__unit"><strong data-unit="minutes">--</strong><span>Minutes</span></span>
                    <span class="campaign-countdown__unit"><strong data-unit="seconds">--</strong><span>Seconds</span></span>
                </span>
            </div>
        </header>

        <section class="campaign-card" aria-label="Campaign response form">
            @if(session('campaign_success'))<div class="campaign-alert" role="status">{{ session('campaign_success') }}</div>@endif
            @if($isOpen)
                @if($errors->any())<div class="campaign-alert campaign-alert--error" role="alert">Please review the highlighted answers and try again.</div>@endif
                @if(filled($campaign->description))<p class="campaign-card__description">{{ $campaign->description }}</p>@endif
                @if(filled($campaign->instructions))
                    <div class="campaign-instructions"><strong>Instructions</strong>{!! nl2br(e($campaign->instructions)) !!}</div>
                @else
                    <p class="campaign-card__intro">Complete the form below to submit your response.</p>
                @endif
                <form action="{{ route('campaigns.public.submit', $campaign->slug) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="campaign-questions-grid">
                    @foreach([
                        ['enabled' => $campaign->enable_designation_options, 'key' => 'designation_id', 'label' => 'Designation/Portfolio', 'items' => $designations],
                        ['enabled' => $campaign->enable_field_options, 'key' => 'field_id', 'label' => 'Field', 'items' => $fields],
                        ['enabled' => $campaign->enable_zone_options, 'key' => 'zone_id', 'label' => 'Zone', 'items' => $zones],
                        ['enabled' => $campaign->enable_chapter_option, 'key' => 'chapter_id', 'label' => 'Chapter', 'items' => $chapters],
                    ] as $campaignOption)
                        @if($campaignOption['enabled'])
                            <div class="campaign-question campaign-question-item" style="--campaign-column-span: 6">
                                <label for="campaign-option-{{ $campaignOption['key'] }}">{{ $campaignOption['label'] }}</label>
                                <select id="campaign-option-{{ $campaignOption['key'] }}"
                                        class="campaign-control campaign-search-select"
                                        name="campaign_options[{{ $campaignOption['key'] }}]"
                                        data-campaign-option="{{ $campaignOption['key'] }}"
                                        @if($campaignOption['key'] === 'designation_id') data-stakeholder-name-url="{{ route('campaigns.public.designation-name', ['slug' => $campaign->slug, 'designation' => '__designation__']) }}" @endif
                                        @if($campaignOption['key'] === 'zone_id' && $campaign->enable_field_options) data-depends-on="field_id" disabled @endif
                                        @if($campaignOption['key'] === 'chapter_id' && $campaign->enable_zone_options) data-depends-on="zone_id" disabled @endif>
                                    <option value="">Choose {{ strtolower($campaignOption['label']) }}</option>
                                    @foreach($campaignOption['items'] as $item)
                                        <option value="{{ $item->id }}"
                                                @if($campaignOption['key'] === 'zone_id') data-field-id="{{ $item->field_id }}" @endif
                                                @if($campaignOption['key'] === 'chapter_id') data-zone-id="{{ $item->zone_id }}" @endif
                                                @selected(old('campaign_options.'.$campaignOption['key']) == $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('campaign_options.'.$campaignOption['key'])<small class="campaign-error">{{ $message }}</small>@enderror
                            </div>
                        @endif
                    @endforeach
                    @foreach($questions as $question)
                        @php
                            $field = 'answers.' . $question->slug;
                            $questionLabel = strtolower(trim($question->label));
                            $questionSlug = strtolower((string) $question->slug);
                            $isFullNameQuestion = $questionSlug === 'full-name' || $questionLabel === 'full name';
                            $isEmailQuestion = str_contains($questionSlug, 'email') || str_contains($questionLabel, 'email');
                            $isPhoneQuestion = str_contains($questionSlug, 'phone') || str_contains($questionSlug, 'mobile') || str_contains($questionLabel, 'phone') || str_contains($questionLabel, 'mobile');
                            $options = collect($question->options ?? [])->map(fn ($option) => is_array($option) ? ['value' => $option['value'] ?? $option['label'] ?? '', 'label' => $option['label'] ?? $option['value'] ?? ''] : ['value' => $option, 'label' => $option])->values();
                            $questionWidth = match ($question->width_class) {
                                'col-md-2' => 2,
                                'col-md-3' => 3,
                                'col-md-4' => 4,
                                'col-md-6' => 6,
                                'col-md-8' => 8,
                                'col-md-12' => 12,
                                default => 6,
                            };
                        @endphp
                        <div class="campaign-question campaign-question-item" style="--campaign-column-span: {{ $questionWidth }}">
                            @if(in_array($question->type, ['radio', 'checkbox'], true))
                                <fieldset style="border:0;padding:0;margin:0">
                                    <legend>{{ $question->label }} @if($question->is_required)<span class="required">*</span>@endif</legend>
                                    @foreach($options as $option)
                                        <label class="campaign-option"><input type="{{ $question->type }}" name="answers[{{ $question->slug }}]{{ $question->type === 'checkbox' ? '[]' : '' }}" value="{{ $option['value'] }}" @checked(in_array($option['value'], (array) old($field, []))) @if($question->is_required) required @endif> {{ $option['label'] }}</label>
                                    @endforeach
                                </fieldset>
                            @else
                                <label for="campaign-question-{{ $question->id }}">{{ $question->label }} @if($question->is_required)<span class="required">*</span>@endif</label>
                                @if(in_array($question->type, ['select', 'rating'], true))
                                    <select id="campaign-question-{{ $question->id }}" class="campaign-control" name="answers[{{ $question->slug }}]" @if($question->is_required) required @endif>
                                        <option value="">Choose an option</option>
                                        @foreach($options as $option)<option value="{{ $option['value'] }}" @selected(old($field) == $option['value'])>{{ $option['label'] }}</option>@endforeach
                                    </select>
                                @elseif($question->type === 'file')
                                    <input id="campaign-question-{{ $question->id }}" class="campaign-control" type="file" name="answers[{{ $question->slug }}]" @if($question->is_required) required @endif>
                                @elseif($question->type === 'textarea' || in_array($question->type, ['dynamic_table', 'income_table'], true))
                                    <textarea id="campaign-question-{{ $question->id }}" class="campaign-control" name="answers[{{ $question->slug }}]" @if($question->is_required) required @endif>{{ old($field) }}</textarea>
                                @else
                                    <input id="campaign-question-{{ $question->id }}" class="campaign-control" type="{{ in_array($question->type, ['number', 'date', 'month', 'year'], true) ? ($question->type === 'year' ? 'number' : $question->type) : 'text' }}" name="answers[{{ $question->slug }}]" value="{{ old($field) }}" @if($isFullNameQuestion) data-campaign-stakeholder-field="name" @elseif($isEmailQuestion) data-campaign-stakeholder-field="email" @elseif($isPhoneQuestion) data-campaign-stakeholder-field="phone" @endif @if($question->is_required) required @endif>
                                @endif
                            @endif
                            @error($field)<small class="campaign-error">{{ $message }}</small>@enderror
                        </div>
                    @endforeach
                    </div>
                    <div class="campaign-question">
                        <label for="campaign-anti-spam">Prove you are not a robot: what is {{ $antiSpamNumbers[0] }} + {{ $antiSpamNumbers[1] }}?</label>
                        <input id="campaign-anti-spam" class="campaign-control" type="number" name="anti_spam" inputmode="numeric" autocomplete="off" required>
                        @error('anti_spam')<small class="campaign-error">{{ $message }}</small>@enderror
                    </div>
                    <button class="button button--blue campaign-submit" type="submit">Submit response <span aria-hidden="true">→</span></button>
                </form>
            @else
                <div class="campaign-closed" role="status">This campaign is not currently accepting responses.</div>
            @endif
        </section>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('app-assets/js/core/libraries/jquery.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script>
    (function () {
        const countdown = document.getElementById('campaign-countdown');
        const eventAt = countdown?.dataset.eventAt;
        const label = document.getElementById('campaign-countdown-label');
        const units = document.getElementById('campaign-countdown-units');

        if (!eventAt) {
            label.textContent = 'Event date to be announced';
            units.hidden = true;
            return;
        }

        const eventTime = new Date(eventAt).getTime();
        function updateCountdown() {
            const remaining = eventTime - Date.now();
            if (remaining <= 0) {
                label.textContent = 'The event has started';
                units.hidden = true;
                return;
            }

            const values = {
                days: Math.floor(remaining / 86400000),
                hours: Math.floor((remaining % 86400000) / 3600000),
                minutes: Math.floor((remaining % 3600000) / 60000),
                seconds: Math.floor((remaining % 60000) / 1000)
            };
            Object.entries(values).forEach(([unit, value]) => {
                countdown.querySelector('[data-unit="' + unit + '"]').textContent = String(value).padStart(2, '0');
            });
        }

        updateCountdown();
        window.setInterval(updateCountdown, 1000);
    })();
</script>
<script>
    jQuery(function ($) {
        const fieldSelect = document.querySelector('[data-campaign-option="field_id"]');
        const zoneSelect = document.querySelector('[data-campaign-option="zone_id"]');
        const chapterSelect = document.querySelector('[data-campaign-option="chapter_id"]');
        const designationSelect = document.querySelector('[data-campaign-option="designation_id"]');
        const zoneDependsOnField = zoneSelect?.dataset.dependsOn === 'field_id';
        const chapterDependsOnZone = chapterSelect?.dataset.dependsOn === 'zone_id';

        const readOptions = (select, parentKey) => select
            ? Array.from(select.options).filter(option => option.value).map(option => ({
                id: option.value,
                name: option.textContent,
                parentId: option.dataset[parentKey] || ''
            }))
            : [];
        const zoneOptions = readOptions(zoneSelect, 'fieldId');
        const chapterOptions = readOptions(chapterSelect, 'zoneId');

        function initSearch(select) {
            if (select && !$(select).hasClass('select2-hidden-accessible')) {
                $(select).select2({
                    width: '100%',
                    minimumResultsForSearch: 0,
                    allowClear: false,
                    placeholder: function () { return $(this).find('option:first').text(); }
                });
            }
        }

        function replaceOptions(select, items, placeholder, selectedId, disabled) {
            if (!select) return;
            if ($(select).hasClass('select2-hidden-accessible')) $(select).select2('destroy');
            select.replaceChildren(new Option(placeholder, ''));
            items.forEach(item => select.add(new Option(item.name, item.id)));
            select.value = items.some(item => String(item.id) === String(selectedId)) ? String(selectedId) : '';
            select.disabled = disabled;
            initSearch(select);
        }

        function updateChapters(zoneId, selectedChapterId = '') {
            if (!chapterSelect || !chapterDependsOnZone) return;
            const available = zoneId
                ? chapterOptions.filter(option => String(option.parentId) === String(zoneId))
                : [];
            replaceOptions(chapterSelect, available, zoneId ? 'Choose chapter' : 'Choose zone first', selectedChapterId, !zoneId);
        }

        function updateZones(fieldId, selectedZoneId = '', selectedChapterId = '') {
            if (!zoneSelect || !zoneDependsOnField) return;
            const available = fieldId
                ? zoneOptions.filter(option => String(option.parentId) === String(fieldId))
                : [];
            replaceOptions(zoneSelect, available, fieldId ? 'Choose zone' : 'Choose field first', selectedZoneId, !fieldId);
            if (chapterDependsOnZone) updateChapters(zoneSelect.value, selectedChapterId);
        }

        document.querySelectorAll('.campaign-search-select').forEach(select => {
            if (select !== zoneSelect && select !== chapterSelect) initSearch(select);
        });

        const initialZoneId = zoneSelect?.value || '';
        const initialChapterId = chapterSelect?.value || '';
        if (zoneDependsOnField) {
            updateZones(fieldSelect?.value || '', initialZoneId, initialChapterId);
            if (fieldSelect) {
                $(fieldSelect).on('change', function () {
                    updateZones($(this).val());
                });
            }
        } else if (zoneSelect) {
            initSearch(zoneSelect);
            if (chapterDependsOnZone) updateChapters(zoneSelect.value, initialChapterId);
        }

        if (chapterDependsOnZone) {
            if (zoneSelect) {
                $(zoneSelect).on('change', function () {
                    updateChapters($(this).val());
                });
            }
        } else if (chapterSelect) {
            initSearch(chapterSelect);
        }

        if (designationSelect) {
            let designationLookup = 0;
            const stakeholderNameUrl = designationSelect.dataset.stakeholderNameUrl;

            function applyStakeholderDetails(details) {
                const stakeholderInputs = {
                    name: document.querySelector('[data-campaign-stakeholder-field="name"]'),
                    email: document.querySelector('[data-campaign-stakeholder-field="email"]'),
                    phone: document.querySelector('[data-campaign-stakeholder-field="phone"]')
                };

                Object.entries(stakeholderInputs).forEach(([key, input]) => {
                    if (!input || !details[key]) return;
                    input.value = details[key];
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });

                const selectAssignedOption = (select, value) => {
                    if (!select || !value) return;
                    const optionExists = Array.from(select.options).some(option => String(option.value) === String(value));
                    if (optionExists) $(select).val(String(value)).trigger('change');
                };

                selectAssignedOption(fieldSelect, details.field_id);
                selectAssignedOption(zoneSelect, details.zone_id);
                selectAssignedOption(chapterSelect, details.chapter_id);
            }

            $(designationSelect).on('change', async function () {
                const designationId = $(this).val();
                if (!designationId || !stakeholderNameUrl) return;

                const lookupId = ++designationLookup;
                const url = stakeholderNameUrl.replace('__designation__', encodeURIComponent(designationId));

                try {
                    const response = await fetch(url, { headers: { Accept: 'application/json' } });
                    if (!response.ok) return;
                    const details = await response.json();
                    if (lookupId !== designationLookup || String($(designationSelect).val()) !== String(designationId)) return;
                    applyStakeholderDetails(details);
                } catch (error) {
                    console.error('Could not load the stakeholder assigned to this designation.', error);
                }
            });

            if ($(designationSelect).val()) $(designationSelect).trigger('change');
        }
    });
</script>
@endpush
