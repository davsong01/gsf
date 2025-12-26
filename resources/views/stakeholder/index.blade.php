@extends('layouts.stakeholderdashboard')
@section('title', 'Reports')
@section('active')
<li class="breadcrumb-item">Reports</li>
@endsection

@section('content')
<div class="content-body">
    <section id="reports-dashboard">
        <div class="row mb-3">
            {{-- {{dd(Auth::guard('stakeholder')->user())}} --}}
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="card-title">All Reports</h4>
                @if(in_array(Auth::guard('stakeholder')->user()->role_id, chapterStakeholders()))
                @if(canAddThisMonthReport(Auth::guard('stakeholder')->user()))
                <a href="{{ route('stakeholders.reports.create') }}" class="btn btn-primary">Add This Month's Report</a>
                @endif
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="" class="row g-2 align-items-end">
                    
                    <div class="col-md-2">
                        <label for="from_date" class="form-label">From</label>
                        <input type="date" name="from_date" id="from_date" class="form-control"
                            value="{{ request('from_date') ?? now()->startOfMonth()->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="to_date" class="form-label">To</label>
                        <input type="date" name="to_date" id="to_date" class="form-control"
                            value="{{ request('to_date') ?? now()->format('Y-m-d') }}">
                    </div>
                    
                    <!-- Chapter Filter (for non-Field Pastor) -->
                    @if(in_array(Auth::guard('stakeholder')->user()->role_id, array_merge(fieldStakeholders(), zoneStakeholders(), secretariatStakeholders(), ncpStakeholders())))
                    <div class="col-md-3">
                        <label for="chapter_filter" class="form-label">Chapter</label>
                        <select name="chapter_filter" id="chapter_filter" class="form-control">
                            <option value="">-- All Chapters --</option>
                            @foreach($chapters as $chapter)
                                <option value="{{ $chapter->id }}" 
                                    {{ request('chapter_filter') == $chapter->id ? 'selected' : '' }}>
                                    {{ $chapter->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if(in_array(Auth::guard('stakeholder')->user()->role_id, array_merge(fieldStakeholders(), secretariatStakeholders(), ncpStakeholders())))
                    <div class="col-md-3">
                        <label for="zone_filter" class="form-label">Zone</label>
                        <select name="zone_filter" id="zone_filter" class="form-control">
                            <option value="">-- All Zones --</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" 
                                    {{ request('zone_filter') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if(in_array(Auth::guard('stakeholder')->user()->role_id, array_merge(secretariatStakeholders(), ncpStakeholders())))
                    <div class="col-md-3">
                        <label for="field_filter" class="form-label">Field</label>
                        <select name="field_filter" id="field_filter" class="form-control">
                            <option value="">-- All Fields --</option>
                            @foreach($fields as $field)
                                <option value="{{ $field->id }}" 
                                    {{ request('field_filter') == $field->id ? 'selected' : '' }}>
                                    {{ $field->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif


                    <!-- Status Filter -->
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">Approval Status</label>
                        <select name="status_filter" id="status_filter" class="form-control">
                            <option value="">-- All Status --</option>
                            <option value="zone_pending" {{ request('status_filter') == 'zone_pending' ? 'selected' : '' }}>Zone Pending</option>
                            <option value="zone_approved" {{ request('status_filter') == 'zone_approved' ? 'selected' : '' }}>Zone Approved</option>
                            <option value="zone_rejected" {{ request('status_filter') == 'zone_rejected' ? 'selected' : '' }}>Zone Rejected</option>
                            <option value="field_pending" {{ request('status_filter') == 'field_pending' ? 'selected' : '' }}>Field Pending</option>
                            <option value="field_approved" {{ request('status_filter') == 'field_approved' ? 'selected' : '' }}>Field Approved</option>
                            <option value="field_rejected" {{ request('status_filter') == 'field_rejected' ? 'selected' : '' }}>Field Rejected</option>
                            <option value="national_pending" {{ request('status_filter') == 'national_pending' ? 'selected' : '' }}>National Pending</option>
                            <option value="national_approved" {{ request('status_filter') == 'national_approved' ? 'selected' : '' }}>National Approved</option>
                            <option value="national_rejected" {{ request('status_filter') == 'national_rejected' ? 'selected' : '' }}>National Rejected</option>
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="col-md-2">
                        <button class="btn btn-secondary w-100" type="submit">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('includes.alerts')
                    <div class="card-body table-responsive">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-center">
                                <thead >
                                    <tr>
                                        <th>S/N</th>
                                        @if(in_array(Auth::guard('stakeholder')->user()->role_id, array_merge(fieldStakeholders(), zoneStakeholders(), secretariatStakeholders(), ncpStakeholders())))
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

                                        @if(in_array(Auth::guard('stakeholder')->user()->role_id, ['Field Pastor','Zonal Pastor','Secretariat']))
                                            <td>{{ $report->chapter->name ?? '—' }}</td>
                                        @endif

                                        <td>{{ date('F', mktime(0, 0, 0, $report->month, 10)) }}, {{ $report->year }}</td>

                                        <td class="text-start">
                                            {{-- FIELD --}}
                                            @php
                                                $fieldStatus = $report->field_status;
                                                $zoneStatus = $report->zone_status;
                                                $natStatus  = $report->status_complete;
                                            @endphp
                                            {{-- Zone --}}
                                            <div class="d-flex align-items-center" style="margin-bottom:5px">
                                                <small class="ms-1 text-muted">Zone &nbsp</small>
                                                @if($report->zone_rejected_at)
                                                    <span class="badge bg-danger">Rejected</span>
                                                    <a href="#zoneRejection{{ $report->id }}" data-toggle="modal" title="View feedback" class="ms-1 text-danger">
                                                        <i class="bx bx-message-rounded-dots"></i>
                                                    </a>
                                                    @include('stakeholder.modals.zone_rejection_comment')
                                                @elseif($report->zone_approved_at || $zoneStatus == 1)
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </div>
                                             {{-- Field --}}
                                            <div class="d-flex align-items-center" style="margin-bottom:5px">
                                                <small class="ms-1 text-muted">Field &nbsp</small>
                                                @if($report->field_rejected_at)
                                                    <span class="badge bg-danger">Rejected</span>
                                                    <a href="#fieldRejection{{ $report->id }}" data-toggle="modal" title="View feedback" class="ms-1 text-danger">
                                                        <i class="bx bx-message-rounded-dots"></i>
                                                    </a>
                                                    @include('stakeholder.modals.field_rejection_comment')
                                                @elseif($report->field_approved_at || $fieldStatus == 1)
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </div>

                                            {{-- National --}}
                                            <div class="d-flex align-items-center">
                                                <small class="ms-1 text-muted">National &nbsp</small>
                                                @if($report->national_rejected_at || $report->status_complete_reject_comment)
                                                    <span class="badge bg-danger">Rejected</span>
                                                    <a href="#secretariatRejection{{ $report->id }}" data-toggle="modal" title="View feedback" class="ms-1 text-danger">
                                                        <i class="bx bx-message-rounded-dots"></i>
                                                    </a>
                                                    @include('stakeholder.modals.secretariat_rejection_comment')
                                                @elseif($report->national_approved_at || $natStatus == 1)
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td>{{ $report->session }}</td>
                                        <td>{{ $report->created_at->format('d M Y, h:i A') }}</td>

                                        <td class="text-center">
                                            {{-- View --}}
                                            <a href="{{ route('stakeholders.reports.show', $report->id) }}" class="text-primary mx-1" title="View Report">
                                                <i class="fa fa-eye"></i>
                                            </a>

                                            {{-- Edit --}}
                                            @if(
                                                (Auth::guard('stakeholder')->user()->role == 'Field Pastor' && $fieldStatus == 0) ||
                                                (Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' && $zoneStatus == 0) ||
                                                (in_array(Auth::guard('stakeholder')->user()->role_id, chapterStakeholders()) && $zoneStatus == 0) ||
                                                Auth::guard('stakeholder')->user()->role == 'Secretariat'
                                            )
                                                <a href="{{ route('stakeholders.reports.edit', $report->id) }}" class="text-warning mx-1" title="Edit Report" onclick="return confirm('Are you sure you want to edit this report?');">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endif

                                            {{-- Print --}}
                                            <a href="{{ route('stakeholders.reports.print', $report->id) }}" target="_blank" class="text-success mx-1" title="Print Report">
                                                <i class="fa fa-print"></i>
                                            </a>

                                            {{-- Nudge --}}
                                            <a href="{{ route('stakeholders.reports.nudge', $report->id) }}" class="text-indigo-600 mx-1" title="Send Nudge">
                                                <i class="fa fa-bullhorn"></i>
                                            </a>

                                            {{-- Delete --}}
                                            @if(Auth::guard('stakeholder')->user()->role == 'Secretariat')
                                                <a href="{{ route('stakeholders.reports.delete', $report->id) }}" class="text-danger mx-1" title="Delete Report" onclick="return confirm('Are you sure you want to delete this report?');">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6l-2 14H7L5 6"/>
                                                        <path d="M10 11v6"/>
                                                        <path d="M14 11v6"/>
                                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                    </svg>
                                                </a>
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
@endsection
