@extends('layouts.dashboard')
@section('title', 'Hostel')
@section('active')
<li class="breadcrumb-item">Hostel</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Hostels</h4>
                        <a href="{{ route('hostels.create') }}" class="btn btn-primary mt-1">Add new Hostel</a>
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
                                            <th>Type</th>
                                            <th>Level</th>
                                            <th>Capacity</th>
                                            <th>Allocation</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hostels as $hostel)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $hostel->name }}</td>
                                            <td>{{ $hostel->type }}</td>
                                            <td>{{ $hostel->level }}</td>
                                            <td>{{ $hostel->capacity }}</td>
                                            <td>{{ $hostel->allocation }}</td>
                                            
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update hostel details" href="{{ route('hostels.edit', $hostel->id) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Hostel" href="{{ route('hostels.delete', $hostel->id) }}"> <i class="fa fa-trash"></i></
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