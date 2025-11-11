@extends('layouts.stakeholderdashboard')
@section('title', 'Reports')
@section('active')
<li class="breadcrumb-item">Reports</li>
@endsection

@section('content')
<div class="content-body">
    <section id="reports-dashboard">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="card-title">My Reports</h4>
                @if(in_array(Auth::guard('stakeholder')->user()->role, ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary']))
                <a href="{{ route('stakeholders.reports.create') }}" class="btn btn-primary">Add This Month's Report</a>
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
                    @if(in_array(Auth::guard('stakeholder')->user()->role, ['Zonal Pastor','Field Pastor','Portfolio','Secretariat']))
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
                    @if(in_array(Auth::guard('stakeholder')->user()->role, ['Field Pastor','Portfolio','Secretariat']))
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
                    @if(in_array(Auth::guard('stakeholder')->user()->role, ['Portfolio','Secretariat']))
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
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            @if(Auth::guard('stakeholder')->user()->role == 'Field Pastor' || Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' || Auth::guard('stakeholder')->user()->role == 'Secretariat')
                                            <th>Chapter</th>
                                            @endif
                                            <th>Month/Year</th>
                                            <th>Approval Status</th>
                                            <th>Academic Session</th>
                                            <th>Created on</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $report)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            @if(Auth::guard('stakeholder')->user()->role == 'Field Pastor' || Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' || Auth::guard('stakeholder')->user()->role == 'Secretariat')
                                            <td>{{ $report->chapter->name }}</td>
                                            @endif
                                            <td>{{ date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year }}'s report</td>
                                            <td>
                                                {{-- Field Status --}}
                                                @if($report->field_rejected_at)
                                                    <i class="bx bxs-circle danger font-small-1 mr-50"></i>
                                                    <small>Field <a href="#fieldRejection{{ $report->id }}" data-toggle="modal" title="View feedback"><span class="info">&#9432;</span></a></small>
                                                    @include('stakeholder.modals.field_rejection_comment')
                                                @elseif($report->field_approved_at)
                                                    <i class="bx bxs-circle success font-small-1 mr-50"></i><small>Field</small><br>
                                                @else
                                                    <i class="bx bxs-circle warning font-small-1 mr-50"></i><small>Field</small><br>
                                                @endif

                                                {{-- Zone Status --}}
                                                @if($report->zone_rejected_at)
                                                    <i class="bx bxs-circle danger font-small-1 mr-50"></i>
                                                    <small>Zone <a href="#zoneRejection{{ $report->id }}" data-toggle="modal" title="View feedback"><span class="info">&#9432;</span></a></small>
                                                    @include('stakeholder.modals.zone_rejection_comment')
                                                @elseif($report->zone_approved_at)
                                                    <i class="bx bxs-circle success font-small-1 mr-50"></i><small>Zone</small><br>
                                                @else
                                                    <i class="bx bxs-circle warning font-small-1 mr-50"></i><small>Zone</small><br>
                                                @endif

                                                {{-- National Status --}}
                                                @if($report->national_rejected_at)
                                                    <i class="bx bxs-circle danger font-small-1 mr-50"></i>
                                                    <small>National <a href="#nationalRejection{{ $report->id }}" data-toggle="modal" title="View feedback"><span class="info">&#9432;</span></a></small>
                                                    @include('stakeholder.modals.national_rejection_comment')
                                                @elseif($report->national_approved_at)
                                                    <i class="bx bxs-circle success font-small-1 mr-50"></i><small>National</small><br>
                                                @else
                                                    <i class="bx bxs-circle warning font-small-1 mr-50"></i><small>National</small><br>
                                                @endif
                                            
                                                @if(!is_null($report->zone_reject_comment))
                                                <i class="bx bxs-circle danger font-small-1 mr-50"></i><small>Zone <a data-target="#zoneRejection{{ $report->id }}" data-toggle="modal" title="Veiw feedback"  href="#zoneRejection{{ $report->id }}"><span class="info">&#9432;</span></small></a>
                                                @include('stakeholder.modals.zone_rejection_comment')
                                                @endif
                                                @if(is_null($report->zone_reject_comment))
                                                <i class="{{ ($report->zone_status == 0) ? 'bx bxs-circle warning font-small-1 mr-50' : 'bx bxs-circle success font-small-1 mr-50' }}"></i><small>Zone</small><br>
                                                @endif

                                                @if(!is_null($report->field_reject_comment))
                                                <i class="bx bxs-circle danger font-small-1 mr-50"></i><small>Field <a data-target="#fieldRejection{{ $report->id }}" data-toggle="modal" title="Veiw feedback"  href="#fieldRejection{{ $report->id }}"><span  class="info">&#9432;</span></small></a>
                                                @include('stakeholder.modals.field_rejection_comment')
                                                @endif
                                                @if(is_null($report->field_reject_comment))
                                                <i class="{{ ($report->field_status == 0) ? 'bx bxs-circle warning font-small-1 mr-50' : 'bx bxs-circle success font-small-1 mr-50' }}"></i><small>Field</small><br>
                                                @endif

                                                @if(!is_null($report->status_complete_reject_comment))
                                               <i class="bx bxs-circle danger font-small-1 mr-50"></i><small>National <a data-target="#secretariatRejection{{ $report->id }}" data-toggle="modal" title="Veiw feedback"  href="#secretariatRejection{{ $report->id }}"><span  class="info">&#9432;</span></small></a>
                                                @include('stakeholder.modals.secretariat_rejection_comment')
                                                @endif
                                                @if(is_null($report->status_complete_reject_comment))
                                                <i class="{{ ($report->field_status == 0) ? 'bx bxs-circle warning font-small-1 mr-50' : 'bx bxs-circle success font-small-1 mr-50' }}"></i><small>National</small><br>
                                                @endif
                                            </td>
                                            <td>{{ $report->session }}</td>
                                            
                                            <td>{{ $report->created_at->format('d-m-Y:h-m-s') }}</td>
                                            
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="View Report" href="{{ route('stakeholders.reports.show', $report->id) }}"> <i class="fa fa-eye actions"></i></a>
                                               
                                            
                                                @if((Auth::guard('stakeholder')->user()->role == 'Field Pastor' && $report->field_status == 0) || (Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' && $report->zone_status == 0) || (Auth::guard('stakeholder')->user()->role == 'President' && $report->zone_status == 0) || Auth::guard('stakeholder')->user()->role == 'Secretariat' )
                                             
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Edit Report" href="{{ route('stakeholders.reports.edit', $report->id) }}"> <i class="fa fa-pencil"></i></a>

                                                @endif
                                                @if(Auth::guard('stakeholder')->user()->role == 'Secretariat')
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Report" href="{{ route('stakeholders.reports.delete', $report->id) }}"> <i class="fa fa-trash actions"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                      
                                        @endforeach
                                    </tbody>
                                    
                                </table>
                            </div>
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
