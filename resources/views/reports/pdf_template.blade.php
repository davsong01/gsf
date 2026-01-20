<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>GSF - {{ $report->chapter->name }} Report for {{ date("F", mktime(0,0,0,$report->month,10)) . ', ' . $report->year }}</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px;
            position: relative;
            z-index: 1;
        }
        /* Watermark */
        .watermark {
            position: fixed;
            top: 40%;
            left: 20%;
            width: 60%;
            text-align: center;
            font-size: 22px;
            color: rgba(0,0,0,0.1); /* light but visible */
            transform: rotate(-30deg);
            z-index: 10; /* higher than container */
            pointer-events: none;
        }
        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: top;
        }
        .header-table img {
            width: 80px;
            height: auto;
        }
        .header-details h1 {
            margin: 0;
            font-size: 16px;
        }
        .header-details p, .status-info {
            margin: 2px 0;
            font-size: 12px;
        }
        .status-info span {
            display: inline-block;
            padding: 2px 5px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
            color: #fff;
        }
        .approved { background-color: #28a745; }
        .rejected { background-color: #dc3545; }
        .pending  { background-color: #ffc107; color: #000; }
        /* Sections */
        .section-title {
            background: #eee;
            font-weight: bold;
            padding: 5px 8px;
            margin-top: 15px;
            border: 1px solid #000;
        }
        .subsection-title {
            background: #f7f7f7;
            font-weight: bold;
            padding: 3px 6px;
            margin-top: 8px;
            border: 1px solid #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0 10px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
            font-size: 12px;
        }
        th { background: #f0f0f0; }
        tfoot th { background: #ddd; font-weight: bold; }
        .signature-block {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-top: 15px;
        }
        .signature-block img {
            width: 100px;
            height: auto;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="watermark">GOFAMINT STUDENTS' FELLOWSHIP</div>

    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 90px;">
                    <img src="{{ public_path('frontend/img/logo.png') }}" alt="Logo">
                </td>
                <td class="header-details" style="padding-left: 10px;">
                    <h1>GOFAMINT STUDENTS’ FELLOWSHIP</h1>
                    <strong>International Headquarters, Aseese, Ogun State</strong>
                    <p>MONTHLY REPORT — <strong>{{ $report->chapter->name }}</strong><br>
                    {{ date("F", mktime(0,0,0,$report->month,10)) . ', ' . $report->year }}</p>
                    <p>LAST UPDATED — <strong>{{ $report->updated_at->format('d M Y H:i') }}</strong></p>

                    {{-- Status Info --}}
                    @php
                        $statuses = [
                            'Zone' => $report->zone_status,
                            'Field' => $report->field_status,
                            'National' => $report->national_status
                        ];
                    @endphp
                    <div class="status-info">
                        @foreach($statuses as $key => $status)
                            <strong>{{ $key }}:</strong>
                            @if($status == 1)
                                <span class="approved">Approved</span>
                            @elseif($status == 2)
                                <span class="rejected">Rejected</span>
                            @else
                                <span class="pending">Pending</span>
                            @endif
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>

        <!-- Sections -->
        @foreach($sections as $section)
            <div class="section-title">{{ $section->name }}</div>

            @foreach($section->subsections as $subsection)
                <div class="subsection-title">{{ $subsection->name }}</div>

                <table>
                    <tbody>
                        @foreach($subsection->questions as $question)
                           @php
                                $answer = $report->answers->firstWhere('question_id', $question->id);

                                $value = $answer
                                    ? (($answer->question_label ?? $answer->answer_value) ?? json_decode($answer->answer_value, true))
                                    : '-';
                            @endphp


                            <!-- Simple Inputs -->
                            @if(in_array($question->type, ['text','number','date','year','month','textarea','select']))
                                <tr>
                                    <th width="60%">{{ $question->label ?? $question->label  }}</th>
                                    <td>{{ $value ?: '-' }}</td>
                                </tr>
                            @endif

                            <!-- Dynamic Table -->
                            @if($question->type === 'dynamic_table')
                                @php
                                    $value = $answer->answer_value ? json_decode($answer->answer_value, true) : [];
                                @endphp
                                <tr>
                                    <td colspan="2">
                                        <table>
                                            <thead>
                                                <tr>
                                                    @foreach($question->options as $col)
                                                        <th>{{ $col['label'] ?? $col }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($value as $row)
                                                    <tr>
                                                        @foreach($question->options as $col)
                                                            @php $key = $col['label'] ?? $col; @endphp
                                                            <td>
                                                                {{ $row[$key] ?? '-' }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ count($question->options) }}">No data</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif

                            <!-- Income Table -->
                            @if($question->type === 'income_table')
                                @php
                                    $value = $answer->answer_value ? json_decode($answer->answer_value, true) : [];

                                    $totals = [];
                                    foreach ($question->options['columns'] as $col) {
                                        if ($col !== 'Remarks') $totals[$col] = 0;
                                    }
                                @endphp
                                <tr>
                                    <td colspan="2">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Week</th>
                                                    @foreach($question->options['columns'] as $col)
                                                        <th>{{ $col }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($question->options['rows'] as $week)
                                                    <tr>
                                                        <td>{{ $week }}</td>
                                                        @foreach($question->options['columns'] as $col)
                                                            @php
                                                                $cell = $value[$week][$col] ?? null;
                                                                if ($col !== 'Remarks' && is_numeric($cell)) $totals[$col] += $cell;
                                                            @endphp
                                                            <td>{{ $cell ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Totals</th>
                                                    @foreach($question->options['columns'] as $col)
                                                        <th>{{ $totals[$col] ?? '-' }}</th>
                                                    @endforeach
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endforeach

        <!-- Signatures -->
        {{-- <div class="section-title">SIGNATURES</div>
        @foreach(['president','gen_sec','fin_sec','evang_sec'] as $role)
            <div class="signature-block">

                @if(!empty($report->chapter->{$role.'_signature'}))
                    <strong>{{ strtoupper(str_replace('_',' ', $role)) }}</strong><br>

                    <img src="{{route('protected.download', ['file' => $report->chapter->{$role.'_signature'}]) }}">
                @else
                    <em>No signature</em>
                @endif
            </div>
        @endforeach --}}
    </div>
</body>
</html>
