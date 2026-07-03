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
</style>
@endsection

@section('content')
<div class="content-body">
    <section id="awards-dashboard">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">{{ $title }}</h4>
            <span class="badge bg-primary rounded-pill font-sm fw-bold px-2 py-0.5">
                    {{ method_exists($awards, 'total') ? $awards->total() : $awards->count() }} Total
                </span>
        </div>

        <!-- Filters Form Panel -->
        <div class="row">
            <div class="col-12">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Reference</label>
                        <input type="text" name="reference" class="form-control" value="{{ request('reference') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">From</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">To</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    @if($canViewField || $isAdmin)
                        <div class="col-md-{{ $hierarchyCol }} mb-2">
                            <label class="form-label">Field</label>
                            <select name="field_id" class="form-control">
                                <option value="">All Fields</option>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}" @selected(request('field_id') == $field->id)>{{ $field->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($canViewZone || $isAdmin)
                        <div class="col-md-{{ $hierarchyCol }} mb-2">
                            <label class="form-label">Zone</label>
                            <select name="zone_id" class="form-control">
                                <option value="">All Zones</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" @selected(request('zone_id') == $zone->id)>{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($canViewChapter || $isAdmin)
                        <div class="col-md-{{ $hierarchyCol }} mb-2">
                            <label class="form-label">Chapter</label>
                            <select name="chapter_id" class="form-control">
                                <option value="">All Chapters</option>
                                @foreach($chapters as $chapter)
                                    <option value="{{ $chapter->id }}" @selected(request('chapter_id') == $chapter->id)>{{ $chapter->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-2 mb-2">
                        <label class="form-label">Approval Status</label>
                        <select name="status_filter" class="form-control">
                            <option value="">All Statuses</option>
                            @foreach($approval_statuses as $key => $label)
                                <option value="{{ $key }}" @selected(request('status_filter') == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Shortlist Stages</label>
                        <select name="current_shortlist_stage_id" class="form-control">
                            <option value="">All Stages</option>
                            @foreach($shortlistStages as $stage)
                                <option value="{{ $stage->id }}" @selected(request('current_shortlist_stage_id') == $stage->id)>{{ $stage->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-secondary w-100">Filter</button>
                    </div>
                    @if($isAdmin)
                    <div class="col-md-2 mb-2">
                        <a href="{{route('award.report.download', $type)}}" class="btn btn-info w-100">Download Report</a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{route('award.report.assets')}}" class="btn btn-danger w-100">Download Assets</a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{route('award.report.remove.duplicates')}}" class="btn btn-dark w-100">Remove Duplicates</a>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Master Data Render Table Component -->
        <div class="row">
            <div class="col-12">
                <form id="bulk-actions-form" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="bulk-form-method" value="POST">

                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-body table-toolbar d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">

                                <select id="wp-bulk-action-select" class="form-select" style="width: 250px; height: 38px;">
                                    <option value="">Select Bulk Action</option>
                                    @if($isAdmin)
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
                                    @endif

                                    @if($isAdmin || $user->email == 'davsong@gmail.com')
                                        <option value="delete"
                                                data-url="{{ route($isAdmin ? 'awards.bulk-delete' : 'stakeholders.awards.bulk-delete') }}"
                                                data-method="DELETE"
                                                data-confirm="Delete selected entries?">
                                            Delete
                                        </option>

                                        <option value="permanent-delete"
                                                data-url="{{ route($isAdmin ? 'awards.bulk-permanent-delete' : 'stakeholders.awards.bulk-permanent-delete') }}"
                                                data-method="DELETE"
                                                data-confirm="Permanently Delete selected entries? This action cannot be undone.">
                                            Permanent Delete
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

                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                                <table class="table table-align-middle mb-0 custom-modern-table">
                                    <thead>
                                        <tr class="bg-light border-bottom">
                                            <th class="ps-4 align-middle" style="width: 45px;">
                                                <div class="form-check custom-checkbox">
                                                    <input type="checkbox" class="form-check-input cursor-pointer shadow-none" id="master-checkbox">
                                                </div>
                                            </th>
                                            <th class="text-uppercase text-muted tracking-wider align-middle" style="width: 60px;">S/N</th>
                                            <th class="text-uppercase text-muted tracking-wider align-middle">Nominee & Ref</th>
                                            <th class="text-uppercase text-muted tracking-wider align-middle" style="min-width: 160px;">Approval Progress</th>
                                            <th class="text-uppercase text-muted tracking-wider align-middle">Submitted On</th>
                                            <th class="text-uppercase text-muted tracking-wider text-end pe-4 align-middle" style="min-width: 180px;">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($awards as $groupKey => $awardGroup)
                                            <tr class="sticky-chapter-row table-sm bg-light-subtle">
                                                <td class="ps-4 align-middle"></td>
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
                                                <td class="ps-4 align-middle">
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" name="ids[]" value="{{ $award->id }}" class="form-check-input row-checkbox cursor-pointer shadow-none">
                                                    </div>
                                                </td>

                                                <td class="text-secondary fw-medium align-middle">
                                                    {{ $loop->iteration }}
                                                </td>

                                                <td class="align-middle">

                                                    <div class="d-flex flex-column gap-2">

                                                        {{-- SHORTLIST STATUS BLOCK --}}
                                                        <div class="p-2 rounded bg-light border">

                                                            @if($award->currentShortlistStage)

                                                                <div class="d-flex flex-column gap-1">

                                                                    <span class="badge bg-success w-fit">
                                                                        {{ $award->currentShortlistStage->stage->title }}
                                                                    </span>
                                                                    <a href="shortlistHistoryModal_{{ $award->id }}"
                                                                        class="view-shortlist-history text-decoration-none font-xs text-muted"
                                                                        data-award="{{ $award->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#shortlistHistoryModal">
                                                                        View History
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <span class="text-danger font-xs fw-semibold">
                                                                    Not Shortlisted
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- CORE INFO --}}
                                                        <span class="text-dark fw-semibold font-base mb-0">
                                                            {{ $award->name }}
                                                        </span>

                                                        @if(empty($award->chapter_id))
                                                            <span class="font-sm lh-sm" style="font-size: 0.68rem;">
                                                                <strong>
                                                                    {{ $award->entries->firstWhere('key', 'select_institution')->value ?? 'N/A' }}
                                                                </strong>
                                                            </span>
                                                        @endif

                                                        <span class="text-muted tracking-tight font-xs font-monospace">
                                                            {{ $award->reference }}
                                                        </span>

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
                                                    <div class="d-flex flex-column gap-1">
                                                        @foreach($statuses as $label => $data)
                                                            <div class="d-flex align-items-center font-xs justify-content-between" style="max-width: 160px;">
                                                                <span class="text-muted fw-normal">{{ $label }}</span>
                                                                @if($data['value'] == 2)
                                                                    <span class="badge bg-soft-danger text-danger d-inline-flex align-items-center gap-1">
                                                                        Rejected
                                                                        <a href="#{{ $data['level'] }}Rejection{{ $award->id }}" data-toggle="modal" class="text-danger lh-1" title="View feedback">
                                                                            <i class="bx bx-message-rounded-dots"></i>
                                                                        </a>
                                                                    </span>
                                                                @elseif($data['value'] == 1)
                                                                    <span class="badge bg-soft-success text-success">Approved</span>
                                                                @else
                                                                    <span class="badge bg-soft-warning text-warning">Pending</span>
                                                                @endif
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
                                                        @if($isAdmin)
                                                        <button type="button" class="btn btn-action-icon text-secondary bg-transparent border-0 p-1" data-toggle="modal" data-target="#awardStatusAdjustModal{{ $award->id }}" title="Adjust Status">
                                                            <i class="fa fa-cog font-sm"></i>
                                                        </button>
                                                        @endif

                                                        <a href="{{ route($isAdmin ? 'awards.show' : 'stakeholders.awards.show', $award->id) }}" class="btn btn-action-icon text-warning p-1" title="Edit Entry" onclick="return confirm('Modify this record?');"><i class="fa fa-edit font-sm"></i></a>

                                                        @if($isAdmin || $user->email == 'davsong@gmail.com')
                                                        <a href="#" class="btn btn-action-icon text-danger p-1" title="Delete Submission" onclick="event.preventDefault(); if (confirm('Remove this submission record?')) { document.getElementById('delete-award-{{ $award->id }}').submit(); }"><i class="fa fa-trash font-sm"></i></a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach {{-- Properly trailing internal items loop iterator --}}
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
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
                </form>
            </div>
        </div>
    </section>
</div>

@include('admin.awards.modals')
@endsection

@section('extra_scripts')
<script>
    $(document).ready(function() {
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
