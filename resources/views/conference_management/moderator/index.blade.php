@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('item')
<li class="breadcrumb-item"><a href="{{ route('conferencemanagement.index',['edition'=>$edition->id ?? $setting->id]) }}">My Conferences</a></li>
@endsection
@section('content')
<div class="content-body">
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
                                    <span class="align-middle text-muted">{{ $thispayment->slot  }} Slot(s) paid for</span>
                                    <div class="d-flex">
                                        <div id="radial-success-chart"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>

                                <div class="sessions-analytics">
                                    <i class="bx bx-trending-down align-middle mr-25" style="color:red"></i>
                                    <span class="align-middle text-muted">{{ $thispayment->slot_filled }} Slot(s) used</span>
                                    <div class="d-flex">
                                        <div id="radial-warning-chart-down"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                                <div class="sessions-analytics">
                                    <i class="bx bx-trending-up align-middle mr-25" style="color:green"></i>
                                    <span class="align-middle text-muted">{{ $thispayment->slot - $thispayment->slot_filled }} Slot(s) remaining</span>
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
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h4 class="card-title mb-0">My Participants</h4>

    @php
        $canDownload = $thispayment->registration_status === 'Complete'
            && !empty($thispayment->hostel)
            && !empty($thispayment->food);
    @endphp

    <div class="d-flex align-items-center flex-wrap gap-2">

        @if($thispayment->slot > $thispayment->slot_filled)
            <a href="{{ route('conference.participants.create',['edition'=>$edition->id]) }}"
               class="btn btn-primary">
                Add new participant
                <strong>({{ $thispayment->slot - $thispayment->slot_filled }} slot(s) left)</strong>
            </a>

            <a class="btn btn-info"
               href="{{ route('conferenceusers.import.index', ['edition'=>$edition->id,'type'=>'Participant']) }}">
                Import Participants
            </a>
        @endif

        @if($thispayment->registration_user_type === 'moderator')
            @if($canDownload)
                <a href="{{ route('participants.card', ['id' => $thispayment->id, 'edition' => $edition->id]) }}"
                   class="btn btn-dark btn-sm glow">
                    <i class="fa fa-print"></i> Download Badge
                </a>

                @if(!empty($edition->material))
                    <a href="{{ route('materials.index', ['edition'=>$edition->id, 'payment_id'=>$thispayment->id]) }}"
                       class="btn btn-warning btn-sm glow">
                        <i class="fa fa-print"></i> Download Materials
                    </a>
                @endif
            @else
                <a href="#"
                   onclick="return false;"
                   data-toggle="tooltip"
                   title="You must complete registration to use this button"
                   class="btn btn-primary glow disabled">
                    <i class="fa fa-print"></i> Badge
                </a>
            @endif
        @endif

    </div>
</div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table class="table zero-configuration">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Passport</th>
                                        <th>Personal Details</th>
                                        <th>Allocation Details</th>
                                        <th>Status</th>

                                        <th>Amount Paid</th>
                                        <th>Uploaded by</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @if(isset($myParticipantsAll) && $myParticipantsAll->count() > 0)
                                    @foreach($myParticipantsAll as $participant)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <img class="mr-1" style="border-radius:50%" src="{{ asset($participant->user->passport ? '/'.$participant->user->passport : '/images/passports/avatar.jpg') }}" alt="avatar" height="40" width="40">
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color:{{$participant->registration_user_type == 'moderator' ? 'teal' : '#f700ff'}}">{{ ucfirst($participant->registration_user_type) }}</span> <br>
                                            <strong>Login ID:</strong> {{ $participant->user->family_id }} <br>
                                            <strong>Name:</strong> {{ $participant->user->name }} <br>
                                            <strong>Email:</strong> {{ $participant->user->email }} <br>
                                            <strong>Phone:</strong> {{ $participant->user->phone }} <br>

                                        </td>
                                        <td>
                                            <strong>Hostel:</strong> {{ $participant->hostel->name ?? 'N/A'}} <br>
                                            <strong>Hostel Number:</strong> {{ $participant->hostel_allocation_number ?? 'N/A' }} <hr style="margin-top: 4px; margin-bottom: 4px;">

                                            <strong>Service Stand:</strong> {{ $participant->food->name ?? 'N/A' }} <br>
                                            <strong>Service Stand No.:</strong> {{ $participant->service_point_allocation_number ?? 'N/A'}}
                                        </td>
                                        <td>@if($participant->registration_status == 'Complete')
                                            <i class="bx bxs-circle success font-small-1 mr-50"></i><small>Complete</small> @else
                                            <i class="bx bxs-circle danger font-small-1 mr-50"></i><small>Pending</small>
                                            @endif
                                        </td>
                                        <td>{!! currency_symbol() !!}{{ number_format($participant->total_amount) }}</td>
                                        <td>
                                            @if(($participant->moderator) && in_array($participant->level, ['Participant', 'Moderator'])){{ $participant->moderator->name }}
                                            @else N/A @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex flex-column align-items-stretch" style="min-width: 180px; gap: 6px;">

                                                {{-- View/Edit Participant --}}
                                                <a href="{{ route('conference.participants.edit', ['edition' => $participant->conference_edition_id, 'id' => $participant->id]) }}"
                                                class="btn btn-sm btn-primary text-nowrap">
                                                    <i class="bx bxs-edit me-1"></i> Edit
                                                </a>

                                                {{-- Delete Participant --}}
                                                @if(auth()->user()->id != $participant->user_id)
                                                    <a href="{{ route('conferenceparticipants.delete', [
                                                            'id' => $participant->user_id,
                                                            'edition' => $participant->conference_edition_id,
                                                            'payment_id' => $participant->id
                                                        ]) }}"
                                                    onclick="return confirm('Are you really sure?');"
                                                    class="btn btn-sm btn-danger text-nowrap">
                                                        <i class="fa fa-trash me-1" style="font-size: 1.2rem;top: 1px"></i> Delete
                                                    </a>
                                                @endif

                                            </div>
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
@endsection
