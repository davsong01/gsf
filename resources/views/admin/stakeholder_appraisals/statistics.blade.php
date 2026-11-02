@extends('layouts.dashboard')

@section('title', 'Appraisal Statistics')

@section('active')
<li class="breadcrumb-item"><a href="{{ route('stakeholderappraisals.index') }}">Appraisals</a></li>
<li class="breadcrumb-item">Statistics Report</li>
@endsection

@section('content')
<style>
    .statistics-page { --ink:#14213d; --muted:#718096; --line:#e8edf3; --blue:#356ae6; --green:#12a875; --amber:#e49b25; --red:#e05252; }
    .statistics-hero { border:0; border-radius:20px; overflow:hidden; color:#fff; background:linear-gradient(120deg,#182b58,#356ae6); box-shadow:0 12px 28px rgba(31,61,123,.16); }
    .statistics-hero .eyebrow { color:#b9d0ff; font-size:.72rem; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
    .statistics-hero h1 { color:#fff !important; font-size:clamp(1.6rem,2.4vw,2.35rem); font-weight:800; letter-spacing:-.04em; }
    .statistics-hero p { color:#dbe7ff; max-width:650px; }
    .statistics-action { border-radius:999px; padding:.65rem 1rem; font-weight:700; }
    .statistics-hero .btn-light { color:#182b58; background:#fff; border-color:#fff; }
    .statistics-hero .btn-light:hover, .statistics-hero .btn-light:focus { color:#102044; background:#eaf1ff; border-color:#eaf1ff; }
    .statistics-hero .btn-outline-light { color:#fff; border-color:rgba(255,255,255,.8); }
    .statistics-hero .btn-outline-light:hover, .statistics-hero .btn-outline-light:focus { color:#182b58; background:#fff; border-color:#fff; }
    .filter-panel, .section-card { border:1px solid var(--line); border-radius:20px; background:#fff; box-shadow:0 8px 24px rgba(20,33,61,.04); }
    .filter-panel { background:#f7f9fc; }
    .filter-panel label { color:var(--ink); font-size:.75rem; font-weight:800; }
    .filter-panel .form-control, .filter-panel .form-select { border-color:#dfe6ef; border-radius:10px; min-height:42px; }
    .filter-panel .btn-primary { color:#fff; background:#2459c7; border-color:#2459c7; font-weight:700; box-shadow:0 4px 10px rgba(36,89,199,.2); }
    .filter-panel .btn-primary:hover, .filter-panel .btn-primary:focus { color:#fff; background:#17449f; border-color:#17449f; }
    .filter-strip { color:var(--muted); font-size:.86rem; }
    .stat-card { position:relative; min-height:112px; border:1px solid var(--line); border-radius:16px; background:#fff; box-shadow:0 6px 18px rgba(20,33,61,.05); overflow:hidden; }
    .stat-card:after { content:''; position:absolute; right:-30px; bottom:-50px; width:130px; height:130px; border-radius:50%; background:rgba(53,106,230,.08); }
    .stat-card .stat-label { color:var(--muted); font-size:.8rem; font-weight:700; }
    .stat-card .stat-number { color:var(--ink); font-size:1.8rem; font-weight:800; letter-spacing:-.05em; }
    .stat-card .stat-note { color:var(--muted); font-size:.78rem; }
    .stat-card--red { border-top:4px solid var(--red); } .stat-card--red .stat-number { color:var(--red); }
    .stat-card--amber { border-top:4px solid var(--amber); } .stat-card--amber .stat-number { color:var(--amber); }
    .stat-card--green { border-top:4px solid var(--green); } .stat-card--green .stat-number { color:var(--green); }
    .stat-card--blue { border-top:4px solid var(--blue); } .stat-card--blue .stat-number { color:var(--blue); }
    .section-card { overflow:hidden; }
    .section-card-header { padding:.85rem 1rem; border-bottom:1px solid var(--line); }
    .section-card-header h2 { margin:0; color:var(--ink); font-size:1.1rem; font-weight:800; }
    .section-card-header p { margin:.25rem 0 0; color:var(--muted); font-size:.82rem; }
    .group-table { margin:0; min-width:980px; }
    .statistics-page .table-responsive { max-height:70vh; overflow:auto; }
    .group-table thead { position:sticky; top:0; z-index:3; }
    .group-table th { color:#8390a5; background:#fbfcfe; border-bottom:1px solid var(--line); font-size:.7rem; letter-spacing:.08em; text-transform:uppercase; white-space:nowrap; }
    .group-table td { color:var(--ink); border-color:#eef2f6; vertical-align:top; }
    .group-name { font-weight:800; min-width:180px; }
    .count-pill { display:inline-flex; align-items:center; justify-content:center; min-width:30px; padding:.28rem .5rem; border-radius:999px; font-weight:800; }
    .count-pill--red { color:#a92f37; background:#fff0f0; } .count-pill--amber { color:#a56a08; background:#fff7e8; } .count-pill--green { color:#087d58; background:#e9fbf4; }
    .person-list { max-width:280px; color:#607089; font-size:.8rem; line-height:1.55; }
    .person-list span { display:block; }
    .person-list span + span { border-top:1px dashed #edf0f4; padding-top:.2rem; margin-top:.2rem; }
    @media (max-width:767px) { .statistics-hero .hero-inner { align-items:flex-start!important; flex-direction:column; } .person-list { max-width:220px; } }
</style>

<div class="content-body statistics-page">
    <div class="statistics-hero p-3 p-lg-4 mb-3">
        <div class="hero-inner d-flex justify-content-between align-items-center gap-4">
            <div>
                <div class="eyebrow mb-2">Appraisal performance report</div>
                <h1 class="mb-1">Report of the Appraisal</h1>
                <p class="mb-0">See who has not submitted, who has submitted but is waiting for evaluation, and who has completed the full appraisal process.</p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <a href="{{ route('stakeholderappraisals.statistics.pdf', request()->query()) }}" class="btn btn-light statistics-action"><i class="fa fa-file-pdf-o me-1"></i> PDF</a>
                <a href="{{ route('stakeholderappraisals.statistics.export', request()->query()) }}" class="btn btn-light statistics-action"><i class="fa fa-file-excel-o me-1"></i> Excel</a>
                <a href="{{ route('stakeholderappraisals.index', request()->query()) }}" class="btn btn-outline-light statistics-action">Back</a>
            </div>
        </div>
    </div>

    <div class="filter-panel p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="mb-1">Filter report</h5><small class="text-muted">Use any combination of filters to focus the statistics.</small></div><i class="fa fa-sliders fa-lg text-primary"></i></div>
        <form method="GET" action="{{ route('stakeholderappraisals.statistics') }}">
            <div class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-6"><label for="search">Search person</label><input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, email, or phone"></div>
                <div class="col-xl-2 col-md-6"><label for="field_id">Field</label><select class="form-select" id="field_id" name="field_id"><option value="">All fields</option>@foreach($fields as $field)<option value="{{ $field->id }}" @selected(request('field_id') == $field->id)>{{ $field->name }}</option>@endforeach</select></div>
                <div class="col-xl-2 col-md-6"><label for="zone_id">Zone</label><select class="form-select" id="zone_id" name="zone_id"><option value="">All zones</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected(request('zone_id') == $zone->id)>{{ $zone->name }}</option>@endforeach</select></div>
                <div class="col-xl-2 col-md-6"><label for="self_status">Submission status</label><select class="form-select" id="self_status" name="self_status"><option value="">All statuses</option><option value="published" @selected(request('self_status') === 'published')>Submitted</option><option value="draft" @selected(request('self_status') === 'draft')>Not submitted</option></select></div>
                <div class="col-xl-2 col-md-6"><label for="evaluation_status">Evaluation status</label><select class="form-select" id="evaluation_status" name="evaluation_status"><option value="">All statuses</option><option value="published" @selected(request('evaluation_status') === 'published')>Evaluated</option><option value="draft" @selected(request('evaluation_status') === 'draft')>Not evaluated</option></select></div>
                <div class="col-xl-1 col-md-6"><button type="submit" class="btn btn-primary w-100 rounded-pill">Apply</button></div>
            </div>
            @if(request()->hasAny(['search','field_id','zone_id','self_status','evaluation_status']))<div class="mt-3"><a href="{{ route('stakeholderappraisals.statistics') }}" class="small text-decoration-none"><i class="fa fa-times me-1"></i>Clear filters</a></div>@endif
        </form>
    </div>

    <div class="filter-strip mb-3"><i class="fa fa-filter me-2"></i><strong>Current view:</strong> {{ request('search') ?: 'All stakeholders' }} @if(request('field_id')) · Field: {{ $fields->firstWhere('id', request('field_id'))?->name }} @endif @if(request('zone_id')) · Zone: {{ $zones->firstWhere('id', request('zone_id'))?->name }} @endif</div>

    <div class="row g-3 mb-3">
        @foreach([['Not submitted', $summary['not_submitted'], $summary['not_submitted_percent'], 'Have not published self-appraisal.', 'red'], ['Submitted, not evaluated', $summary['submitted_not_evaluated'], $summary['submitted_not_evaluated_percent'], 'Waiting for evaluation.', 'amber'], ['Evaluated', $summary['evaluated'], $summary['evaluated_percent'], 'Evaluation is published.', 'green'], ['Total stakeholders', $summary['total_stakeholders'], null, 'Included in this report.', 'blue']] as [$label, $value, $percent, $note, $color])
            <div class="col-xl-3 col-md-6"><div class="stat-card stat-card--{{ $color }} p-3 h-100"><div class="stat-label">{{ $label }}</div><div class="stat-number mt-1">{{ $value }}@if($percent !== null) <small>({{ $percent }}%)</small>@endif</div><div class="stat-note mt-1">{{ $note }}</div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-3"><div class="col-md-6"><div class="section-card p-2 d-flex justify-content-between align-items-center"><div><small class="text-muted">Submission rate</small><div class="h5 mb-0">{{ $summary['submission_rate_percent'] }}%</div></div><i class="fa fa-file-text-o fa-lg text-primary"></i></div></div><div class="col-md-6"><div class="section-card p-2 d-flex justify-content-between align-items-center"><div><small class="text-muted">Evaluation rate</small><div class="h5 mb-0">{{ $summary['evaluation_rate_percent'] }}%</div></div><i class="fa fa-check-circle fa-lg text-success"></i></div></div></div>

    @foreach([['Fields', $byField, 'Field'], ['Zones', $byZone, 'Zone']] as [$title, $rows, $groupHeading])
        <div class="section-card mb-3"><div class="section-card-header"><h2>Statistics by {{ $title }}</h2><p>Counts and names are grouped by {{ strtolower($title) }}.</p></div><div class="table-responsive"><table class="table table-sm group-table align-middle"><thead><tr><th class="ps-3">{{ $groupHeading }}</th><th>Total</th><th>Not submitted</th><th>Submitted, not evaluated</th><th>Evaluated</th></tr></thead><tbody>
            @forelse($rows as $row)<tr><td class="ps-3 group-name">{{ $row['Group'] }}</td><td><strong>{{ $row['Stakeholders'] }}</strong></td><td><span class="count-pill count-pill--red">{{ $row['not_submitted_count'] }}</span><div class="person-list mt-1">@foreach($row['not_submitted_people'] as $index => $person)<span>{{ $index + 1 }}. {{ $person }}</span>@endforeach</div></td><td><span class="count-pill count-pill--amber">{{ $row['submitted_not_evaluated_count'] }}</span><div class="person-list mt-1">@foreach($row['submitted_not_evaluated_people'] as $index => $person)<span>{{ $index + 1 }}. {{ $person }}</span>@endforeach</div></td><td><span class="count-pill count-pill--green">{{ $row['evaluated_count'] }}</span><div class="person-list mt-1">@foreach($row['evaluated_people'] as $index => $person)<span>{{ $index + 1 }}. {{ $person }}</span>@endforeach</div></td></tr>
            @empty<tr><td colspan="5" class="text-center text-muted py-4">No data available.</td></tr>@endforelse
        </tbody></table></div></div>
    @endforeach
</div>
@endsection
