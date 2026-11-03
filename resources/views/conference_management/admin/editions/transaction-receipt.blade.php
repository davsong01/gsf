<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Transaction Receipt — {{ $transaction->transid }}</title>
    <style>
        @page { size:A4 portrait; margin:24px 32px; }
        body { color:#24314a; font-family:DejaVu Sans, sans-serif; font-size:9px; line-height:1.35; }
        .topline { height:4px; margin-bottom:12px; background:#c7a34a; }
        .brand-table { width:100%; margin-bottom:13px; border-collapse:collapse; }
        .brand-table td { padding:0; vertical-align:middle; }
        .brand-logo { width:48px; height:44px; }
        .brand { color:#12274d; font-size:14px; font-weight:bold; letter-spacing:.2px; }
        .brand-sub { margin-top:2px; color:#78849a; font-size:7px; letter-spacing:1.2px; text-transform:uppercase; }
        .brand-note { color:#78849a; font-size:8px; text-align:right; }
        .receipt-title { margin:12px 0 2px; color:#12274d; font-family:DejaVu Serif, serif; font-size:22px; font-weight:normal; }
        .edition { margin:0; color:#66738a; font-size:10px; }
        .status { display:inline-block; margin-top:8px; padding:4px 8px; border-radius:10px; color:#155e45; background:#e5f4ed; font-size:8px; font-weight:bold; text-transform:uppercase; }
        .status.pending { color:#8b5a12; background:#fff3d9; }
        .amount-box { margin:11px 0; padding:10px 13px; border:1px solid #dfe5ed; background:#f7f9fc; }
        .amount-label { color:#76839a; font-size:7px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; }
        .amount { margin-top:2px; color:#12274d; font-size:22px; font-weight:bold; }
        .section-title { margin:12px 0 5px; padding-bottom:4px; border-bottom:1px solid #dfe5ed; color:#12274d; font-size:10px; font-weight:bold; }
        table { width:100%; border-collapse:collapse; }
        td { width:50%; padding:4px 10px 4px 0; vertical-align:top; }
        .label { display:block; margin-bottom:1px; color:#78849a; font-size:7px; font-weight:bold; letter-spacing:.5px; text-transform:uppercase; }
        .value { color:#24314a; font-size:9px; font-weight:bold; overflow-wrap:anywhere; }
        .line { padding:4px 0; border-bottom:1px solid #edf0f4; }
        .line-label { color:#67748b; }
        .line-value { float:right; color:#24314a; font-weight:bold; }
        .total { margin-top:3px; padding-top:6px; border-top:1px solid #bfc9d8; font-size:10px; font-weight:bold; }
        .notice { margin-top:10px; padding:7px 10px; border-left:3px solid #c7a34a; color:#5e6b81; background:#f7f9fc; font-size:8px; }
        .footer { margin-top:12px; padding-top:6px; border-top:1px solid #dfe5ed; color:#8490a3; font-size:7px; }
        table, tr, .amount-box, .notice { page-break-inside:avoid; }
    </style>
</head>
<body>
    @php($isPaid = $transaction->status === 'Complete' || $transaction->registration_status === 'Complete')
    <div class="topline"></div>
    <table class="brand-table">
        <tr>
            <td style="width:58px"><img class="brand-logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('frontend/img/logo.png'))) }}" alt="GSF logo"></td>
            <td><div class="brand">GOFAMINT Students’ Fellowship</div><div class="brand-sub">National Secretariat</div></td>
            <td class="brand-note">FAITH · LEADERSHIP · SERVICE</td>
        </tr>
    </table>

    <h1 class="receipt-title">{{ $isPaid ? 'Payment Receipt' : 'Transaction Record' }}</h1>
    <p class="edition">{{ $edition->conference_theme }}</p>
    <span class="status {{ $isPaid ? '' : 'pending' }}">{{ $isPaid ? 'Payment confirmed' : 'Payment not confirmed · '.$transaction->status }}</span>

    <div class="amount-box">
        <div class="amount-label">{{ $isPaid ? 'Total received' : 'Total payable' }}</div>
        <div class="amount">₦{{ number_format((float) ($transaction->total_amount ?? $transaction->amount_paid), 2) }}</div>
    </div>

    <h2 class="section-title">Participant details</h2>
    <table>
        <tr>
            <td><span class="label">Name</span><span class="value">{{ $transaction->name ?: '—' }}</span></td>
            <td><span class="label">Email</span><span class="value">{{ $transaction->email ?: '—' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Phone</span><span class="value">{{ $transaction->phone ?: '—' }}</span></td>
            <td><span class="label">Registration plan</span><span class="value">{{ $transaction->conferenceplan->title ?? '—' }}</span></td>
        </tr>
    </table>

    <h2 class="section-title">Transaction details</h2>
    <table>
        <tr>
            <td><span class="label">Transaction reference</span><span class="value">{{ $transaction->transid }}</span></td>
            <td><span class="label">Provider reference</span><span class="value">{{ $transaction->provider_reference ?: '—' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Date</span><span class="value">{{ $transaction->created_at?->format('d M Y, g:i a') ?: '—' }}</span></td>
            <td><span class="label">Transaction status</span><span class="value">{{ $transaction->status ?: '—' }}</span></td>
        </tr>
        <tr><td><span class="label">Registration status</span><span class="value">{{ $transaction->registration_status ?: '—' }}</span></td><td></td></tr>
    </table>

    <h2 class="section-title">Payment breakdown</h2>
    <div class="line"><span class="line-label">Registration amount</span><span class="line-value">₦{{ number_format((float) $transaction->amount_paid, 2) }}</span></div>
    @if((float) $transaction->provider_charge > 0)
        <div class="line"><span class="line-label">Provider charge</span><span class="line-value">₦{{ number_format((float) $transaction->provider_charge, 2) }}</span></div>
    @endif
    <div class="line total"><span class="line-label">{{ $isPaid ? 'Total received' : 'Total payable' }}</span><span class="line-value">₦{{ number_format((float) ($transaction->total_amount ?? $transaction->amount_paid), 2) }}</span></div>

    @if($transaction->allocationFields->isNotEmpty())
        <h2 class="section-title">Registration information</h2>
        <table>
            @if($chapter)
                <tr>
                    <td><span class="label">Chapter</span><span class="value">{{ $chapter->name }}</span></td>
                    <td><span class="label">Zone</span><span class="value">{{ $chapter->zone->name ?? '—' }}</span></td>
                </tr>
                <tr><td><span class="label">Field</span><span class="value">{{ $chapter->field->name ?? '—' }}</span></td><td></td></tr>
            @endif
            @foreach($transaction->allocationFields->reject(fn ($field) => in_array(strtolower($field->key), ['chapter', 'chapter_id'], true))->chunk(2) as $row)
                <tr>
                    @foreach($row as $field)
                        <td><span class="label">{{ \Illuminate\Support\Str::headline($field->key) }}</span><span class="value">{{ $field->value ?: '—' }}</span></td>
                    @endforeach
                    @if($row->count() === 1)<td></td>@endif
                </tr>
            @endforeach
        </table>
    @endif

    @if(!$isPaid)
        <div class="notice">This document records a transaction attempt only. Payment has not been confirmed by the provider and this is not proof of payment.</div>
    @else
        <div class="notice">Thank you for registering. Keep this receipt for your records.</div>
    @endif

    <div class="footer">Generated {{ now()->format('d M Y, g:i a') }} · {{ $edition->conference_theme }} · GSF National Secretariat</div>
</body>
</html>
