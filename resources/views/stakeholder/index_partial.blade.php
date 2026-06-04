<div class="content-body">
    <section id="reports-dashboard">
        <h4 class="card-title">All Reports</h4>
        @if($isAdmin)
        <a class="btn btn-primary btn-sm" href="{{route('report.fix.orphan')}}">Fix orphan reports</a>
        @endif
        {{-- {{dd(canAddNextMonthReport($user))}} --}}
        @php
            $eligibleMonth = canAddReport($user->chapter_id);
        @endphp

        @if($eligibleMonth['eligible'] && !$isAdmin)
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <a href="{{ route('stakeholders.reports.create') }}" class="btn btn-primary">
                    Add {{ $eligibleMonth['month']}} {{$eligibleMonth['year']}}’s Report
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
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Approval Status</label>
                        <select name="status_filter" class="form-control">
                            <option value="">All Status</option>
                            @foreach($approval_statuses as $key => $label)
                                <option value="{{ $key }}" @selected(request('status_filter') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Button --}}
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-secondary w-100">Filter</button>
                    </div>

                    @if($canViewZone || $canViewChapter || $canViewField)
                    @php
                        if($isAdmin){
                            $route = 'reports.analytics';
                        }else{
                            $route = 'stakeholders.reports.analytics';
                        }
                    @endphp
                    <div class="col-md-2 mb-2">
                        <a href="{{route($route)}}" class="btn btn-primary w-100">View Analytics</a>
                    </div>

                    <div class="col-md-2 mb-2">
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['download' => 1])) }}"
                        class="btn btn-success w-100">
                            Download
                        </a>
                    </div>
                    @endif
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
                                            <td>
                                                {{ $report->chapter->name ?? '—' }} <br>
                                                <small>
                                                    <strong>Zone:</strong> {{ $report->zone->name ?? 'N/A' }}<br>
                                                    <strong>Field:</strong> {{ $report->field->name ?? 'N/A' }}<br>
                                                </small>

                                            </td>
                                        @endif
                                        <td>
                                            {{ date('F', mktime(0, 0, 0, $report->month, 10)) }}, {{ $report->year }}
                                            <br>
                                            <span class="badge {{ $report->edit_mode ? 'bg-warning' : 'bg-success' }}">
                                                {{ $report->edit_mode ? 'Currently Editing' : 'Final Submission' }}
                                            </span>
                                        </td>


                                        <td class="text-start">
                                            @php
                                                $canEdit  = app(\App\Services\ReportService::class)->canEditReport($report, $user);

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

                                                    @if($data['value'] == 2)
                                                        <span class="badge bg-danger">Rejected</span>

                                                        <a href="#{{ $data['modal'] }}{{ $report->id }}"
                                                        data-toggle="modal"
                                                        class="text-danger"
                                                        title="View feedback">
                                                            <i class="bx bx-message-rounded-dots"></i>
                                                        </a>

                                                        @include($data['view'])

                                                    @elseif($data['value'] == 1)
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
                                            @if($isAdmin)
                                                <button
                                                    type="button"
                                                    class="btn btn-link text-primary mx-1"
                                                    data-toggle="modal"
                                                    data-target="#statusAdjustModal{{ $report->id }}"
                                                    title="Adjust Approval Status">
                                                    <i class="fa fa-cog"></i>
                                                </button>
                                            @endif
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
                                            @if(!$canEdit['allApproved'])
                                            <a
                                                href="{{ route($isAdmin ? 'stakeholderreports.nudge' : 'stakeholders.reports.nudge', $report->id) }}"
                                                class="text-indigo-600 mx-1"
                                                title="Send Nudge"
                                            >
                                                <i class="fa fa-bullhorn"></i>
                                            </a>
                                            @endif

                                            {{-- Delete --}}
                                            @if($isAdmin && !$canEdit['allApproved'])
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
                                    <div class="modal fade" id="statusAdjustModal{{ $report->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <form method="POST" action="{{ route('stakeholderreports.adjust.status', $report->id) }}">
                                                @csrf

                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Adjust Approval Status</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Approval Status</label>
                                                            <select name="approval_status" class="form-control" required>
                                                                <option value="">-- Select Status --</option>
                                                                @foreach($approval_statuses as $key => $label)
                                                                    @if(!in_array($key, ['zone_pending','field_pending', 'national_pending']))
                                                                    <option value="{{ $key }}" @selected(request('approval_status') == $key)>
                                                                        {{ $label }}
                                                                    </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Reason</label>
                                                            <textarea
                                                                name="rejection_reason"
                                                                class="form-control"
                                                                rows="3"
                                                                placeholder="Enter reason (optional)">
                                                            </textarea>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Submit</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>
                                        </div>
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
