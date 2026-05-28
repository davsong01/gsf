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
</style>
@endsection

@section('content')
<div class="content-body">
    <section id="awards-dashboard">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">{{ $title }}</h4>
        </div>

        <!-- Filters Form Panel -->
        <div class="row">
            <div class="col-12">
                <form method="GET" class="row g-2 align-items-end">
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

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Approval Status</label>
                        <select name="status_filter" class="form-control">
                            <option value="">All Statuses</option>
                            @foreach($approval_statuses as $key => $label)
                                <option value="{{ $key }}" @selected(request('status_filter') == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-secondary w-100">Filter</button>
                    </div>
                    @if($isAdmin)
                    <div class="col-md-3 mb-2">
                        <a href="{{route('award.report.download', $type)}}" class="btn btn-info w-100">Download Report</a>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Master Data Render Table Component -->
        <div class="row">
            <div class="col-12">
                <form id="bulk-actions-form" method="POST" action="{{ route($isAdmin ? 'awards.bulk-delete' : 'stakeholders.awards.bulk-delete') }}" onsubmit="return confirm('Confirm total purge of selected entries?');">
                    @csrf

                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden p-1">
                        <!-- Clean, Integrated Table Container -->
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                                <table class="table table-align-middle mb-0 custom-modern-table">
                                    <thead>
                                        <tr class="bg-light">
                                            <!-- Embedded Action Cell -->
                                            <th class="ps-4 align-middle" style="width: 60px; min-width: 60px;">
                                                <div class="d-flex align-items-center gap-1.5">
                                                    <!-- Master Checkbox -->
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input cursor-pointer shadow-none" id="master-checkbox">
                                                    </div>
                                                    
                                                    <!-- Floating Micro-Dropdown Inline (Icon Only) -->
                                                    <div class="dropdown" id="bulk-dropdown-wrapper" style="opacity: 0; pointer-events: none; transition: all 0.2s ease-in-out;">
                                                        
                                                        <button class="btn btn-white btn-sm border p-0 d-flex align-items-center justify-content-center shadow-none text-secondary rounded-2" 
                                                                type="button" 
                                                                id="bulkActionsMenu" 
                                                                data-toggle="dropdown" 
                                                                aria-haspopup="true" 
                                                                aria-expanded="false"
                                                                style="width: 24px; height: 24px;"
                                                                title="Bulk Actions">
                                                            <i class="bx bx-dots-vertical-rounded font-sm"></i>
                                                        </button>
                                                     
                                                        <div class="dropdown-menu border-0 shadow-sm rounded-2 font-xs py-1" aria-labelledby="bulkActionsMenu">
                                                            <!-- Action 1: Bulk Approve -->
                                                            @if($isAdmin)
                                                            <button type="button" 
                                                                    class="dropdown-item text-success d-flex align-items-center gap-2 py-1.5 fw-medium bulk-action-trigger" 
                                                                    data-action-url="{{ route('awards.bulk-approve') }}" 
                                                                    data-method="POST" 
                                                                    data-confirm="Are you sure you want to APPROVE all (<span class='selected-count-placeholder'>0</span>) selected entries?">
                                                                <i class="fa fa-check font-xs pr-1"></i> Approve Selected (<span class="selected-count-placeholder">0</span>)
                                                            </button>
                                                            @endif
                                                            @if($user->email == 'davsong@gmail.com')
                                                            <!-- Action 2: Bulk Delete -->
                                                            <button type="button" 
                                                                    class="dropdown-item text-danger d-flex align-items-center gap-2 py-1.5 fw-medium bulk-action-trigger" 
                                                                    data-action-url="{{ route($isAdmin ? 'awards.bulk-delete' : 'stakeholders.awards.bulk-delete') }}" 
                                                                    data-method="DELETE" 
                                                                    data-confirm="Are you absolutely sure you want to DELETE and purge all (<span class='selected-count-placeholder'>0</span>) selected entries? This cannot be undone.">
                                                                <i class="fa fa-trash font-xs pr-1"></i> Delete Selected (<span class="selected-count-placeholder">0</span>)
                                                            </button>
                                                            @endif
                                                        </div>
                                                    </div>
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
                                            <tr class="sticky-chapter-row">
    
                                                <td colspan="3" class="ps-4 align-middle pt-1 pb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex flex-column">
                                                            <span class="text-dark fw-bold font-base m-0 lh-base" style="letter-spacing: -0.01em;">
                                                                {{ $groupKey }}
                                                            </span>
                                                            @if($awardGroup->first() && $awardGroup->first()->chapter)
                                                                <span class="text-muted font-xs lh-sm" style="font-size: 0.68rem;">
                                                                    {{ $awardGroup->first()->zone->name ?? 'N/A' }} &bull; {{ $awardGroup->first()->field->name ?? 'N/A' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="align-middle pt-3 pb-2.5">
                                                    <span class="badge bg-secondary font-xs rounded-pill fw-medium px-2 py-1" style="font-size: 0.7rem;">
                                                        {{ $awardGroup->count() }} {{ Str::plural('Entry', $awardGroup->count()) }}
                                                    </span>
                                                </td>

                                                <td colspan="3" class="pe-4 align-middle pt-3 pb-2.5">
                                                    @if($awardGroup->first() && $awardGroup->first()->chapter && ($isAdmin || in_array($user->role_id, array_merge(fieldStakeholders(), zoneStakeholders(), secretariatStakeholders(), ncpStakeholders(), chapterStakeholders()))))
                                                        @php
                                                            $chapterCompliance = $awardGroup->first()->chapter->reportCompliance();
                                                            
                                                            if ($chapterCompliance >= 80) {
                                                                $progressBarColor = 'bg-success';
                                                                $badgeColor = 'bg-soft-success';
                                                            } elseif ($chapterCompliance >= 50) {
                                                                $progressBarColor = 'bg-warning';
                                                                $badgeColor = 'bg-soft-warning';
                                                            } else {
                                                                $progressBarColor = 'bg-danger';
                                                                $badgeColor = 'bg-soft-danger';
                                                            }
                                                        @endphp
                                                        
                                                        <div class="d-flex flex-column gap-1 ms-auto" style="max-width: 240px;">
                                                            <span class="text-muted font-xs fw-semibold tracking-wide text-uppercase" style="font-size: 0.65rem;">
                                                                Report Compliance Level
                                                            </span>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="progress rounded-pill w-100 shadow-none border-0 mb-0" style="height: 5px; background-color: #cbd5e1;">
                                                                    <div class="progress-bar {{ $progressBarColor }} rounded-pill" 
                                                                        role="progressbar" 
                                                                        style="width: {{ $chapterCompliance }}%;" 
                                                                        aria-valuenow="{{ $chapterCompliance }}" aria-valuemin="0" aria-valuemax="100">
                                                                    </div>
                                                                </div>
                                                                <span class="badge badge-status {{ $badgeColor }} text-nowrap fw-bold" style="font-size: 0.68rem !important; min-width: 45px; text-align: center; padding: 2px 6px;">
                                                                    {{ $chapterCompliance }}%
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-end text-muted font-xs pe-2">—</div>
                                                    @endif
                                                </td>

                                            </tr>

                                            <!-- Internal Submissions Loop Iterator -->
                                            @foreach($awardGroup as $award)
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
                                                        <span class="text-dark fw-semibold font-base mb-0">{{ $award->name }}</span>
                                                        @if(empty($award->chapter_id))
                                                        <span class="font-sm lh-sm" style="font-size: 0.68rem;">
                                                            <strong>{{ $award->entries->firstWhere('key', 'select_institution')->value ?? 'N/A' }}</strong>
                                                        </span>
                                                        @endif
                                                        <span class="text-muted tracking-tight font-xs font-monospace">{{ $award->reference }}</span>
                                                        @if($award->national_status == 1)
                                                            <br>
                                                            <span><strong>Approved By:</strong> {{ $award->approvedBy?->name ?? 'System' }}</span><br>
                                                            <span><strong>Approved On:</strong> {{ $award->national_approved_on ? \Carbon\Carbon::parse($award->national_approved_on)->format('d M Y, h:i A') : '—' }}</span><br>
                                                        @endif

                                                        @if($award->national_status == 2)
                                                            <br>
                                                            <span><strong>Rejected By:</strong> {{ $award->rejectedBy?->name ?? 'System' }}</span><br>
                                                            <span><strong>Rejected On:</strong> {{ $award->national_rejected_on ? \Carbon\Carbon::parse($award->national_rejected_on)->format('d M Y, h:i A') : '—' }}</span><br>
                                                        @endif
                                                        <span style="width: 60%;" class="badge badge-modern bg-soft-primary text-primary">{{ $award->type == 'go' ? 'GO AWARD' : $award->type }}</span>

                                                    </div>
                                                </td>
                                                
                                                <td>
                                                    @php
                                                        $statuses = [
                                                            'Chapter'     => ['value' => $award->chapter_status,     'level' => 'chapter'],
                                                            'Zone'     => ['value' => $award->zone_status,     'level' => 'zone'],
                                                            'Field'    => ['value' => $award->field_status,    'level' => 'field'],
                                                            'National' => ['value' => $award->national_status, 'level' => 'national'],
                                                        ];
                                                    @endphp
                                                    
                                                    <div class="d-flex flex-column gap-1">
                                                        @foreach($statuses as $label => $data)
                                                            <div class="d-flex align-items-center font-xs">
                                                                <!-- Pro tip: changed width: 100% to a fixed width or flex-grow to ensure the badges line up neatly -->
                                                                <span class="text-muted fw-normal flex-grow-1">{{ $label }}</span>
                                                                
                                                                @if($data['value'] == 2)
                                                                    <span class="badge badge-status bg-soft-danger text-danger d-inline-flex align-items-center gap-1">
                                                                        Rejected
                                                                        <!-- The target ID now cleanly matches our dynamic pattern: #zoneRejection12, #fieldRejection12, etc. -->
                                                                        <a href="#{{ $data['level'] }}Rejection{{ $award->id }}" data-toggle="modal" class="text-danger lh-1 font-sm" title="View feedback">
                                                                            <i class="bx bx-message-rounded-dots"></i>
                                                                        </a>
                                                                    </span>
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
                                                        @if($isAdmin)
                                                        <button type="button" class="btn btn-action-icon text-secondary bg-transparent border-0 p-1" data-toggle="modal" data-target="#statusAdjustModal{{ $award->id }}" title="Adjust Status">
                                                            <i class="fa fa-cog font-sm"></i>
                                                        </button>
                                                        @endif
                                                        
                                                        <a href="{{ route($isAdmin ? 'awards.show' : 'stakeholders.awards.show', $award->id) }}" class="btn btn-action-icon text-warning p-1" title="Edit Entry" onclick="return confirm('Modify this record?');"><i class="fa fa-edit font-sm"></i></a>
                                                        
                                                        @if($isAdmin && $user->email == 'davsong@gmail.com')
                                                        <a href="#" class="btn btn-action-icon text-danger p-1" title="Delete Submission" onclick="event.preventDefault(); if (confirm('Remove this submission record?')) { document.getElementById('delete-award-{{ $award->id }}').submit(); }"><i class="fa fa-trash font-sm"></i></a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach

                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">
                                                    <i class="bx bx-file-blank display-4 d-block mb-2 text-light"></i>
                                                    No award submissions matched your search index parameters.
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
                </form>
            </div>
        </div>
    </section>
</div>

<!-- =========================================================================
     MODAL LAYERS & ALTERNATE STANDALONE DELETION FRAMES SUB-DECK 
     ========================================================================= -->
@foreach($awards as $awardGroup)
    @foreach($awardGroup as $award)
        
        {{-- Status Adjustment Modal --}}
        <div class="modal fade" id="statusAdjustModal{{ $award->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <!-- Status setting operations form content can be structured inside this container -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Adjust Status: {{ $award->reference }}</h5>
                        <button type="button" class="btn-close border-0 bg-transparent" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <!-- Custom settings inputs matching controller handlers -->
                        <p class="font-sm text-muted">Modify verification markers for this nomination context profile.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dynamic Feedback Comment Partials Backdrops --}}
        @foreach([
            'chapter'  => ['status' => $award->chapter_status, 'field' => 'chapter_comment', 'title' => 'Chapter Office'],
            'zone'     => ['status' => $award->zone_status, 'field' => 'zone_comment', 'title' => 'Zone'],
            'field'    => ['status' => $award->field_status, 'field' => 'field_comment', 'title' => 'Field'],
            'national' => ['status' => $award->national_status, 'field' => 'national_comment', 'title' => 'National Secretariat']
        ] as $level => $config)
            
            @if($config['status'] == 2)
                @include('stakeholder.modals.award_rejection_comments', [
                    'level'   => $level,
                    'title'   => $config['title'],
                    'comment' => $award->{$config['field']},
                    'report'  => $award
                ])
            @endif

        @endforeach

        {{-- Single Record Destructive Action Execution Target Handler --}}
        <form id="delete-award-{{ $award->id }}" action="{{ route($isAdmin ? 'awards.delete' : 'stakeholders.awards.delete', $award->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

    @endforeach
@endforeach

@endsection

@section('extra_scripts')
<script>
    $(document).ready(function() {
        const masterCheckbox = $('#master-checkbox');
        const rowCheckboxes = $('.row-checkbox');
        const bulkWrapper = $('#bulk-dropdown-wrapper');
        const form = $('#bulk-actions-form');
        const methodContainer = $('#method-spoofing-container');

        function evaluateBulkToolbarState() {
            const checkedCount = $('.row-checkbox:checked').length;
            if (checkedCount > 0) {
                bulkWrapper.css({'opacity': '1', 'pointer-events': 'auto'});
                $('.selected-count-placeholder').text(checkedCount);
            } else {
                bulkWrapper.css({'opacity': '0', 'pointer-events': 'none'});
                $('.selected-count-placeholder').text('0');
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

        // Intercept action options inside dropdown items contextually
        $('.bulk-action-trigger').on('click', function(e) {
            e.preventDefault();
            
            const targetUrl = $(this).data('action-url');
            const httpMethod = $(this).data('method').toUpperCase();
            const checkedCount = $('.row-checkbox:checked').length;
            
            // Build confirmation message text
            let confirmationMessage = $(this).data('confirm');
            confirmationMessage = confirmationMessage.replace("<span class='selected-count-placeholder'>0</span>", checkedCount)
                                                    .replace("<span class=\"selected-count-placeholder\">0</span>", checkedCount);

            if (confirm(confirmationMessage)) {
                // 1. Dynamic route injection
                form.attr('action', targetUrl);
                
                // 2. Clear previous dynamic REST verbs
                methodContainer.empty();
                
                // 3. Inject Laravel method spoofing syntax conditionally 
                if (httpMethod === 'DELETE') {
                    methodContainer.html('<input type="hidden" name="_method" value="DELETE">');
                } else if (httpMethod === 'PUT' || httpMethod === 'PATCH') {
                    methodContainer.html(`<input type="hidden" name="_method" value="${httpMethod}">`);
                }

                // Execute processing submit pipeline
                form.submit();
            }
        });
    });
</script>
@endsection