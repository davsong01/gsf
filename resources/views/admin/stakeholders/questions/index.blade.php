@extends('layouts.dashboard')
@section('title', 'Report Questions')
@section('active')
<li class="breadcrumb-item">Report Items</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Report Items</h4>
                        <a href="{{ route('stakeholder.questions.create') }}" class="btn btn-primary mt-1">Add New Item</a>                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Section</th>
                                            <th>Order</th>
                                            <th>Label</th>
                                            <th>Type</th>
                                            <th>Required</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($questions as $question)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $question->section }}</td>
                                            <td>{{ $question->order }}</td>
                                            <td>{{ $question->label }}</td>
                                            <td>{{ ucfirst($question->type) }}</td>
                                            <td>{{ $question->is_required ? 'Yes' : 'No' }}</td>
                                            
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="Edit Question" href="{{ route('stakeholder.questions.edit', $question->id) }}"> 
                                                    <i class="bx bxs-edit actions"></i>
                                                </a>
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you sure you want to delete this question?');" title="Delete Question" href="{{ route('stakeholder.questions.destroy', $question->id) }}"> 
                                                    <i class="fa fa-trash actions"></i>
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
