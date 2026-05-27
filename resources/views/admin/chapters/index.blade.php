@extends('layouts.dashboard')
@section('title', 'Chapters')
@section('active')
<li class="breadcrumb-item">Chapters</li>
@endsection
@section('content')
<div class="content-body custom-modern-view">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h4 class="card-title text-dark fw-bold mb-1">All Chapters</h4>
                            <p class="text-muted font-xs mb-0">Manage institutional chapters, track compliance indexes, tokens, and regional statistics.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('chapters.export') }}" class="btn btn-light border font-sm rounded-2 px-3 py-2 d-flex align-items-center gap-1 shadow-none">
                                <i class="bx bx-download text-secondary"></i> Export
                            </a>
                            <a href="{{ route('chapters.create') }}" class="btn btn-primary font-sm rounded-2 px-3 py-2 d-flex align-items-center gap-1 shadow-none">
                                <i class="bx bx-plus"></i> Add New Chapter
                            </a>
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-align-middle mb-0 custom-modern-table">
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
                                                            Token: {{ $chapter->token }}
                                                        </span>
                                                        <a target="_blank" href="{{ route('campus.single', $chapter->id) }}" class="text-primary text-decoration-none">
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

                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .custom-modern-view {
        background-color: #f8fafc;
    }
    .custom-modern-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .custom-modern-table thead th {
        background-color: #fdfdfd;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding-top: 14px;
        padding-bottom: 14px;
        border-bottom: 1px solid #edf2f7;
    }
    .custom-modern-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .custom-modern-table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .custom-modern-table tbody td {
        padding-top: 12px;
        padding-bottom: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-align-middle td, .table-align-middle th {
        vertical-align: middle !important;
    }
    
    /* Global Typography Resizing */
    .font-base { font-size: 0.925rem !important; }
    .font-sm { font-size: 0.825rem !important; }
    .font-xs { font-size: 0.74rem !important; }
    .tracking-tight { letter-spacing: -0.01em; }
    .hover-underline:hover { text-decoration: underline !important; }

    /* Custom Modern Badges */
    .badge-status {
        font-size: 0.65rem !important;
        font-weight: 700;
        padding: 3px 6px;
        border-radius: 4px;
    }
    .badge-counter {
        font-size: 0.7rem !important;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 6px;
        background-color: #ffffff !important;
        border-color: #e2e8f0 !important;
    }
    .bg-soft-success { background-color: #def7ec !important; }
    .bg-soft-danger { background-color: #fde8e8 !important; }
    .bg-soft-warning { background-color: #fef08a !important; }

    /* Action Buttons Icon Engine Overrides */
    .btn-action-icon {
        width: 28px;
        height: 28px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        transition: all 0.15s ease;
    }
    .btn-action-icon:hover {
        background-color: #f1f5f9;
        border-color: #e2e8f0;
        transform: translateY(-1px);
    }
</style>
@endsection