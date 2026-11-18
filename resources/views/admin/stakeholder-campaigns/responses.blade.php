@extends('layouts.dashboard')

@section('title', 'Campaign Responses')

@section('item')
<li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ $campaign->title }} Responses</li>
@endsection

@section('content')
<style>
    .campaign-responses-summary { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; }
    .campaign-responses-stat { display:flex; align-items:center; gap:13px; padding:14px 18px; border:1px solid #e6ebf1; border-radius:12px; background:#f8fafc; }
    .campaign-responses-stat__icon { display:grid; width:42px; height:42px; place-items:center; border-radius:11px; color:#2767b1; background:#eaf2ff; font-size:18px; }
    .campaign-responses-stat strong { display:block; color:#1f2937; font-size:21px; line-height:1.1; }
    .campaign-responses-stat small { color:#758197; font-size:11px; }
    .campaign-response { margin-top:14px; overflow:hidden; border:1px solid #e5eaf0; border-radius:12px; background:#fff; }
    .campaign-response summary { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:16px 18px; cursor:pointer; list-style:none; }
    .campaign-response summary::-webkit-details-marker { display:none; }
    .campaign-response summary:hover { background:#f8fafc; }
    .campaign-response__identity { display:flex; align-items:center; gap:12px; min-width:0; }
    .campaign-response__number { display:grid; width:38px; height:38px; flex:0 0 38px; place-items:center; border-radius:50%; color:#3159a5; background:#edf3ff; font-size:12px; font-weight:800; }
    .campaign-response__title { color:#263449; font-size:13px; font-weight:750; }
    .campaign-response__date { display:block; margin-top:2px; color:#7b8798; font-size:11px; }
    .campaign-response__toggle { color:#77849a; font-size:12px; }
    .campaign-response[open] .campaign-response__toggle { transform:rotate(180deg); }
    .campaign-response__answers { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; padding:0 18px 18px; }
    .campaign-response__answer { min-width:0; padding:12px 14px; border:1px solid #edf0f4; border-radius:9px; background:#fbfcfe; }
    .campaign-response__answer dt { margin-bottom:6px; color:#77849a; font-size:10px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
    .campaign-response__answer dd { margin:0; color:#263449; font-size:12px; overflow-wrap:anywhere; }
    .campaign-response__value { margin:0; white-space:pre-wrap; font:inherit; }
    .campaign-responses-empty { padding:54px 20px; text-align:center; }
    .campaign-responses-empty__icon { display:grid; width:58px; height:58px; margin:0 auto 14px; place-items:center; border-radius:18px; color:#527db8; background:#edf3ff; font-size:22px; }
    .campaign-responses-empty h5 { margin-bottom:6px; color:#29384d; }
    .campaign-responses-empty p { margin:0; color:#7b8798; font-size:13px; }
    @media(max-width:700px) { .campaign-response__answers { grid-template-columns:1fr; } .campaign-response summary { align-items:flex-start; } }
</style>

<div class="content-body">
    <section>
        <div class="card">
            <div class="card-header campaign-responses-summary">
                <div>
                    <h4 class="card-title mb-1">{{ $campaign->title }} responses</h4>
                    <small class="text-muted">Review the submissions received for this campaign.</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('campaigns.responses.export', $campaign) }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-download mr-1" aria-hidden="true"></i> Export responses
                    </a>
                    <a href="{{ route('campaigns.public.show', $campaign->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Open public form <i class="fa fa-arrow-up-right-from-square ms-1"></i></a>
                    <a href="{{ route('campaigns.index') }}" class="btn btn-sm btn-light border">Back to campaigns</a>
                </div>
            </div>
            <div class="card-body">
                <div class="campaign-responses-stat mb-4">
                    <span class="campaign-responses-stat__icon"><i class="fa fa-inbox"></i></span>
                    <span><strong>{{ number_format($responses->total()) }}</strong><small>Total submissions</small></span>
                </div>

                @forelse($responses as $response)
                    <details class="campaign-response">
                        <summary>
                            <span class="campaign-response__identity">
                                <span class="campaign-response__number">#{{ $responses->firstItem() + $loop->index }}</span>
                                <span><span class="campaign-response__title">Campaign response</span><span class="campaign-response__date">Submitted {{ $response->submitted_at?->format('M j, Y \\a\\t g:i A') ?? $response->created_at?->format('M j, Y \\a\\t g:i A') }}</span></span>
                            </span>
                            <span class="campaign-response__toggle" aria-hidden="true"><i class="fa fa-chevron-down"></i></span>
                        </summary>
                        <dl class="campaign-response__answers">
                            @foreach(['__field' => 'Field', '__zone' => 'Zone', '__chapter' => 'Chapter', '__designation' => 'Designation'] as $optionKey => $optionLabel)
                                @if(filled(data_get($response->answers ?? [], $optionKey)))
                                    <div class="campaign-response__answer"><dt>{{ $optionLabel }}</dt><dd>{{ data_get($response->answers ?? [], $optionKey) }}</dd></div>
                                @endif
                            @endforeach
                            @forelse($response->answers ?? [] as $slug => $answer)
                                @continue(str_starts_with($slug, '__'))
                                @php
                                    $responseQuestion = $campaignQuestions->get($slug);
                                    $answerLabel = $responseQuestion?->label ?? str($slug)->replace('-', ' ')->title();
                                    $answerDisplay = is_array($answer)
                                        ? json_encode($answer, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                                        : (filled($answer) ? (string) $answer : 'No answer provided');
                                @endphp
                                <div class="campaign-response__answer">
                                    <dt>{{ $answerLabel }}</dt>
                                    <dd><pre class="campaign-response__value">{{ $answerDisplay }}</pre></dd>
                                </div>
                            @empty
                                <div class="campaign-response__answer"><dt>Answers</dt><dd>No answers were recorded.</dd></div>
                            @endforelse
                        </dl>
                    </details>
                @empty
                    <div class="campaign-responses-empty">
                        <span class="campaign-responses-empty__icon"><i class="fa fa-inbox"></i></span>
                        <h5>No responses yet</h5>
                        <p>Submissions will appear here once someone completes the campaign form.</p>
                    </div>
                @endforelse

                @if($responses->hasPages())
                    <div class="mt-4">{{ $responses->links() }}</div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
