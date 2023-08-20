@extends('layouts.conference')
@section('title', 'Conference Participants')
@section('active')
<li class="breadcrumb-item">{{ $edition->conference_theme }} Participants ({{ $type }})</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All {{ $type }}s</h4>
                        <div class="">
                            <a href="{{ route('conference.participants.create', ['edition'=>$edition->id, 'type'=>$type]) }}" class="btn btn-primary mt-1">Add new</a>
                              <a class="btn btn-info mt-1" href="{{ route('conferenceusers.import.index', ['edition'=>$edition->id,'type'=>$type]) }}">Import {{ $type }}</a>

                            {{-- <a href="{{ route('conferenceusers.export',  ['edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Export</a> --}}
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
                                            <th>Amount Paid</th>
                                            <th>Uploaded by</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                        @if(isset($participants) && $participants->count() > 0)
                                        @foreach($participants as $participant)
                                       
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $participant->created_at->format('Y-m-d : h-i-a') }} <br>
                                               <strong>Status: </strong> @if($participant->registration_status == 'Complete')
                                                <i class="bx bxs-circle success font-small-1 mr-50"></i> @else
                                                <i class="bx bxs-circle danger font-small-1 mr-50"></i>
                                                @endif <br>
                                                <strong>Campus:</strong>{{ isset($participant->user->campus) ? $participant->user->campus->name : 'N/A'}} <br>
                                                <strong>Level:</strong> {{ $participant->level }}
                                                @if(isset($participant->hostel_id)) <br>
                                                <strong>Hostel:</strong> {{ $participant->hostel->name }}
                                                @endif
                                                @if(isset($participant->food_id)) <br>
                                                <strong>Foodstand:</strong> {{ $participant->food->name }}
                                                @endif
                                                @if($type == 'Moderator') <br>
                                                <span>
                                                    <strong style="color:blue">Slots:</strong> {{ $participant->slot }} <br>
                                                    <strong style="color:blue">Slots Available:</strong> {{ $participant->slot - $participant->slot_filled }} 

                                                </span>
                                                @endif <br>
                                                <span style="color:blue">Payment Location: <strong>{{ $participant->location ?? 'Online' }}</strong></span> <br>
                                                <span id="single-{{$participant->transid}}"><span>
                                            </td>
                                            <td>
                                                <img class="mr-1" style="border-radius:50%" src="{{ asset($participant->user->passport ? $participant->user->passport : 'frontend/passports/avatar.jpg') }}" alt="avatar" height="40" width="40">
                                            </td>
                                            <td>
                                                <small>
                                                    <b>{{ $participant->user->family_id }}</b> <br>
                                                    <span style="color:red">
                                                        <strong>Trans ID:</strong> {{ $participant->transid }} <br>
                                                    </span>
                                                    <strong>Name:</strong> {{ $participant->user->name }} <br>
                                                    <strong>Email:</strong> {{ $participant->user->email }} <br>
                                                    <strong>Phone:</strong> {{ $participant->user->phone }} <br>
                                                </small>
                                            </td>
                                            <td>&#8358;{{ number_format($participant->amount_paid) ?? 0 }}</td>
                                            <td></strong>@if(isset($participant->moderator->name) && ($participant->level) == 'Participant'){{ $participant->moderator->name }}
                                                @else N/A @endif</td>
                                            
                                           
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="View/Edit User" href="{{ route('conference.participants.edit', ['edition'=>$edition->id,'id'=>$participant->id]) }}"> <i class="bx bxs-edit actions"></i></
                                                </a>
                                                <a class="actions" onclick="return confirm('You are about to resend welcome email to this participant?');" data-toggle="tooltip" data-placement="top" title="Resend welcome mail"
                                                    href="{{ route('participants.resendmail', ['edition'=>$edition->id,'id'=>$participant->id]) }}"><i
                                                        class="fa fa-envelope"></i>
                                                </a>
                                                <a class="actions" data-toggle="tooltip" data-placement="top" title="Switch To"
                                                    href="{{ route('switchuser', ['edition'=>$edition->id,'id'=>$participant->user->id]) }}"><i
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
</div>
@endsection