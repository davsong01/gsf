<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>GSF — {{ $report->chapter->name }} Report for {{ date('F',mktime(0,0,0,$report->month,10)) }}, {{ $report->year }}</title>
    <style>
        @page { margin: 28px 32px 36px; }
        * { box-sizing: border-box; }
        body { margin:0; color:#29384a; font-family:DejaVu Sans,Arial,sans-serif; font-size:10px; line-height:1.5; }
        .page-footer { position:fixed; right:0; bottom:-22px; left:0; color:#89929d; font-size:8px; text-align:center; }
        .page-footer .page:after { content:counter(page); }
        .document-header { width:100%; margin-bottom:14px; padding:15px; border:0; border-radius:8px; color:#fff; background:#173b60; }
        .document-header td { border:0; vertical-align:middle; }
        .document-header__logo { width:72px; padding-right:14px; }
        .document-header__logo img { width:60px; height:auto; }
        .document-eyebrow { margin:0 0 3px; color:#ebc981; font-size:8px; font-weight:bold; letter-spacing:1.1px; text-transform:uppercase; }
        .document-title { margin:0 0 3px; color:#fff; font-size:18px; line-height:1.2; }
        .document-subtitle { margin:0; color:#d9e8ef; font-size:10px; }
        .summary-table { width:100%; margin:0 0 12px; border-collapse:separate; border-spacing:5px 0; }
        .summary-table td { width:25%; padding:8px 9px; border:1px solid #e4d8c8; border-radius:6px; background:#fff9ef; }
        .summary-table span,.summary-table strong { display:block; }
        .summary-table span { color:#806d58; font-size:7px; font-weight:bold; letter-spacing:.7px; text-transform:uppercase; }
        .summary-table strong { margin-top:2px; color:#303d4c; font-size:9px; }
        .status-table { width:100%; margin:0 0 14px; border-collapse:collapse; }
        .status-table td { width:33.33%; padding:7px 9px; border:1px solid #dfe5e8; background:#fafbfc; }
        .status-table b { display:block; margin-bottom:3px; color:#6e7a89; font-size:7px; letter-spacing:.7px; text-transform:uppercase; }
        .status { display:inline-block; padding:2px 7px; border-radius:10px; font-size:7px; font-weight:bold; }
        .approved { color:#285d42; background:#e6f2e9; }.rejected { color:#8b3832; background:#fae8e5; }.pending { color:#75571c; background:#fff0cd; }
        .school-notice { margin:14px 0; padding:18px; border:1px solid #e4c36e; border-radius:7px; color:#6f511c; background:#fff5dc; text-align:center; }
        .school-notice strong { display:block; margin-bottom:4px; font-size:14px; }
        .report-section { margin:0 0 13px; border:1px solid #e8dac5; border-radius:7px; page-break-inside:auto; background:#fffaf2; }
        .section-title { padding:9px 11px; color:#403226; background:#efd9b7; font-size:11px; font-weight:bold; }
        .section-title .number { display:inline-block; margin-right:7px; padding:3px 6px; border-radius:4px; color:#fff; background:#936536; font-size:8px; }
        .subsection { margin:8px; padding:8px; border:1px solid #eadfd3; border-radius:5px; background:#fff; page-break-inside:avoid; }
        .subsection-title { margin:0 0 6px; padding-bottom:5px; border-bottom:2px solid #c98e4c; color:#584330; font-size:9px; font-weight:bold; }
        .answers { width:100%; margin:0; border-collapse:collapse; }
        .answers th,.answers td { padding:6px 7px; border:1px solid #e7e2dc; vertical-align:top; overflow-wrap:anywhere; }
        .answers th { width:39%; color:#5f6c7b; background:#faf8f4; font-size:8px; text-align:left; }
        .answers td { color:#29384a; background:#fff; font-size:8px; }
        .data-table { width:100%; margin:2px 0; border-collapse:collapse; }
        .data-table th,.data-table td { padding:4px; border:1px solid #dfd7ce; font-size:7px; text-align:left; }
        .data-table th { color:#604a34; background:#f7ead8; text-transform:uppercase; }
        .data-table tfoot th { background:#f0dbb9; }
        .signatures { margin-top:15px; padding:10px; border:1px solid #dfe4e7; border-radius:7px; page-break-inside:avoid; }
        .signatures h2 { margin:0 0 8px; color:#344253; font-size:11px; }
        .signature-table { width:100%; border-collapse:separate; border-spacing:5px; }
        .signature-table td { width:25%; height:75px; padding:7px; border:1px dashed #cfd8de; border-radius:5px; vertical-align:top; background:#fafbfc; }
        .signature-table b { display:block; color:#6e7987; font-size:7px; text-transform:uppercase; }
        .signature-table img { display:block; max-width:85px; max-height:45px; margin-top:6px; }
        .signature-table em { display:block; margin-top:12px; color:#979fa8; font-size:7px; font-style:normal; }
    </style>
</head>
<body>
@php
    $monthName=date('F',mktime(0,0,0,(int)$report->month,10));
    $logoPath=public_path('frontend/img/logo.png');
    $logoData=file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
    $statusMeta=fn($status)=>$status==1?['Approved','approved']:($status==2?['Rejected','rejected']:['Pending','pending']);
@endphp
<div class="page-footer">GOFAMINT Students’ Fellowship · Monthly Report · Page <span class="page"></span></div>
<table class="document-header"><tr><td class="document-header__logo">@if($logoData)<img src="{{ $logoData }}" alt="GSF logo">@endif</td><td><p class="document-eyebrow">GOFAMINT Students’ Fellowship</p><h1 class="document-title">Monthly Fellowship Report</h1><p class="document-subtitle">{{ $report->chapter->name }} · {{ $monthName }}, {{ $report->year }}</p></td></tr></table>
<table class="summary-table"><tr><td><span>Report reference</span><strong>#{{ $report->id }}</strong></td><td><span>Reporting period</span><strong>{{ $monthName }}, {{ $report->year }}</strong></td><td><span>Session</span><strong>{{ $report->session ?: 'Not specified' }}</strong></td><td><span>Last updated</span><strong>{{ $report->updated_at?->format('d M Y') }}</strong></td></tr></table>
<table class="status-table"><tr>
@foreach(['Zone'=>$report->zone_status,'Field'=>$report->field_status,'National'=>$report->national_status] as $label=>$status)
    @php
        [$statusLabel, $statusClass] = $statusMeta($status);
    @endphp
    <td><b>{{ $label }} review</b><span class="status {{ $statusClass }}">{{ $statusLabel }}</span></td>
@endforeach
</tr></table>

@if($report->school_not_in_session)
    <div class="school-notice"><strong>School not in session</strong>No activity sections were submitted for this reporting period.</div>
@else
    @foreach($sections as $section)
        <div class="report-section"><div class="section-title"><span class="number">{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span>{{ $section->name }}</div>
        @foreach($section->subsections as $subsection)
            <div class="subsection"><div class="subsection-title">{{ $subsection->name }}</div><table class="answers"><tbody>
            @foreach($subsection->questions as $question)
                @php
                    $answer=$report->answers->firstWhere('question_id',$question->id);
                    $decoded=$answer ? json_decode($answer->answer_value,true) : null;
                    $value=$answer ? (json_last_error()===JSON_ERROR_NONE ? $decoded : $answer->answer_value) : null;
                @endphp
                @if($question->type==='dynamic_table' && is_array($value))
                    <tr><th>{{ $question->label }}</th><td><table class="data-table">
                        <thead><tr>@foreach($question->options as $col)<th>{{ $col['label'] ?? $col }}</th>@endforeach</tr></thead>
                        <tbody>@forelse($value as $row)<tr>
                            @foreach($question->options as $col)
                                @php
                                    $key = $col['label'] ?? $col;
                                @endphp
                                <td>{{ $row[$key] ?? '—' }}</td>
                            @endforeach
                        </tr>@empty<tr><td colspan="{{ count($question->options) }}">No entries</td></tr>@endforelse</tbody>
                    </table></td></tr>
                @elseif($question->type==='income_table')
                    @php
                        $totals = collect($question->options['columns'])
                            ->reject(fn ($col) => $col === 'Remarks')
                            ->mapWithKeys(fn ($col) => [$col => 0])
                            ->all();
                    @endphp
                    <tr><th>{{ $question->label }}</th><td><table class="data-table">
                        <thead><tr><th>Week</th>@foreach($question->options['columns'] as $col)<th>{{ $col }}</th>@endforeach</tr></thead>
                        <tbody>@foreach($question->options['rows'] as $week)<tr><td>{{ $week }}</td>
                            @foreach($question->options['columns'] as $col)
                                @php
                                    $cell = $value[$week][$col] ?? null;
                                    if ($col !== 'Remarks' && is_numeric($cell)) {
                                        $totals[$col] += $cell;
                                    }
                                @endphp
                                <td>{{ $cell ?? '—' }}</td>
                            @endforeach
                        </tr>@endforeach</tbody>
                        <tfoot><tr><th>Totals</th>@foreach($question->options['columns'] as $col)<th>{{ $totals[$col] ?? '—' }}</th>@endforeach</tr></tfoot>
                    </table></td></tr>
                @elseif($question->type==='file')
                    <tr><th>{{ $question->label }}</th><td>{{ $value ? 'Attachment submitted' : '—' }}</td></tr>
                @else
                    <tr><th>{{ $question->label }}</th><td>{{ filled($value) ? (is_array($value) ? json_encode($value) : $value) : '—' }}</td></tr>
                @endif
            @endforeach
            </tbody></table></div>
        @endforeach
        </div>
    @endforeach
@endif

<div class="signatures"><h2>Chapter Signatures</h2><table class="signature-table"><tr>
@foreach(['president'=>'President','gen_sec'=>'General Secretary','fin_sec'=>'Financial Secretary','evang_sec'=>'Evangelism Secretary'] as $role=>$label)
    @php
        $signatureName=$report->chapter->{$role.'_signature'};
        $signaturePath=$signatureName ? public_path('stakeholdersignature/'.$signatureName) : null;
        $signatureData=$signaturePath && file_exists($signaturePath) ? 'data:image/'.pathinfo($signaturePath,PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($signaturePath)) : null;
    @endphp
    <td><b>{{ $label }}</b>@if($signatureData)<img src="{{ $signatureData }}" alt="{{ $label }} signature">@else<em>No signature submitted</em>@endif</td>
@endforeach
</tr></table></div>
</body>
</html>
