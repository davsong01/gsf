@extends('layouts.dashboard')
@section('title', isset($awardShortlistStage->id) ? 'Edit Shortlist Stage' : 'Create Shortlist Stage')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('shortlist.index') }}">Award Shortlist Stages</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">
    {{ isset($awardShortlistStage->id) ? 'Edit Stage' : 'Create Stage' }}
</li>
@endsection

@section('content')
@php
    $conditions = old('system_conditions', $awardShortlistStage->system_conditions ?? []);
    $reportStatuses = $conditions['report_statuses'] ?? [];
    $approvalMatch = old('approval_match', $conditions['approval_match'] ?? 'all');
    $reportApprovalMatch = old('report_approval_match', $conditions['report_approval_match'] ?? 'all');

    $helpIcon = function (string $title) {
        return '<i class="bx bx-help-circle text-muted ml-25" data-toggle="tooltip" title="' . e($title) . '"></i>';
    };
@endphp

<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        @include('includes.alerts')
                        <h4 class="card-title mt-1">
                            {{ isset($awardShortlistStage->id) ? 'Modify Workflow Stage' : 'Configure New Shortlist Stage' }}
                        </h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form
                                action="{{ isset($awardShortlistStage->id) ? route('shortlist.update', $awardShortlistStage->id) : route('shortlist.store') }}"
                                method="POST"
                            >
                                @csrf
                                @if(isset($awardShortlistStage->id))
                                    @method('PATCH')
                                @endif

                                <div class="row">
                                    {{-- Left Column: Core Identity --}}
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="title">Stage Title</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="title"
                                                name="title"
                                                value="{{ old('title', $awardShortlistStage->title ?? '') }}"
                                                placeholder="e.g., First Review, Semi-Finals"
                                                required
                                            >
                                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="description">
                                                Short Description
                                                {!! $helpIcon('Briefly explain what this stage represents. This helps admins understand the purpose of the stage at a glance.') !!}
                                            </label>
                                            <textarea
                                                class="form-control"
                                                id="description"
                                                name="description"
                                                rows="2"
                                                maxlength="255"
                                                placeholder="e.g., Entries that passed chapter and zone review"
                                            >{{ old('description', $awardShortlistStage->description ?? '') }}</textarea>
                                            <small class="text-muted d-block mt-25">Optional. Keep it short, maximum 255 characters.</small>
                                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="slug">Custom URL Slug (Optional)</label>
                                            <input
                                                type="text"
                                                class="form-control font-monospace"
                                                id="slug"
                                                name="slug"
                                                value="{{ old('slug', $awardShortlistStage->slug ?? '') }}"
                                                placeholder="Leave empty to auto-generate from title"
                                            >
                                            <small class="text-muted d-block mt-0.5">Unique structural tracking key.</small>
                                            @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="award_type">
                                                Award Type
                                                {!! $helpIcon('Limits this stage to a specific award category. Leave as Both to allow GO and ETF entries to use this stage.') !!}
                                            </label>
                                            <select class="form-control" name="award_type" id="award_type">
                                                <option value="" {{ old('award_type', $awardShortlistStage->award_type ?? '') === '' ? 'selected' : '' }}>Both Award Types</option>
                                                <option value="go" {{ old('award_type', $awardShortlistStage->award_type ?? '') === 'go' ? 'selected' : '' }}>G.O. Award</option>
                                                <option value="etf" {{ old('award_type', $awardShortlistStage->award_type ?? '') === 'etf' ? 'selected' : '' }}>E.T.F. Award</option>
                                            </select>
                                            @error('award_type') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>
                                    </div>

                                    {{-- Right Column: Routing Rules & Pipeline Position --}}
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="position">
                                                Processing Priority Order
                                                {!! $helpIcon('System stages are checked from the smallest position to the largest position.') !!}
                                            </label>
                                            <input
                                                type="number"
                                                min="1"
                                                class="form-control"
                                                id="position"
                                                name="position"
                                                value="{{ old('position', $awardShortlistStage->position ?? 1) }}"
                                                required
                                            >
                                            <small class="text-muted d-block mt-0.5">Determines pipeline layout flow order (ascending).</small>
                                            @error('position') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="active">Stage Availability Status</label>
                                            <select class="form-control" name="active" id="active" required>
                                                <option value="1" {{ old('active', $awardShortlistStage->active ?? 1) == 1 ? 'selected' : '' }}>Active (Visible in Pipeline)</option>
                                                <option value="0" {{ old('active', $awardShortlistStage->active ?? 1) == 0 ? 'selected' : '' }}>Inactive (Disabled)</option>
                                            </select>
                                            @error('active') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group mt-2">
                                            <label for="mark_as_final">
                                                Pipeline Finality Status
                                                {!! $helpIcon('A final stage marks matching entries as finally approved when they enter this stage.') !!}
                                            </label>
                                            <select class="form-control" name="mark_as_final" id="mark_as_final" required>
                                                <option value="0" {{ old('mark_as_final', $awardShortlistStage->mark_as_final ?? 0) == 0 ? 'selected' : '' }}>Intermediary Stage</option>
                                                <option value="1" {{ old('mark_as_final', $awardShortlistStage->mark_as_final ?? 0) == 1 ? 'selected' : '' }}>Final Stage (Locks Submissions upon Entry)</option>
                                            </select>
                                            @error('mark_as_final') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                    </div>
                                </div>

                                <div class="border rounded p-2 mt-2" style="background:#f8fafc;">
                                    <div class="row align-items-center">
                                        <div class="col-md-6 col-sm-12">
                                            <h5 class="mb-25">Automation Settings</h5>
                                            <p class="text-muted mb-1">
                                                Choose whether this stage is controlled manually or by shortlist conditions.
                                            </p>

                                            <fieldset class="form-group mb-md-0">
                                                <label for="stage_engine">
                                                    Stage Engine
                                                    {!! $helpIcon('Manual stages are moved by an admin. System stages move matching award entries automatically based on the conditions below.') !!}
                                                </label>
                                                <select class="form-control" name="stage_engine" id="stage_engine" required>
                                                    <option value="manual" {{ old('stage_engine', $awardShortlistStage->stage_engine ?? 'manual') === 'manual' ? 'selected' : '' }}>Manual - admin moves entries into this stage</option>
                                                    <option value="system" {{ old('stage_engine', $awardShortlistStage->stage_engine ?? 'manual') === 'system' ? 'selected' : '' }}>System - automatically move matching entries</option>
                                                </select>
                                                @error('stage_engine') <small class="text-danger">{{ $message }}</small> @enderror
                                            </fieldset>
                                        </div>

                                        <div class="col-md-6 col-sm-12">
                                            <div class="border rounded bg-white p-1 mt-1 mt-md-0">
                                                <div class="d-flex align-items-center mb-25">
                                                    <i class="bx bx-cog text-primary mr-50"></i>
                                                    <strong class="text-dark">What System mode does</strong>
                                                </div>
                                                <div class="text-muted">
                                                    When System is selected, the app checks award approvals and chapter report history, then moves matching entries into this stage.
                                                </div>
                                            </div>

                                            @if(isset($awardShortlistStage->id))
                                                <button
                                                    type="submit"
                                                    form="move-matching-awards-form"
                                                    class="btn btn-outline-success mt-1 w-100"
                                                    {{ ($awardShortlistStage->stage_engine ?? 'manual') !== 'system' ? 'disabled' : '' }}
                                                    onclick="return confirm('Move all awards that currently meet this stage criteria into this stage?');"
                                                >
                                                    <i class="bx bx-transfer-alt mr-50"></i>
                                                    Move Matching Awards Into This Stage
                                                </button>

                                                @if(($awardShortlistStage->stage_engine ?? 'manual') !== 'system')
                                                    <small class="text-muted d-block mt-25">
                                                        Save this stage with Stage Engine set to System to enable criteria-based movement.
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <div id="system-conditions-panel" class="mt-2" style="{{ old('stage_engine', $awardShortlistStage->stage_engine ?? 'manual') === 'system' ? '' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="bg-white border rounded p-2 h-100">
                                                <h6 class="mb-1">
                                                    Award Approval Requirements
                                                    {!! $helpIcon('These conditions check the approval status saved directly on the award entry.') !!}
                                                </h6>

                                                @foreach(['chapter_status' => 'Chapter office has approved this award entry', 'zone_status' => 'Zone office has approved this award entry', 'field_status' => 'Field office has approved this award entry'] as $field => $label)
                                                    <div class="form-check mb-1">
                                                        <input type="checkbox" class="form-check-input" id="{{ $field }}" name="{{ $field }}" value="1" {{ old($field, $conditions[$field] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                                                    </div>
                                                @endforeach

                                                <fieldset class="form-group">
                                                    <label for="approval_match">
                                                        How should selected award approvals be matched?
                                                        {!! $helpIcon('All requires every selected approval to be approved. Any accepts one or more. At least and Exactly use the number below.') !!}
                                                    </label>
                                                    <select class="form-control match-rule-select" name="approval_match" id="approval_match" data-count-target="#approval-count-field">
                                                        <option value="all" {{ $approvalMatch === 'all' ? 'selected' : '' }}>All selected approvals must be approved</option>
                                                        <option value="any" {{ $approvalMatch === 'any' ? 'selected' : '' }}>Any one selected approval can be approved</option>
                                                        <option value="at_least" {{ $approvalMatch === 'at_least' ? 'selected' : '' }}>At least a specific number must be approved</option>
                                                        <option value="exactly" {{ $approvalMatch === 'exactly' ? 'selected' : '' }}>Exactly a specific number must be approved</option>
                                                    </select>
                                                    @error('approval_match') <small class="text-danger">{{ $message }}</small> @enderror
                                                </fieldset>

                                                <fieldset class="form-group" id="approval-count-field">
                                                    <label for="approval_count">
                                                        Required number of award approvals
                                                        {!! $helpIcon('Only used when the match rule is At least or Exactly. Example: choose 2 to require two of Chapter, Zone, and Field to be approved.') !!}
                                                    </label>
                                                    <input type="number" min="1" max="3" class="form-control" id="approval_count" name="approval_count" value="{{ old('approval_count', $conditions['approval_count'] ?? 1) }}">
                                                    @error('approval_count') <small class="text-danger">{{ $message }}</small> @enderror
                                                </fieldset>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-12">
                                            <div class="bg-white border rounded p-2 h-100">
                                                <h6 class="mb-1">
                                                    Chapter Monthly Report Requirements
                                                    {!! $helpIcon('These conditions check monthly reports submitted by the chapter attached to the award entry.') !!}
                                                </h6>

                                                <div class="form-check mb-1">
                                                    <input type="checkbox" class="form-check-input" id="uses_report_metrics" name="uses_report_metrics" value="1" {{ old('uses_report_metrics', $conditions['uses_report_metrics'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="uses_report_metrics">
                                                        Require the award entry chapter to have monthly reports
                                                        {!! $helpIcon('When enabled, the system checks reports for the chapter linked to the award entry before moving it to this stage.') !!}
                                                    </label>
                                                </div>

                                                <fieldset class="form-group">
                                                    <label for="report_metric_months">
                                                        Number of recent report months required
                                                        {!! $helpIcon('Leave empty to check all reports since reporting began. Enter 3 to require reports in at least three distinct recent months.') !!}
                                                    </label>
                                                    <input type="number" min="1" max="60" class="form-control" id="report_metric_months" name="report_metric_months" value="{{ old('report_metric_months', $conditions['report_metric_months'] ?? '') }}" placeholder="Leave empty for all historical reports">
                                                    <small class="text-muted d-block mt-25">Blank means the system checks reports from the beginning of report records.</small>
                                                    @error('report_metric_months') <small class="text-danger">{{ $message }}</small> @enderror
                                                </fieldset>

                                                @foreach(['report_zone_status' => ['Report is approved by Zone', 'zone_status'], 'report_field_status' => ['Report is approved by Field', 'field_status'], 'report_national_status' => ['Report is approved by National Secretariat', 'national_status']] as $field => [$label, $statusKey])
                                                    <div class="form-check mb-1">
                                                        <input type="checkbox" class="form-check-input" id="{{ $field }}" name="{{ $field }}" value="1" {{ old($field, $reportStatuses[$statusKey] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                                                    </div>
                                                @endforeach

                                                <fieldset class="form-group">
                                                    <label for="report_approval_match">
                                                        How should selected report approvals be matched?
                                                        {!! $helpIcon('This applies to each qualifying report. All requires every selected report approval to be approved. Any accepts one or more.') !!}
                                                    </label>
                                                    <select class="form-control match-rule-select" name="report_approval_match" id="report_approval_match" data-count-target="#report-approval-count-field">
                                                        <option value="all" {{ $reportApprovalMatch === 'all' ? 'selected' : '' }}>All selected report approvals must be approved</option>
                                                        <option value="any" {{ $reportApprovalMatch === 'any' ? 'selected' : '' }}>Any one selected report approval can be approved</option>
                                                        <option value="at_least" {{ $reportApprovalMatch === 'at_least' ? 'selected' : '' }}>At least a specific number must be approved</option>
                                                        <option value="exactly" {{ $reportApprovalMatch === 'exactly' ? 'selected' : '' }}>Exactly a specific number must be approved</option>
                                                    </select>
                                                    @error('report_approval_match') <small class="text-danger">{{ $message }}</small> @enderror
                                                </fieldset>

                                                <fieldset class="form-group" id="report-approval-count-field">
                                                    <label for="report_approval_count">
                                                        Required number of report approvals
                                                        {!! $helpIcon('Only used when the report match rule is At least or Exactly. Example: choose 2 to require two of Zone, Field, and National report approvals.') !!}
                                                    </label>
                                                    <input type="number" min="1" max="3" class="form-control" id="report_approval_count" name="report_approval_count" value="{{ old('report_approval_count', $conditions['report_approval_count'] ?? 1) }}">
                                                    @error('report_approval_count') <small class="text-danger">{{ $message }}</small> @enderror
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100 py-1 font-weight-bold" type="submit">
                                            {{ isset($awardShortlistStage->id) ? 'Save Workflow Adjustments' : 'Initialize Shortlist Stage' }}
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if(isset($awardShortlistStage->id))
                                <form
                                    id="move-matching-awards-form"
                                    method="POST"
                                    action="{{ route('shortlist.move-matching-awards', $awardShortlistStage->id) }}"
                                    class="d-none"
                                >
                                    @csrf
                                </form>
                            @endif
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
    $(function () {
        function toggleSystemPanel() {
            $('#system-conditions-panel').toggle($('#stage_engine').val() === 'system');
        }

        function toggleCountField(select) {
            const $select = $(select);
            const target = $select.data('count-target');
            const shouldShow = ['at_least', 'exactly'].includes($select.val());

            if (target) {
                $(target).toggle(shouldShow);
            }
        }

        $('#stage_engine').on('change select2:select', toggleSystemPanel);
        $('.match-rule-select').on('change select2:select', function () {
            toggleCountField(this);
        }).each(function () {
            toggleCountField(this);
        });

        toggleSystemPanel();

        if (typeof $.fn.tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
</script>
@endsection
