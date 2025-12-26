@extends('layouts.stakeholderdashboard')
@section('title', 'Reports')
@section('active')
<li class="breadcrumb-item">Reports</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    {{-- <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">My Reports</h4>
                        @if(in_array(Auth::guard('stakeholder')->user()->role_id, chapterStakeholders()))
                        <a href="{{ route('reports.create') }}" class="btn btn-primary mt-1">Add this month's report <strong></strong></a>
                        @endif
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
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
                                        <h6>Status Legend: </h6>
                                            <i class="bx bxs-circle warning font-small-1 mr-50"></i>Pending Approval
                                            <i class="bx bxs-circle success font-small-1 mr-50"></i>Approved
                                            <i class="bx bxs-circle danger font-small-1 mr-50"></i>Rejected
                                        @foreach($reports as $report)
                                        <tr>
                                            <td>{{ $count ++}}</td>
                                            @if(Auth::guard('stakeholder')->user()->role == 'Field Pastor' || Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' || Auth::guard('stakeholder')->user()->role == 'Secretariat')
                                            <td>{{ $report->chapter->name }}</td>
                                            @endif
                                            <td>{{ date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year }}'s report</td>
                                            <td>
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
                                                <a class="actions" data-toggle="tooltip" title="View Report" href="{{ route('reports.show', $report->id) }}"> <i class="fa fa-eye actions"></i></a>
                                               
                                            
                                                @if((Auth::guard('stakeholder')->user()->role == 'Field Pastor' && $report->field_status == 0) || (Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' && $report->zone_status == 0) || (Auth::guard('stakeholder')->user()->role == 'President' && $report->zone_status == 0) || Auth::guard('stakeholder')->user()->role == 'Secretariat' )
                                             
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Edit Report" href="{{ route('reports.edit', $report->id) }}"> <i class="fa fa-pencil"></i></a>

                                                @endif
                                                @if(Auth::guard('stakeholder')->user()->role == 'Secretariat')
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Report" href="{{ route('reports.delete', $report->id) }}"> <i class="fa fa-trash actions"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                      
                                        @endforeach
                                    </tbody>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!--/ Zero configuration table -->         
</div>
@endsection