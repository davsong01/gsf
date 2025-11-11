@extends('layouts.dashboard')
@section('title', 'Stakeholder')
@section('active')
<li class="breadcrumb-item">Stakeholder</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All stakeholders</h4>
                        <a href="{{ route('stakeholderpersonnel.create') }}" class="btn btn-primary mt-1">Add new stakeholder</a>                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Assignment</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stakeholders as $stakeholder)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>
                                                {{ $stakeholder->name }} <br>
                                                <small><strong>Status: </strong>{{ ucfirst($stakeholder->status) }}</small>
                                            </td>
                                            <td>{{ $stakeholder->phone }}</td>
                                            <td>{{ $stakeholder->email }}</td>
                                            <td>
                                                @if(in_array($stakeholder->role, ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary']) && !is_null($stakeholder->chapter_id))<span>{{$stakeholder->role}}, </span><a target="_blank" style="color:blue" href="{{ route('chapters.edit', $stakeholder->chapter->id) }}">{{ $stakeholder->chapter->name ?? 'N/A' }}</a>@endif
                                                @if($stakeholder->role == 'Zonal Pastor' && !is_null($stakeholder->zone_id))<span style="color:blue">Zonal Pastor, </span>{{ $stakeholder->zone->name ?? 'N/A' }}@endif
                                                @if($stakeholder->role == 'Field Pastor' && !is_null($stakeholder->field_id)) <span style="color:blue">Field Pastor, </span>{{ $stakeholder->field->name ?? 'N/A' }}@endif
                                                @if($stakeholder->role == 'Secretariat')Official @endif
                                                @if($stakeholder->role == 'Financial Secretary')Official @endif
                                                @if($stakeholder->role == 'Portfolio'){{ $stakeholder->portfolio }} @endif
                                            </td>
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update stakeholder details" href="{{ route('stakeholderpersonnel.edit', $stakeholder->id) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                            
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete stakeholder" href="{{ route('stakeholderpersonnel.delete', $stakeholder->id) }}"> <i class="fa fa-trash actions"></i></a>
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
    </section>
    <!--/ Zero configuration table -->         
</div>
@endsection