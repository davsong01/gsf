@php
    $awards = $entries['awards'];
    $chapters = $entries['chapters'];
    $fields = $entries['fields'];
    $zones = $entries['zones'];

    $permissions = resolveAwardIndexContext();

    $canViewChapter = $permissions->canViewChapter;
    $canViewZone = $permissions->canViewZone;
    $canViewField = $permissions->canViewField;

    $canEdit = $permissions->canEdit;

    $hierarchyCount = $permissions->hierarchyCount;
    $hierarchyCol = $permissions->hierarchyCol;
    $approval_statuses = $permissions->statuses;
    $approval_status_groups = [
        'Chapter' => [
            'chapter_pending' => 'Chapter Pending',
            'chapter_approved' => 'Chapter Approved',
            'chapter_rejected' => 'Chapter Rejected',
        ],
        'Zone' => [
            'zone_pending' => 'Zone Pending',
            'zone_approved' => 'Zone Approved',
            'zone_rejected' => 'Zone Rejected',
        ],
        'Field' => [
            'field_pending' => 'Field Pending',
            'field_approved' => 'Field Approved',
            'field_rejected' => 'Field Rejected',
        ],
        'National' => [
            'national_pending' => 'National Pending',
            'national_approved' => 'National Approved',
            'national_rejected' => 'National Rejected',
        ],
    ];
    $isArchivedView = request()->boolean('archived');
    $activeQuery = request()->except(['archived', 'page']);
    $archivedQuery = array_merge($activeQuery, ['archived' => 1]);
    $activeUrl = url()->current() . (count($activeQuery) ? '?' . http_build_query($activeQuery) : '');
    $archivedUrl = url()->current() . '?' . http_build_query($archivedQuery);
    $canArchiveSubmission = $isAdmin && userHasPermission('awards.archive-submission', $user);
    $canDeleteSubmission = $isAdmin && userHasPermission('awards.delete-submission', $user);
    $canAdjustApprovalStatus = $isAdmin && userHasPermission('awards.adjust-approval-status', $user);
    $visibleAwards = $awards->getCollection()->flatMap(fn ($group) => $group);
    $visibleApproved = $visibleAwards->filter(fn ($award) => (int) $award->national_status === 1)->count();
    $visiblePending = $visibleAwards->filter(fn ($award) => (int) $award->national_status === 0)->count();
    $visibleNeedsAttention = $visibleAwards->filter(fn ($award) => collect([
        $award->chapter_status,
        $award->zone_status,
        $award->field_status,
        $award->national_status,
    ])->contains(fn ($status) => (int) $status === 2))->count();

@endphp

