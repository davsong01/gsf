@extends('layouts.dashboard')
@section('title', 'Participants')
@section('active')
<li class="breadcrumb-item">Conference Participants</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Participants</h4>
                        <a href="{{ route('users.create') }}" class="btn btn-primary mt-1">Add new</a>
                        @include('includes.alerts')
                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Conference ID</th>
                                            <th>Status</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Amount Paid</th>
                                            <th>Uploaded by</th>
                                            
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $participant)
                                        <tr>
                                            <td>{{ $count }}</td>
                                            <td>{{ $participant->conference_number }}</td>
                                            <td>@if($participant->status == 'Complete'))
                                                <i class="bx bxs-circle success font-small-1 mr-50"></i><small>Complete</small> @else
                                                <i class="bx bxs-circle danger font-small-1 mr-50"></i><small>Pending</small>
                                                @endif
                                            </td>
                                            
                                            <td>{{ $participant->name }}</td>
                                            <td>{{ $participant->email }}</td>
                                            <td>{{ $participant->phone }}</td>
                                            <td>&#8358;{{ $participant->amount_paid }}</td>
                                            <td>@if(isset($participant->moderator->name) && ($participant->level) == 'Participant'){{ $participant->moderator->name }}
                                                @else N/A @endif
                                            </td>
                                            
                                                
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Edit User" href="{{ route('users.edit', $participant->id) }}"> <i class="bx bxs-edit actions"></i></
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" data-placement="top" title="Switch To"
                                                href="{{ route('switchuser', $participant->id) }}"><i
                                                    class="fa fa-unlock actions"></i>
                                            </a>
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete User" href="{{ route('users.delete', $participant->id) }}"> <i class="fa fa-trash"></i></
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