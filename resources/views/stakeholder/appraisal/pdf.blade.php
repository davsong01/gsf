<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appraisal PDF</title>
    <style>
        @page {
            margin: 6mm 7mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8px;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 0;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #193085;
            margin-bottom: 3px;
            padding-bottom: 2px;
        }

        .header td {
            vertical-align: top;
        }

        .logo {
            width: 38px;
        }

        .title {
            font-size: 10px;
            font-weight: 700;
            color: #193085;
            margin: 0 0 1px 0;
        }

        .subtitle {
            font-size: 7px;
            color: #475569;
            margin: 0;
        }

        .meta {
            width: 100%;
            margin: 3px 0 4px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .meta td {
            padding: 1px 3px;
            border: 1px solid #d6e1ee;
            vertical-align: top;
        }

        .meta .label {
            background: #f8fbff;
            font-weight: 700;
            width: 18%;
        }

        .question-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 4px;
        }

        .question-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .section-row td,
        .subsection-row td {
            background: #eef5fb;
            color: #193085;
            padding: 2px 4px;
            font-weight: 700;
            border: 1px solid #c9d7ea;
        }

        .subsection-row td {
            background: #f8fbff;
            color: #102542;
            border-color: #d6e1ee;
        }

        .question-cell {
            width: 50%;
            vertical-align: top;
            border: 1px solid #d6e1ee;
            padding: 8px 2px !important;
            text-align: left !important;
            direction: ltr;
            unicode-bidi: plaintext;
        }

        .question-cell--full {
            width: 100%;
        }

        .question-title {
            font-weight: 700;
            color: #102542;
            line-height: 1.12;
            margin: 0 0 1px 0;
            padding: 0;
            text-align: left !important;
            display: block;
        }

        .question-value {
            line-height: 1.02;
            margin: 0 !important;
            padding: 0 !important;
            text-align: left !important;
            white-space: pre-wrap;
            display: block;
            width: 100%;
            direction: ltr;
            unicode-bidi: plaintext;
        }

        .question-value,
        .question-value * {
            text-align: left !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .question-value img {
            display: block;
            margin: 0 !important;
        }

        .question-value span,
        .question-value div {
            display: block;
        }

        .muted {
            color: #64748b;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 999px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .file-preview {
            margin-top: 1px;
            max-width: 68px;
            max-height: 28px;
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('frontend/img/logo.png');
    $logo = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    $stakeholderTitle = $target->office ?? $target->designation?->name ?? 'Officer';
    $selfBadge = ($selfStatus ?? 'draft') === 'published' ? 'badge-success' : 'badge-secondary';
    $evaluationBadge = ($evaluationStatus ?? 'draft') === 'published' ? 'badge-success' : 'badge-secondary';

    $resolveAnswerText = function ($question, $value) {
        if ($value === null || $value === '') {
            return '-';
        }

        $options = is_array($question->options ?? null) ? $question->options : [];

        $findLabel = function ($needle) use ($options) {
            foreach ($options as $option) {
                $optionValue = $option['value'] ?? null;
                $optionLabel = $option['label'] ?? $optionValue;

                if ((string) $optionValue === (string) $needle) {
                    return $optionLabel;
                }
            }

            return null;
        };

        if (is_array($value)) {
            $mapped = array_map(function ($item) use ($findLabel) {
                return $findLabel($item) ?? $item;
            }, $value);

            return implode(', ', array_filter($mapped, fn ($item) => $item !== null && $item !== ''));
        }

        return $findLabel($value) ?? $value;
    };

    $questionNumber = 1;

    $renderQuestionCell = function ($question, $value, bool $fullWidth = false) use (&$questionNumber, $resolveAnswerText) {
        $decodedPath = $question->type === 'file' && $value ? base64_decode($value, true) : null;
        $absolutePath = $decodedPath ? \Illuminate\Support\Facades\Storage::disk('protected_uploads')->path($decodedPath) : null;
        $isImage = $absolutePath && file_exists($absolutePath) && in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'], true);
        $label = $questionNumber++ . '. ' . $question->label;
        $class = 'question-cell' . ($fullWidth ? ' question-cell--full' : '');
        $colspan = $fullWidth ? ' colspan="2"' : '';
        $html = '<td class="' . $class . '" ' . $colspan . ' align="left" valign="top">';
        $html .= '<div class="question-title">' . e($label) . '</div>';
        $html .= '<div class="question-value">';

        if (($question->type ?? 'text') === 'file') {
            if ($isImage && $absolutePath) {
                $html .= '<img class="file-preview" src="data:image/' . e(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION))) . ';base64,' . e(base64_encode(file_get_contents($absolutePath))) . '" alt="' . e($question->label) . '">';
            } elseif ($value) {
                $html .= e(basename($decodedPath ?: $value));
            } else {
                $html .= '<span class="muted">No file uploaded</span>';
            }
        } else {
            $html .= e($resolveAnswerText($question, $value));
        }

        $html .= '</div></td>';

        return $html;
    };

    $renderAppraisalTable = function ($sections, $answers, $title) use (&$questionNumber, $renderQuestionCell) {
        $questionNumber = 1;
        if ($sections->isEmpty()) {
            return '';
        }

        $html = '<table class="question-table"><tbody>';
        $html .= '<tr class="section-row"><td colspan="2">' . e($title) . '</td></tr>';

        foreach ($sections as $section) {
            if ($sections->count() > 1) {
                $html .= '<tr class="section-row"><td colspan="2">' . e($section->name) . '</td></tr>';
            }

            foreach ($section->subsections as $subsection) {
                $questions = $subsection->questions;
                $pairedQuestions = $questions->filter(fn ($question) => ($question->type ?? 'text') !== 'textarea')->values();
                $textQuestions = $questions->filter(fn ($question) => ($question->type ?? 'text') === 'textarea')->values();
                $showSubsectionTitle = $section->subsections->count() > 1;

                if ($showSubsectionTitle) {
                    $html .= '<tr class="subsection-row"><td colspan="2">' . e($subsection->name) . '</td></tr>';
                }

                foreach ($pairedQuestions->chunk(2) as $pair) {
                    $pair = $pair->values();
                    $html .= '<tr>';
                    $first = $pair->get(0);
                    $second = $pair->get(1);
                    $html .= $renderQuestionCell($first, $answers->get($first?->slug)?->answer_value ?? null);
                    $html .= $second
                        ? $renderQuestionCell($second, $answers->get($second?->slug)?->answer_value ?? null)
                        : '<td class="question-cell" align="left" valign="top">&nbsp;</td>';
                    $html .= '</tr>';
                }

                foreach ($textQuestions as $question) {
                    $html .= '<tr>';
                    $html .= $renderQuestionCell($question, $answers->get($question->slug)?->answer_value ?? null, true);
                    $html .= '</tr>';
                }
            }
        }

        $html .= '</tbody></table>';

        return $html;
    };