@extends($isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard')

@section('title', 'Award Entries')

@section($isAdmin ? 'item' : 'active')
    @if($isAdmin)
        <li class="breadcrumb-item">
            <a href="{{ route($isAdmin ? 'award.go' : 'stakeholders.award.go') }}">Award Entries</a>
        </li>
    @endif
@endsection

@section('extra_styles')
<style>
    .award-workspace { --aw-ink:#172b43; --aw-muted:#718096; --aw-line:#e5eaf0; }
    .award-hero { position:relative; display:flex; align-items:flex-end; justify-content:space-between; gap:24px; overflow:hidden; margin-bottom:18px; padding:26px 30px; border:1px solid #dfe8ee; border-radius:20px; background:radial-gradient(circle at 92% 12%,rgba(209,164,82,.16),transparent 28%),linear-gradient(120deg,#fff 0%,#f7fafc 55%,#f3f7f5 100%); box-shadow:0 12px 34px rgba(30,53,75,.055); }
    .award-hero::after { position:absolute; right:30px; bottom:-95px; width:230px; height:230px; border:1px solid rgba(47,102,122,.12); border-radius:50%; box-shadow:0 0 0 28px rgba(47,102,122,.025),0 0 0 58px rgba(47,102,122,.02); content:""; pointer-events:none; }
    .award-hero__copy,.award-hero__actions { position:relative; z-index:1; }
    .award-hero__eyebrow { color:#9b742b; font-size:10px; font-weight:850; letter-spacing:.16em; text-transform:uppercase; }
    .award-hero h1 { margin:8px 0 6px; color:var(--aw-ink); font-size:clamp(23px,3vw,32px); font-weight:800; letter-spacing:-.035em; }
    .award-hero p { margin:0; color:var(--aw-muted); font-size:13px; }
    .award-hero__count { display:inline-flex; align-items:center; gap:8px; margin-top:15px; padding:7px 11px; border:1px solid #e3eaf0; border-radius:999px; color:#506477; background:rgba(255,255,255,.78); font-size:11px; font-weight:750; }
    .award-hero__count i { color:#aa7b2d; }
    .award-hero__actions { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:8px; }
    .award-summary-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:18px; }
    .award-summary-card { display:flex; align-items:center; gap:13px; min-width:0; padding:15px 17px; border:1px solid var(--aw-line); border-radius:15px; background:#fff; box-shadow:0 7px 20px rgba(30,53,75,.035); }
    .award-summary-card__icon { display:grid; width:39px; height:39px; flex:0 0 39px; place-items:center; border-radius:12px; color:var(--aw-accent); background:var(--aw-soft); font-size:16px; }
    .award-summary-card span { display:block; color:var(--aw-muted); font-size:10px; font-weight:750; letter-spacing:.06em; text-transform:uppercase; }
    .award-summary-card strong { display:block; margin-top:3px; color:var(--aw-ink); font-size:22px; font-weight:850; line-height:1.1; }
    .award-summary-caption { margin:-9px 2px 15px; color:#8793a1; font-size:10px; text-align:right; }
    .award-filters-card { margin-bottom:18px; padding:17px 18px 12px; border:1px solid var(--aw-line); border-radius:17px; background:#fff; box-shadow:0 8px 24px rgba(30,53,75,.04); }
    .award-filters-card__heading { display:flex; align-items:center; gap:9px; margin-bottom:11px; color:var(--aw-ink); font-size:13px; font-weight:800; }
    .award-filters-card__heading i { color:#aa7b2d; }
    .award-results-card { overflow:hidden; border:1px solid var(--aw-line)!important; border-radius:17px!important; box-shadow:0 10px 30px rgba(30,53,75,.05)!important; }
    .award-results-card .card-body.table-toolbar { min-height:62px; padding:11px 16px; background:#f9fbfc; }
    .award-results-card .table-responsive { max-height:75vh; }
    .award-results-card .custom-modern-table { min-width:940px; }
    .award-results-card .custom-modern-table thead th { padding-top:13px; padding-bottom:13px; color:#66778b!important; background:#f7f9fb!important; font-size:10px; font-weight:850; letter-spacing:.08em; }
    .award-results-card .custom-modern-table { border-collapse:separate; border-spacing:0 7px; padding:0 10px; background:#f5f7fa; }
    .award-results-card .custom-modern-table thead th { border:0; }
    .award-results-card .award-row-item td { padding:15px 12px; border-top:1px solid #e4eaf0; border-bottom:1px solid #e4eaf0; background:#fff; transition:background .16s ease,box-shadow .16s ease; }
    .award-results-card .award-row-item td:first-child { border-left:1px solid #e4eaf0; border-radius:13px 0 0 13px; }
    .award-results-card .award-row-item td:last-child { border-right:1px solid #e4eaf0; border-radius:0 13px 13px 0; }
    .award-results-card .award-row-item:hover td { background:#fcfdff; box-shadow:0 4px 12px rgba(31,55,79,.035); }
    .award-results-card .sticky-chapter-row td { top:45px; padding-top:11px!important; padding-bottom:11px!important; border:0!important; background:#eaf0f5!important; box-shadow:none!important; }
    .award-results-card .sticky-chapter-row td:nth-child(2) { border-radius:11px 0 0 11px; }
    .award-results-card .sticky-chapter-row td:last-child { border-radius:0 11px 11px 0; }
    .award-workspace--admin .award-results-card .award-row-item > td:nth-child(3),.award-workspace--stakeholder .award-results-card .award-row-item > td:nth-child(2) { min-width:265px; }
    .award-workspace--admin .award-results-card .award-row-item > td:nth-child(4),.award-workspace--stakeholder .award-results-card .award-row-item > td:nth-child(3) { min-width:250px; }
    .award-workspace--admin .award-results-card .award-row-item > td:nth-child(5),.award-workspace--stakeholder .award-results-card .award-row-item > td:nth-child(4) { min-width:130px; white-space:nowrap; }
    .award-workspace--admin .award-results-card .award-row-item > td:nth-child(6),.award-workspace--stakeholder .award-results-card .award-row-item > td:nth-child(5) { min-width:150px; }
    .award-serial { display:inline-grid; width:30px; height:30px; place-items:center; border:1px solid #e3eaf0; border-radius:10px; color:#53677c; background:#f8fafc; font-size:11px; font-weight:800; }
    .award-shortlist-state { display:flex; min-height:38px; align-items:center; padding:6px 9px; border:1px solid #e9edf1; border-radius:10px; background:#fafbfd; }
    .award-shortlist-state .badge { font-size:10px; }
    .award-shortlist-state .text-danger { color:#7d8996!important; font-size:10px; font-weight:700; }
    .award-entry-name { color:#20364d!important; font-size:14px!important; font-weight:800!important; }
    .award-reference { display:inline-flex; align-self:flex-start; padding:4px 7px; border:1px solid #e6ebf0; border-radius:6px; color:#64748b!important; background:#f8fafc; font-size:10px!important; }
    .award-approval-track { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; min-width:220px; }
    .award-approval-step { display:flex; min-width:0; align-items:center; justify-content:space-between; gap:6px; padding:6px 8px; border:1px solid #e8edf1; border-radius:8px; background:#fbfcfd; }
    .award-approval-step__level { color:#758397; font-size:9px; font-weight:800; letter-spacing:.045em; text-transform:uppercase; }
    .award-approval-step__status { display:inline-flex; align-items:center; gap:4px; font-size:9px; font-weight:800; white-space:nowrap; }
    .award-approval-step__status i { font-size:10px; }
    .award-approval-step--approved { border-color:#dcefe5; background:#f5fbf7; }
    .award-approval-step--approved .award-approval-step__status { color:#21805a; }
    .award-approval-step--pending { border-color:#f0e8d5; background:#fffcf5; }
    .award-approval-step--pending .award-approval-step__status { color:#9a762a; }
    .award-approval-step--rejected { border-color:#f2dfe1; background:#fff8f8; }
    .award-approval-step--rejected .award-approval-step__status { color:#b54e56; }
    .award-approval-step__feedback { margin-left:2px; color:inherit; }
    .award-results-card .card-footer { border-color:var(--aw-line)!important; }
    @media(max-width:767px) { .award-hero { align-items:flex-start; flex-direction:column; padding:21px; } .award-hero__actions { justify-content:flex-start; } .award-summary-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:9px; } .award-summary-card { padding:12px; } .award-summary-card__icon { width:34px; height:34px; flex-basis:34px; } .award-summary-card strong { font-size:19px; } .award-filters-card { padding:14px 12px 8px; } .award-summary-caption { text-align:left; } .award-results-card .custom-modern-table { min-width:940px; } }
    .custom-modern-table thead th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 1022;
        background-color: #ffffff;
    }

    /* Force the entire chapter cell block to stick cleanly right under the headers */
    .sticky-chapter-row td {
        position: -webkit-sticky;
        position: sticky;
        /* Increased slightly from 41px to prevent any overlapping cutoff */
        top: 46px;
        z-index: 1021;
        background-color: #f1f5f9 !important;
        border-top: 1px solid #cbd5e1 !important;
        border-bottom: 1px solid #cbd5e1 !important;
        box-shadow: inset 0 -1px 0 #cbd5e1;
    }
    .compliance-mini-panel {
        max-width: 320px;
        width: 100%;
    }
    .custom-modern-table th {
        background-color: #ffffff;
        position: sticky;
        top: 0;
        z-index: 1021;
    }
    .bg-soft-success { background-color: rgba(34, 197, 94, 0.12) !important; color: #16a34a !important; }
    .bg-soft-warning { background-color: rgba(234, 179, 8, 0.12) !important; color: #ca8a04 !important; }
    .bg-soft-danger { background-color: rgba(239, 68, 68, 0.12) !important; color: #dc2626 !important; }
    .badge-status { font-size: 0.72rem !important; font-weight: 600; padding: 4px 8px; border-radius: 4px; }

    .table-toolbar{
        padding: 10px;
        margin-bottom: 10px;
    }

    .table-toolbar .form-select{
        min-height: 31px;
        font-size: 13px;
    }

    .table-toolbar .btn{
        min-height: 31px;
        padding: 0 14px;
        font-size: 13px;
    }

    .table-toolbar #selected-counter{
        font-size: 12px;
    }
    .bulk-control {
        height: 38px;
    }

    .shortlist-stage-option {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        padding: 0.55rem 0.75rem;
        border: 1px solid transparent;
        border-radius: 0.5rem;
        transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .shortlist-stage-option:hover {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.45);
    }

    .shortlist-stage-option:not(:last-child) {
        margin-bottom: 0.35rem;
    }

    .shortlist-stage-option .form-check-input {
        margin-top: 0.2rem;
        flex: 0 0 auto;
    }

    .shortlist-stage-option .form-check-label {
        margin-bottom: 0;
        cursor: pointer;
        width: 100%;
        line-height: 1.25;
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <section id="awards-dashboard" class="award-workspace {{ $isAdmin ? 'award-workspace--admin' : 'award-workspace--stakeholder' }}">
        <header class="award-hero">
            <div class="award-hero__copy">
                <div class="award-hero__eyebrow">Education &amp; Excellence Awards</div>
                <h1>{{ $title }}</h1>
                <p>{{ $isArchivedView ? 'Review and manage archived nominations.' : 'Review submissions, follow the approval journey, and manage nominations.' }}</p>
                <span class="award-hero__count"><i class="fa fa-file-text-o" aria-hidden="true"></i>{{ number_format($awards->total()) }} {{ $isArchivedView ? 'archived' : 'active' }} {{ \Illuminate\Support\Str::plural('submission', $awards->total()) }}</span>
            </div>
            @if($isAdmin)
                <div class="award-hero__actions">
                    <a href="{{ $activeUrl }}" class="btn btn-sm {{ $isArchivedView ? 'btn-outline-primary' : 'btn-primary' }}">Active Awards</a>
                    <a href="{{ $archivedUrl }}" class="btn btn-sm {{ $isArchivedView ? 'btn-primary' : 'btn-outline-primary' }}">Archived Awards</a>
                </div>
            @endif
        </header>

        <div class="award-summary-grid" aria-label="Current page summary">
            <article class="award-summary-card" style="--aw-accent:#376d9b;--aw-soft:#e8f1f8"><span class="award-summary-card__icon"><i class="fa fa-files-o" aria-hidden="true"></i></span><div><span>On this page</span><strong>{{ number_format($visibleAwards->count()) }}</strong></div></article>
            <article class="award-summary-card" style="--aw-accent:#24845e;--aw-soft:#e6f5ee"><span class="award-summary-card__icon"><i class="fa fa-check-circle" aria-hidden="true"></i></span><div><span>Nationally approved</span><strong>{{ number_format($visibleApproved) }}</strong></div></article>
            <article class="award-summary-card" style="--aw-accent:#aa7928;--aw-soft:#fff3dc"><span class="award-summary-card__icon"><i class="fa fa-hourglass-half" aria-hidden="true"></i></span><div><span>Awaiting national decision</span><strong>{{ number_format($visiblePending) }}</strong></div></article>
            <article class="award-summary-card" style="--aw-accent:#bd4e55;--aw-soft:#fbeaec"><span class="award-summary-card__icon"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span><div><span>Rejected at a stage</span><strong>{{ number_format($visibleNeedsAttention) }}</strong></div></article>
        </div>
        <div class="award-summary-caption">Summary counts reflect the submissions currently shown on this page.</div>

        <!-- Filters Form Panel -->
        <div class="award-filters-card">
            <div class="award-filters-card__heading"><i class="fa fa-sliders" aria-hidden="true"></i> Find submissions</div>
        <div class="row">
            <div class="col-12">
                <form
                    method="GET"
                    action="{{ url()->current() }}"
                    class="row gx-2 gy-1 align-items-end"
                    id="awardEntriesFilterForm"
                >
                    @if($isArchivedView)
                        <input type="hidden" name="archived" value="1">
                    @endif

                    <div class="col-md-4 mb-1">
                        <label class="form-label mb-1 small">
                            Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ request('name') }}"
                        >
                    </div>

                    <div class="col-md-4 mb-1">
                        <label class="form-label mb-1 small">
                            Reference
                        </label>

                        <input
                            type="text"
                            name="reference"
                            class="form-control"
                            value="{{ request('reference') }}"
                        >
                    </div>

                    <div class="col-md-2 mb-1">
                        <label class="form-label mb-1 small">
                            From
                        </label>

                        <input
                            type="date"
                            name="from_date"
                            class="form-control"
                            value="{{ request('from_date') }}"
                        >
                    </div>

                    <div class="col-md-2 mb-1">
                        <label class="form-label mb-1 small">
                            To
                        </label>

                        <input
                            type="date"
                            name="to_date"
                            class="form-control"
                            value="{{ request('to_date') }}"
                        >
                    </div>

                    @if($canViewField || $isAdmin)
                        <div class="col-md-{{ $hierarchyCol }} mb-1">
                            <label class="form-label mb-1 small">
                                Field
                            </label>

                            <select
                                name="field_id"
                                class="form-control"
                            >
                                <option value="">
                                    All Fields
                                </option>

                                @foreach($fields as $field)
                                    <option
                                        value="{{ $field->id }}"
                                        @selected(
                                            (string) request('field_id') === (string) $field->id
                                        )
                                    >
                                        {{ $field->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($canViewZone || $isAdmin)
                        <div class="col-md-{{ $hierarchyCol }} mb-1">
                            <label class="form-label mb-1 small">
                                Zone
                            </label>

                            <select
                                name="zone_id"
                                class="form-control"
                            >
                                <option value="">
                                    All Zones
                                </option>

                                @foreach($zones as $zone)
                                    <option
                                        value="{{ $zone->id }}"
                                        @selected(
                                            (string) request('zone_id') === (string) $zone->id
                                        )
                                    >
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($canViewChapter || $isAdmin)
                        <div class="col-md-{{ $hierarchyCol }} mb-1">
                            <label class="form-label mb-1 small">
                                Chapter
                            </label>

                            <select
                                name="chapter_id"
                                class="form-control"
                            >
                                <option value="">
                                    All Chapters
                                </option>

                                @foreach($chapters as $chapter)
                                    <option
                                        value="{{ $chapter->id }}"
                                        @selected(
                                            (string) request('chapter_id') === (string) $chapter->id
                                        )
                                    >
                                        {{ $chapter->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-3 mb-1">
                        <label class="form-label mb-1 small">
                            Approval Status
                        </label>

                        <select
                            name="status_filter[]"
                            class="form-control select2-award-status-filter"
                            multiple
                        >
                            @foreach($approval_status_groups as $groupLabel => $statuses)
                                <optgroup label="{{ $groupLabel }}">
                                    @foreach($statuses as $key => $label)
                                        <option
                                            value="{{ $key }}"
                                            @selected(
                                                in_array(
                                                    $key,
                                                    (array) request('status_filter', []),
                                                    true
                                                )
                                            )
                                        >
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    @if($isAdmin)
                        <div class="col-md-2 mb-1">
                            <label class="form-label mb-1 small">
                                Shortlist Stages
                            </label>

                            <select
                                name="current_shortlist_stage_id"
                                class="form-control"
                            >
                                <option value="">
                                    All Stages
                                </option>

                                @foreach($shortlistStages as $stage)
                                    <option
                                        value="{{ $stage->id }}"
                                        @selected(
                                            (string) request('current_shortlist_stage_id')
                                            === (string) $stage->id
                                        )
                                    >
                                        {{ $stage->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-2 mb-1">
                        <button
                            type="submit"
                            name="action"
                            value="view"
                            class="btn btn-secondary btn-sm w-100"
                        >
                            Filter
                        </button>
                    </div>

                    @if($isAdmin)
                        <div class="col-md-5 mb-1">
                            <div class="d-flex flex-wrap gap-1">
                                <button
                                    type="submit"
                                    name="action"
                                    value="download_report"
                                    class="btn btn-info btn-sm px-2 py-1"
                                >
                                    Download Report
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm px-2 py-1"
                                    data-toggle="modal"
                                    data-target="#downloadAssetsModal"
                                >
                                    Download Assets
                                </button>

                                @if(request()->hasAny([
                                    'name',
                                    'reference',
                                    'from_date',
                                    'to_date',
                                    'field_id',
                                    'zone_id',
                                    'chapter_id',
                                    'status_filter',
                                    'current_shortlist_stage_id',
                                ]))
                                    <a
                                        href="{{ url()->current() }}{{ $isArchivedView ? '?archived=1' : '' }}"
                                        class="btn btn-light btn-sm px-2 py-1"
                                    >
                                        Clear Filters
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div
                            class="modal fade"
                            id="downloadAssetsModal"
                            tabindex="-1"
                            role="dialog"
                            aria-labelledby="downloadAssetsModalLabel"
                            aria-hidden="true"
                        >
                            <div
                                class="modal-dialog modal-dialog-centered"
                                role="document"
                            >
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5
                                            class="modal-title"
                                            id="downloadAssetsModalLabel"
                                        >
                                            Download Award Assets
                                        </h5>

                                        <button
                                            type="button"
                                            class="close"
                                            data-dismiss="modal"
                                            aria-label="Close"
                                        >
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group mb-0">
                                            <label for="assetType">
                                                Select Asset
                                            </label>

                                            <select
                                                name="asset_type"
                                                id="assetType"
                                                class="form-control"
                                            >
                                                <option value="">
                                                    Choose an asset
                                                </option>

                                                <option
                                                    value="result_file"
                                                    @selected(request('asset_type') === 'result_file')
                                                >
                                                    Result
                                                </option>

                                                <option
                                                    value="picture"
                                                    @selected(request('asset_type') === 'picture')
                                                >
                                                    Picture
                                                </option>
                                            </select>

                                            <small class="form-text text-muted">
                                                Only assets belonging to records matching the
                                                current filters will be downloaded.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-dismiss="modal"
                                        >
                                            Cancel
                                        </button>

                                        <button
                                            type="submit"
                                            name="action"
                                            value="download_assets"
                                            class="btn btn-danger"
                                            id="downloadAssetsSubmitButton"
                                        >
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
        </div>

        <!-- Master Data Render Table Component -->
        <div class="row">
            <div class="col-12">
                @if($isAdmin)
                    <form id="bulk-actions-form" method="POST" action="">
                        @csrf
                        <input type="hidden" name="_method" id="bulk-form-method" value="POST">
                @endif

                    <div class="card award-results-card border-0 shadow-sm rounded-3 overflow-hidden">
                        @if($isAdmin)
                            <div class="card-body table-toolbar d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">

                                    <select id="wp-bulk-action-select" class="form-select" style="width: 350px; height: 38px;">
                                        <option value="">Select Bulk Action</option>
                                        <option value="shortlist"
                                                data-url="{{ route('awards.bulk.shortlist') }}"
                                                data-method="POST"
                                                data-confirm="Shortlist all selected entries?">
                                            Shortlist
                                        </option>
                                        <option value="approve"
                                                data-url="{{ route('awards.bulk-approve') }}"
                                                data-method="POST"
                                                data-confirm="Approve selected entries?">
                                            Approve
                                        </option>
                                        <option value="reject"
                                                data-url="{{ route('awards.bulk-reject') }}"
                                                data-method="POST"
                                                data-confirm="Reject selected entries?">
                                            Reject
                                        </option>
                                        @if(!$isArchivedView && $canArchiveSubmission)
                                            <option value="archive"
                                                    data-url="{{ route('awards.bulk-archive') }}"
                                                    data-method="POST"
                                                    data-confirm="Archive selected entries?">
                                                Archive
                                            </option>
                                        @endif
                                        @if($canDeleteSubmission && !$isArchivedView)
                                            <option value="delete"
                                                    data-url="{{ route('awards.bulk-delete') }}"
                                                    data-method="POST"
                                                    data-confirm="Delete selected entries and their submitted data? This cannot be undone.">
                                                Delete
                                            </option>
                                        @endif

                                        @if($canDeleteSubmission && $isArchivedView)
                                            <option value="delete"
                                                    data-url="{{ route('awards.bulk-permanent-delete') }}"
                                                    data-method="POST"
                                                    data-confirm="Delete selected archived entries and their submitted data? This cannot be undone.">
                                                Delete
                                            </option>
                                        @endif
                                    </select>

                                    <button type="button" id="wp-bulk-action-apply" class="btn btn-primary px-4" style="height: 38px;" disabled>
                                        Apply
                                    </button>

                                </div>

                                <div class="small text-muted">
                                    <span id="selected-counter">0 selected</span>
                                </div>
                            </div>
                        @endif

                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                                <table class="table table-align-middle mb-0 custom-modern-table">
                                    <thead>
                                        <tr class="bg-light border-bottom">
                                            @if($isAdmin)
                                                <th class="ps-4 align-middle" style="width: 45px;">
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input cursor-pointer shadow-none" id="master-checkbox">
                                                    </div>
                                                </th>
                                            @endif
                                            <th class="text-uppercase text-muted tracking-wider align-middle" style="width: 60px;">S/N</th>
                                            <th class="text-uppercase text-muted tracking-wider align-middle">Nominee & Ref</th>
                                            <th class="text-uppercase text-muted tracking-wider align-middle" style="min-width: 160px;">Approval Progress</th>
                                            <th class="text-uppercase text-muted tracking-wider align-middle">Submitted On</th>
                                            <th class="text-uppercase text-muted tracking-wider text-end pe-4 align-middle" style="min-width: 180px;">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $serialNumber = $awards->firstItem() ?? 1;
                                        @endphp
                                        @forelse($awards as $groupKey => $awardGroup)
                                            <tr class="sticky-chapter-row table-sm bg-light-subtle">
                                                @if($isAdmin)
                                                    <td class="ps-4 align-middle"></td>
                                                @endif
                                                <td></td>
                                                <td colspan="2" class="align-middle pt-2 pb-2">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-dark fw-bold font-base m-0 lh-base">
                                                            {{ $groupKey }}
                                                        </span>
                                                        @if($awardGroup->first() && $awardGroup->first()->chapter)
                                                            <span class="text-muted font-xs lh-sm" style="font-size: 0.68rem;">
                                                                {{ $awardGroup->first()->zone->name ?? 'N/A' }} &bull; {{ $awardGroup->first()->field->name ?? 'N/A' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-secondary font-xs rounded-pill fw-medium px-2 py-0.5" style="font-size: 0.7rem;">
                                                        {{ $awardGroup->count() }} {{ Str::plural('Entry', $awardGroup->count()) }}
                                                    </span>
                                                </td>
                                                <td class="pe-4 align-middle text-end">
                                                    @if($awardGroup->first() && $awardGroup->first()->chapter && ($isAdmin || in_array($user->role_id, array_merge(fieldStakeholders(), zoneStakeholders(), secretariatStakeholders(), ncpStakeholders(), chapterStakeholders()))))
                                                        @php
                                                            $chapterCompliance = $awardGroup->first()->chapter->reportCompliance();
                                                            $progressBarColor = $chapterCompliance >= 80 ? 'bg-success' : ($chapterCompliance >= 50 ? 'bg-warning' : 'bg-danger');
                                                            $badgeColor = $chapterCompliance >= 80 ? 'bg-soft-success text-success' : ($chapterCompliance >= 50 ? 'bg-soft-warning text-warning' : 'bg-soft-danger text-danger');
                                                        @endphp
                                                        <div class="d-flex align-items-center gap-2 ms-auto" style="max-width: 240px;">
                                                            <div class="progress rounded-pill w-100 shadow-none border-0 mb-0" style="height: 5px; background-color: #cbd5e1;">
                                                                <div class="progress-bar {{ $progressBarColor }} rounded-pill" role="progressbar" style="width: {{ $chapterCompliance }}%;"></div>
                                                            </div>
                                                            <span class="badge {{ $badgeColor }} text-nowrap fw-bold" style="font-size: 0.68rem !important; min-width: 45px;">
                                                                {{ $chapterCompliance }}%
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="text-end text-muted font-xs pe-2">—</div>
                                                    @endif
                                                </td>
                                            </tr>

                                            @foreach($awardGroup as $award)
                                            <tr class="award-row-item">
                                                @if($isAdmin)
                                                    <td class="ps-4 align-middle">
                                                        <div class="form-check custom-checkbox">
                                                            <input type="checkbox" name="ids[]" value="{{ $award->id }}" class="form-check-input row-checkbox cursor-pointer shadow-none">
                                                        </div>
                                                    </td>
                                                @endif

                                                <td class="text-secondary fw-medium align-middle">
                                                    <span class="award-serial">{{ $serialNumber }}</span>
                                                    @php
                                                        $serialNumber++;
                                                    @endphp
                                                </td>

                                                <td class="align-middle">

                                                    <div class="d-flex flex-column gap-2">

                                                        {{-- SHORTLIST STATUS BLOCK --}}
                                                        <div class="award-shortlist-state">

                                                            @if($award->currentShortlistStage)

                                                                <div class="d-flex flex-column gap-1">

                                                                    <span class="badge bg-success w-fit">
                                                                        {{ $award->currentShortlistStage->stage->title }}
                                                                    </span>
                                                                </div>
                                                            @else
                                                                <span class="text-danger font-xs fw-semibold">
                                                                    Not Shortlisted
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- CORE INFO --}}
                                                        <span class="award-entry-name text-dark fw-semibold font-base mb-0">
                                                            {{ $award->name }}
                                                        </span>

                                                        @if(empty($award->chapter_id))
                                                            <span class="font-sm lh-sm" style="font-size: 0.68rem;">
                                                                <strong>
                                                                    {{ $award->entry?->select_institution ?? 'N/A' }}
                                                                </strong>
                                                            </span>
                                                        @endif

                                                        <span class="award-reference text-muted tracking-tight font-xs font-monospace">
                                                            {{ $award->reference }}
                                                        </span>

                                                        @if($isAdmin)
                                                            @if($award->national_status == 1)
                                                                <span class="font-xs mt-1">
                                                                    <strong>Approved By:</strong>
                                                                    {{ $award->approvedBy?->name ?? 'System' }}
                                                                    on {{ $award->national_approved_on ? \Carbon\Carbon::parse($award->national_approved_on)->format('d M Y, h:i A') : '—' }}
                                                                </span>
                                                            @endif

                                                            @if($award->national_status == 2)
                                                                <span class="font-xs mt-1">
                                                                    <strong>Rejected By:</strong>
                                                                    {{ $award->rejectedBy?->name ?? 'System' }}
                                                                    on {{ $award->national_rejected_on ? \Carbon\Carbon::parse($award->national_rejected_on)->format('d M Y, h:i A') : '—' }}
                                                                </span>
                                                            @endif
                                                        @endif

                                                        <span class="badge badge-modern bg-soft-primary text-primary mt-1 w-fit">
                                                            {{ $award->type == 'go' ? 'GO AWARD' : $award->type }}
                                                        </span>

                                                    </div>

                                                </td>

                                                <td class="align-middle">
                                                    @php
                                                        $statuses = [
                                                            'Chapter' => ['value' => $award->chapter_status, 'level' => 'chapter'],
                                                            'Zone'    => ['value' => $award->zone_status, 'level' => 'zone'],
                                                            'Field'   => ['value' => $award->field_status, 'level' => 'field'],
                                                            'National'=> ['value' => $award->national_status, 'level' => 'national'],
                                                        ];
                                                    @endphp
                                                    <div class="award-approval-track">
                                                        @foreach($statuses as $label => $data)
                                                            @php
                                                                $statusClass = $data['value'] == 2 ? 'rejected' : ($data['value'] == 1 ? 'approved' : 'pending');
                                                                $statusText = $data['value'] == 2 ? 'Rejected' : ($data['value'] == 1 ? 'Approved' : 'Pending');
                                                                $statusIcon = $data['value'] == 2 ? 'fa-times-circle' : ($data['value'] == 1 ? 'fa-check-circle' : 'fa-clock-o');
                                                            @endphp
                                                            <div class="award-approval-step award-approval-step--{{ $statusClass }}">
                                                                <span class="award-approval-step__level">{{ $label }}</span>
                                                                <span class="award-approval-step__status"><i class="fa {{ $statusIcon }}" aria-hidden="true"></i>{{ $statusText }}
                                                                    @if($data['value'] == 2)
                                                                        <a href="#{{ $data['level'] }}Rejection{{ $award->id }}" data-toggle="modal" class="award-approval-step__feedback" title="View rejection feedback" aria-label="View {{ strtolower($label) }} rejection feedback"><i class="bx bx-message-rounded-dots" aria-hidden="true"></i></a>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>

                                                <td class="text-secondary font-sm align-middle">
                                                    {{ $award->created_at->format('d M Y') }}
                                                    <div class="font-xs text-muted mt-0">{{ $award->created_at->format('h:i A') }}</div>
                                                </td>

                                                <td class="text-end pe-4 align-middle">
                                                    <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                                        @if($canAdjustApprovalStatus)
                                                        <button type="button" class="btn btn-action-icon text-secondary bg-transparent border-0 p-1" data-toggle="modal" data-target="#awardStatusAdjustModal{{ $award->id }}" title="Adjust Status">
                                                            <i class="fa fa-cog font-sm"></i>
                                                        </button>
                                                        @endif

                                                        <a href="{{ route($isAdmin ? 'awards.show' : 'stakeholders.awards.show', $award->id) }}" class="btn btn-action-icon text-warning p-1" title="Edit Entry" onclick="return confirm('Modify this record?');"><i class="fa fa-edit font-sm"></i></a>

                                                        @if(($canArchiveSubmission || $canDeleteSubmission) && !$isArchivedView)
                                                            @if($canArchiveSubmission)
                                                            <a href="#" class="btn btn-action-icon text-secondary p-1" title="Archive Submission" onclick="event.preventDefault(); if (confirm('Archive this submission record?')) { document.getElementById('archive-award-{{ $award->id }}').submit(); }"><i class="fa fa-archive font-sm"></i></a>
                                                            @endif
                                                            @if($canDeleteSubmission)
                                                            <a href="#" class="btn btn-action-icon text-danger p-1" title="Delete Submission" onclick="event.preventDefault(); if (confirm('Delete this submission and its submitted data? This cannot be undone.')) { document.getElementById('delete-award-{{ $award->id }}').submit(); }"><i class="fa fa-trash font-sm"></i></a>
                                                            @endif
                                                        @endif

                                                        @if($isAdmin && $isArchivedView)
                                                            @if($canDeleteSubmission)
                                                            <a href="#" class="btn btn-action-icon text-danger p-1" title="Delete Submission" onclick="event.preventDefault(); if (confirm('Delete this archived award nomination and its submitted data? This cannot be undone.')) { document.getElementById('permanent-delete-award-{{ $award->id }}').submit(); }"><i class="fa fa-trash font-sm"></i></a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach {{-- Properly trailing internal items loop iterator --}}
                                                @empty
                                            <tr>
                                                <td colspan="{{ $isAdmin ? 6 : 5 }}" class="text-center py-5 text-muted">
                                                    <i class="bx bx-file-blank display-4 d-block mb-2 text-light"></i>
                                                    No award submissions matched your parameters.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if($awards->hasPages())
                            <div class="card-footer bg-white border-top px-4 py-3 d-flex justify-content-center">
                                {{ $awards->links() }}
                            </div>
                        @endif
                    </div>
                @if($isAdmin)
                    </form>
                @endif
            </div>
        </div>
    </section>
</div>

@if($isAdmin)
    @include('admin.awards.modals')
@endif
@endsection

@section('extra_scripts')

<script>
    $(function () {
        $('#downloadAssetsSubmitButton').on('click', function (event) {
            var assetType = $('#assetType').val();

            if (!assetType) {
                event.preventDefault();

                $('#assetType').addClass('is-invalid');

                return false;
            }

            $('#assetType').removeClass('is-invalid');

            return true;
        });

        $('#assetType').on('change', function () {
            $(this).removeClass('is-invalid');
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.select2-award-status-filter').select2({
            width: '100%',
            placeholder: 'Filter approval status',
            closeOnSelect: false
        });

        @if(!$isAdmin)
            return;
        @endif

        const masterCheckbox = $('#master-checkbox');
        const rowCheckboxes = $('.row-checkbox');
        const actionSelect = $('#wp-bulk-action-select');
        const applyButton = $('#wp-bulk-action-apply');
        const selectedCounter = $('#selected-counter');
        const bulkForm = $('#bulk-actions-form');
        const methodInput = $('#bulk-form-method');
        const shortlistModal = $('#shortlistStageModal');

        function evaluateBulkToolbarState() {
            const checkedCount = $('.row-checkbox:checked').length;
            const selectedAction = actionSelect.val();

            selectedCounter.text(`${checkedCount} selected`);

            $('.row-checkbox').each(function() {
                const row = $(this).closest('.award-row-item');
                if (this.checked) {
                    row.addClass('table-primary-subtle bg-opacity-25');
                } else {
                    row.removeClass('table-primary-subtle bg-opacity-25');
                }
            });

            if (checkedCount > 0 && selectedAction !== "") {
                applyButton.removeAttr('disabled');
            } else {
                applyButton.attr('disabled', 'true');
            }
        }

        masterCheckbox.on('change', function() {
            rowCheckboxes.prop('checked', this.checked);
            evaluateBulkToolbarState();
        });

        rowCheckboxes.on('change', function() {
            masterCheckbox.prop('checked', rowCheckboxes.length === $('.row-checkbox:checked').length);
            evaluateBulkToolbarState();
        });

        actionSelect.on('change', function() {
            evaluateBulkToolbarState();
        });

        // Intercept Apply Button Click
        applyButton.on('click', function(e) {
            e.preventDefault();

            const selectedOption = actionSelect.find('option:selected');
            const actionValue = actionSelect.val();
            const targetUrl = selectedOption.data('url');
            const httpMethod = selectedOption.data('method');
            const confirmationMessage = selectedOption.data('confirm');

            if (!targetUrl) return;

            // CASE 1: Action is Shortlist -> Open the Modal instead of confirming/submitting directly
            if (actionValue === 'shortlist') {
                // Ensure form points to the shortlist route and spoofing field matches
                bulkForm.attr('action', targetUrl);
                methodInput.val(httpMethod.toUpperCase());

                // Reset any previously picked radio buttons inside the modal
                $('input[name="shortlist_stage_id"]').prop('checked', false);

                // Fire Bootstrap Modal (Works for standard BS4 or BS5 data setups)
                shortlistModal.modal('show');
                return;
            }

            // CASE 2: Standard Direct Actions (Approve, Reject, Delete)
            if (confirm(confirmationMessage)) {
                bulkForm.attr('action', targetUrl);
                methodInput.val(httpMethod.toUpperCase());
                bulkForm.submit();
            }
        });

        // Handle the "Continue" submission button inside the Shortlist Modal
        $('#submit-shortlist-action').on('click', function() {

            const selectedStage = $('input[name="shortlist_stage_id"]:checked').val();
            const remarks = $('#remarks').val();

            if (!selectedStage) {
                alert('Please select a shortlist stage to proceed.');
                return;
            }

            // remove old payload inputs
            $('#dynamic-modal-payload').remove();
            $('#dynamic-remarks-payload').remove();

            // append stage
            bulkForm.append(`
                <input type="hidden"
                    id="dynamic-modal-payload"
                    name="shortlist_stage_id"
                    value="${selectedStage}">
            `);

            // append remarks (optional but safe)
            if (remarks) {
                bulkForm.append(`
                    <input type="hidden"
                        id="dynamic-remarks-payload"
                        name="remarks"
                        value="${remarks}">
                `);
            }

            // close modal
            shortlistModal.modal('hide');

            // submit form
            bulkForm.submit();
        });
    });
</script>
@endsection
