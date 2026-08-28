@extends('layouts.dashboard')

@section('title', 'Stakeholders')

@section('active')
<li class="breadcrumb-item">Stakeholders</li>
@endsection

@section('content')
<style>
    .stakeholder-dashboard-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    }

    .stakeholder-stat {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        height: 100%;
    }

    .stakeholder-stat .label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .stakeholder-stat .value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .stakeholder-stat .icon {
        width: 2.9rem;
        height: 2.9rem;
        border-radius: 0.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 99, 235, 0.11);
        color: #1d4ed8;
        border: 1px solid rgba(37, 99, 235, 0.14);
        flex: 0 0 auto;
    }

    .stakeholder-stat--success .icon {
        background: rgba(16, 185, 129, 0.11);
        color: #047857;
        border-color: rgba(16, 185, 129, 0.14);
    }

    .stakeholder-stat--warning .icon {
        background: rgba(245, 158, 11, 0.12);
        color: #b45309;
        border-color: rgba(245, 158, 11, 0.16);
    }

    .stakeholder-stat--neutral .icon {
        background: rgba(100, 116, 139, 0.11);
        color: #334155;
        border-color: rgba(100, 116, 139, 0.14);
    }

    .stakeholder-filter-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    }

    .stakeholder-filter-card label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.35rem;
    }

    .stakeholder-filter-card .form-control,
    .stakeholder-filter-card .form-select {
        border-radius: 0.85rem;
        border-color: #dbe3ee;
        box-shadow: none;
    }

    .stakeholder-filter-card .form-control:focus,
    .stakeholder-filter-card .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.12);
    }

    .stakeholder-filter-card .select2-container,
    .stakeholder-dashboard-card .select2-container {
        width: 100% !important;
    }

    .stakeholder-filter-card .select2-container--default .select2-selection--single,
    .stakeholder-filter-card .select2-container--default .select2-selection--multiple,
    .stakeholder-dashboard-card .select2-container--default .select2-selection--single,
    .stakeholder-dashboard-card .select2-container--default .select2-selection--multiple {
        min-height: 42px;
        border-radius: 0.85rem;
        border: 1px solid #dbe3ee;
        background-color: #fff;
        box-shadow: none;
    }

    .stakeholder-filter-card .select2-container--default .select2-selection--single .select2-selection__rendered,
    .stakeholder-filter-card .select2-container--default .select2-selection--multiple .select2-selection__rendered,
    .stakeholder-dashboard-card .select2-container--default .select2-selection--single .select2-selection__rendered,
    .stakeholder-dashboard-card .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        line-height: 40px;
        padding-left: 0.9rem;
        color: #334155;
    }

    .stakeholder-filter-card .select2-container--default .select2-selection--single .select2-selection__arrow,
    .stakeholder-dashboard-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 6px;
    }

    .stakeholder-filter-card .select2-container--default.select2-container--focus .select2-selection--single,
    .stakeholder-filter-card .select2-container--default.select2-container--focus .select2-selection--multiple,
    .stakeholder-dashboard-card .select2-container--default.select2-container--focus .select2-selection--single,
    .stakeholder-dashboard-card .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.12);
    }

    .stakeholder-filter-card .select2-container--default .select2-selection--single .select2-selection__placeholder,
    .stakeholder-dashboard-card .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8;
    }

    .stakeholder-filter-card .select2-container--default .select2-selection--multiple .select2-selection__choice,
    .stakeholder-dashboard-card .select2-container--default .select2-selection--multiple .select2-selection__choice {
        border-radius: 999px;
        border: 1px solid rgba(37, 99, 235, 0.18);
        background: rgba(37, 99, 235, 0.08);
        color: #1d4ed8;
        padding: 0.15rem 0.45rem;
        margin-top: 6px;
    }

    .stakeholder-action-btn {
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.32rem 0.75rem;
        line-height: 1.1;
    }

    .stakeholder-bulk-bar {
        gap: 0.75rem;
    }

    .stakeholder-bulk-bar .form-select {
        min-width: 240px;
        flex: 1 1 240px;
    }

    .stakeholder-bulk-bar .btn {
        flex: 0 0 auto;
    }

    .appraisal-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        border-radius: 999px;
        padding: 0.24rem 0.55rem;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .appraisal-badge--system {
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        border: 1px solid rgba(37, 99, 235, 0.15);
    }

    .appraisal-badge--evaluation {
        background: rgba(16, 185, 129, 0.1);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.15);
    }

    .appraisal-badge--none {
        background: rgba(100, 116, 139, 0.1);
        color: #334155;
        border: 1px solid rgba(100, 116, 139, 0.15);
    }

    .table-stakeholders th {
        white-space: nowrap;
    }

    .table-stakeholders td {
        vertical-align: middle;
    }

    .stakeholder-section {
        margin-bottom: 1rem;
    }

    .stakeholder-dashboard-card .card-body,
    .stakeholder-filter-card .card-body {
        padding: 1.5rem;
    }

    .stakeholder-toolbar {
        margin-bottom: 0.75rem;
    }

    .stakeholder-toolbar .card-title {
        margin-bottom: 0.25rem;
    }

    .stakeholder-toolbar .btn {
        min-width: 10rem;
    }
