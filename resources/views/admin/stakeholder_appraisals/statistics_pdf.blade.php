<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report of the Appraisal - Statistics</title>
    <style>
        @page { margin: 18px 20px; }
        * { box-sizing: border-box; }
        body { color:#17233d; font-family: DejaVu Sans, sans-serif; font-size:9px; line-height:1.35; }
        h1 { color:#182b58; font-size:20px; margin:0 0 4px; }
        h2 { color:#182b58; font-size:13px; margin:20px 0 7px; border-bottom:2px solid #356ae6; padding-bottom:4px; }
        h3 { color:#182b58; font-size:11px; margin:0 0 4px; }
        .muted { color:#718096; }
        .header { border-bottom:3px solid #356ae6; padding-bottom:8px; }
        .fellowship-brand { text-align:center; margin-bottom:9px; }
        .fellowship-brand img { width:52px; height:auto; display:block; margin:0 auto 3px; }
        .fellowship-brand-name { color:#182b58; font-size:13px; font-weight:bold; letter-spacing:.4px; }
        .header-table, .summary-table, .group-table { width:100%; border-collapse:collapse; }
        .header-table td { vertical-align:top; }
        .header-meta { text-align:right; color:#718096; }
        .summary-grid { margin-top:13px; }
        .summary-card { border:1px solid #dfe6ef; border-top:3px solid #356ae6; padding:6px 8px; height:50px; }
        .summary-card.red { border-top-color:#e05252; } .summary-card.amber { border-top-color:#e49b25; } .summary-card.green { border-top-color:#12a875; }
        .summary-label { color:#718096; font-size:8px; }
        .summary-value { font-size:15px; font-weight:bold; margin-top:2px; }
        .summary-note { color:#718096; font-size:7px; }
        .filters { background:#f4f7fb; border:1px solid #e2e8f0; padding:5px 7px; margin-top:8px; }
        .group-table { page-break-inside:auto; }
        .group-table tr { page-break-inside:auto; page-break-after:auto; }
        .group-table th { background:#182b58; color:#fff; font-size:8px; text-align:left; padding:4px; }
        .group-table td { border:1px solid #e2e8f0; padding:4px; vertical-align:top; }
        .group-table tr:nth-child(even) td { background:#f8fafc; }
        .group { width:16%; font-weight:bold; }
        .total { width:7%; text-align:center; font-weight:bold; }
        .status { width:25.5%; }
        .status strong { display:block; font-size:11px; margin-bottom:3px; }
        .not-submitted strong { color:#b7343d; } .waiting strong { color:#a56a08; } .evaluated strong { color:#087d58; }
        .people { color:#53627a; line-height:1.35; }
        .footer { color:#8a96a8; font-size:8px; margin-top:10px; border-top:1px solid #e2e8f0; padding-top:4px; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('frontend/img/logo.png');
        $logo = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp
    <div class="fellowship-brand">
        @if($logo)<img src="data:image/png;base64,{{ $logo }}" alt="GSF logo">@endif
        <div class="fellowship-brand-name">GOFAMINT STUDENTS’ FELLOWSHIP</div>
    </div>
    <div class="header">
        <table class="header-table"><tr>
            <td><h1>Report of the Appraisal — Statistics</h1><div class="muted">Comprehensive submission and evaluation report</div></td>
            <td class="header-meta">Generated {{ now()->format('d M Y, h:i A') }}<br>{{ $summary['total_stakeholders'] }} stakeholders in view</td>
        </tr></table>
    </div>

    <div class="filters"><strong>Report scope:</strong> {{ $filters['search'] ?? 'All stakeholders' }}
        @if(!empty($filters['field_id'])) · Field: {{ $fields->firstWhere('id', $filters['field_id'])?->name }} @endif
        @if(!empty($filters['zone_id'])) · Zone: {{ $zones->firstWhere('id', $filters['zone_id'])?->name }} @endif
        @if(!empty($filters['self_status'])) · Submission: {{ ucfirst($filters['self_status']) }} @endif
        @if(!empty($filters['evaluation_status'])) · Evaluation: {{ ucfirst($filters['evaluation_status']) }} @endif
    </div>

    <h2 style="margin-top:12px">Comprehensive Summary</h2>
    <table class="summary-table summary-grid"><tr>
        <td style="width:25%;padding-right:7px"><div class="summary-card"><div class="summary-label">TOTAL STAKEHOLDERS</div><div class="summary-value">{{ $summary['total_stakeholders'] }}</div><div class="summary-note">People included in this report</div></div></td>
        <td style="width:25%;padding:0 4px"><div class="summary-card red"><div class="summary-label">NOT SUBMITTED</div><div class="summary-value">{{ $summary['not_submitted'] }} ({{ $summary['not_submitted_percent'] }}%)</div><div class="summary-note">Have not published self-appraisal</div></div></td>
        <td style="width:25%;padding:0 4px"><div class="summary-card amber"><div class="summary-label">SUBMITTED, NOT EVALUATED</div><div class="summary-value">{{ $summary['submitted_not_evaluated'] }} ({{ $summary['submitted_not_evaluated_percent'] }}%)</div><div class="summary-note">Waiting for evaluation</div></div></td>
        <td style="width:25%;padding-left:7px"><div class="summary-card green"><div class="summary-label">EVALUATED</div><div class="summary-value">{{ $summary['evaluated'] }} ({{ $summary['evaluated_percent'] }}%)</div><div class="summary-note">Evaluation has been published</div></div></td>
    </tr></table>
    <table class="summary-table" style="margin-top:8px"><tr><td><strong>Submission rate:</strong> {{ $summary['submission_rate_percent'] }}%</td><td><strong>Evaluation rate:</strong> {{ $summary['evaluation_rate_percent'] }}%</td><td><strong>Submitted total:</strong> {{ $summary['submitted_total'] }}</td></tr></table>

    @foreach([['Fields', $byField, 'Field'], ['Zones', $byZone, 'Zone']] as [$title, $rows, $groupHeading])
        <h2 style="margin-top:12px">Breakdown by {{ $title }}</h2>
        <table class="group-table">
            <thead><tr><th class="group">{{ $groupHeading }}</th><th class="total">Total</th><th class="status not-submitted">Not submitted</th><th class="status waiting">Submitted, not evaluated</th><th class="status evaluated">Evaluated</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                @php
                    $notSubmittedPeople = $row['not_submitted_people'];
                    $waitingPeople = $row['submitted_not_evaluated_people'];
                    $evaluatedPeople = $row['evaluated_people'];
                    $personRows = max(count($notSubmittedPeople), count($waitingPeople), count($evaluatedPeople), 1);
                @endphp
                @for($index = 0; $index < $personRows; $index++)
                    <tr>
                        <td class="group">{{ $index === 0 ? $row['Group'] : '' }}</td>
                        <td class="total">{{ $index === 0 ? $row['Stakeholders'] : '' }}</td>
                        <td class="status not-submitted">
                            @if($index === 0)<strong>{{ $row['not_submitted_count'] }}</strong>@endif
                            <div class="people">@if(isset($notSubmittedPeople[$index])){{ $index + 1 }}. {{ $notSubmittedPeople[$index] }}@elseif($index === 0)None @endif</div>
                        </td>
                        <td class="status waiting">
                            @if($index === 0)<strong>{{ $row['submitted_not_evaluated_count'] }}</strong>@endif
                            <div class="people">@if(isset($waitingPeople[$index])){{ $index + 1 }}. {{ $waitingPeople[$index] }}@elseif($index === 0)None @endif</div>
                        </td>
                        <td class="status evaluated">
                            @if($index === 0)<strong>{{ $row['evaluated_count'] }}</strong>@endif
                            <div class="people">@if(isset($evaluatedPeople[$index])){{ $index + 1 }}. {{ $evaluatedPeople[$index] }}@elseif($index === 0)None @endif</div>
                        </td>
                    </tr>
                @endfor
            @empty
                <tr><td colspan="5">No data available.</td></tr>
            @endforelse
            </tbody>
        </table>
    @endforeach

    <div class="footer">GSF Appraisal Statistics · This report reflects the filters and workflow status at the time of export.</div>
</body>
</html>
