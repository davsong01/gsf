<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GSF Monthly Report Status</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 11px;
            color: #666;
        }

        .section-title {
            margin-top: 20px;
            font-size: 13px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }

        .status-title {
            margin-top: 10px;
            font-weight: bold;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 12px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f5f5f5;
        }

        .empty {
            font-style: italic;
            color: #999;
            margin: 5px 0;
        }
    </style>
</head>

<body>

<div class="header">
    <div class="title">GSF MONTHLY REPORT SUBMISSION STATUS</div>
    <div class="subtitle">{{ strtoupper($type ?? '') }}</div>
    <div class="subtitle">{{ $from ?? '-' }} - {{ $to ?? '-' }}</div>
</div>

@php
    function formatMonthSafe($m)
    {
        try {
            if (!$m) return '-';

            // strict Y-m format
            if (preg_match('/^\d{4}-\d{2}$/', $m)) {
                return \Carbon\Carbon::createFromFormat('Y-m', $m)->format('F Y');
            }

            // fallback parsing
            return \Carbon\Carbon::parse($m)->format('F Y');
        } catch (\Throwable $e) {
            return $m;
        }
    }

    function renderTable($data) {
        if (empty($data)) {
            return '<div class="empty">No records found</div>';
        }

        $html = '<table>';
        $html .= '<thead><tr><th>Field</th><th>Campus</th><th>Months</th></tr></thead><tbody>';

        foreach ($data as $field => $chapters) {
            foreach ($chapters as $chapter => $months) {
                
                $monthText = is_array($months)
                    ? implode(', ', array_map(fn($m) => formatMonthSafe($m), $months))
                    : formatMonthSafe($months);

                $html .= "<tr>
                    <td>{$field}</td>
                    <td>{$chapter}</td>
                    <td>{$monthText}</td>
                </tr>";
            }
        }

        $html .= '</tbody></table>';

        return $html;
    }
@endphp

{{-- ========================= --}}
{{-- NATIONAL --}}
{{-- ========================= --}}
<div class="section-title">1. NATIONAL REPORTS</div>

<div class="status-title">Approved</div>
{!! renderTable($nationallyApproved) !!}

<div class="status-title">Pending</div>
{!! renderTable($nationalPending) !!}

<div class="status-title">Rejected</div>
{!! renderTable($nationalDeclined) !!}

{{-- ========================= --}}
{{-- ZONE --}}
{{-- ========================= --}}
<div class="section-title">2. ZONE REPORTS</div>

<div class="status-title">Approved</div>
{!! renderTable($zoneApproved) !!}

<div class="status-title">Pending</div>
{!! renderTable($pendingZoneApproval) !!}

<div class="status-title">Rejected</div>
{!! renderTable($zoneDeclined) !!}

{{-- ========================= --}}
{{-- FIELD --}}
{{-- ========================= --}}
<div class="section-title">3. FIELD REPORTS</div>

<div class="status-title">Approved</div>
{!! renderTable($fieldApproved) !!}

<div class="status-title">Pending</div>
{!! renderTable($pendingFieldApproval) !!}

<div class="status-title">Rejected</div>
{!! renderTable($fieldDeclined) !!}

{{-- ========================= --}}
{{-- MONTHS YET TO SUBMIT --}}
{{-- ========================= --}}
<div class="section-title">4. DEFAULTERS</div>

{!! renderTable($monthsYetToSubmit) !!}

{{-- ========================= --}}
{{-- NEVER SUBMITTED --}}
{{-- ========================= --}}
{{-- <div class="section-title">5. CAMPUSES YET TO SUBMIT ANY REPORT</div>
{!! renderTable($neverSubmitted) !!} --}}

</body>
</html>
{{dd('sd')}}