@extends('layouts.dashboard')
@section('title', 'Materials')
@section('active')
<li class="breadcrumb-item">Conference Materials</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Conference materials</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materials as $material)
                                        <tr>
                                            <td>{{ $count }}</td>
                                            
                                            <td>{{ $material->name }}</td>                                           
                                            <td>
                                                 <a class="actions" data-toggle="tooltip" title="Download Material" href="{{ route('materials.show', $material->id) }}"> <i class="bx bxs-download actions"></i>Download
                                                </a>
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
    </section>
    <!--/ Zero configuration table -->         
</div>
@endsection