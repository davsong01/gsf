

@section('extra_styles')
<style>
    .section-card {
        background-color: #f9f9f9;
        border: 1px solid #d1d1d1;
        border-radius: 8px;
        margin-top: 25px;
        padding: 10px;
    }

    .add-row,.remove-row{
        padding: 0.167rem 0.5rem !important;
    }

    .header-section{
        background-color: #004080;
        color: #fff;
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 1.25rem;
    }

    .sub-section-card {
        border-left: 4px solid #004080;
        background-color: #ffffff;
        padding: 5px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .sub-section{
        color: #004080;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 15px;
    }

    label {
        margin-top: 15px !important;
        font-weight: 500;
    }

    .totals-row {
        font-weight: bold;
        background-color: #f2f2f2;
    }

    .dynamic-table input, .income-table input {
        height: calc(1.8em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
    }

    .dynamic-table th, .income-table th {
        background-color: #e9ecef;
        text-align: center;
    }

    .dynamic-table td, .income-table td {
        text-align: center;
        vertical-align: middle;
    }

    .table-responsive {
        overflow-x: auto;
        margin-bottom: 20px;
    }

    /* Sticky bottom-right container */
    .sticky-action-buttons {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 1000;
    }

    .btn-circle {
        position: relative;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        transition: transform 0.2s;
        text-decoration: none;
        color: #fff;
    }

    .btn-circle:hover {
        transform: scale(1.1);
    }

    .btn-circle .btn-label {
        position: absolute;
        right: 70px; /* show label to the left of button */
        white-space: nowrap;
        background: rgba(0,0,0,0.75);
        padding: 4px 8px;
        border-radius: 4px;
        opacity: 0;
        color: #fff;
        font-size: 12px;
        pointer-events: none;
        transition: opacity 0.2s;
    }

    .btn-circle:hover .btn-label {
        opacity: 1;
    }
</style>
@endsection
@section('content')
@php
    $userRole = $user->role_id;
@endphp
<div class="content-body">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the errors below:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section id="basic-input">
        <form action="{{ isset($report) ? route(($isAdmin ? 'stakeholderreports.update' : 'stakeholders.reports.update'), $report->id) : route('stakeholders.reports.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('You are about to submit this report, action is irreversible');">
            @csrf
            @if(isset($report))
                @method('PUT')
            @endif

            @php $questionNumber = 1; @endphp

            @foreach($sections as $section)
                @php $sectionAccess = app('App\Services\StakeholderRolePermissionService'::class)->sectionAccess($user, $section); @endphp
                @if($sectionAccess['view'] || $isAdmin)
                    <div class="section-card">
                        <h3 class="header-section">{{ $section->name }}</h3>

                        @foreach($section->subsections as $subsection)
                            @php $subAccess =  app('App\Services\StakeholderRolePermissionService'::class)->sectionAccess($user, $section); @endphp
                            @if($subAccess['view'] || $isAdmin)
                                <div class="sub-section-card">
                                    <h5 class="sub-section">{{ $subsection->name }}</h5>
                                    <div class="row">
                                        @foreach($subsection->questions as $question)
                                            @php
                                                $qAccess = app('App\Services\StakeholderRolePermissionService'::class)->questionAccess($user, $question, $isAdmin);

                                                $value = old('responses.' . $question->slug);
                                                if(isset($report) && $report->answers) {
                                                    $answer = $report->answers->firstWhere('question_id', $question->id);
                                                    if($answer) {
                                                        $decoded = json_decode($answer->answer_value, true);
                                                        $value = $decoded ?? $answer->answer_value;
                                                    }
                                                }
                                                if(!$value && isset($prefillData[$question->slug])) {
                                                    $value = $prefillData[$question->slug];
                                                }

                                                $disabled = !$qAccess['edit'] ? 'disabled' : '';
                                            @endphp

                                            <div class="{{ $question->width_class ?? 'col-md-6' }} mb-1">
                                                <label for="{{ $question->slug }}" class="d-flex align-items-center gap-2">
                                                    <span>
                                                        {{ $questionNumber }}. {{ $question->label }}
                                                        @if($question->is_required && $qAccess['edit'] && !$isAdmin)
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </span>

                                                    @if($question->type === 'file' && !empty($value))
                                                        <a style="margin-left:10px" href="{{ route('protected.download', ['file' => $value]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            View
                                                        </a>
                                                    @endif

                                                </label>
                                                @switch($question->type)
                                                    @case('text')
                                                    @case('year')
                                                    @case('month')
                                                    @case('number')
                                                    @case('date')
                                                        <input type="{{ $question->type }}"
                                                            class="form-control"
                                                            id="{{ $question->slug }}"
                                                            name="responses[{{ $question->slug }}]"
                                                            value="{{ $value }}"
                                                            {{ $disabled }}
                                                            @if($question->is_required && $qAccess['edit'] && !$isAdmin) required @endif>
                                                        @break

                                                    @case('textarea')
                                                        <textarea class="form-control"
                                                                id="{{ $question->slug }}"
                                                                name="responses[{{ $question->slug }}]"
                                                                rows="3"
                                                                {{ $disabled }}
                                                                @if($question->is_required && $qAccess['edit'] && !$isAdmin) required @endif>{{ $value }}</textarea>
                                                        @break

                                                    @case('select')
                                                        @php
                                                            if ($question->slug == 'session') {
                                                                $options = app('App\Services\ReportService')->sessionRange();
                                                            } else {
                                                                $options = $question->options;
                                                            }
                                                        @endphp
                                                        <select class="form-select"
                                                                id="{{ $question->slug }}"
                                                                name="responses[{{ $question->slug }}]"
                                                                {{ $disabled }}
                                                                @if($question->is_required && $qAccess['edit'] && !$isAdmin) required @endif>

                                                            <option value="">Select...</option>
                                                            @foreach($options ?? [] as $opt)
                                                                @if(isset($opt['label'], $opt['value']))
                                                                    <option value="{{ $opt['value'] }}" {{ ($value ?? '') == $opt['value'] ? 'selected' : '' }}>
                                                                        {{ $opt['label'] }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @break

                                                    @case('dynamic_table')
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered dynamic-table" data-slug="{{ $question->slug }}">
                                                                <thead>
                                                                    <tr>
                                                                        @foreach($question->options as $col)
                                                                            <th>{{ $col['label'] ?? $col }}</th>
                                                                        @endforeach
                                                                        @if($qAccess['edit'])
                                                                            <th>Action</th>
                                                                        @endif
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $rows = $value ?? [[]]; @endphp
                                                                    @foreach($rows as $rowIndex => $row)
                                                                        <tr>
                                                                            @foreach($question->options as $col)
                                                                                @php $colLabel = $col['label'] ?? $col; @endphp
                                                                                <td>
                                                                                    <input type="{{ $col['type'] ?? 'text' }}"
                                                                                        name="responses[{{ $question->slug }}][{{ $rowIndex }}][{{ $colLabel }}]"
                                                                                        class="form-control"
                                                                                        value="{{ $row[$colLabel] ?? '' }}"
                                                                                        {{ $disabled }}
                                                                                        @if(!empty($col['required']) && $qAccess['edit'] && $question->is_required && !$isAdmin) required @endif>
                                                                                </td>
                                                                            @endforeach
                                                                            @if($qAccess['edit'])
                                                                                <td>
                                                                                    <button type="button" class="btn btn-sm btn-success add-row">+</button>
                                                                                    <button type="button" class="btn btn-sm btn-danger remove-row">-</button>
                                                                                </td>
                                                                            @endif
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        @break

                                                    @case('income_table')
                                                        {{-- <strong style="color:red"><small>(Ensure each column has a minimum of 0)</small></strong> --}}
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered income-table" data-slug="{{ $question->slug }}">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Week</th>
                                                                        @foreach($question->options['columns'] as $col)
                                                                            <th>{{ $col }}</th>
                                                                        @endforeach
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($question->options['rows'] as $week)
                                                                        <tr>
                                                                            <td>{{ $week }}</td>
                                                                            @foreach($question->options['columns'] as $col)
                                                                                <td>
                                                                                    <input type="{{ $col == 'Remarks' ? 'text' : 'number' }}"
                                                                                        class="form-control numeric-input"
                                                                                        name="responses[{{ $question->slug }}][{{ $week }}][{{ $col }}]"
                                                                                        value="{{ $value[$week][$col] ?? '' }}"
                                                                                        {{ $disabled }}
                                                                                        {{-- @if($qAccess['edit'] && $col != 'Remarks') required min="0" @endif> --}}
                                                                                        >
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr class="totals-row">
                                                                        <td>Totals</td>
                                                                        @foreach($question->options['columns'] as $col)
                                                                            @if($col != 'Remarks')
                                                                            <td class="total-cell" data-column="{{ $col }}">0</td>
                                                                            @endif
                                                                        @endforeach
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    @break
                                                    @case('file')
                                                        <input
                                                            type="file"
                                                            class="form-control"
                                                            id="{{ $question->slug }}"
                                                            name="responses[{{ $question->slug }}]"
                                                            {{ $disabled }}
                                                            accept=".jpg,.jpeg,.png"
                                                            @if($question->is_required && $qAccess['edit'] && !$isAdmin) required @endif
                                                        >
                                                        <small class="text-danger">
                                                            Only JPG, JPEG, and PNG files are allowed.
                                                        </small>
                                                    @break

                                                @endswitch
                                            </div>

                                            @php $questionNumber++; @endphp
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endforeach

            <div class="card mb-4">
                <div class="card-body">
                    <div class="form-check">
                        <input type="checkbox" name="confirm_information" value="1" class="form-check-input" id="confirmInfo" required>
                        <label style="margin-top: 0 !important" class="form-check-label fw-semibold" for="confirmInfo">
                            I hereby confirm that all information provided in this report is true, current, and accurate to the best of my knowledge.
                        </label>
                    </div>
                </div>
            </div>

            <style>
                .button-gap > * + * {
                    margin-left: 15px; /* adjust gap as needed */
                }
            </style>

            <div class="mt-4 d-flex button-gap">
                @if(in_array($userRole, chapterStakeholders()) || $isAdmin)
                <button class="btn btn-warning flex-fill" name="edit_mode" value="1" type="submit">
                    Save and Submit later
                </button>

                @endif

                @if(!$isAdmin)
                <button class="btn btn-success flex-fill" name="edit_mode" value="0" type="submit">
                    {{ isset($report) ? 'Update and Final Submission' : 'Final Submission' }}
                </button>
                @endif
            </div>
        </form>
    </section>
</div>

@php
    $canAct = false;
    if(isset($report)){
        $inEditMode = $report?->edit_mode;

        // 🚨 GLOBAL BLOCK: edit mode disables all actions except later allowed logic (if any)
        if (!$inEditMode) {

            // Zone approval
            if (
                in_array($userRole, zoneStakeholders()) &&
                in_array($report->zone_status, [0,2])
            ) {
                $canAct = true;
                $approveRoute = route('stakeholders.reports.approve', $report->id);
                $rejectRoute  = route('stakeholders.reports.reject', $report->id);
                $tooltipApprove = 'Approve for Zone';
                $tooltipReject = 'Reject for Zone';
            }

            // Field approval (only after zone approval)
            elseif (
                in_array($userRole, fieldStakeholders()) &&
                $report->zone_status == 1 &&
                in_array($report->field_status, [0,2])
            ) {
                $canAct = true;
                $approveRoute = route('stakeholders.reports.approve', $report->id);
                $rejectRoute  = route('stakeholders.reports.reject', $report->id);
                $tooltipApprove = 'Approve for Field';
                $tooltipReject = 'Reject for Field';
            }

            // National approval (only after zone & field approval)
            elseif (
                in_array($userRole, array_merge(secretariatStakeholders(), ncpStakeholders())) &&
                $report->zone_status == 1 &&
                $report->field_status == 1 &&
                in_array($report->national_status, [0,2])
            ) {
                $canAct = true;
                $approveRoute = route('stakeholders.reports.approve', $report->id);
                $rejectRoute  = route('stakeholders.reports.reject', $report->id);
                $tooltipApprove = 'Approve for National';
                $tooltipReject = 'Reject for National';
            }
        }

        // OPTIONAL: edit mode override (only chapter if you want)
        if ($inEditMode && in_array($userRole, chapterStakeholders())) {
            $canAct = true;
            $approveRoute = route('stakeholders.reports.approve', $report->id);
            $rejectRoute  = route('stakeholders.reports.reject', $report->id);
            $tooltipApprove = 'Edit Mode Action';
            $tooltipReject = 'Edit Mode Action';
        }
    }
@endphp
@if($canAct && $approveRoute)
    <div class="sticky-action-buttons">
    <!-- Approve Button -->
        <a href="{{ $approveRoute }}"
        class="btn btn-success btn-circle"
        title="{{ $tooltipApprove }}"
        onclick="return confirm('Are you sure you want to approve this report?');">
            <i class="fa fa-check"></i>
            <span class="btn-label">{{ $tooltipApprove }}</span>
        </a>

    <!-- Reject Button triggers modal -->
    <button type="button" class="btn btn-danger btn-circle" data-toggle="modal" data-target="#rejectModal">
        <i class="fa fa-times"></i>
        <span class="btn-label">{{ $tooltipReject }}</span>
    </button>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ $rejectRoute }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Report</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for rejection</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Report</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('extra_scripts')
<script>
    // Dynamic table row add/remove
    function reindexRows(table) {
        table.find('tbody tr').each(function(rowIndex) {
            $(this).find('td').each(function(colIndex) {
                let input = $(this).find('input');
                if (input.length) {
                    let colLabel = input.data('col') || input.attr('name').match(/\[([^\]]+)\]$/)[1];
                    input.attr('name', `responses[${table.data('slug')}][${rowIndex}][${colLabel}]`);
                }
            });
        });
    }

    $(document).on('click', '.add-row', function(){
        let table = $(this).closest('table');
        let newRow = table.find('tbody tr:first').clone();
        newRow.find('input').val('');
        table.find('tbody').append(newRow);
        reindexRows(table);
        recalcTotals(table);
    });

    $(document).on('click', '.remove-row', function(){
        let table = $(this).closest('table');
        if(table.find('tbody tr').length > 1){
            $(this).closest('tr').remove();
            reindexRows(table);
            recalcTotals(table);
        }
    });

    // Income Table Totals
    function recalcTotals(table){
        if(table.hasClass('income-table')){
            table.find('tfoot td.total-cell').each(function(){
                let col = $(this).data('column');
                let sum = 0;
                table.find('tbody tr').each(function(){
                    let val = parseFloat($(this).find('input[name*="['+col+']"]').val()) || 0;
                    sum += val;
                });
                $(this).text(sum.toFixed(2));
            });
        }
    }

    $(document).on('input', '.income-table .numeric-input', function(){
        let table = $(this).closest('table');
        recalcTotals(table);
    });

    // Initialize totals on page load
    $('.income-table').each(function(){
        recalcTotals($(this));
    });

    $(function () {

        const getVal = id => parseFloat($(id).val()) || 0;
        const setVal = (id, value) => $(id).val(value.toFixed(2));

        const recalculate = () => {
            const cash = getVal('#total-tithe-received-by-cash');
            const bank = getVal('#total-tithe-paid-to-bank');

            const totalTithe = cash + bank;
            const tenPercent = totalTithe * 0.1;

            setVal('#total-tithe-for-the-month', totalTithe);
            setVal('#10-of-total-tithe', tenPercent);
        };

        // Recalculate on any input change
        $('#total-tithe-received-by-cash, #total-tithe-paid-to-bank').on('input keyup change', recalculate);
    });
</script>
