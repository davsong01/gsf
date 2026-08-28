@extends('layouts.dashboard')

@section('title', 'Appraisals')

@section('active')
<li class="breadcrumb-item">Appraisals</li>
@endsection

@section('content')
<style>
    .appraisal-action-btn {
        padding: 0.28rem 0.65rem;
        font-size: 0.78rem;
        line-height: 1.1;
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .appraisal-action-btn i {
        font-size: 0.78rem;
    }

    .appraisal-stakeholder-link {
        color: #0f172a;
        text-decoration: none;
        transition: color 0.15s ease, text-decoration-color 0.15s ease;
    }

    .appraisal-stakeholder-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
        text-underline-offset: 0.18rem;
    }

    .appraisal-stakeholder-link i {
        font-size: 0.72rem;
        color: #64748b;
    }

    .appraisal-stat-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .appraisal-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.1);
        border-color: rgba(59, 130, 246, 0.18);
    }

    .appraisal-stat-card::after {
        content: '';
        position: absolute;
        inset: auto -18% -42% auto;
        width: 9rem;
        height: 9rem;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(59, 130, 246, 0) 70%);
        pointer-events: none;
    }

    .appraisal-stat-card--secondary::after {
        background: radial-gradient(circle, rgba(16, 185, 129, 0.14) 0%, rgba(16, 185, 129, 0) 70%);
    }

    .appraisal-stat-card--muted::after {
        background: radial-gradient(circle, rgba(100, 116, 139, 0.14) 0%, rgba(100, 116, 139, 0) 70%);
    }

    .appraisal-stat-inner {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 0.95rem;
        min-height: 100%;
        padding: 1rem 1.1rem;
    }

    .appraisal-stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        color: #0f172a;
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.14);
    }

    .appraisal-stat-card--secondary .appraisal-stat-icon {
        color: #047857;
        background: rgba(16, 185, 129, 0.12);
        border-color: rgba(16, 185, 129, 0.14);
    }

    .appraisal-stat-card--muted .appraisal-stat-icon {
        color: #334155;
        background: rgba(100, 116, 139, 0.12);
        border-color: rgba(100, 116, 139, 0.14);
    }

    .appraisal-stat-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .appraisal-stat-value {
        font-size: 2rem;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }

    .appraisal-stat-copy {
        font-size: 0.84rem;
        color: #475569;
    }

    .appraisal-filter-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    }

    .appraisal-filter-card label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.35rem;
    }

    .appraisal-filter-card .form-control,
    .appraisal-filter-card .form-select {
        border-radius: 0.85rem;
        border-color: #dbe3ee;
        box-shadow: none;
    }

    .appraisal-filter-card .form-control:focus,
    .appraisal-filter-card .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.12);
    }

    .appraisal-filter-actions .btn {
        min-width: 7rem;
        flex: 0 0 auto;
    }
