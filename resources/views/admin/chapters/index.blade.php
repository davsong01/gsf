@extends('layouts.dashboard')
@section('title', 'Chapters')
@section('active')
<li class="breadcrumb-item">Chapters</li>
@endsection
@section('content')
<div class="content-body chapter-index">
    @include('includes.alerts')
    <header class="chapter-index__hero">
        <div><span class="chapter-index__eyebrow"><i class="fa fa-map-marker" aria-hidden="true"></i> GSF network</span><h1>Chapters</h1><p>Manage campus fellowships, chapter leadership, reporting progress and membership.</p></div>
        <div class="chapter-index__hero-actions">
            <a href="{{ route('chapters.export') }}" class="chapter-index__button chapter-index__button--light"><i class="fa fa-download" aria-hidden="true"></i> Export</a>
            <a href="{{ route('chapters.create') }}" class="chapter-index__button chapter-index__button--primary"><i class="fa fa-plus" aria-hidden="true"></i> Add chapter</a>
        </div>
    </header>
    <div class="chapter-index__metrics" aria-label="Chapter overview">
        <div class="chapter-index__metric"><span class="chapter-index__metric-icon"><i class="fa fa-university" aria-hidden="true"></i></span><span><small>Campus fellowships</small><strong>{{ number_format($chapters->count()) }}</strong></span></div>
        <div class="chapter-index__metric"><span class="chapter-index__metric-icon chapter-index__metric-icon--green"><i class="fa fa-user-circle" aria-hidden="true"></i></span><span><small>With a chapter representative</small><strong>{{ number_format($chapters->filter(fn ($chapter) => $chapter->stakeholder)->count()) }}</strong></span></div>
    </div>
    <section class="chapter-index__panel" id="basic-datatable">
        <div class="chapter-index__panel-head"><div><h2>All chapters</h2><p>Search, review reporting compliance, or manage a chapter.</p></div><label class="chapter-index__search"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search chapters</span><input id="chapter-index-search" type="search" placeholder="Find a chapter…" autocomplete="off"></label></div>
        <div class="card-content">
            <div class="card-body p-0">
                <div class="table-responsive">
                                <table id="chapters-table" class="table zero-configuration table-align-middle mb-0 chapter-index__table">
                                    <thead>
                                        <tr>
                                            <th class="ps-4" style="width: 60px;">S/N</th>
                                            <th>Chapter Name</th>
                                            <th>Chapter Rep</th>
                                            <th style="min-width: 200px;">Location & Compliance</th>
                                            <th>Contact Info</th>
                                            <th>Statistics Metrics</th>
                                            <th class="text-end pe-4" style="min-width: 160px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($chapters as $chapter)
                                        <tr>
                                            <td class="ps-4 text-secondary fw-medium font-sm">
                                                {{ $count++ }}
                                            </td>

                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-semibold font-base mb-0">
                                                        {{ $chapter->name }}
                                                    </span>
                                                    <div class="d-flex align-items-center gap-2 mt-0.5 font-xs">
                                                        <span class="badge bg-soft-danger text-danger tracking-tight" style="font-size: 0.68rem; padding: 2px 5px;">
                                                            Slug: {{ $chapter->slug }}
                                                        </span>
                                                        <a target="_blank" href="{{ route('campus.single', $chapter->slug) }}" class="text-primary text-decoration-none">
                                                            <i class="bx bx-link-external font-xs"></i> Portal View
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="font-sm">
                                                @if ($chapter->stakeholder)
                                                    <a href="{{ route('stakeholderpersonnel.edit', $chapter->stakeholder->id) }}" class="fw-medium text-dark text-decoration-none hover-underline">
                                                        <i class="fa fa-user-circle text-muted font-xs me-1"></i> {{ $chapter->stakeholder->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted tracking-tight">N/A</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="compliance-wrapper">
                                                    @php
                                                        $chapterCompliance = $chapter ? $chapter->reportCompliance() : 0;
                                                        
                                                        if ($chapterCompliance >= 80) {
                                                            $progressBarColor = 'bg-success';
                                                            $badgeColor = 'bg-soft-success text-success';
                                                        } elseif ($chapterCompliance >= 50) {
                                                            $progressBarColor = 'bg-warning text-dark';
                                                            $badgeColor = 'bg-soft-warning text-warning';
                                                        } else {
                                                            $progressBarColor = 'bg-danger';
                                                            $badgeColor = 'bg-soft-danger text-danger';
                                                        }
                                                    @endphp

                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="text-muted font-xs tracking-tight text-truncate" style="max-width: 130px;" title="{{ $chapter->zone->name ?? 'N/A' }} • {{ $chapter->field->name ?? 'N/A' }}">
                                                            {{ $chapter->zone->name ?? 'N/A' }} &bull; {{ $chapter->field->name ?? 'N/A' }}
                                                        </span>
                                                        <span class="badge badge-status {{ $badgeColor }}">
                                                            {{ $chapterCompliance }}%
                                                        </span>
                                                    </div>

                                                    <div class="progress rounded-pill shadow-none" style="height: 5px; background-color: #f1f5f9;">
                                                        <div class="progress-bar {{ $progressBarColor }} rounded-pill" 
                                                            role="progressbar" 
                                                            style="width: {{ $chapterCompliance }}%;" 
                                                            aria-valuenow="{{ $chapterCompliance }}" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex flex-column font-xs gap-0.5 text-secondary">
                                                    <span class="text-truncate" style="max-width: 160px;" title="{{ $chapter->email }}">
                                                        <i class="bx bx-envelope font-xs text-muted me-0.5"></i>{{ $chapter->email }}
                                                    </span>
                                                    <span>
                                                        <i class="bx bx-phone font-xs text-muted me-0.5"></i>{{ $chapter->phone }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex flex-wrap gap-1 font-xs">
                                                    <span class="badge badge-counter bg-light border text-dark">
                                                        <strong class="text-primary">{{ $chapter->members()->count() }}</strong> Stu
                                                    </span>
                                                    <span class="badge badge-counter bg-light border text-dark">
                                                        <strong class="text-success">{{ $chapter->alumni()->count() }}</strong> Alum
                                                    </span>
                                                    <span class="badge badge-counter bg-light border text-dark">
                                                        <strong class="text-warning text-dark">{{ $chapter->stakeholders->count() }}</strong> Stk
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="text-end pe-4">
                                                <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                                    <button type="button" class="btn btn-action-icon text-secondary" data-toggle="modal" data-target="#moveChapterModal{{ $chapter->id }}" title="Move members, alumni & stakeholders">
                                                        <i class="fa fa-exchange font-sm"></i>
                                                    </button>

                                                    <a href="{{ route('chapters.edit', $chapter->id) }}" class="btn btn-action-icon text-primary" title="View/Update chapter details">
                                                        <i class="fa fa-edit font-sm"></i>
                                                    </a>

                                                    <a href="{{ route('chapter.newtoken', $chapter->id) }}" class="btn btn-action-icon text-warning" title="Generate new token">
                                                        <i class="fa fa-refresh font-sm"></i>
                                                    </a>

                                                    <a href="{{ route('chapters.delete', $chapter->id) }}" class="btn btn-action-icon text-danger" title="Delete chapter" onclick="return confirm('Are you really sure you want to remove this chapter?');">
                                                        <i class="fa fa-trash font-sm"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="moveChapterModal{{ $chapter->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                                <form method="POST" action="{{ route('chapters.move-members', $chapter->id) }}">
                                                    @csrf
                                                    <div class="modal-content border-0 shadow-lg rounded-3">
                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <h5 class="modal-title fw-bold text-dark font-base">Move Chapter Core Assets</h5>
                                                            <button type="button" class="btn-close shadow-none border-0 bg-transparent text-muted" data-dismiss="modal" aria-label="Close" style="font-size: 1.25rem;">&times;</button>
                                                        </div>

                                                        <div class="modal-body py-3 font-sm">
                                                            <div class="alert bg-soft-danger border-0 p-3 rounded-3 mb-3 text-danger">
                                                                <div class="fw-bold mb-1"><i class="fa fa-exclamation-triangle me-1"></i> Irreversible Operation Alert</div>
                                                                This action completely relocates <strong>all registered students, alumni configurations, and stakeholders</strong> from <strong>{{ $chapter->name }}</strong>.
                                                            </div>

                                                            <p class="text-secondary mb-3">
                                                                Target entities will automatically inherit regional configuration records mapped to their new destination chapter.
                                                            </p>

                                                            <div class="mb-2">
                                                                <label class="form-label font-sm fw-medium text-secondary mb-1">Select Destination Chapter Space</label>
                                                                <select name="new_chapter_id" class="form-select form-control font-sm rounded-2" required>
                                                                    <option value="">-- Select Chapter Location --</option>
                                                                    @foreach($chapters as $targetChapter)
                                                                        @if($targetChapter->id != $chapter->id)
                                                                            <option value="{{ $targetChapter->id }}">
                                                                                {{ $targetChapter->name }}
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer border-top-0 pt-0">
                                                            <button type="button" class="btn btn-light font-sm px-3 rounded-2" data-dismiss="modal">Cancel Pipeline</button>
                                                            <button type="submit" onclick="return confirm('Confirm destination criteria allocation? This process can not be undone.');" class="btn btn-danger font-sm px-3 rounded-2">
                                                                Execute Migration
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

    </section>
</div>

<style>
    .chapter-index { max-width:1600px; margin:0 auto; color:#183251; }
    .chapter-index__hero { display:flex; align-items:center; justify-content:space-between; gap:24px; margin-bottom:18px; padding:26px 30px; border:1px solid #e4ebf2; border-radius:18px; background:linear-gradient(120deg,#fff 0%,#f7fbfe 100%); box-shadow:0 10px 28px rgba(26,52,80,.045); }
    .chapter-index__eyebrow { display:flex; align-items:center; gap:8px; margin-bottom:8px; color:#21809a; font-size:10px; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }
    .chapter-index__hero h1 { margin:0 0 6px; color:#183251; font-family:Georgia,serif; font-size:29px; font-weight:650; }
    .chapter-index__hero p,.chapter-index__panel-head p { margin:0; color:#8090a3; font-size:12px; }
    .chapter-index__hero-actions { display:flex; flex-wrap:wrap; gap:9px; }
    .chapter-index__button { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:40px; padding:0 15px; border:1px solid #e1e8ef; border-radius:9px; font-size:12px; font-weight:750; text-decoration:none!important; white-space:nowrap; }
    .chapter-index__button--light { color:#49647d; background:#fff; }
    .chapter-index__button--light:hover { color:#244b69; background:#f7fafc; }
    .chapter-index__button--primary { border-color:#1e6695; color:#fff; background:#1e6695; }
    .chapter-index__button--primary:hover { color:#fff; background:#175779; }
    .chapter-index__metrics { display:grid; grid-template-columns:repeat(2,minmax(220px,1fr)); gap:14px; margin-bottom:18px; }
    .chapter-index__metric { display:flex; align-items:center; gap:13px; min-height:82px; padding:15px 18px; border:1px solid #e5ebf2; border-radius:14px; background:#fff; box-shadow:0 6px 18px rgba(26,52,80,.035); }
    .chapter-index__metric-icon { display:grid; width:42px; height:42px; place-items:center; border-radius:12px; color:#236b98; background:#eaf4fa; font-size:16px; }
    .chapter-index__metric-icon--green { color:#287866; background:#eaf6f1; }
    .chapter-index__metric small,.chapter-index__metric strong { display:block; }
    .chapter-index__metric small { margin-bottom:3px; color:#7b8ca0; font-size:11px; }
    .chapter-index__metric strong { color:#203d5b; font-size:20px; line-height:1.1; }
    .chapter-index__panel { overflow:hidden; border:1px solid #e5ebf2; border-radius:16px; background:#fff; box-shadow:0 10px 28px rgba(26,52,80,.05); }
    .chapter-index__panel-head { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:20px 23px; border-bottom:1px solid #edf1f5; }
    .chapter-index__panel-head h2 { margin:0 0 4px; color:#203b58; font-size:16px; font-weight:800; }
    .chapter-index__search { display:flex; align-items:center; gap:9px; width:min(100%,300px); height:40px; padding:0 12px; border:1px solid #dfe7ef; border-radius:9px; color:#8594a5; background:#fff; }
    .chapter-index__search:focus-within { border-color:#81b4d0; box-shadow:0 0 0 3px rgba(50,130,175,.1); }
    .chapter-index__search input { width:100%; border:0; outline:0; color:#28435e; background:transparent; font-size:12px; }
    .chapter-index__search input::placeholder { color:#9aa7b5; }
    .chapter-index__table { min-width:1050px; margin:0; border-collapse:separate; border-spacing:0; }
    .chapter-index__table thead th { padding:13px 14px; border-bottom:1px solid #edf1f5; color:#718096; background:#fbfcfd; font-size:10px; font-weight:850; letter-spacing:.07em; text-transform:uppercase; white-space:nowrap; }
    .chapter-index__table tbody td { padding:14px; border-bottom:1px solid #f0f3f6; color:#596b80; font-size:12px; vertical-align:middle!important; }
    .chapter-index__table tbody tr { transition:background .15s; }
    .chapter-index__table tbody tr:hover { background:#f8fafc; }
    .chapter-index__table tbody tr:last-child td { border-bottom:0; }
    .chapter-index__table .font-base { color:#203b58!important; font-size:13px!important; }
    .chapter-index__table .font-sm { font-size:12px!important; }
    .chapter-index__table .font-xs { font-size:10px!important; }
    .chapter-index__table .badge-status { padding:4px 7px; border-radius:6px; font-size:10px!important; font-weight:800; }
    .chapter-index__table .badge-counter { padding:5px 7px; border:1px solid #e4ebf1!important; border-radius:7px; background:#fff!important; font-size:10px!important; }
    .chapter-index__table .bg-soft-success { background:#def7ec!important; }
    .chapter-index__table .bg-soft-danger { background:#fde8e8!important; }
    .chapter-index__table .bg-soft-warning { background:#fef3c7!important; }
    .btn-action-icon { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; padding:0; border:1px solid transparent; border-radius:8px; background:transparent; transition:all .15s ease; }
    .btn-action-icon:hover { transform:translateY(-1px); border-color:#e2eaf0; background:#f1f6f9; }
    .chapter-index .dataTables_wrapper { padding:0 18px 14px; }
    .chapter-index .dataTables_filter { display:none; }
    .chapter-index .dataTables_info,.chapter-index .dataTables_length { color:#8290a0; font-size:11px; }
    .chapter-index .dataTables_length select { margin:0 4px; border:1px solid #e1e8ef; border-radius:6px; color:#405a73; }
    .chapter-index .dataTables_paginate .paginate_button { border-radius:7px!important; font-size:11px; }
    .chapter-index .dataTables_paginate .paginate_button.current { border-color:#dceaf3!important; color:#1c638f!important; background:#edf6fb!important; }
    @media(max-width:760px) { .chapter-index__hero { align-items:flex-start; flex-direction:column; padding:22px; } .chapter-index__hero-actions { width:100%; } .chapter-index__hero-actions .chapter-index__button { flex:1; } .chapter-index__metrics { grid-template-columns:1fr; gap:9px; } .chapter-index__panel-head { align-items:flex-start; flex-direction:column; padding:18px; } .chapter-index__search { width:100%; } .chapter-index .dataTables_wrapper { padding:0 12px 12px; } }
</style>
@section('extra_scripts')
<script>
$(function () {
    var bindChapterSearch = function () {
        var table = $(this).DataTable();
        $('#chapter-index-search').on('input', function () { table.search(this.value).draw(); });
    };
    var $table = $('#chapters-table');
    if ($.fn.DataTable.isDataTable($table)) bindChapterSearch.call($table);
    else $table.one('init.dt', bindChapterSearch);
});
</script>
@endsection
@endsection
