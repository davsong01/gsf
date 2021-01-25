@extends('layouts.dashboard')
@section('title', 'Food Stands')
@section('active')
<li class="breadcrumb-item">Food Stands</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Foodstands</h4>
                        <a href="{{ route('foods.create') }}" class="btn btn-primary mt-1">Add new Food Stand</a>
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
                                            <th>Level</th>
                                            <th>Capacity</th>
                                            <th>Allocation</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($foods as $food)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $food->name }}</td>
                                            <td>{{ $food->level }}</td>
                                            <td>{{ $food->capacity }}</td>
                                            <td>{{ $food->allocation }}</td>
                                            
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update food details" href="{{ route('foods.edit', $food->id) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete food" href="{{ route('foods.delete', $food->id) }}"> <i class="fa fa-trash"></i></
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