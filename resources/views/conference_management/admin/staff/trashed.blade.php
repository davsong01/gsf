@extends('layouts.conference')
@section('title', 'Trashed Participants')
@section('item')
<li class="breadcrumb-item"><a href="{{ route('conference.participants', ['edition'=>$edition->id,'type'=>'Participant']) }}">Conference Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Trashed Conference Participants</li>
@endsection

@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Trashed Participants</h4>
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
                                            <th>Details</th>
                                            <th>Level</th>
                                            <th>Amount Paid</th>

                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $participant)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $participant->deleted_at->format('Y-m-d') }}</td>
                                            <td><img class="mr-1" style="border-radius:50%" src="{{ asset('/'.$participant->passport ? $participant->passport : '/frontend/passports/avatar.jpg') }}" alt="avatar" height="40" width="40"></td>
                                            <td><b>{{ $participant->family_id }}</b> <br>
                                                Name: {{ $participant->name }} <br>
                                                Email: {{ $participant->email }} <br>
                                                Phone: {{ $participant->phone }} <br>
                                            </td>
                                            <td>{{ $participant->level }}</td>
                                            <td>{!! currency_symbol() !!}{{ number_format($participant->amount_paid ?? 0 ) }}</td>

                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="Restore" href="{{ route('users.restore', $participant->id) }}"> <i class="fa fa-undo"></i></
                                                </a>

                                                <a class="actions" data-toggle="tooltip" title="Delete permanently" href="#" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $participant->id }}').submit();"> <i class="fa fa-trash"></i></
                                                </a>
                                                <form id="delete-form-{{ $participant->id }}" action="{{ route('users.destroy', $participant->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>


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
