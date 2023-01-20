@extends('layouts.conference')
@section('title', 'Conference Participants')
@section('active')
<li class="breadcrumb-item">Conference Participants</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Participants</h4>
                        <div class="">
                            <a href="{{ route('participants.create') }}" class="btn btn-primary mt-1">Add new</a>
                            <a href="{{ route('conferenceusers.import.index') }}" class="btn btn-primary mt-1">Import</a>
                            <a href="{{ route('conferenceusers.export') }}" class="btn btn-primary mt-1">Export</a>
                        </div>                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date</th>
                                            <th>Avatar</th>
                                            <th>Details</th>
                                            <th>Level</th>
                                            <th>Status</th>
                                            <th>Amount Paid</th>
                                            <th>Uploaded by</th>
                                            
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $participant)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $participant->created_at->format('Y-m-d : h-i-a') }}</td>
                                            <td>
                                                <img class="mr-1" style="border-radius:50%" src="{{ asset($participant->user->passport ? $participant->user->passport : 'frontend/passports/avatar.jpg') }}" alt="avatar" height="40" width="40">
                                            </td>
                                            <td><b>{{ $participant->user->family_id }}</b> <br>
                                                Name: {{ $participant->user->name }} <br>
                                                Email: {{ $participant->user->email }} <br>
                                                Phone: {{ $participant->user->phone }} <br>

                                            </td>
                                            <td>{{ $participant->level }}</td>
                                            <td>@if($participant->registration_status == 'Complete')
                                                <i class="bx bxs-circle success font-small-1 mr-50"></i> @else
                                                <i class="bx bxs-circle danger font-small-1 mr-50"></i>
                                                @endif
                                            </td>
                                            <td>&#8358;{{ $participant->amount_paid ?? 0 }}</td>
                                            <td>@if(isset($participant->moderator->name) && ($participant->level) == 'Participant'){{ $participant->moderator->name }}
                                                @else N/A @endif
                                            </td>
                                            
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="View/Edit User" href="{{ route('conference.participants.edit', $participant->id) }}"> <i class="bx bxs-edit actions"></i></
                                                </a>
                                                <a class="actions" onclick="return confirm('You are about to resend welcome email to this participant?');" data-toggle="tooltip" data-placement="top" title="Resend welcome mail"
                                                    href="{{ route('participants.resendmail', $participant->id) }}"><i
                                                        class="fa fa-envelope"></i>
                                                </a>
                                                <a class="actions" data-toggle="tooltip" data-placement="top" title="Switch To"
                                                    href="{{ route('switchuser', $participant->user->id) }}"><i
                                                        class="fa fa-unlock actions"></i>
                                                </a>
                                                 @if($participant->registration_status == 'Complete')
                                                <a class="actions" data-toggle="tooltip" title=" Print/download Conferene I.D" href="{{ route('participants.card', $participant->id) }}"> <i class="fa fa-print actions"></i></
                                                </a>
                                            
                                                @endif
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete User" href="{{ route('conferenceparticipants.delete', $participant->user->id) }}"> <i class="fa fa-recycle"></i></
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
</div>
@endsection