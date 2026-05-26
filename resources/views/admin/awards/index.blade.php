@php
    $awards = $entries['awards'];
    $chapters = $entries['chapters'];
    $fields = $entries['fields'];
    $zones = $entries['zones'];
@endphp
@extends('layouts.dashboard')
@section('title', 'Award Entries')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('stakeholderreports.award.go') }}">Award Entries</a></li>
@endsection

@section('content')
<div class="content-body">
    <section id="awards-dashboard">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">All Award Entries</h4>
        </div>

        <!-- Filters -->
        @php
            $canViewChapter = in_array($user->role_id, array_merge(
                fieldStakeholders(),
                zoneStakeholders(),
                secretariatStakeholders(),
                ncpStakeholders()
            ));

            $canViewZone = in_array($user->role_id, array_merge(
                fieldStakeholders(),
                secretariatStakeholders(),
                ncpStakeholders()
            ));

            $canViewField = in_array($user->role_id, array_merge(
                secretariatStakeholders(),
                ncpStakeholders()
            ));

            $hierarchyCount = collect([
                $canViewField,
                $canViewZone,
                $canViewChapter
            ])->filter()->count();

            $hierarchyCol = $hierarchyCount > 1 ? intval(12 / $hierarchyCount) : 4;

            $approval_statuses = [
                'zone_pending'     => 'Zone Pending',
                'zone_approved'    => 'Zone Approved',
                'zone_rejected'    => 'Zone Rejected',

                'field_pending'    => 'Field Pending',
                'field_approved'   => 'Field Approved',
                'field_rejected'   => 'Field Rejected',

                'national_pending' => 'National Pending',
                'national_approved'=> 'National Approved',
                'national_rejected'=> 'National Rejected',
            ];
        @endphp

        <div class="row mb-3">
            <div class="col-12">
                <form method="GET" class="row g-2 align-items-end">

                    {{-- Date range --}}
                    <div class="col-md-2 mb-2">
                        <label class="form-label">From</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="form-label">To</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    {{-- Field --}}
                    @if($canViewField || $isAdmin)
                        <div class="col-md-{{ $hierarchyCol }} mb-2">
                            <label class="form-label">Field</label>
                            <select name="field_filter" class="form-control">
                                <option value="">All Fields</option>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}" @selected(request('field_filter') == $field->id)>
                                        {{ $field->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Zone --}}
                    @if($canViewZone || $isAdmin)
                        <div class="col-md-{{ $hierarchyCol }} mb-2">
                            <label class="form-label">Zone</label>
                            <select name="zone_filter" class="form-control">
                                <option value="">All Zones</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" @selected(request('zone_filter') == $zone->id)>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Chapter --}}
                    @if($canViewChapter || $isAdmin)
                        <div class="col-md-{{ $hierarchyCol }} mb-2">
                            <label class="form-label">Chapter</label>
                            <select name="chapter_filter" class="form-control">
                                <option value="">All Chapters</option>
                                @foreach($chapters as $chapter)
                                    <option value="{{ $chapter->id }}" @selected(request('chapter_filter') == $chapter->id)>
                                        {{ $chapter->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Status --}}
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Approval Status</label>
                        <select name="status_filter" class="form-control">
                            <option value="">All Statuses</option>
                            @foreach($approval_statuses as $key => $label)
                                <option value="{{ $key }}" @selected(request('status_filter') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Awards Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-align-middle mb-0 custom-modern-table">
                                <tbody>
                                    @forelse($awards as $award)
                                    
                                    {{-- Status Adjustment Modal Frame --}}
                                    <div class="modal fade" id="statusAdjustModal{{ $award->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <form id="bulk-actions-form" method="POST" action="{{ route('stakeholderreports.awards.bulk-delete') }}" onsubmit="return confirm('Are you sure you want to delete all selected items?');">
                                                @csrf
                                                @method('DELETE')

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                                                            
                                                            <!-- Bulk Actions Toolbar Header -->
                                                            <div class="card-header bg-white border-bottom-0 pt-3 pb-2 px-4 d-flex align-items-center justify-content-between">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="dropdown" id="bulk-dropdown-wrapper" style="opacity: 0.5; pointer-events: none; transition: all 0.2s ease;">
                                                                        <button class="btn btn-light border font-sm rounded-2 dropdown-toggle px-3 py-1.5 d-flex align-items-center gap-1 shadow-none" type="button" id="bulkActionsMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <i class="bx bx-layer font-base text-secondary"></i> Bulk Actions
                                                                        </button>
                                                                        <div class="dropdown-menu border-0 shadow-sm rounded-2 font-sm py-1" aria-labelledby="bulkActionsMenu">
                                                                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                                                                <i class="fa fa-trash font-xs"></i> Delete Selected
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <span id="selected-count-badge" class="badge bg-soft-primary text-primary font-xs rounded-pill px-2 py-1 d-none">0 Selected</span>
                                                                </div>
                                                            </div>

                                                            <!-- Modern Table Layout -->
                                                            <div class="card-body p-0">
                                                                <div class="table-responsive">
                                                                    <table class="table table-align-middle mb-0 custom-modern-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="ps-4" style="width: 40px;">
                                                                                    <div class="form-check custom-checkbox">
                                                                                        <input type="checkbox" class="form-check-input cursor-pointer shadow-none" id="master-checkbox">
                                                                                    </div>
                                                                                </th>
                                                                                <th class="text-uppercase text-muted tracking-wider" style="width: 60px;">S/N</th>
                                                                                <th class="text-uppercase text-muted tracking-wider">Nominee & Ref</th>
                                                                                <th class="text-uppercase text-muted tracking-wider">Award Type</th>
                                                                                @if($isAdmin || in_array($user->role_id, array_merge(fieldStakeholders(), zoneStakeholders(), secretariatStakeholders(), ncpStakeholders())))
                                                                                    <th class="text-uppercase text-muted tracking-wider">Origin Location</th>
                                                                                @endif
                                                                                <th class="text-uppercase text-muted tracking-wider">Approval Progress</th>
                                                                                <th class="text-uppercase text-muted tracking-wider">Submitted On</th>
                                                                                <th class="text-uppercase text-muted tracking-wider text-end pe-4" style="min-width: 180px;">Actions</th>
                                                                            </tr>
                                                                        </thead>

                                                                        <tbody>
                                                                            @forelse($awards as $award)
                                                                            <tr>
                                                                                <td class="ps-4">
                                                                                    <div class="form-check custom-checkbox">
                                                                                        <input type="checkbox" name="ids[]" value="{{ $award->id }}" class="form-check-input row-checkbox cursor-pointer shadow-none">
                                                                                    </div>
                                                                                </td>
                                                                                <td class="text-secondary fw-medium">
                                                                                    {{ $loop->iteration }}
                                                                                </td>
                                                                                <td>
                                                                                    <div class="d-flex flex-column">
                                                                                        <span class="text-dark fw-semibold font-base mb-0">
                                                                                            {{ $award?->entries?->firstWhere('key', 'name_surname_first')?->value ?? 'Unnamed Nominee' }}
                                                                                        </span>
                                                                                        <span class="text-muted tracking-tight font-xs">
                                                                                            {{ $award->reference }}
                                                                                        </span>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge badge-modern bg-soft-primary text-primary">
                                                                                        {{ $award->type }}
                                                                                    </span>
                                                                                </td>

                                                                                @if($isAdmin || in_array($user->role_id, array_merge(fieldStakeholders(), zoneStakeholders(), secretariatStakeholders(), ncpStakeholders())))
                                                                                    <td>
                                                                                        <div class="d-flex flex-column font-sm">
                                                                                            <span class="text-dark fw-medium">{{ $award->chapter->name ?? '—' }}</span>
                                                                                            <span class="text-muted font-xs">
                                                                                                {{ $award->zone->name ?? 'N/A' }} &bull; {{ $award->field->name ?? 'N/A' }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                @endif

                                                                                <td>
                                                                                    @php
                                                                                        $statuses = [
                                                                                            'Zone'     => ['value' => $award->zone_status, 'modal' => 'zoneRejection', 'view' => 'stakeholder.modals.zone_rejection_comment'],
                                                                                            'Field'    => ['value' => $award->field_status, 'modal' => 'fieldRejection', 'view' => 'stakeholder.modals.field_rejection_comment'],
                                                                                            'National' => ['value' => $award->national_status, 'modal' => 'secretariatRejection', 'view' => 'stakeholder.modals.secretariat_rejection_comment'],
                                                                                        ];
                                                                                    @endphp

                                                                                    <div class="d-flex flex-column gap-1">
                                                                                        @foreach($statuses as $label => $data)
                                                                                            <div class="d-flex align-items-center font-xs">
                                                                                                <span class="text-muted fw-normal" style="width: 55px;">{{ $label }}</span>
                                                                                                
                                                                                                @if($data['value'] == 2)
                                                                                                    <span class="badge badge-status bg-soft-danger text-danger d-inline-flex align-items-center gap-1">
                                                                                                        Rejected
                                                                                                        <a href="#{{ $data['modal'] }}{{ $award->id }}" data-toggle="modal" class="text-danger lh-1 font-sm" title="View feedback">
                                                                                                            <i class="bx bx-message-rounded-dots"></i>
                                                                                                        </a>
                                                                                                    </span>
                                                                                                    @include($data['view'], ['item' => $award])
                                                                                                @elseif($data['value'] == 1)
                                                                                                    <span class="badge badge-status bg-soft-success text-success">Approved</span>
                                                                                                @else
                                                                                                    <span class="badge badge-status bg-soft-warning text-warning">Pending</span>
                                                                                                @endif
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </td>

                                                                                <td class="text-secondary font-sm">
                                                                                    {{ $award->created_at->format('d M Y') }}
                                                                                    <div class="font-xs text-muted mt-0">{{ $award->created_at->format('h:i A') }}</div>
                                                                                </td>

                                                                                <td class="text-end pe-4">
                                                                                    <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                                                                        {{-- Adjust Status --}}
                                                                                        <button type="button" class="btn btn-action-icon text-secondary" data-toggle="modal" data-target="#statusAdjustModal{{ $award->id }}" title="Adjust Status">
                                                                                            <i class="fa fa-cog font-sm"></i>
                                                                                        </button>

                                                                                        {{-- View Details --}}
                                                                                        <a href="{{ route('stakeholderreports.awards.show', $award->id) }}" class="btn btn-action-icon text-primary" title="View Submission">
                                                                                            <i class="fa fa-eye font-sm"></i>
                                                                                        </a>

                                                                                        {{-- Edit handler --}}
                                                                                        <a href="{{ route('stakeholderreports.awards.edit', $award->id) }}" class="btn btn-action-icon text-warning" title="Edit Entry" onclick="return confirm('Are you sure you want to modify this record?');">
                                                                                            <i class="fa fa-edit font-sm"></i>
                                                                                        </a>

                                                                                        {{-- Download PDF --}}
                                                                                        <a href="{{ route('stakeholderreports.awards.download', $award->id) }}" target="_blank" class="btn btn-action-icon text-success" title="Download Dossier">
                                                                                            <i class="fa fa-download font-sm"></i>
                                                                                        </a>

                                                                                        {{-- Single Row Delete execution --}}
                                                                                        <a href="#" class="btn btn-action-icon text-danger" title="Delete Submission" onclick="event.preventDefault(); if (confirm('Are you absolutely sure you want to remove this submission record?')) { document.getElementById('delete-award-{{ $award->id }}').submit(); }">
                                                                                            <i class="fa fa-trash font-sm"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            @empty
                                                                            <tr>
                                                                                <td colspan="8" class="text-center py-5 text-muted">
                                                                                    <i class="bx bx-file-blank display-4 d-block mb-2 text-light"></i>
                                                                                    No award submissions matched your current search index parameters.
                                                                                </td>
                                                                            </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>

                                                            @if($awards->hasPages())
                                                                <div class="card-footer bg-white border-top-0 px-4 py-3 d-flex justify-content-center">
                                                                    {{ $awards->links() }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                            <!-- Single Row Non-Bulk Forms Fallbacks (Rendered outside primary master loop tag) -->
                                            @foreach($awards as $award)
                                                <form id="delete-award-{{ $award->id }}" action="{{ route('stakeholderreports.awards.delete', $award->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                {{-- Status Adjustment Modal --}}
                                                <div class="modal fade" id="statusAdjustModal{{ $award->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <form method="POST" action="{{ route('awards.adjust.status', $award->id) }}">
                                                            @csrf
                                                            <div class="modal-content border-0 shadow">
                                                                <div class="modal-header border-bottom-0 pb-0">
                                                                    <h5 class="modal-title fw-bold text-dark font-base">Override Status</h5>
                                                                    <button type="button" class="btn-close shadow-none border-0 bg-transparent text-muted" data-dismiss="modal" aria-label="Close" style="font-size: 1.25rem;">&times;</button>
                                                                </div>
                                                                <div class="modal-body py-3">
                                                                    <div class="mb-3">
                                                                        <label class="form-label font-sm fw-medium text-secondary mb-1">Target Action State</label>
                                                                        <select name="approval_status" class="form-select form-control font-sm rounded-2" required>
                                                                            <option value="">-- Select Status --</option>
                                                                            @foreach($approval_statuses as $key => $label)
                                                                                @if(!str_ends_with($key, '_pending'))
                                                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div>
                                                                        <label class="form-label font-sm fw-medium text-secondary mb-1">Reason / Contextual Feedback</label>
                                                                        <textarea name="rejection_reason" class="form-control font-sm rounded-2" rows="3" placeholder="Provide context or a reason for override..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-top-0 pt-0">
                                                                    <button type="button" class="btn btn-light font-sm px-3 rounded-2" data-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-success font-sm px-3 rounded-2">Save Adjustments</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <!-- JavaScript Interactive Script Matrix -->
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function () {
                                                    const masterCheckbox = document.getElementById('master-checkbox');
                                                    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
                                                    const bulkDropdownWrapper = document.getElementById('bulk-dropdown-wrapper');
                                                    const selectedCountBadge = document.getElementById('selected-count-badge');

                                                    function updateBulkInterfaceState() {
                                                        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                                                        
                                                        if (checkedCount > 0) {
                                                            bulkDropdownWrapper.style.opacity = "1";
                                                            bulkDropdownWrapper.style.pointerEvents = "auto";
                                                            selectedCountBadge.textContent = `${checkedCount} Selected`;
                                                            selectedCountBadge.classList.remove('d-none');
                                                        } else {
                                                            bulkDropdownWrapper.style.opacity = "0.5";
                                                            bulkDropdownWrapper.style.pointerEvents = "none";
                                                            selectedCountBadge.classList.add('d-none');
                                                        }
                                                    }

                                                    // Toggle all rows when master checkbox changes
                                                    if (masterCheckbox) {
                                                        masterCheckbox.addEventListener('change', function () {
                                                            rowCheckboxes.forEach(checkbox => {
                                                                checkbox.checked = this.checked;
                                                            });
                                                            updateBulkInterfaceState();
                                                        });
                                                    }

                                                    // Individual row checkbox interactions
                                                    rowCheckboxes.forEach(checkbox => {
                                                        checkbox.addEventListener('change', function () {
                                                            if (!this.checked && masterCheckbox) {
                                                                masterCheckbox.checked = false;
                                                            } else if (document.querySelectorAll('.row-checkbox:checked').length === rowCheckboxes.length && masterCheckbox) {
                                                                masterCheckbox.checked = true;
                                                            }
                                                            updateBulkInterfaceState();
                                                        });
                                                    });
                                                });
                                            </script>

                                            <style>
                                                /* Styling extension to keep layout clean and interactive */
                                                .cursor-pointer { cursor: pointer; }
                                                .custom-checkbox .form-check-input {
                                                    width: 16px;
                                                    height: 16px;
                                                    border-radius: 4px;
                                                    border: 1.5px solid #cbd5e1;
                                                }
                                                .custom-checkbox .form-check-input:checked {
                                                    background-color: #3b82f6;
                                                    border-color: #3b82f6;
                                                }
                                                /* Rest of style matrix styles from previous design follow smoothly... */
                                                .custom-modern-table { border-collapse: separate; border-spacing: 0; }
                                                .custom-modern-table thead th { background-color: #fdfdfd; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.05em; padding-top: 14px; padding-bottom: 14px; border-bottom: 1px solid #edf2f7; }
                                                .custom-modern-table tbody tr:hover { background-color: #f8fafc !important; }
                                                .custom-modern-table tbody td { padding-top: 14px; padding-bottom: 14px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
                                                .font-base { font-size: 0.925rem !important; }
                                                .font-sm { font-size: 0.835rem !important; }
                                                .font-xs { font-size: 0.75rem !important; }
                                                .badge-modern { font-size: 0.7rem !important; font-weight: 600; padding: 5px 10px; border-radius: 6px; }
                                                .bg-soft-primary { background-color: #e3efff !important; }
                                                .badge-status { font-size: 0.65rem !important; font-weight: 600; padding: 2px 6px; border-radius: 4px; }
                                                .bg-soft-success { background-color: #def7ec !important; }
                                                .bg-soft-danger { background-color: #fde8e8 !important; }
                                                .bg-soft-warning { background-color: #fef08a !important; }
                                                .btn-action-icon { width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid transparent; background: transparent; transition: all 0.15s ease; }
                                                .btn-action-icon:hover { background-color: #f1f5f9; border-color: #e2e8f0; transform: translateY(-1px); }
                                            </style>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bx bx-file-blank display-4 d-block mb-2 text-light"></i>
                                            No award submissions matched your current search index parameters.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($awards->hasPages())
                        <div class="card-footer bg-white border-top-0 px-4 py-3 d-flex justify-content-center">
                            {{ $awards->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

<!-- Modern UI Style Overrides -->
<style>
    .custom-modern-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .custom-modern-table thead th {
        background-color: #fdfdfd;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
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
        padding-top: 14px;
        padding-bottom: 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-align-middle td, .table-align-middle th {
        vertical-align: middle !important;
    }
    
    /* Font Sizing Hierarchy Helpers */
    .font-base { font-size: 0.925rem !important; }
    .font-sm { font-size: 0.835rem !important; }
    .font-xs { font-size: 0.75rem !important; }
    .tracking-wider { letter-spacing: 0.06em; }
    .tracking-tight { letter-spacing: -0.01em; }

    /* Custom Modern Badges */
    .badge-modern {
        font-size: 0.7rem !important;
        font-weight: 600;
        letter-spacing: 0.02em;
        padding: 5px 10px;
        border-radius: 6px;
    }
    .bg-soft-primary { background-color: #e3efff !important; }
    
    /* Micro Approval Badges */
    .badge-status {
        font-size: 0.65rem !important;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
    }
    .bg-soft-success { background-color: #def7ec !important; }
    .bg-soft-danger { background-color: #fde8e8 !important; }
    .bg-soft-warning { background-color: #fef08a !important; }

    /* Action Buttons Design */
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
    </section>
</div>
@endsection