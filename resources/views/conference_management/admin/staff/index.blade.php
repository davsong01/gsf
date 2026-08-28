@extends('layouts.conference')
@section('title', 'Conference Participants')
@section('active')
<li class="breadcrumb-item">{{ $edition->conference_theme }} Staff</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Staffs</h4>
                        <div class="">
                            <a href="{{ route('conference.staff.create', ['edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Add new</a>
                        </div>                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date Added</th>
                                            <th>Avatar</th>
                                            <th>Details</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                        @if(isset($staff) && $staff->count() > 0)
                                        @foreach($staff as $participant)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $participant->created_at }} <br>
                                                 <span style="color:blue">
                                                    <strong>Admin Role:</strong> {{ ucfirst($participant->conference_role) }}</span>
                                            </td>
                                            <td>{!! renderAvatar($participant, 40, 'mr-1') !!}</td>
                                            <td>
                                                <small>
                                                    <b>{{ $participant->family_id }}</b> <br>
                                                    <strong>Name:</strong> {{ $participant->name }} <br>
                                                    <strong>Email:</strong> {{ $participant->email }} <br>
                                                    <strong>Phone:</strong> {{ $participant->phone }} <br>
                                                   
                                                </small>
                                            </td>
                                            
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="View/Edit Staff" href="{{ route('conference.staff.edit', ['edition'=>$edition->id,'id'=>$participant->id]) }}"> <i class="bx bxs-edit actions"></i></
                                                </a>
                                                
                                                <a class="actions" data-toggle="tooltip" data-placement="top" title="Switch To"
                                                    href="{{ route('switchuser', ['edition'=>$edition->id,'id'=>$participant->id]) }}"><i
                                                        class="fa fa-unlock actions"></i>
                                                </a>
                                                 
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Staff" href="{{ route('conferencestaff.delete', $participant->id) }}"> <i class="fa fa-recycle"></i></
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
