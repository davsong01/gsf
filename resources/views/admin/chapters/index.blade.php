@extends('layouts.dashboard')
@section('title', 'Chapters')
@section('active')
<li class="breadcrumb-item">Chapters</li>
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
                                            <th>President</th>
                                            <th>Details</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Statistics</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($chapters as $chapter)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $chapter->name }} <br>
                                               <small style="color:red">Token: {{ $chapter->token }}</small> <br>
                                                <small><a target="_blank" href="{{ route('campus.single', $chapter->id) }}"><i class="fa fa-eye"></i> View on website</a></small>
                                            </td>
                                            <td>{{ $chapter->stakeholder->name ?? 'N/A' }}</td>
                                            <td>
                                                <small>
                                                    Field: {{ $chapter->field->name ?? 'N/A' }} <br>
                                                    Zone: {{ $chapter->zone->name ?? 'N/A' }} <br>
                                                </small>
                                            </td>
                                            <td>{{ $chapter->email }}</td>
                                            <td>{{ $chapter->phone }}</td>
                                            <td>
                                                <small>
                                                    Students: {{ $chapter->users->where('status', 0)->count() }} <br>
                                                    Alumni: {{ $chapter->users->where('status', 1)->count() }} 
                                                    {{-- @if($setting) --}}
                                                    <br>
                                                    Conference Participants: {{ $chapter->registerdParticipants->count() }}
                                                    {{-- @endif --}}
                                                </small>
                                                
                                            </td>
                                           
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update chapter details" href="{{ route('chapters.edit', $chapter->id) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                            <a class="actions" data-toggle="tooltip" title="Generate new token" href="{{ route('chapter.newtoken', $chapter->id) }}"> <i class="fa fa-refresh actions"></i>
                                            </a>
                                            
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete chapter" href="{{ route('chapters.delete', $chapter->id) }}"> <i class="fa fa-trash actions"></i></
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