@extends('layouts.dashboard')
@section('title', 'Trashed Participants')
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
                        <h4 class="card-title">Trashed Participants</h4>

                        @include('includes.alerts')

                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date Trashed</th>
                                            <th>Avatar</th>
                                            <th>Conference ID</th>
                                            <th>Level</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Amount Paid</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $participant)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $participant->deleted_at->format('Y-m-d') }}</td>
                                            <td>{!! renderAvatar($participant, 40, 'mr-1') !!}</td>
                                            <td>{{ $participant->conference_number }}</td>
                                            <td>{{ $participant->level }}</td>

                                            <td>{{ $participant->name }}</td>
                                            <td>{{ $participant->email }}</td>
                                            <td>{{ $participant->phone }}</td>
                                            <td>{!! currency_symbol() !!}{{ $participant->amount_paid }}</td>

                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="Restore" href="{{ route('users.restore', $participant->id) }}"> <i class="fa fa-undo"></i></
                                                </a>

                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Permanently" href="{{ route('users.delete', $participant->id) }}"> <i class="fa fa-trash"></i></
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
