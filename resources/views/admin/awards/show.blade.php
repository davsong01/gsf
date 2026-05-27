@extends('layouts.dashboard')
@section('title', 'Edit Award Submission')
@section('content')

@section('extra_styles')
<style>
    /* Premium Page Layout Structure */
    .premium-page-wrapper {
        background-color: #f8fafc;
        color: #1e293b;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }
    
    /* Top Profile Meta Deck Frame with Integrated Image Box */
    .overview-banner-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #f8fafc;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
    .header-avatar-preview {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 10px;
        border: 3px solid rgba(255, 255, 255, 0.15);
        cursor: zoom-in;
        transition: transform 0.2s ease, border-color 0.2s;
    }
    .header-avatar-preview:hover {
        transform: scale(1.04);
        border-color: #3b82f6;
    }
    .header-avatar-placeholder {
        width: 90px;
        height: 90px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.05);
        border: 2px dashed rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
    }
    .meta-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        font-weight: 600;
    }
    .meta-value {
        font-size: 0.95rem;
        font-weight: 500;
        color: #f1f5f9;
    }

    /* Modern Dynamic Form Cards */
    .section-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
    }
    .form-field-group {
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    .form-field-group:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }
    
    label {
        font-weight: 600;
        color: #334155;
        font-size: 0.875rem;
        text-transform: capitalize;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        font-size: 0.875rem;
        transition: all 0.15s ease-in-out;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    /* Media File Handling Layouts */
    .file-thumbnail-preview {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        cursor: zoom-in;
        transition: opacity 0.2s;
    }
    .file-thumbnail-preview:hover {
        opacity: 0.85;
    }

    /* Sticky Bottom Actions Command Station */
    .sticky-action-buttons {
        position: fixed;
        bottom: 30px;
        right: 30px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        z-index: 1050;
    }
    .btn-circle {
        position: relative;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        transition: transform 0.2s ease, background-color 0.15s;
        border: none;
    }
    .btn-circle:hover {
        transform: translateY(-2px) scale(1.05);
    }
    .btn-circle .btn-label {
        position: absolute;
        right: 68px;
        white-space: nowrap;
        background: #0f172a;
        padding: 6px 12px;
        border-radius: 6px;
        opacity: 0;
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        pointer-events: none;
        transition: opacity 0.2s ease;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
    }
    .btn-circle:hover .btn-label {
        opacity: 1;
    }
</style>
@endsection

<div class="content-body premium-page-wrapper">
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <strong>Please fix the errors below:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // Dynamically find a profile photo or primary image from key-value pairs to feature on the header banner
        $fileFields = ['picturesave_picture_as_your_name','upload_a_clear_and_recent_picture_of_yourself'];
        $headerImageEntry = $award->entries->first(function($entry) use ($fileFields) {
            return in_array(strtolower($entry->key), $fileFields) && !empty($entry->value);
        });

        // $isEditable = 
    @endphp

    <!-- Top Profile Banner Block with Header Image Positioned Legibly -->
  <div class="card overview-banner-card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                
                <!-- Left Column: Avatar Picture Matrix Container -->
                <div class="col-auto">
                    @if($headerImageEntry)
                        <img src="{{ route($isAdmin ? 'admin.protected.download' : 'protected.download', ['file' => $headerImageEntry->value]) }}" 
                             class="header-avatar-preview trigger-zoom-modal" 
                             data-file-url="{{ route('protected.download', ['file' => $headerImageEntry->value]) }}"
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

                        <!-- Award Classification Type Pill Tag -->
                        {{-- <div>
                            <span class="badge bg-primary text-uppercase font-xs px-2 py-1 shadow-sm" style="font-size: 0.72rem; letter-spacing: 0.02em;">
                                {{ $award->type }}
                            </span>
                        </div> --}}
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

                <!-- Right Column: Integrated Regional Context & Report Compliance Tracking Node -->
                <div class="col-12 col-lg-4">
                    <div class="compliance-wrapper header-compliance-card">
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
                                <span class="font-sm lh-sm" style="font-size: 1rem;">
                                    <strong>{{ $award->entries->firstWhere('key', 'select_institution')->value ?? 'N/A' }}</strong>
                                </span>
                                @else
                                <span class="font-sm lh-sm" style="font-size: 1rem;">
                                    {{ $award->chapter->name ?? '—' }}
                                </span>
                                @endif
                                <span class="font-xs text-muted mt-0.5">
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
                </div>

            </div>
        </div>
    </div>

    <!-- Main Dynamic Key-Value Form Section -->
    <section id="kv-entry-form">
    <form action="{{ route('stakeholderreports.awards.update', $award->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Confirm changes? This action will save edits to this award submission record.');">
        @csrf

        <div class="section-card">
            <h4 class="text-dark fw-bold mb-4 border-bottom pb-2">Nomination Entry Form Values</h4>
            
            <!-- Grid Wrap to enforce 2-column distribution on medium screens and up -->
            <div class="row g-4">
                @foreach($award->entries as $entry)
                    @php
                        $isInstitutionKey = in_array($entry->key, ['select_institution', 'chapter_id']);
                        $isFieldFile = in_array(strtolower($entry->key), ['attach_your_latest_official_school_result_with_your_departments_stamp_and_hod_signature','attach_your_latest_official_school_result_with_your_departments_stamp_and_hod_signature_file_id','upload_a_clear_and_recent_picture_of_yourself_file_id', 'upload_a_clear_and_recent_picture_of_yourself', 'document', 'uploaded_file', 'proof', 'image', 'attachment', 'signature', 'photo', 'picture', 'avatar']) || str_contains(strtolower($entry->key), 'file') || str_contains(strtolower($entry->key), 'image');
                    @endphp

                    {{-- CRITICAL: Completely skip rendering the entire layout block if it is an institution key and the user is NOT an admin --}}
                    @if($isInstitutionKey && !$isAdmin)
                        @continue
                    @endif

                    <div class="col-12 col-md-6">
                        <div class="form-field-group border-0 h-100 justify-content-end">
                            
                            <label for="entry-{{ $entry->id }}" class="form-label text-dark fw-semibold font-sm mb-1">
                                {{ $entry->name }}
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
                                            <input type="file" 
                                                class="form-control form-control-sm border-0 p-0 shadow-none mb-1" 
                                                id="entry-{{ $entry->id }}"
                                                name="entries[{{ $entry->key }}]" 
                                                accept=".jpg,.jpeg,.png">
                                            <div class="font-xs text-muted mt-0.5">Upload to replace file asset (Accepts: JPG, JPEG, PNG)</div>
                                        </div>
                                    </div>

                                {{-- Render institution select dropdown (Only reached if $isAdmin is true due to the @continue check above) --}}
                                @elseif($isInstitutionKey)
                                    <select class="form-select w-100 mb-1" 
                                            id="entry-{{ $entry->id }}" 
                                            name="entries[{{ $entry->key }}]">
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
                                                rows="3">{{ $entry->value }}</textarea>
                                    @else
                                        <input type="text" 
                                            class="form-control w-100 mb-1" 
                                            id="entry-{{ $entry->id }}" 
                                            name="entries[{{ $entry->key }}]" 
                                            value="{{ $entry->value }}">
                                    @endif
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bottom Control Submit Button Grid -->
        <div class="mt-4 mb-5">
            <button type="submit" class="btn btn-primary fw-semibold px-5 py-2.5" style="border-radius:8px">
                <i class="fa fa-save me-1"></i> Save Changes & Update Records
            </button>
        </div>
    </form>
</section>
</div>

<!-- Zoom System Modal Frame Container -->
<div class="modal fade" id="imageZoomSystemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold text-dark text-truncate" id="zoomModalLabel">File Asset Focus</h6>
                <button type="button" class="btn-close shadow-none border-0 bg-transparent text-muted" data-dismiss="modal" aria-label="Close" style="font-size:1.25rem;">&times;</button>
            </div>
            <div class="modal-body text-center p-4">
                <img src="" id="zoomedTargetImageElement" class="img-fluid rounded border shadow-sm" style="max-height: 65vh; object-fit: contain;" alt="Focus Workspace">
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-between">
                <span class="text-muted font-xs">Click outside or press ESC to close full view mode</span>
                <button type="button" class="btn btn-light font-sm px-3 rounded-2" data-dismiss="modal">Close View</button>
            </div>
        </div>
    </div>
</div>

@php
    $canAct = false;
    $userRole = $user->role_id;
    
    if(in_array($userRole, zoneStakeholders()) && in_array($award->zone_status, [0,2])) {
        $canAct = true;
        $approveRoute = route('stakeholders.reports.approve', $award->id);
        $rejectRoute  = route('stakeholders.reports.reject', $award->id);
        $tooltipApprove = 'Approve for Zone';
        $tooltipReject = 'Reject for Zone';
    }
    elseif(in_array($userRole, fieldStakeholders()) && $award->zone_status == 1 && in_array($award->field_status, [0,2])) {
        $canAct = true;
        $approveRoute = route('stakeholders.reports.approve', $award->id);
        $rejectRoute  = route('stakeholders.reports.reject', $award->id);
        $tooltipApprove = 'Approve for Field';
        $tooltipReject = 'Reject for Field';
    }
    elseif(in_array($userRole, array_merge(secretariatStakeholders(), ncpStakeholders()))
           && $award->zone_status == 1
           && $award->field_status == 1
           && in_array($award->national_status, [0,2])) {
        $canAct = true;
        $approveRoute = route('stakeholders.reports.approve', $award->id);
        $rejectRoute  = route('stakeholders.reports.reject', $award->id);
        $tooltipApprove = 'Approve for National';
        $tooltipReject = 'Reject for National';
    }
@endphp

@if($canAct && $approveRoute)
<!-- Floating Action Button Controls Frame -->
<div class="sticky-action-buttons">
    <a href="{{ $approveRoute }}"
       class="btn-circle bg-success text-white d-flex align-items-center justify-content-center"
       title="{{ $tooltipApprove }}"
       onclick="return confirm('Are you sure you want to approve this submission record?');">
        <i class="fa fa-check"></i>
        <span class="btn-label">{{ $tooltipApprove }}</span>
    </a>

    <button type="button" 
            class="btn-circle bg-danger text-white d-flex align-items-center justify-content-center" 
            data-toggle="modal" 
            data-target="#rejectModal"
            title="{{ $tooltipReject }}">
        <i class="fa fa-times"></i>
        <span class="btn-label">{{ $tooltipReject }}</span>
    </button>
</div>

<!-- Rejection Criteria Context Form Modal Backdrop Box -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ $rejectRoute }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark font-base" id="rejectModalLabel">Reject Submission Record</h5>
                    <button type="button" class="btn-close shadow-none border-0 bg-transparent text-muted" data-dismiss="modal" aria-label="Close" style="font-size:1.25rem;">&times;</button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-2">
                        <label for="rejection_reason" class="form-label font-sm fw-medium text-secondary mb-1">Reason explaining the rejection context</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control font-sm rounded-2" rows="4" placeholder="Provide details to the chapter clarifying why adjustments are required..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light font-sm px-3 rounded-2" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger font-sm px-3 rounded-2">Reject Submission</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('extra_scripts')
<script>
    $(document).ready(function() {
        // Shared global zoom system binding logic for headers and fields
        $('.trigger-zoom-modal').on('click', function() {
            const assetUrl = $(this).data('file-url');
            const fieldLabel = $(this).data('label');
            
            $('#zoomModalLabel').text(`Attachment Content: ${fieldLabel}`);
            $('#zoomedTargetImageElement').attr('src', assetUrl);
            $('#imageZoomSystemModal').modal('show');
        });
    });
</script>
@endsection
