@extends('layouts.dashboard')
@section('title', 'Dashboard')

@section('content')
<div class="content-body">
    @include('includes.alerts')
    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics">
        <div class="row">
            <!-- Website Analytics Starts-->
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Moderator Analytics</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body pb-1">
                            <div class="d-flex justify-content-around align-items-center flex-wrap">
                                <div class="user-analytics">
                                    <i class="bx bx-user mr-25 align-middle"></i>
                                    <span class="align-middle text-muted">{{ auth()->user()->payment->slot  }} Slot(s) paid for</span>
                                    <div class="d-flex">
                                        <div id="radial-success-chart"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                                <div class="sessions-analytics">
                                    <i class="bx bx-trending-down align-middle mr-25" style="color:red"></i>
                                    <span class="align-middle text-muted">{{ auth()->user()->payment->slot_filled }} Slot(s) used</span>
                                    <div class="d-flex">
                                        <div id="radial-warning-chart-down"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                                <div class="sessions-analytics">
                                    <i class="bx bx-trending-up align-middle mr-25" style="color:green"></i>
                                    <span class="align-middle text-muted">{{ auth()->user()->payment->slot - auth()->user()->payment->slot_filled }} Slot(s) remaining</span>
                                    <div class="d-flex">
                                        <div id="radial-warning-chart"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                                <div class="bounce-rate-analytics">
                                    <i class="bx bx-pie-user align-middle mr-25"></i>
                                    <span class="align-middle text-muted">{{ $pending_registration }} Pending Registration</span>
                                    <div class="d-flex">
                                        <div id="radial-danger-chart"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                                <div class="bounce-rate-analytics">
                                    <i class="fa fa-registered align-middle mr-25"></i>
                                    <span class="align-middle text-muted">{{ $completed_registration }} Complete Registration</span>
                                    <div class="d-flex">
                                        <div id="radial-danger-chart"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                            </div>
                            <div id="analytics-bar-chart"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
<section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">My Participants</h4>
                        @if(auth()->user()->payment->slot >  auth()->user()->payment->slot_filled)
                        <a href="{{ route('users.create') }}" class="btn btn-primary mt-1">Add new participant <strong>({{ (auth()->user()->slot -  (auth()->user()->slot_filled )) }} slot(s) left)</strong></a>  
                        <a href="{{ route('moderator.conference.import.index') }}" class="btn btn-primary mt-1">Import</a>
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
                                        @if(isset($myParticipantsAll))
                                        @foreach($myParticipantsAll as $participant)
                                        <tr>
                                            <td>{{ $count ++}}</td>
                                            <td>
                                                <img class="mr-1" style="border-radius:50%" src="{{ asset($participant->passport ? '/'.$participant->passport : '/frontend/passports/avatar.jpg') }}" alt="avatar" height="40" width="40">
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
                                            <a class="actions" data-toggle="tooltip" title=" Print/download Conferene I.D" href="{{ route('participants.card', $participant->id) }}"> <i class="fa fa-print actions"></i></
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
                                        @endif
                                    </tbody>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection