<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>GSF - {{ $report->chapter->name }} Report for {{ date("F", mktime(0,0,0,$report->month,10)) . ', ' . $report->year }}</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 15px;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header img {
            width: 90px;
            margin-right: 15px;
        }
        .header .details {
            flex-grow: 1;
            text-align: center;
            line-height: 1.3;
        }
        h1 {
            margin: 0;
            font-size: 18px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12.5px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        .table th {
            /* background: #000;
            color: #fff; */
            text-transform: uppercase;
            text-align: inherit;
        }
        .section-title {
            background: #193085;
            font-weight: bold;
            padding: 6px;
            border: 1px solid #000;
            margin-top: 25px;
            color:white
        }
        .subsection-title {
            background: #0281cc;
            font-weight: bold;
            padding: 3px 6px;
            margin-top: 8px;
            border: 1px solid #ccc;
            color:white
        }
        .signatures {
            width: 100px;
            height: auto;
            margin-top: 5px;
        }
        .signature-block {
            width: 50%;
            display: inline-block;
            vertical-align: top;
            margin-top: 10px;
        }
        .signature-block span {
            display: block;
            font-weight: bold;
        }
        .actions {
            text-align: center;
            margin-top: 20px;
        }
        @media print {
            .actions { display: none; }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('frontend/img/logo.png') }}" alt="Logo">
            <div class="details">
                <h1>GOFAMINT STUDENTS’ FELLOWSHIP</h1>
                <strong>GOFAMINT International Headquarters, Aseese, Ogun State, Nigeria</strong>
                <p>MONTHLY REPORT for <strong>{{ $report->chapter->name }}</strong> — <strong>{{ date("F", mktime(0,0,0,$report->month,10)) . ', ' . $report->year }}</strong></p>
            </div>
        </div>
        <!-- Legend / Key -->
        <div class="section" style="margin-top: 15px; margin-bottom: 25px;">
            <div class="section-title" style="background: #f0f0f0; font-weight: bold; padding: 6px; border: 1px solid #ccc;">
                LEGEND / GUIDE
            </div>

            <table class="table" style="font-size: 10px; text-align: center;margin-bottom: 5px">
                <tbody>
                    <tr>
                        <td>1 - Poor</td>
                        <td>2 - Fair</td>
                        <td>3 - Good</td>
                        <td>4 - Very Good</td>
                        <td>5 - Excellent</td>
                    </tr>

                </tbody>
            </table>

            <p style="margin-top: 2px; font-size: 13px;">
                <strong>Note:</strong> The scale above applies where applicable.</strong>
            </p>
        </div>

        <!-- Report Sections -->
        @foreach($sections as $section)
            <div class="section">
                <div class="section-title">{{ $section->name }}</div>

                @foreach($section->subsections as $subsection)
                    <div class="subsection-title">{{ $subsection->name }}</div>

                    <table class="table">
                        <tbody>
                            @foreach($subsection->questions as $question)
                                @php
                                    $value = null;
                                    if(isset($report) && $report->answers) {
                                        $answer = $report->answers->firstWhere('question_id', $question->id);
                                        if($answer) {
                                            $decoded = json_decode($answer->answer_value, true);
                                            $value = is_array($decoded) ? $decoded : $answer->answer_value;
                                        }
                                    }
                                @endphp

                                @if(in_array($question->type, ['text','year','month','number','date','textarea','select']))
                                    <tr>
                                        <th width="60%">{{ $question->label }}</th>
                                        <td>{{ $value ?: '-' }}</td>
                                    </tr>
                                @elseif($question->type === 'dynamic_table' && is_array($value))
                                    <tr>
                                        <td colspan="2">
                                            <table class="table">
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
                                                                @php $colLabel = $col['label'] ?? $col; @endphp
                                                                <td>{{ $row[$colLabel] ?? '-' }}</td>
                                                            @endforeach
                                                        </tr>
                                                    @empty
                                                        <tr><td colspan="{{ count($question->options) }}" class="text-center">No data</td></tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @elseif($question->type === 'income_table')
                                    <tr>
                                        <td colspan="2">
                                            <table class="table">
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
                                                                <td>{{ $value[$week][$col] ?? '-' }}</td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        @endforeach

        <!-- Signatures -->
        <div class="section">
            <div class="section-title">SIGNATURES</div>
            @foreach(['president', 'gen_sec', 'fin_sec', 'evang_sec'] as $role)
                <div class="signature-block">
                    <span>{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                    @if(!empty($report->chapter->{$role . '_signature'}))
                        <img class="signatures" src="/stakeholdersignature/{{ $report->chapter->{$role . '_signature'} }}" alt="{{ $role }}">
                    @else
                        <p>No signature</p>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Actions -->
        <div class="actions">
            <a href="{{ route('stakeholders.dashboard') }}">Back</a> |
            <a href="javascript:window.print();">Print</a>
        </div>
    </div>
</body>
</html>
