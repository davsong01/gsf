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
                        <a href="{{ route('staff.create') }}" class="btn btn-primary mt-1">Add new stakeholder</a>
                        @include('includes.alerts')
                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Zone</th>
                                            <th>Field</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stakeholders as $stakeholder)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $stakeholder->name }}</td>
                                            <td>{{ $stakeholder->email }}</td>
                                            <td>{{ isset($stakeholder->zone) ? $stakeholder->zone->name : '' }}</td>
                                            <td>{{ isset($stakeholder->field) ? $stakeholder->field->name : '' }}</td> 
                                            <td>{{ $stakeholder->role ?: '' }}</td>
                                          
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update stakeholder details" href="{{ route('staff.edit', $stakeholder->id) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete stakeholder" href="{{ route('staff.delete', $stakeholder->id) }}"> <i class="fa fa-trash"></i></
                                            </a>
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