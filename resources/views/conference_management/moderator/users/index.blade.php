@extends('layouts.dashboard')
@section('title', 'My Participants')
@section('active')
<li class="breadcrumb-item">My Participants</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">My Participants</h4>
                        @if(auth()->user()->payment->slot >  auth()->user()->payment->slot_filled)
                        <a href="{{ route('users.create') }}" class="btn btn-primary mt-1">Add new participant <strong>({{ (auth()->user()->slot -  (auth()->user()->slot_filled )) }} slot(s) left)</strong></a>  
                        <a href="{{ route('moderator.users.import.index') }}" class="btn btn-primary mt-1">Import</a>
                        @endif
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Passport</th>
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
                                            <td>{{ $count ++}}</td>
                                            <td>
                                                <img class="mr-1" style="border-radius:50%" src="{{ asset($participant->user->passport  ? '/'.$participant->user->passport : '/frontend/passports/avatar.jpg') }}" alt="avatar" height="40" width="40">
                                            </td>
                                            <td>{{ $participant->conference_number }}</td>
                                            <td>@if($participant->registration_status == 'Complete')
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
                                            <a class="actions" data-toggle="tooltip" title="View/Edit Participant" href="{{ route('users.edit', $participant->id) }}"> <i class="bx bxs-edit actions"></i></
                                            </a>
                                            
                                            @if($participant->registration_status == 'Complete')
                                            <a class="actions" data-toggle="tooltip" title=" Print/download Conferene I.D" href="{{ route('user.card', $participant->id) }}"> <i class="fa fa-print actions"></i></
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" title=" Print/download Conferene I.D" href="{{ route('meal.ticket', $participant->id) }}"> <i class="icon-food actions"></i></
                                            </a>
                                            @endif
                                            @if(auth()->user()->id != $participant->id)
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Participant" href="{{ route('users.delete', $participant->id) }}"> <i class="fa fa-trash"></i></
                                            </a>
                                            @endif
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