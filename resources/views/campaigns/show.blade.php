<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $campaign->title }} | GSF</title>
    <style>
        *{box-sizing:border-box}body{margin:0;color:#20324a;background:#f4f7fa;font-family:Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}.campaign-page{width:min(860px,calc(100% - 32px));margin:48px auto}.campaign-brand{margin:0 0 14px;color:#64748b;font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}.campaign-card{overflow:hidden;border:1px solid #e3e9ef;border-radius:18px;background:#fff;box-shadow:0 20px 55px rgba(25,48,76,.09)}.campaign-hero{padding:34px 38px;color:#fff;background:linear-gradient(125deg,#17375f,#19516d 68%,#8c6338 150%)}.campaign-hero h1{margin:0;color:#fff;font-size:clamp(28px,5vw,42px);font-weight:800;letter-spacing:-.035em;line-height:1.1}.campaign-description{margin:12px 0 0;color:#d9e6ef;font-size:14px;line-height:1.6}.campaign-countdown{display:inline-flex;align-items:center;gap:12px;margin-top:20px;padding:12px 15px;border:1px solid rgba(255,255,255,.2);border-radius:12px;background:rgba(255,255,255,.09)}.campaign-countdown__label{color:#d9e6ef;font-size:10px;font-weight:800;letter-spacing:.09em;text-transform:uppercase}.campaign-countdown__units{display:flex;gap:9px}.campaign-countdown__unit{min-width:38px;text-align:center}.campaign-countdown__unit strong{display:block;color:#fff;font-size:18px;font-variant-numeric:tabular-nums;line-height:1.1}.campaign-countdown__unit span{display:block;margin-top:3px;color:#d9e6ef;font-size:8px;font-weight:750;letter-spacing:.07em;text-transform:uppercase}.campaign-body{padding:30px 38px}.campaign-instructions{margin-bottom:24px;padding:15px 17px;border-left:4px solid #c49755;border-radius:0 9px 9px 0;color:#58697d;background:#f8f6f1;font-size:13px;line-height:1.65}.campaign-instructions strong{display:block;margin-bottom:4px;color:#34465c}.campaign-question{margin:0 0 20px}.campaign-question label,.campaign-question legend{display:block;margin-bottom:7px;color:#293d56;font-size:13px;font-weight:750}.campaign-question .required{color:#bd5144}.campaign-control{display:block;width:100%;min-height:44px;padding:10px 12px;border:1px solid #d7e0e8;border-radius:9px;color:#26394f;background:#fff;font:inherit;font-size:13px}.campaign-control:focus{border-color:#3986b9;outline:3px solid rgba(57,134,185,.12)}textarea.campaign-control{min-height:110px;resize:vertical}.campaign-option{display:flex;align-items:center;gap:9px;margin:9px 0;color:#4a5b70;font-size:13px}.campaign-option input{accent-color:#1d6c67}.campaign-error{display:block;margin-top:5px;color:#ae443c;font-size:11px}.campaign-alert{margin-bottom:20px;padding:12px 14px;border:1px solid #b8dfc7;border-radius:9px;color:#246343;background:#eef9f1;font-size:13px}.campaign-button{display:inline-flex;align-items:center;justify-content:center;min-height:45px;padding:11px 18px;border:0;border-radius:9px;color:#fff;background:#1d6c67;font-size:13px;font-weight:800;cursor:pointer}.campaign-button:hover{background:#185c58}.campaign-closed{margin:8px 0;padding:18px;border:1px solid #e8d7b7;border-radius:10px;color:#755626;background:#fff8e8;text-align:center;font-size:14px;line-height:1.6}.campaign-footer{margin-top:18px;color:#8592a1;font-size:10px;text-align:center}@media(max-width:600px){.campaign-page{margin:20px auto}.campaign-hero{padding:27px 22px}.campaign-body{padding:22px}.campaign-countdown{align-items:flex-start;flex-direction:column}}
    </style>
</head>
<body>
    <main class="campaign-page">
        <p class="campaign-brand">GOFAMINT Students’ Fellowship · Campaign</p>
        <article class="campaign-card">
            <header class="campaign-hero">
                <h1>{{ $campaign->title }}</h1>
                @if($campaign->description)<p class="campaign-description">{{ $campaign->description }}</p>@endif
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
            <div class="campaign-body">
                @if(session('campaign_success'))<div class="campaign-alert" role="status">{{ session('campaign_success') }}</div>@endif
                @if($isOpen)
                    @if($errors->any())<div class="campaign-alert" role="alert" style="border-color:#e6b7b1;color:#8f3932;background:#fff1ef">Please review the highlighted answers and try again.</div>@endif
                    <div class="campaign-instructions"><strong>Instructions</strong>{!! nl2br(e($campaign->instructions)) !!}</div>
                    <form action="{{ route('campaigns.public.submit', $campaign->slug) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @foreach($questions as $question)
                            @php
                                $field = 'answers.' . $question->slug;
                                $options = collect($question->options ?? [])->map(fn ($option) => is_array($option) ? ['value' => $option['value'] ?? $option['label'] ?? '', 'label' => $option['label'] ?? $option['value'] ?? ''] : ['value' => $option, 'label' => $option])->values();
                            @endphp
                            <div class="campaign-question">
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
                        <button class="campaign-button" type="submit">Submit response <span aria-hidden="true" style="margin-left:8px">→</span></button>
                    </form>
                @else
                    <div class="campaign-closed" role="status">This campaign is not currently accepting responses.</div>
                @endif
            </div>
        </article>
        <p class="campaign-footer">GOFAMINT Students’ Fellowship</p>
    </main>
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
                countdown.querySelector('[data-unit=\"' + unit + '\"]').textContent = String(value).padStart(2, '0');
            });
        }

        updateCountdown();
        window.setInterval(updateCountdown, 1000);
    })();
</script>
</body>
</html>
