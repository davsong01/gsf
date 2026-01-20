<div class="content-body">
    <section id="reports-dashboard">
        <h4 class="card-title">All Reports</h4>
        {{-- {{dd(canAddNextMonthReport($user))}} --}}
        @php
            $eligibleMonth = canAddReport($user);
        @endphp

        @if($eligibleMonth)
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <a href="{{ route('stakeholders.reports.create') }}" class="btn btn-primary">
                    Add {{ $eligibleMonth }}’s Report
                </a>
            </div>
        </div>
        @endif
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

            $hierarchyCol = $hierarchyCount > 1 ? intval(12 / $hierarchyCount) : 5;
        @endphp

        <div class="row mb-3">
            <div class="col-12">
                <form method="GET" class="row g-2 align-items-end">

                    {{-- Date range --}}
                    <div class="col-md-2 mb-2">
                        <label class="form-label">From</label>
                        <input type="date" name="from_date" class="form-control"
                            value="{{ request('from_date') ?? now()->startOfMonth()->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="form-label">To</label>
                        <input type="date" name="to_date" class="form-control"
                            value="{{ request('to_date') ?? now()->format('Y-m-d') }}">
                    </div>

                    {{-- Field (highest first) --}}
                    @if($canViewField)
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
                    @if($canViewZone)
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
                    @if($canViewChapter)
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
                            <option value="">All Status</option>
                            <option value="zone_pending" @selected(request('status_filter')=='zone_pending')>Zone Pending</option>
                            <option value="zone_approved" @selected(request('status_filter')=='zone_approved')>Zone Approved</option>
                            <option value="zone_rejected" @selected(request('status_filter')=='zone_rejected')>Zone Rejected</option>
                            <option value="field_pending" @selected(request('status_filter')=='field_pending')>Field Pending</option>
                            <option value="field_approved" @selected(request('status_filter')=='field_approved')>Field Approved</option>
                            <option value="field_rejected" @selected(request('status_filter')=='field_rejected')>Field Rejected</option>
                            <option value="national_pending" @selected(request('status_filter')=='national_pending')>National Pending</option>
                            <option value="national_approved" @selected(request('status_filter')=='national_approved')>National Approved</option>
                            <option value="national_rejected" @selected(request('status_filter')=='national_rejected')>National Rejected</option>
                        </select>
                    </div>

                    {{-- Button --}}
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-secondary w-100">Filter</button>
                    </div>

                </form>
            </div>
        </div>


        <!-- Reports Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead >
                                    <tr>
                                        <th>S/N</th>
                                        @if(in_array($user->role_id, array_merge(fieldStakeholders(), zoneStakeholders(), secretariatStakeholders(), ncpStakeholders())))
                                            <th>Chapter</th>
                                        @endif
                                        <th>Month/Year</th>
                                        <th>Approval Status</th>
                                        <th>Academic Session</th>
                                        <th>Created On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($reports as $report)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        @if(in_array($user->role_id, array_merge(fieldStakeholders(), zoneStakeholders(), secretariatStakeholders(), ncpStakeholders())))
                                            <td>{{ $report->chapter->name ?? '—' }}</td>
                                        @endif

                                        <td>{{ date('F', mktime(0, 0, 0, $report->month, 10)) }}, {{ $report->year }}</td>

                                        <td class="text-start">
                                            @php
                                                $canEdit     = app(\App\Services\ReportService::class)->canEditReport($report, $user);

                                                $statuses = [
                                                    'Zone' => [
                                                        'value' => $report->zone_status,
                                                        'modal' => 'zoneRejection',
                                                        'view'  => 'stakeholder.modals.zone_rejection_comment',
                                                    ],
                                                    'Field' => [
                                                        'value' => $report->field_status,
                                                        'modal' => 'fieldRejection',
                                                        'view'  => 'stakeholder.modals.field_rejection_comment',
                                                    ],
                                                    'National' => [
                                                        'value' => $report->national_status,
                                                        'modal' => 'secretariatRejection',
                                                        'view'  => 'stakeholder.modals.secretariat_rejection_comment',
                                                    ],
                                                ];
                                            @endphp

                                            @foreach($statuses as $label => $data)
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <small class="text-muted fw-medium pr-1">{{ $label }}</small>

                                                    @if($data['value'] === 2)
                                                        <span class="badge bg-danger">Rejected</span>

                                                        <a href="#{{ $data['modal'] }}{{ $report->id }}"
                                                        data-toggle="modal"
                                                        class="text-danger"
                                                        title="View feedback">
                                                            <i class="bx bx-message-rounded-dots"></i>
                                                        </a>

                                                        @include($data['view'])

                                                    @elseif($data['value'] === 1)
                                                        <span class="badge bg-success">Approved</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>

                                        <td>{{ $report->session }}</td>
                                        <td>{{ $report->created_at->format('d M Y, h:i A') }}</td>

                                        <td class="text-center">
                                            {{-- View --}}
                                            <a href="{{ route($isAdmin ? 'stakeholderreports.show' : 'stakeholders.reports.show', $report->id) }}" class="text-primary mx-1" title="View Report">
                                                <i class="fa fa-eye"></i>
                                            </a>

                                            {{-- Edit --}}
                                            @if($canEdit['canEdit'])
                                                <a
                                                    href="{{ route($isAdmin ? 'stakeholderreports.edit' : 'stakeholders.reports.edit', $report->id) }}"
                                                    class="text-warning mx-1"
                                                    title="Edit Report"
                                                    onclick="return confirm('Are you sure you want to edit this report?');"
                                                >
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endif

                                            {{-- Download --}}
                                            @if($canEdit['allApproved'])
                                                <a
                                                    href="{{ route($isAdmin ? 'stakeholderreports.download' : 'stakeholders.reports.download', $report->id) }}"
                                                    target="_blank"
                                                    class="text-success mx-1"
                                                    title="Download Report"
                                                >
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            @endif

                                            {{-- Nudge --}}
                                            <a
                                                href="{{ route($isAdmin ? 'stakeholderreports.nudge' : 'stakeholders.reports.nudge', $report->id) }}"
                                                class="text-indigo-600 mx-1"
                                                title="Send Nudge"
                                            >
                                                <i class="fa fa-bullhorn"></i>
                                            </a>

                                            {{-- Delete --}}
                                            @if(
                                                $isAdmin &&
                                                $statuses['Zone']['value'] == 0 &&
                                                $statuses['Field']['value'] == 0 &&
                                                $statuses['National']['value'] == 0
                                            )
                                                <a
                                                    href="#"
                                                    class="text-danger mx-1"
                                                    title="Delete Report"
                                                    onclick="
                                                        event.preventDefault();
                                                        if (confirm('Are you sure you want to delete this report?')) {
                                                            document.getElementById('delete-report-{{ $report->id }}').submit();
                                                        }
                                                    "
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6l-2 14H7L5 6"/>
                                                        <path d="M10 11v6"/>
                                                        <path d="M14 11v6"/>
                                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                    </svg>
                                                </a>

                                                {{-- Hidden destroy form --}}
                                                <form
                                                    id="delete-report-{{ $report->id }}"
                                                    action="{{ route('stakeholderreports.destroy', $report->id) }}"
                                                    method="POST"
                                                    class="d-none"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif


                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-3">
                                {{ $reports->links() }}
                            </div>
                        </div>

                        <!-- Pagination -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
