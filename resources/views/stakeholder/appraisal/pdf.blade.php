<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GSF Appraisal — {{ $target->name }}</title>
    <style>
        @page { margin: 28px 32px 38px; }
        * { box-sizing: border-box; }
        body { margin:0; color:#29384a; font-family:DejaVu Sans,Arial,sans-serif; font-size:9px; line-height:1.5; }
        .page-footer { position:fixed; right:0; bottom:-23px; left:0; color:#89929d; font-size:8px; text-align:center; }
        .page-footer .page:after { content:counter(page); }
        .document-header { width:100%; margin-bottom:13px; padding:14px; border:0; border-radius:8px; color:#fff; background:#173b60; }
        .document-header td { border:0; vertical-align:middle; }
        .document-header__logo { width:70px; padding-right:12px; }
        .document-header__logo img { width:56px; height:auto; }
        .document-eyebrow { margin:0 0 3px; color:#ebc981; font-size:8px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; }
        .document-title { margin:0 0 3px; color:#fff; font-size:18px; line-height:1.2; }
        .document-subtitle { margin:0; color:#d9e8ef; font-size:9px; }
        .summary-table { width:100%; margin:0 0 10px; border-collapse:separate; border-spacing:4px 0; }
        .summary-table td { width:33.33%; padding:8px 9px; border:1px solid #e4d8c8; border-radius:6px; background:#fff9ef; vertical-align:top; }
        .summary-table span,.summary-table strong { display:block; }
        .summary-table span { color:#806d58; font-size:7px; font-weight:bold; letter-spacing:.7px; text-transform:uppercase; }
        .summary-table strong { margin-top:3px; color:#303d4c; font-size:9px; line-height:1.35; overflow-wrap:anywhere; }
        .status-table { width:100%; margin:0 0 15px; border-collapse:collapse; }
        .status-table td { width:50%; padding:8px 10px; border:1px solid #dfe5e8; background:#fafbfc; }
        .status-table b { display:block; margin-bottom:4px; color:#6e7a89; font-size:7px; letter-spacing:.7px; text-transform:uppercase; }
        .status { display:inline-block; padding:3px 8px; border-radius:10px; font-size:7px; font-weight:bold; text-transform:uppercase; }
        .status--published { color:#285d42; background:#e6f2e9; }
        .status--draft { color:#75571c; background:#fff0cd; }
        .report-part { margin:0 0 16px; page-break-inside:auto; }
        .part-title { margin:0 0 9px; padding:9px 11px; border-radius:6px; color:#fff; background:#1c496c; font-size:12px; font-weight:bold; page-break-after:avoid; }
        .part-title .number { display:inline-block; margin-right:8px; padding:2px 6px; border-radius:4px; color:#2f3c4b; background:#ebc981; font-size:8px; }
        .report-section { margin:0 0 11px; border:1px solid #e8dac5; border-radius:7px; background:#fffaf2; page-break-inside:auto; }
        .section-title { padding:8px 10px; color:#403226; background:#efd9b7; font-size:10px; font-weight:bold; page-break-after:avoid; }
        .section-title .number { display:inline-block; margin-right:7px; padding:2px 6px; border-radius:4px; color:#fff; background:#936536; font-size:8px; }
        .subsection { margin:8px; padding:8px; border:1px solid #eadfd3; border-radius:5px; background:#fff; page-break-inside:auto; }
        .subsection-title { margin:0 0 7px; padding-bottom:5px; border-bottom:2px solid #c98e4c; color:#584330; font-size:9px; font-weight:bold; page-break-after:avoid; }
        .answers { width:100%; margin:0; border-collapse:collapse; }
        .answers tr { page-break-inside:avoid; }
        .answers th,.answers td { padding:7px 8px; border:1px solid #e7e2dc; vertical-align:top; overflow-wrap:anywhere; }
        .answers th { width:39%; color:#5f6c7b; background:#faf8f4; font-size:8px; font-weight:bold; text-align:left; }
        .answers td { color:#29384a; background:#fff; font-size:8px; white-space:pre-wrap; }
        .answers .empty { color:#89929d; font-style:italic; }
        .file-preview { display:block; max-width:150px; max-height:100px; margin:3px 0; }
        .comment-card { margin-top:12px; padding:10px 12px; border:1px solid #d9e7e2; border-radius:7px; background:#f7fbf9; page-break-inside:avoid; }
        .comment-card h2 { margin:0 0 6px; color:#275c56; font-size:10px; }
        .comment-card p { margin:0; color:#374b56; font-size:9px; white-space:pre-wrap; }
        .empty-notice { margin:8px 0; padding:11px; border:1px solid #e4e8eb; border-radius:5px; color:#7c8997; background:#fafbfc; }
    </style>
</head>
<body>
@php
    $logoPath = public_path('frontend/img/logo.png');
    $logoData = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
    $stakeholderTitle = $target->office ?? $target->designation?->name ?? 'Officer';

    $resolveAnswerText = function ($question, $value) {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            }
        }

        $options = is_array($question->options ?? null) ? $question->options : [];
        $findLabel = function ($needle) use ($options) {
            foreach ($options as $option) {
                $optionValue = $option['value'] ?? $option['label'] ?? $option;
                $optionLabel = $option['label'] ?? $option['value'] ?? $option;
                if ((string) $optionValue === (string) $needle) {
                    return $optionLabel;
                }
            }
            return null;
        };

        if (is_array($value)) {
            $mapped = array_map(fn ($item) => is_array($item) ? json_encode($item) : ($findLabel($item) ?? $item), $value);
            return implode(', ', array_filter($mapped, fn ($item) => $item !== null && $item !== ''));
        }

        return $findLabel($value) ?? $value;
    };

    $renderAnswerRow = function ($question, $value, int $number) use ($resolveAnswerText) {
        $html = '<tr><th>' . e($number.'. '.$question->label) . '</th><td>';
        if (($question->type ?? 'text') === 'file') {
            $decodedPath = $value ? base64_decode($value, true) : null;
            $absolutePath = $decodedPath ? \Illuminate\Support\Facades\Storage::disk('protected_uploads')->path($decodedPath) : null;
            $isImage = $absolutePath && file_exists($absolutePath) && in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['jpg','jpeg','png'], true);
            if ($isImage) {
                $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
                $html .= '<img class="file-preview" src="data:image/'.e($extension).';base64,'.e(base64_encode(file_get_contents($absolutePath))).'" alt="'.e($question->label).'">';
            } elseif ($value) {
                $html .= e(basename($decodedPath ?: $value));
            } else {
                $html .= '<span class="empty">No file uploaded</span>';
            }
        } else {
            $answer = $resolveAnswerText($question, $value);
            $html .= $answer === null || $answer === '' ? '<span class="empty">No response</span>' : e($answer);
        }
        return $html.'</td></tr>';
    };

    $renderAppraisalSections = function ($sections, $answers, string $title, string $partNumber) use ($renderAnswerRow) {
        $html = '<div class="report-part"><div class="part-title"><span class="number">'.e($partNumber).'</span>'.e($title).'</div>';
        if ($sections->isEmpty()) {
            return $html.'<div class="empty-notice">No sections are available for this appraisal.</div></div>';
        }

        $questionNumber = 1;
        $sectionNumber = 0;
        foreach ($sections as $section) {
            $sectionNumber++;
            $sectionName = trim((string) ($section->name ?? ''));
            $html .= '<div class="report-section"><div class="section-title"><span class="number">'.str_pad((string) $sectionNumber,2,'0',STR_PAD_LEFT).'</span>'.e($sectionName).'</div>';
            foreach ($section->subsections as $subsection) {
                $subsectionName = trim((string) ($subsection->name ?? ''));
                $showSubsectionTitle = $subsectionName !== '' && strcasecmp($sectionName, $subsectionName) !== 0;
                $html .= '<div class="subsection">';
                if ($showSubsectionTitle) {
                    $html .= '<div class="subsection-title">'.e($subsectionName).'</div>';
                }
                $html .= '<table class="answers"><tbody>';
                foreach ($subsection->questions as $question) {
                    $html .= $renderAnswerRow($question, $answers->get($question->slug)?->answer_value ?? null, $questionNumber++);
                }
                $html .= '</tbody></table></div>';
            }
            $html .= '</div>';
        }
        return $html.'</div>';
    };
@endphp

<div class="page-footer">GOFAMINT Students’ Fellowship · Stakeholder Appraisal · Page <span class="page"></span></div>
<table class="document-header"><tr><td class="document-header__logo">@if($logoData)<img src="{{ $logoData }}" alt="GSF logo">@endif</td><td><p class="document-eyebrow">GOFAMINT Students’ Fellowship</p><h1 class="document-title">Stakeholder Appraisal</h1><p class="document-subtitle">{{ $target->name }} · Printed {{ now()->format('d M Y, h:i A') }}</p></td></tr></table>

<table class="summary-table"><tr>
    <td><span>Officer</span><strong>{{ $target->name }}</strong></td>
    <td><span>Designation</span><strong>{{ $target->designation?->name ?? 'Officer' }}</strong></td>
    <td><span>Stakeholder title</span><strong>{{ $stakeholderTitle }}</strong></td>
</tr></table>

<table class="status-table"><tr>
    <td><b>Self appraisal</b><span class="status {{ ($selfStatus ?? 'draft') === 'published' ? 'status--published' : 'status--draft' }}">{{ $selfStatus ?? 'draft' }}</span></td>
    <td><b>Evaluation · {{ $evaluationAuthorityLabel ?? 'Evaluator' }}</b><span class="status {{ ($evaluationStatus ?? 'draft') === 'published' ? 'status--published' : 'status--draft' }}">{{ $evaluationStatus ?? 'draft' }}</span></td>
</tr></table>

{!! $renderAppraisalSections($selfSections, $selfAnswers, 'Self appraisal', '01') !!}
{!! $renderAppraisalSections($evaluationSections, $evaluationAnswers, 'Evaluation responses', '02') !!}

@php($finalComment = $evaluationAnswers->get(\App\Services\AppraisalService::FINAL_COMMENT_SLUG)?->answer_value ?? '')
<div class="comment-card"><h2>Final comment</h2><p>{{ $finalComment !== '' ? $finalComment : 'No final comment provided.' }}</p></div>
</body>
</html>
