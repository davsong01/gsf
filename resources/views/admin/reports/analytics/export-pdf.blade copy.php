<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GSF Monthly Report Status</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }

        .section {
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }

        .field-title {
            font-weight: bold;
            margin-top: 10px;
        }

        ul {
            margin: 5px 0 10px 20px;
        }

        li {
            margin-bottom: 3px;
        }

        .empty {
            color: #999;
            font-style: italic;
        }
    </style>
</head>

<body>

<div class="header">
    <div class="title">GSF MONTHLY REPORT SUBMISSION STATUS</div>

    <div class="subtitle">
        {{ strtoupper($type ?? '') }}
    </div>

    <div class="subtitle">
        {{ $from ?? '-' }} - {{ $to ?? '-' }}
    </div>
</div>

{{-- ========================= --}}
{{-- 1. NATIONAL --}}
{{-- ========================= --}}
<div class="section">
    <div class="section-title">1. NATIONAL REPORTS</div>

    <div class="field-title">Approved</div>
    @forelse($nationallyApproved as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">No approved reports</div>
    @endforelse

    <div class="field-title">Pending</div>
    @forelse($nationalPending as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">No pending reports</div>
    @endforelse

    <div class="field-title">Rejected</div>
    @forelse($nationalDeclined as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">No rejected reports</div>
    @endforelse
</div>

{{-- ========================= --}}
{{-- 2. ZONE --}}
{{-- ========================= --}}
<div class="section">
    <div class="section-title">2. ZONE REPORTS</div>

    <div class="field-title">Approved</div>
    @forelse($zoneApproved as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">No approved reports</div>
    @endforelse

    <div class="field-title">Pending</div>
    @forelse($pendingZoneApproval as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">No pending reports</div>
    @endforelse

    <div class="field-title">Rejected</div>
    @forelse($zoneDeclined as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">No rejected reports</div>
    @endforelse
</div>

{{-- ========================= --}}
{{-- 3. FIELD --}}
{{-- ========================= --}}
<div class="section">
    <div class="section-title">3. FIELD REPORTS</div>

    <div class="field-title">Approved</div>
    @forelse($fieldApproved as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">No approved reports</div>
    @endforelse

    <div class="field-title">Pending</div>
    @forelse($pendingFieldApproval as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">No pending reports</div>
    @endforelse

    <div class="field-title">Rejected</div>
    @forelse($fieldDeclined as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">No rejected reports</div>
    @endforelse
</div>

{{-- ========================= --}}
{{-- 4. MONTHS YET TO SUBMIT --}}
{{-- ========================= --}}
<div class="section">
    <div class="section-title">4. MONTHS YET TO BE SUBMITTED</div>

    @forelse($monthsYetToSubmit as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter => $months)
                <li>
                    {{ $chapter ?? '-' }} -
                    {{ is_array($months) ? implode(', ', $months) : $months }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">All submissions complete</div>
    @endforelse
</div>

{{-- ========================= --}}
{{-- 5. NEVER SUBMITTED --}}
{{-- ========================= --}}
<div class="section">
    <div class="section-title">5. CAMPUSES YET TO SUBMIT ANY REPORT</div>

    @forelse($neverSubmitted as $field => $chapters)
        <div class="field-title">{{ $field }}</div>
        <ul>
            @foreach($chapters as $chapter)
                <li>
                    {{ is_array($chapter) ? implode(', ', $chapter) : $chapter }}
                </li>
            @endforeach
        </ul>
    @empty
        <div class="empty">All campuses have submitted at least one report</div>
    @endforelse
</div>

</body>
</html>
{{dd('sd')}}