</style>

<div class="content-body">
    <section id="stakeholder-dashboard">
        <div class="row stakeholder-section">
            <div class="col-12">
                <div class="card stakeholder-dashboard-card">
                    <div class="card-body">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-7 stakeholder-toolbar">
                                <h4 class="card-title mb-1">Stakeholder Dashboard</h4>
                                <small class="text-muted">Manage stakeholder access, profile data, and appraisal visibility from one place.</small>
                            </div>
                            <div class="col-lg-5">
                                <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                                    <a href="{{ route('stakeholderpersonnel.create') }}" class="btn btn-primary btn-sm rounded-pill">
                                        <i class="fa fa-plus me-1"></i> Add New Stakeholder
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <div class="stakeholder-stat p-3 p-lg-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="icon"><i class="fa fa-users"></i></span>
                                        <div>
                                            <span class="label">Total Stakeholders</span>
                                            <div class="value">{{ $stats['total'] ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stakeholder-stat stakeholder-stat--success p-3 p-lg-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="icon"><i class="fa fa-check-circle"></i></span>
                                        <div>
                                            <span class="label">Active</span>
                                            <div class="value">{{ $stats['active'] ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stakeholder-stat stakeholder-stat--warning p-3 p-lg-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="icon"><i class="fa fa-pencil-square-o"></i></span>
                                        <div>
                                            <span class="label">Appraisal Access</span>
                                            <div class="value">{{ $stats['appraisal_system'] ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stakeholder-stat stakeholder-stat--neutral p-3 p-lg-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="icon"><i class="fa fa-cogs"></i></span>
                                        <div>
                                            <span class="label">Evaluation Access</span>
                                            <div class="value">{{ $stats['appraisal_evaluation'] ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row stakeholder-section">
            <div class="col-12">
                <div class="card stakeholder-filter-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('stakeholderpersonnel.index') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-4 col-md-6">
                                    <label for="search">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search name, email, or phone">
                                </div>
                                <div class="col-lg-2 col-md-6">
                                    <label for="role_id">Role</label>
                                    <select class="form-select" id="role_id" name="role_id">
                                        <option value="">All roles</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-6">
                                    <label for="status">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All statuses</option>
                                        <option value="active" @selected(request('status') === 'active')>Active</option>
                                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-6">
                                    <label for="appraisal_access">Appraisal Access</label>
                                    <select class="form-select" id="appraisal_access" name="appraisal_access">
                                        <option value="">Any</option>
                                        <option value="system" @selected(request('appraisal_access') === 'system')>System</option>
                                        <option value="evaluation" @selected(request('appraisal_access') === 'evaluation')>Evaluation</option>
                                        <option value="none" @selected(request('appraisal_access') === 'none')>None</option>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary rounded-pill" style="min-width: 7rem;">
                                            Filter
                                        </button>
                                        <a href="{{ route('stakeholderpersonnel.index') }}" class="btn btn-outline-secondary rounded-pill">
                                            Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('stakeholderpersonnel.bulk-action') }}" id="stakeholder-bulk-form">
            @csrf
            <div class="row stakeholder-section">
                <div class="col-12">
                <div class="card stakeholder-dashboard-card">
                        <div class="card-body">
                            <div class="row align-items-center g-3 mb-3">
                                <div class="col-lg-6">
                                    <h5 class="mb-1">Bulk Actions</h5>
                                    <small class="text-muted">Select one or more stakeholders, then apply a bulk update.</small>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex stakeholder-bulk-bar align-items-center justify-content-lg-end flex-nowrap">
                                        <select name="bulk_action" class="form-select form-select-sm">
                                            <option value="">Choose action</option>
                                            <option value="allow_appraisal_access">Allow Appraisal Access</option>
                                            <option value="remove_appraisal_access">Remove Appraisal Access</option>
                                        </select>
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill" style="min-width: 6rem;">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-stakeholders mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;">
                                                <input type="checkbox" id="select-all-stakeholders" class="form-check-input">
                                            </th>
                                            <th>S/N</th>
                                            <th>Stakeholder</th>
                                            <th>Role / Placement</th>
                                            <th>Appraisal Access</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stakeholders as $stakeholder)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_ids[]" value="{{ $stakeholder->id }}" class="form-check-input stakeholder-row-check">
                                                </td>
                                                <td>{{ $count + $loop->iteration }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        {!! renderAvatar($stakeholder, 42) !!}
                                                        <div>
                                                            <div class="fw-semibold">
                                                                <a href="{{ route('stakeholderpersonnel.edit', $stakeholder->id) }}" class="text-decoration-none">
                                                                    {{ $stakeholder->name }}
                                                                </a>
                                                            </div>
                                                            <small class="text-muted d-block">{{ $stakeholder->email }}</small>
                                                            <small class="text-muted">{{ $stakeholder->phone ?: 'No phone' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $stakeholder->role?->name ?? 'N/A' }}</div>
                                                    <small class="text-muted d-block">
                                                        @if(!is_null($stakeholder->designation?->name))
                                                            {{ $stakeholder->designation?->name }}
                                                        @elseif(!empty($stakeholder->chapter_id) && $stakeholder->chapter)
                                                            <a href="{{ route('chapters.edit', $stakeholder->chapter->id) }}" class="text-decoration-none">
                                                                Chapter: {{ $stakeholder->chapter->name }}
                                                            </a>
                                                        @elseif(!empty($stakeholder->zone_id) && $stakeholder->zone)
                                                            <a href="{{ route('zones.edit', $stakeholder->zone->id) }}" class="text-decoration-none">
                                                                Zone: {{ $stakeholder->zone->name }}
                                                            </a>
                                                        @elseif(!empty($stakeholder->field_id) && $stakeholder->field)
                                                            <a href="{{ route('fields.edit', $stakeholder->field->id) }}" class="text-decoration-none">
                                                                Field: {{ $stakeholder->field->name }}
                                                            </a>
                                                        @elseif(!empty($stakeholder->portfolio))
                                                            Office: {{ $stakeholder->portfolio }}
                                                        @else
                                                            No placement
                                                        @endif
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @if($stakeholder->access_appraisal_system)
                                                            <span class="appraisal-badge appraisal-badge--system">
                                                                <i class="fa fa-pen-to-square"></i> System
                                                            </span>
                                                        @endif
                                                        @if($stakeholder->access_appraisal_evaluation)
                                                            <span class="appraisal-badge appraisal-badge--evaluation">
                                                                <i class="fa fa-users-gear"></i> Evaluation
                                                            </span>
                                                        @endif
                                                        @if(! $stakeholder->access_appraisal_system && ! $stakeholder->access_appraisal_evaluation)
                                                            <span class="appraisal-badge appraisal-badge--none">
                                                                <i class="fa fa-minus"></i> None
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $stakeholder->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($stakeholder->status) }}
                                                    </span>
                                                    {{-- @if($stakeholder->credentials_sent)
                                                        <div><small class="text-success">Credentials sent</small></div>
                                                    @endif --}}
                                                    @if($stakeholder->last_login)
                                                        <div><small class="text-muted">Last login: {{ $stakeholder->last_login }}</small></div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <a class="btn btn-outline-primary btn-sm stakeholder-action-btn"
                                                           href="{{ route('stakeholderpersonnel.edit', $stakeholder->id) }}">
                                                            <i class="bx bxs-edit me-1"></i> Edit
                                                        </a>
                                                        <button type="submit"
                                                                class="btn btn-outline-success btn-sm stakeholder-action-btn"
                                                                formaction="{{ route('stakeholderpersonnel.resend-credentials', $stakeholder->id) }}"
                                                                formmethod="POST"
                                                                onclick="return confirm('Reset password and resend credentials to this stakeholder?');">
                                                            <i class="fa fa-paper-plane me-1"></i> Resend
                                                        </button>
                                                        <a class="btn btn-outline-dark btn-sm stakeholder-action-btn"
                                                           onclick="return confirm('Login as this stakeholder?');"
                                                           href="{{ route('switchuser', ['id' => $stakeholder->id, 'type' => 'stakeholder']) }}">
                                                            <i class="fa fa-unlock me-1"></i> Switch
                                                        </a>
                                                        <a class="btn btn-outline-danger btn-sm stakeholder-action-btn"
                                                           onclick="return confirm('Are you really sure?');"
                                                           href="{{ route('stakeholderpersonnel.delete', $stakeholder->id) }}">
                                                            <i class="fa fa-trash me-1"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">No stakeholders found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">
                                <div class="text-muted small">
                                    Showing {{ $stakeholders->firstItem() ?? 0 }} to {{ $stakeholders->lastItem() ?? 0 }} of {{ $stakeholders->total() }} stakeholders
                                </div>
                                <div>
                                    {{ $stakeholders->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

@endsection

@section('extra_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all-stakeholders');
    const checks = document.querySelectorAll('.stakeholder-row-check');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checks.forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });
        });
    }
});
</script>
@endsection
