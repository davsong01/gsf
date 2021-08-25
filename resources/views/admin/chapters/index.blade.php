@extends('layouts.dashboard')
@section('title', 'chapter')
@section('active')
<li class="breadcrumb-item">chapter</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All chapters</h4>
                        <a href="{{ route('chapters.create') }}" class="btn btn-primary mt-1">Add new chapter</a>
                        <a href="{{ route('chapters.export') }}" class="btn btn-primary mt-1">Export</a>
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
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Token(Special)</th>
                                            <th>Registered Participants</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($chapters as $chapter)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $chapter->name }}</td>
                                            <td>{{ $chapter->phone }}</td>
                                            <td>{{ $chapter->email }}</td>
                                            <td>{{ $chapter->token }}</td>
                                            <td>{{ $chapter->users_count }}</td>
                                           
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update chapter details" href="{{ route('chapters.edit', $chapter->id) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                            <a class="actions" data-toggle="tooltip" title="Generate new token" href="{{ route('chapter.newtoken', $chapter->id) }}"> <i class="fa fa-refresh actions"></i>
                                            </a>
                                            
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete chapter" href="{{ route('chapters.delete', $chapter->id) }}"> <i class="fa fa-trash"></i></
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