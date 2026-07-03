@php
    // Dynamically find a profile photo or primary image from key-value pairs to feature on the header banner
    $fileFields = fileFields();;
    $headerImageEntry = $award->entries->first(function($entry) {
        return in_array(strtolower($entry->key), [
            'upload_a_clear_and_recent_picture_of_yourself_file_id',
            'upload_a_clear_and_recent_picture_of_yourself',
            'picturesave_picture_as_your_name'
        ]) && !empty($entry->value);
    });

    $permissions = resolveAwardPermissions($award);
    $canAct = $permissions->canAct;
    $canEdit = $permissions->canEdit;
    $canComment = $permissions->canComment;
    $approveRoute = $permissions->approveRoute;
    $rejectRoute = $permissions->rejectRoute;
    $tooltipApprove = $permissions->tooltipApprove;
    $tooltipReject = $permissions->tooltipReject;

    $userRole = $user->role_id;
    $settings = awardSettings();
    $isAdmin = isAdmin()['status'] ?? false;
@endphp

@extends($isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard')
@section('title', 'Edit Award Submission')
@section('content')

<div class="content-body premium-page-wrapper">
    <!-- Top Profile Banner Block with Header Image Positioned Legibly -->
    <div class="card overview-banner-card mb-2">
    <div class="card-body p-1">
        <div class="row align-items-center g-4">

            <!-- Left Column: Avatar Picture Matrix Container -->
            <div class="col-auto">
                @if($headerImageEntry)
                    <img src="{{ route($isAdmin ? 'admin.protected.download' : 'protected.download', ['file' => $headerImageEntry->value]) }}"
                         class="header-avatar-preview trigger-zoom-modal"
                         data-file-url="{{ route($isAdmin ? 'admin.protected.download' : 'protected.download', ['file' => $headerImageEntry->value]) }}"
                         data-label="Nominee Profile Photo"
                         title="Click to expand snapshot portrait"
                         alt="Nominee Picture">
                @else
                    <div class="header-avatar-placeholder">
                        <i class="bx bx-user" style="font-size: 2.2rem;"></i>
                    </div>
                @endif
            </div>

            <!-- Center Column: Core Award Profile Identification Details -->
            <div class="col flex-grow-1 ml-1">
                <div class="d-flex align-items-start align-items-sm-center justify-content-between flex-wrap gap-2 mb-3">
                    <!-- Title & Reference ID Stack Block -->
                    <div>
                        <h4 class="text-white fw-bold mb-1 d-flex align-items-center gap-2">
                            <i class="bx bx-award text-white"></i>{{ucfirst($award->type)}} Award Nomination
                        </h4>
                        <div class="font-monospace text-muted font-xs tracking-tight ms-4 ps-1" style="color: rgba(255, 255, 255, 0.6) !important;">
                            {{ $award->reference }}
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-6 col-md-6">
                        <div class="meta-label">Nominee Profile</div>
                        <div class="meta-value text-truncate" style="max-width: 220px;" title="{{ $award->name }}">{{ $award->name ?? 'Unnamed Nominee' }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="meta-label">Submitted On</div>
                        <div class="meta-value">{{ $award->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Integrated Regional Context, Compliance Tracking & Workflow Statuses -->
            <div class="col-12 col-lg-5">
                <div class="compliance-wrapper header-compliance-card h-100 d-flex flex-column justify-content-between gap-3">

                    <!-- Top Sub-Section: Regional Compliance Information -->
                    <div>
                        @php
                            $chapterCompliance = $award->chapter ? $award->chapter->reportCompliance() : 0;

                            if ($chapterCompliance >= 80) {
                                $progressBarColor = 'bg-success';
                                $badgeColor = 'bg-soft-success';
                            } elseif ($chapterCompliance >= 50) {
                                $progressBarColor = 'bg-warning text-dark';
                                $badgeColor = 'bg-soft-warning';
                            } else {
                                $progressBarColor = 'bg-danger';
                                $badgeColor = 'bg-soft-danger';
                            }
                        @endphp

                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex flex-column">
                                @if(empty($award->chapter_id))
                                <span class="font-sm lh-sm text-white" style="font-size: 1rem;">
                                    <strong>{{ $award->entries->firstWhere('key', 'select_institution')->value ?? 'N/A' }}</strong>
                                </span>
                                @else
                                <span class="font-sm lh-sm text-white" style="font-size: 1rem;">
                                    {{ $award->chapter->name ?? '—' }}
                                </span>
                                @endif
                                <span class="font-xs text-muted mt-0.5" style="color: rgba(255,255,255,0.5) !important;">
                                    {{ $award->zone->name ?? 'N/A' }} &bull; {{ $award->field->name ?? 'N/A' }}
                                </span>
                            </div>

                            <span class="badge badge-status {{ $badgeColor }}">
                                {{ $chapterCompliance }}% Compliance
                            </span>
                        </div>

                        <div class="progress rounded-pill shadow-none" style="height: 6px; background-color: rgba(255,255,255,0.1);">
                            <div class="progress-bar {{ $progressBarColor }} rounded-pill"
                                role="progressbar"
                                style="width: {{ $chapterCompliance }}%;"
                                aria-valuenow="{{ $chapterCompliance }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <!-- Divider separating context from approval flow metrics -->
                    <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); width: 100%;"></div>
                    <div>
                        @if($award->currentShortlistStage)

                            <div class="d-flex align-items-center justify-content-between mb-2 p-2 rounded"
                                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">

                                <div class="d-flex flex-column">

                                    <span class="meta-label" style="font-size: 0.65rem; color: rgba(255,255,255,0.6);">
                                        Shortlist Stage
                                    </span>

                                    <span class="text-white fw-semibold" style="font-size: 0.85rem;">
                                        {{ $award->currentShortlistStage->stage->title }} @if($award->currentShortlistStage->stage->mark_as_final)<span style="color:red">(Final Stage)</span>@endif
                                    </span>

                                </div>

                                <a href="#"
                                class="btn btn-sm btn-outline-light"
                                data-toggle="modal"
                                data-target="#shortlistHistoryModal">
                                    History
                                </a>

                            </div>

                        @else

                            <div class="p-2 rounded mb-2"
                                style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);">

                                <span class="text-danger font-xs fw-semibold">
                                    Not Shortlisted
                                </span>

                            </div>

                        @endif
                        <div class="meta-label mb-1 mt-2" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.8px; color: rgba(255, 255, 255, 0.5) !important;">
                            Approval Status Clearance
                        </div>

                        @php
                            $stages = [
                                'Chapter'  => ['value' => $award->chapter_status,  'level' => 'chapter'],
                                'Zone'     => ['value' => $award->zone_status,     'level' => 'zone'],
                                'Field'    => ['value' => $award->field_status,    'level' => 'field'],
                                'National' => ['value' => $award->national_status, 'level' => 'national'],
                            ];
                        @endphp

                        <div class="row g-2">
                            @foreach($stages as $label => $data)
                                <div class="col-6 col-sm-3 col-lg-6 col-xl-3">
                                    <!-- Calibrated Micro-Card Background and Borders for Dark Blue backgrounds -->
                                    <div class="p-2 rounded d-flex flex-column align-items-start justify-content-between"
                                        style="background-color: rgba(15, 23, 42, 0.4); min-height: 54px; border: 1px solid rgba(255, 255, 255, 0.08);">

                                        <span class="mb-1" style="font-size: 0.65rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.3px; color: white !important;">
                                            {{ $label }}
                                        </span>

                                        @if($data['value'] == 2)
                                            <!-- High-Contrast Neon Red Tag -->
                                            <span class="badge d-inline-flex align-items-center gap-1 border"
                                                style="font-size: 0.65rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; background-color: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.4) !important; color: #ff6b6b !important;">
                                                Rejected
                                                <a href="#{{ $data['level'] }}Rejection{{ $award->id }}" data-toggle="modal" class="lh-1 m-0 p-0" style="color: #ff6b6b !important;" title="View feedback">
                                                    <i class="bx bx-message-rounded-dots" style="font-size: 0.85rem; vertical-align: middle;"></i>
                                                </a>
                                            </span>
                                        @elseif($data['value'] == 1)
                                            <!-- High-Contrast Vibrant Emerald Tag -->
                                            <span class="badge border"
                                                style="font-size: 0.65rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; background-color: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.4) !important; color: #34d399 !important;">
                                                Approved
                                            </span>
                                        @else
                                            <!-- High-Contrast Crisp Amber Gold Tag -->
                                            <span class="badge border"
                                                style="font-size: 0.65rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; background-color: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.4) !important; color: #fbbf24 !important;">
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <div class="row align-items-center g-4">
            @if ($isAdmin)
            <div class="col-12">

                <div class="meta-label">
                    National Status Details
                </div>

                @if ($award->national_status == 1)

                    <div>
                        <strong>Approved By:</strong>
                        {{ optional($award->approvedBy)->name ?? 'N/A' }}
                    </div>

                    <div class="meta-value">
                        {{ optional(\Carbon\Carbon::parse($award->national_approved_on))?->format('d M Y, h:i A') ?? 'N/A' }}
                    </div>

                @elseif ($award->national_status == 2)

                    <div>
                        <strong>Rejected By:</strong>
                        {{ optional($award->rejectedBy)->name ?? 'N/A' }}
                    </div>

                    <div class="meta-value">
                        {{ optional(\Carbon\Carbon::parse($award->national_rejected_on))?->format('d M Y, h:i A') ?? 'N/A' }}
                    </div>

                    @if ($award->national_comment)
                        <div class="mt-2">
                            <strong>Rejection Comment:</strong>
                            <p class="mb-0">
                                {{ $award->national_comment }}
                            </p>
                        </div>
                    @endif

                @endif

            </div>
            @endif
        </div>
    </div>
</div>

<!-- Main Dynamic Key-Value Form Section -->
<section id="kv-entry-form">
    <form action="{{ route($isAdmin ? 'awards.update' : 'stakeholders.awards.update') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Confirm changes? This action will save edits to this award submission record.');">
        @csrf

        <div class="section-card">
            <h4 class="text-dark fw-bold mb-2 border-bottom pb-2">Nomination Entry Form Values</h4>
            <div class="row g-4">
               
                @foreach($award->entries as $entry)
                    @php
                        $isInstitutionKey = in_array($entry->key, ['select_institution', 'chapter_id']);

                        $cleanKey = strtolower($entry->key);

                        $isFieldFile = in_array($entry->key, $fileFields) || str_contains(strtolower($entry->key), 'file') || str_contains(strtolower($entry->key), 'image');

                        $specialFields = specialFormFields();
                        $hasSpecialSchema = array_key_exists($cleanKey, $specialFields);
                        $fieldSchema = $hasSpecialSchema ? $specialFields[$cleanKey] : null;
                        // dd($hasSpecialSchema);
                    @endphp

                    @if($isInstitutionKey && !$isAdmin)
                        @continue
                    @endif

                    <div class="col-12 col-md-6">
                        <div class="form-field-group border-0 h-100 justify-content-end">

                            <label for="entry-{{ $entry->id }}"
                                class="form-label text-dark fw-semibold font-sm mb-1 d-flex align-items-center gap-2">

                                <span>{{ $entry->name }}</span>

                                @if($isFieldFile)
                                    <a href="{{route('award.sync.asset', $entry->id)}}" class="ml-1 badge bg-light text-primary border fw-normal d-inline-flex align-items-center gap-1"
                                        style="font-size: 0.7rem; cursor: pointer;">
                                        <i class="fa fa-sync-alt"></i>
                                        Re-sync
                                    </a>
                                @endif

                            </label>

                            <div class="w-100">
                                @if($isFieldFile)
                                    <div class="d-flex align-items-center gap-3 border rounded-3 p-2 bg-white" style="min-height: 70px;">
                                        @if(!empty($entry->value))
                                            <img src="{{ route($isAdmin ? 'admin.protected.download' : 'protected.download', ['file' => $entry->value]) }}"
                                                class="file-thumbnail-preview trigger-zoom-modal"
                                                data-file-url="{{ route(($isAdmin ? 'admin.protected.download' : 'protected.download'), ['file' => $entry->value]) }}"
                                                data-label="{{ str_replace('_', ' ', $entry->key) }}"
                                                title="Click to zoom file context"
                                                alt="Attachment">
                                        @else
                                            <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center" style="width:50px; height:50px; flex-shrink: 0;">
                                                <i class="fa fa-file-image-o font-medium"></i>
                                            </div>
                                        @endif

                                        <div class="flex-grow-1">
                                            {{-- Hide file uploader entirely if form is not editable --}}
                                            @if($canEdit == 1)
                                                <input type="file"
                                                    class="form-control form-control-sm border-0 p-0 shadow-none mb-1"
                                                    id="entry-{{ $entry->id }}"
                                                    name="entries[{{ $entry->key }}]"
                                                    accept=".jpg,.jpeg,.png">
                                                <div class="font-xs text-muted mt-0.5">Upload to replace file asset (Accepts: JPG, JPEG, PNG)</div>
                                            @else
                                                <span class="text-muted font-xs d-block"><i class="bx bx-lock-alt me-0.5"></i> Document locked for review</span>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($hasSpecialSchema)
                                    {{-- Handle Custom Select Dropdown Options --}}
                                    @if($fieldSchema['type'] === 'select')
                                        <select class="form-select w-100 mb-1"
                                                id="entry-{{ $entry->id }}"
                                                name="entries[{{ $entry->key }}]"
                                                {{ $canEdit == 0 ? 'disabled' : '' }}>
                                            <option value="">-- Choose Option --</option>
                                            @foreach($fieldSchema['options'] as $option)
                                                <option value="{{ $option }}" {{ (string)$entry->value === (string)$option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>

                                    {{-- Handle Native HTML Date Inputs --}}
                                    @elseif($fieldSchema['type'] === 'date')
                                        <input type="date"
                                            class="form-control w-100 mb-1"
                                            id="entry-{{ $entry->id }}"
                                            name="entries[{{ $entry->key }}]"
                                            value="{{ !empty($entry->value) ? date('Y-m-d', strtotime($entry->value)) : '' }}"
                                            {{ $canEdit == 0 ? 'disabled' : '' }}>
                                    @endif
                                @elseif($isInstitutionKey)
                                    <select class="form-select w-100 mb-1"
                                            id="entry-{{ $entry->id }}"
                                            name="entries[{{ $entry->key }}]"
                                            {{ $canEdit == 0 ? 'disabled' : '' }}>
                                        <option value="">-- Select or Search Institution --</option>
                                        @foreach($chapters as $chapter)
                                            <option value="{{ $chapter->id }}" {{ (string)$entry->value === (string)$chapter->id ? 'selected' : '' }}>
                                                {{ $chapter->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                @else
                                    @if(strlen($entry->value) > 100)
                                        <textarea class="form-control w-100"
                                                id="entry-{{ $entry->id }}"
                                                name="entries[{{ $entry->key }}]"
                                                rows="3"
                                                {{ $canEdit == 0 ? 'disabled' : '' }}>{{ $entry->value }}</textarea>
                                    @else
                                        <input type="text"
                                            class="form-control w-100 mb-1"
                                            id="entry-{{ $entry->id }}"
                                            name="entries[{{ $entry->key }}]"
                                            value="{{ $entry->value }}"
                                            {{ $canEdit == 0 ? 'disabled' : '' }}>
                                    @endif
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
            <input type="hidden" name="award_id" value="{{ $award->id }}">
            <div class="row g-4 mt-2  pt-2 mt-2 border-top">
                @php
                    $role = auth()->user()->role_id
                        ?? (auth()->user()->role
                        ?? (auth()->guard('stakeholder')->user()->role_id ?? null));

                    $commentFields = [
                        'chapter' => [
                            'comment_field' => 'chapter_comment',
                            'label'         => 'Chapter Review',
                            'icon'          => 'bx-layer',
                            'theme'         => 'warning',
                            'active'        => in_array($role, chapterStakeholders()),
                            'comment_value' => $award->chapter_comment ?? '',
                            'status_value'  => $award->chapter_status ?? 0
                        ],
                        'zone' => [
                            'comment_field' => 'zone_comment',
                            'label'         => 'Zone Review',
                            'icon'          => 'bx-layer',
                            'theme'         => 'warning',
                            'active'        => in_array($role, zoneStakeholders()),
                            'comment_value' => $award->zone_comment ?? '',
                            'status_value'  => $award->zone_status ?? 0
                        ],

                        'field' => [
                            'comment_field' => 'field_comment',
                            'label'         => 'Field Review',
                            'icon'          => 'bx-globe',
                            'theme'         => 'info',
                            'active'        => in_array($role, fieldStakeholders()),
                            'comment_value' => $award->field_comment ?? '',
                            'status_value'  => $award->field_status ?? 0
                        ],

                        'national' => [
                            'comment_field' => 'national_comment',
                            'label'         => 'National Review',
                            'icon'          => 'bx-shield-quarter',
                            'theme'         => 'success',
                            'active'        => in_array($role, secretariatStakeholders()),
                            'comment_value' => $award->national_comment ?? '',
                            'status_value'  => $award->national_status ?? 0
                        ],

                        // 'final' => [
                        //     'comment_field' => 'final_comment',
                        //     'label'         => 'Final Administrative Decision',
                        //     'icon'          => 'bx-check-shield',
                        //     'theme'         => 'primary',
                        //     'active'        => ($isAdmin || in_array($role, ncpStakeholders())),
                        //     'comment_value' => $award->final_comment ?? '',
                        //     'status_value'  => $award->final_status ?? 0
                        // ]
                    ];
                @endphp
                @foreach($commentFields as $tier => $config)

                    @if($config['active'] || $isAdmin || !empty($config['comment_value']))

                        <div class="col-12 col-lg-12">
                            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background:yellow">
                                <div class="card-body">

                                    <!-- Comment Block Only -->
                                    <div>

                                        <label class="form-label fw-semibold text-dark small mb-2">
                                            {{ $config['label'] }} Comments
                                        </label>

                                        {{-- Enforce explicit editable rights ONLY for Admin users --}}
                                        @if($isAdmin || $canComment)

                                            <textarea
                                                class="form-control rounded-3 shadow-none border"
                                                id="{{ $config['comment_field'] }}"
                                                name="{{ $config['comment_field'] }}"
                                                rows="5"
                                                placeholder="Enter review observations, recommendations, or administrative remarks..."
                                            >{{ $config['comment_value'] }}</textarea>

                                        @else

                                            <div class="border rounded-3 bg-light p-3 text-secondary"
                                                style="min-height: 130px; white-space: pre-wrap; line-height: 1.6;">

                                                @if(!empty($config['comment_value']))
                                                    {{ $config['comment_value'] }}
                                                @else
                                                    <span class="text-muted">
                                                        No comments submitted for this review stage.
                                                    </span>
                                                @endif

                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                @endforeach

            </div>
            @if($canEdit || $canComment)
            <div class="mt-4 mb-1">
                <button type="submit" class="btn btn-primary fw-semibold px-5 py-2.5" style="border-radius:8px">
                    <i class="fa fa-save me-1"></i> Save Changes & Update Records
                </button>
            </div>
            @endif
        </div>
        <!-- Bottom Control Submit Button Grid -->
    </form>
</section>
</div>

@include('admin.awards.actions')

@endsection
@include('admin.awards.show_script')
