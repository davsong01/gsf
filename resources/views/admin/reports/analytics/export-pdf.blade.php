@php
    function countRows($data): int
    {
        $count = 0;

        foreach ($data as $field => $chapters) {
            $count += is_array($chapters) ? count($chapters) : 1;
        }

        return $count;
    }
@endphp
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

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
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

{{-- ========================= --}}
{{-- HEADER (FIXED FOR ANALYTICS CONTEXT) --}}
{{-- ========================= --}}
<table class="header-table">
    <tr>
        <td style="width: 90px; vertical-align: top;">
            @php
                $logoPath = public_path('frontend/img/logo.png');
                $logo = file_exists($logoPath)
                    ? base64_encode(file_get_contents($logoPath))
                    : null;
            @endphp

            @if($logo)
                <img src="data:image/png;base64,{{ $logo }}" width="80">
            @endif
        </td>

        <td style="padding-left: 10px; vertical-align: top;">
            <div class="title">GOFAMINT STUDENTS’ FELLOWSHIP</div>

            <div class="subtitle">
                International Headquarters, Ogunmakin, Lagos–Ibadan Expressway, Ogun State
            </div>

            <div style="margin-top: 6px;">
                <strong>REPORT TYPE:</strong>
                {{ strtoupper($type ?? 'GSF REPORT') }}
            </div>

            <div>
                <strong>PERIOD:</strong>
                {{ $from ? \Carbon\Carbon::parse($from)->format('F Y') : '-' }}
                -
                {{ $to ? \Carbon\Carbon::parse($to)->format('F Y') : '-' }}
            </div>

            <div>
                <strong>GENERATED:</strong>
                {{ now()->format('d M Y H:i') }}
            </div>
        </td>
    </tr>
</table>

@php
    function formatMonthSafe($m)
    {
        try {
            if (!$m) return '-';

            if (preg_match('/^\d{4}-\d{2}$/', $m)) {
                return \Carbon\Carbon::createFromFormat('Y-m', $m)->format('F Y');
            }

            return \Carbon\Carbon::parse($m)->format('F Y');
        } catch (\Throwable $e) {
            return $m;
        }
    }

    function renderTable($data)
    {
        if (empty($data)) {
            return '<div class="empty">No records found</div>';
        }

        $html = '<table>';
        $html .= '<thead>
                    <tr>
                        <th>Field</th>
                        <th>Campus</th>
                        <th>Months</th>
                    </tr>
                  </thead><tbody>';

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

<div class="section-title">1. NATIONAL REPORTS</div>

<div class="status-title">Approved ({{ countRows($nationallyApproved) }})</div>
{!! renderTable($nationallyApproved) !!}

<div class="status-title">Pending ({{ countRows($nationalPending) }})</div>
{!! renderTable($nationalPending) !!}

<div class="status-title">Rejected ({{ countRows($nationalDeclined) }})</div>
{!! renderTable($nationalDeclined) !!}


<div class="section-title">2. ZONE REPORTS</div>

<div class="status-title">Approved ({{ countRows($zoneApproved) }})</div>
{!! renderTable($zoneApproved) !!}

<div class="status-title">Pending ({{ countRows($pendingZoneApproval) }})</div>
{!! renderTable($pendingZoneApproval) !!}

<div class="status-title">Rejected ({{ countRows($zoneDeclined) }})</div>
{!! renderTable($zoneDeclined) !!}


<div class="section-title">3. FIELD REPORTS</div>

<div class="status-title">Approved ({{ countRows($fieldApproved) }})</div>
{!! renderTable($fieldApproved) !!}

<div class="status-title">Pending ({{ countRows($pendingFieldApproval) }})</div>
{!! renderTable($pendingFieldApproval) !!}

<div class="status-title">Rejected ({{ countRows($fieldDeclined) }})</div>
{!! renderTable($fieldDeclined) !!}


<div class="section-title">
    4. DEFAULTERS ({{ countRows($monthsYetToSubmit) }})
</div>

{!! renderTable($monthsYetToSubmit) !!}
</body>
</html>