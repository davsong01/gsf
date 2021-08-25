@extends('layouts.dashboard')
@section('title', 'Field')
@section('active')
<li class="breadcrumb-item">field</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All fields</h4>
                        <a href="{{ route('fields.create') }}" class="btn btn-primary mt-1">Add new field</a>
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
                                            <th>Pastor</th>
                                            <th>Zone Count</th>
                                            <th>Chapter Count</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($fields as $field)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $field->name }}</td>
                                            <td>{{ $field->stakeholder->name }}</td>
                                            <td>{{ $field->zones->count() }}</td>
                                            <td>{{ $field->chapters->count() }}</td>
                                          
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update field details" href="{{ route('fields.edit', $field->id) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete field" href="{{ route('fields.delete', $field->id) }}"> <i class="fa fa-trash"></i></
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