@endphp

<div class="container">
    <table class="header">
        <tr>
            <td style="width: 88px;">
                @if($logo)
                    <img class="logo" src="data:image/png;base64,{{ $logo }}" alt="Logo">
                @endif
            </td>
            <td>
                <div class="title">GOFAMINT STUDENTS' FELLOWSHIP APPRAISAL</div>
                <p class="subtitle">Appraisal and evaluation summary | Printed: {{ now()->format('d M, Y h:i A') }}</p>
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td class="label">Designation</td>
            <td>{{ $target->designation?->name ?? 'Officer' }}</td>
            <td class="label">Stakeholder</td>
            <td>{{ $target->name }}</td>
        </tr>
        <tr>
            <td class="label">Stakeholder Title</td>
            <td>{{ $stakeholderTitle }}</td>
            <td class="label">Evaluation Authority</td>
            <td>{{ $evaluationAuthorityLabel ?? 'Evaluator' }}</td>
        </tr>
        <tr>
            <td class="label">Self Status</td>
            <td><span class="badge {{ $selfBadge }}">{{ $selfStatus ?? 'draft' }}</span></td>
            <td class="label">Evaluation Status</td>
            <td><span class="badge {{ $evaluationBadge }}">{{ $evaluationStatus ?? 'draft' }}</span></td>
        </tr>
    </table>

    {!! $renderAppraisalTable($selfSections, $selfAnswers, 'Self Appraisal') !!}
    {!! $renderAppraisalTable($evaluationSections, $evaluationAnswers, 'Evaluation Responses') !!}
</div>
</body>
</html>
{{-- {{dd('kk')}} --}}
