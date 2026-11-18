@extends('frontend.site.layout')

@section('title', $campaign->title.' — GOFAMINT Students’ Fellowship')
@section('description', $campaign->description ?: $campaign->title.' campaign by GOFAMINT Students’ Fellowship.')
@section('mainClass', 'public-page campaign-public-page')
@section('activeSection', 'events')

@push('head')
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
                        ['enabled' => $campaign->enable_field_options, 'key' => 'field_id', 'label' => 'Field', 'items' => $fields],
                        ['enabled' => $campaign->enable_zone_options, 'key' => 'zone_id', 'label' => 'Zone', 'items' => $zones],
                        ['enabled' => $campaign->enable_chapter_option, 'key' => 'chapter_id', 'label' => 'Chapter', 'items' => $chapters],
                        ['enabled' => $campaign->enable_designation_options, 'key' => 'designation_id', 'label' => 'Designation', 'items' => $designations],
                    ] as $campaignOption)
                        @if($campaignOption['enabled'])
                            <div class="campaign-question campaign-question-item" style="--campaign-column-span: 6">
                                <label for="campaign-option-{{ $campaignOption['key'] }}">{{ $campaignOption['label'] }}</label>
                                <select id="campaign-option-{{ $campaignOption['key'] }}" class="campaign-control" name="campaign_options[{{ $campaignOption['key'] }}]">
                                    <option value="">Choose {{ strtolower($campaignOption['label']) }}</option>
                                    @foreach($campaignOption['items'] as $item)
                                        <option value="{{ $item->id }}" @selected(old('campaign_options.'.$campaignOption['key']) == $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('campaign_options.'.$campaignOption['key'])<small class="campaign-error">{{ $message }}</small>@enderror
                            </div>
                        @endif
                    @endforeach
                    @foreach($questions as $question)
                        @php
                            $field = 'answers.' . $question->slug;
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
                                    <input id="campaign-question-{{ $question->id }}" class="campaign-control" type="{{ in_array($question->type, ['number', 'date', 'month', 'year'], true) ? ($question->type === 'year' ? 'number' : $question->type) : 'text' }}" name="answers[{{ $question->slug }}]" value="{{ old($field) }}" @if($question->is_required) required @endif>
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
@endpush