</style>
<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="card-title mb-1">Stakeholder Appraisals</h4>
                            <small class="text-muted">Track self appraisal and evaluation status, then reopen a published form when needed.</small>
                        </div>
                        <span class="badge bg-light text-dark border">{{ $filteredTotal ?? $stakeholders->total() }} stakeholders</span>
                    </div>

                    <div class="card-body">
                        <div class="card appraisal-filter-card mb-3">
                            <div class="card-body">
                                <form method="GET" action="{{ route('stakeholderappraisals.index') }}">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-lg-2 col-md-6">
                                            <label for="search">Search</label>
                                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search name, email, or phone">
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <label for="field_id">Field</label>
                                            <select class="form-select" id="field_id" name="field_id">
                                                <option value="">All fields</option>
                                                @foreach($fields as $field)
                                                    <option value="{{ $field->id }}" @selected(request('field_id') == $field->id)>{{ $field->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <label for="zone_id">Zone</label>
                                            <select class="form-select" id="zone_id" name="zone_id">
                                                <option value="">All zones</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}" @selected(request('zone_id') == $zone->id)>{{ $zone->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-6">
                                            <label for="self_status">Fill Status</label>
                                            <select class="form-select" id="self_status" name="self_status">
                                                <option value="">Any</option>
                                                <option value="draft" @selected(request('self_status') === 'draft')>Draft</option>
                                                <option value="published" @selected(request('self_status') === 'published')>Published</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-6">
                                            <label for="evaluation_status">Evaluation Status</label>
                                            <select class="form-select" id="evaluation_status" name="evaluation_status">
                                                <option value="">Any</option>
                                                <option value="draft" @selected(request('evaluation_status') === 'draft')>Draft</option>
                                                <option value="published" @selected(request('evaluation_status') === 'published')>Published</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end mt-1 appraisal-filter-actions">
                                        <button type="submit" class="btn btn-primary rounded-pill">Filter</button>
                                        <button type="submit" class="btn btn-success rounded-pill" formmethod="GET" formaction="{{ route('stakeholderappraisals.export') }}">
                                            Download Excel
                                        </button>
                                        <a href="{{ route('stakeholderappraisals.index') }}" class="btn btn-outline-secondary rounded-pill">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="appraisal-stat-card">
                                    <div class="appraisal-stat-inner">
                                        <div class="appraisal-stat-icon">
                                            <i class="fa fa-users fa-lg"></i>
                                        </div>
                                        <div>
                                            <span class="appraisal-stat-label">Stakeholders</span>
                                            <div class="appraisal-stat-value">{{ $filteredTotal ?? $stakeholders->total() }}</div>
                                            <div class="appraisal-stat-copy">Filtered stakeholders currently in view.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="appraisal-stat-card appraisal-stat-card--secondary">
                                    <div class="appraisal-stat-inner">
                                        <div class="appraisal-stat-icon">
                                            <i class="fa fa-file-text-o fa-lg"></i>
                                        </div>
                                        <div>
                                            <span class="appraisal-stat-label">Appraised</span>
                                            <div class="appraisal-stat-value">{{ $appraisedCount ?? 0 }}</div>
                                            <div class="appraisal-stat-copy">Filtered stakeholders who have submitted self appraisals.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="appraisal-stat-card appraisal-stat-card--muted">
                                    <div class="appraisal-stat-inner">
                                        <div class="appraisal-stat-icon">
                                            <i class="fa fa-check-circle fa-lg"></i>
                                        </div>
                                        <div>
                                            <span class="appraisal-stat-label">Appraisals Evaluated</span>
                                            <div class="appraisal-stat-value">{{ $evaluatedCount ?? 0 }}</div>
                                            <div class="appraisal-stat-copy">Filtered self appraisals that already have evaluation feedback.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- @if(session('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif --}}

                        <div class="card appraisal-filter-card mb-3">
                            <div class="card-body">
                                <form method="POST" action="{{ route('stakeholderappraisals.bulk-remind') }}" id="bulkReminderForm">
                                    @csrf
                                    <div class="row g-2 align-items-end">
                                        <div class="col-lg-5 col-md-6">
                                            <label for="bulk_action" class="form-label">Bulk Action</label>
                                            <select name="action" id="bulk_action" class="form-select">
                                                <option value="remind">Send Reminder</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <button type="submit" class="btn btn-primary rounded-pill w-100" onclick="return confirm('Queue reminder emails for the selected stakeholders?')">
                                                Apply
                                            </button>
                                        </div>
                                    </div>
                                    <div id="bulkReminderInputs"></div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped align-middle zero-configuration">
                                <thead>
                                    <tr>
                                        <th style="width: 42px;">
                                            <input type="checkbox" id="selectAllStakeholders">
                                        </th>
                                        <th>S/N</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Self Status</th>
                                        <th>Evaluation Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stakeholders as $stakeholder)
                                        @php
                                            $appraisal = $stakeholder->appraisal;
                                            $pendingReminders = $appraisalService->appraisalReminderPayloads($stakeholder, $appraisal);
                                            $showEvaluationStatus = (bool) (
                                                $appraisal?->evaluation_status
                                                || $appraisal?->evaluation_published_at
                                                || $appraisal?->evaluator_id
                                                || $appraisalService->hasEvaluationStatus($stakeholder)
                                            );
                                            $flashLabel = session('appraisal_status_label');
                                            $flashScope = session('appraisal_status_scope');
                                            $flashStakeholderId = session('appraisal_status_stakeholder_id');
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input stakeholder-row-checkbox" value="{{ $stakeholder->id }}">
                                            </td>
                                            <td>{{ ($stakeholders->firstItem() ?? 0) + $loop->index }}</td>
                                            <td>
                                                <a href="{{ route('stakeholderpersonnel.edit', $stakeholder->id) }}" target="_blank" rel="noopener" class="fw-semibold appraisal-stakeholder-link">
                                                    {{ $stakeholder->name }}
                                                    <i class="fa fa-arrow-up-right-from-square ms-1"></i>
                                                </a> <br>
                                                <small class="text-muted">{{ $stakeholder->email }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $stakeholder->role?->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $stakeholder->designation?->name ?? 'No designation' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ ($appraisal?->self_status ?? 'draft') === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $appraisal?->self_status ?? 'draft' }}
                                                </span>
                                                @if(($appraisal?->self_status ?? 'draft') === 'published')
                                                    <small class="text-muted d-block mt-1">Locked</small>
                                                @endif
                                                <div class="small text-muted mt-1">
                                                    {{ $appraisal?->self_published_at ? 'Published ' . $appraisal->self_published_at->format('d M Y, h:i A') : 'Not published yet' }}
                                                </div>
                                                @if($flashLabel && $flashScope === 'self' && (int) $flashStakeholderId === (int) $stakeholder->id)
                                                    <small class="text-success d-block mt-1">{{ $flashLabel }}</small>
                                                @endif
                                                @if(($appraisal?->self_status ?? 'draft') === 'published')
                                                    <form action="{{ route('stakeholderappraisals.unlock-self', $stakeholder) }}" method="POST" class="mt-2" onsubmit="return confirm('Reopen this self appraisal?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-warning appraisal-action-btn">
                                                            <i class="fa fa-eraser me-1"></i>
                                                            Clear Form
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                @if($showEvaluationStatus)
                                                    <span class="badge {{ ($appraisal?->evaluation_status ?? 'draft') === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $appraisal?->evaluation_status ?? 'draft' }}
                                                    </span>
                                                    @if(($appraisal?->evaluation_status ?? 'draft') === 'published')
                                                        <small class="text-muted d-block mt-1">Locked</small>
                                                    @endif
                                                    <div class="small text-muted mt-1">
                                                        {{ $appraisal?->evaluation_published_at ? 'Published ' . $appraisal->evaluation_published_at->format('d M Y, h:i A') : 'Not published yet' }}
                                                    </div>
                                                    @if($flashLabel && $flashScope === 'evaluation' && (int) $flashStakeholderId === (int) $stakeholder->id)
                                                        <small class="text-success d-block mt-1">{{ $flashLabel }}</small>
                                                    @endif
                                                    @if(($appraisal?->evaluation_status ?? 'draft') === 'published')
                                                        <form action="{{ route('stakeholderappraisals.unlock-evaluation', $stakeholder) }}" method="POST" class="mt-2" onsubmit="return confirm('Reopen this evaluation?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-secondary appraisal-action-btn">
                                                                <i class="fa fa-eraser me-1"></i>
                                                                Clear Evaluation
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                                    <a href="{{ route('stakeholderappraisals.evaluation.edit', $stakeholder) }}"
                                                       class="btn btn-outline-primary appraisal-action-btn">
                                                        <i class="fa fa-eye me-1"></i>
                                                        View
                                                    </a>
                                                    <a href="{{ route('stakeholderappraisals.pdf', $stakeholder) }}"
                                                       target="_blank" rel="noopener"
                                                       class="btn btn-outline-danger appraisal-action-btn">
                                                        <i class="fa fa-file-pdf me-1"></i>
                                                        PDF
                                                    </a>
                                                    @if(count($pendingReminders))
                                                        <form action="{{ route('stakeholderappraisals.remind', $stakeholder) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="btn btn-outline-warning appraisal-action-btn"
                                                                    onclick="return confirm('Queue reminder email(s) for this stakeholder?')">
                                                                <i class="fa fa-bell me-1"></i>
                                                                Reminder
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-light text-muted border">No reminders due</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">No stakeholders found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">
                                <div class="text-muted small">
                                    Showing {{ $stakeholders->firstItem() ?? 0 }} to {{ $stakeholders->lastItem() ?? 0 }} of {{ $filteredTotal ?? $stakeholders->total() }} stakeholders
                                </div>
                                <div>
                                    {{ $stakeholders->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </section>
</div>
@endsection

@section('extra_scripts')
<script>
    document.getElementById('selectAllStakeholders')?.addEventListener('change', function () {
        document.querySelectorAll('.stakeholder-row-checkbox').forEach(function (checkbox) {
            checkbox.checked = this.checked;
        }, this);
    });

    document.getElementById('bulkReminderForm')?.addEventListener('submit', function (event) {
        const container = document.getElementById('bulkReminderInputs');
        const selected = Array.from(document.querySelectorAll('.stakeholder-row-checkbox:checked'));

        if (!selected.length) {
            event.preventDefault();
            alert('Please select at least one stakeholder.');
            return;
        }

        container.innerHTML = '';
        selected.forEach(function (checkbox) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'stakeholders[]';
            input.value = checkbox.value;
            container.appendChild(input);
        });
    });
</script>
@endsection
