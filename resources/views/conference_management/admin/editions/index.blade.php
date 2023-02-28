@extends('layouts.dashboard')
@section('extra_styles')
    <style>
        .btn i{
            position: inherit !important;
            top: unset !important;
        }
    </style>
@endsection
@section('title', 'Conference Editions')
@section('active')
<li class="breadcrumb-item">Conference Editions</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Conference Editions</h4>
                        <a href="{{ route('conferenceeditions.create') }}" class="btn btn-primary mt-1">Add conference editions</a>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Theme</th>
                                            <th>Dates</th>
                                            <th>Fee Details</th>
                                            <th>Analytics</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($editions as $edition)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $edition->conference_theme }} <br>
                                                <p class="btn btn-{{ $edition->status == 'active'?'primary':'danger' }} btn-sm" readonly>{{ $edition->status }}</p>
                                            </td>
                                            <td>{{ $edition->level }}
                                                <span style="color:red">{{ $edition->start_date }}</span> - <span style="color:green">{{ $edition->end_date }}</span>  <br>
                                                <small class="blue">Close Reg: {{ $edition->close_registration }}</small>
                                            </td>
                                            
                                            <td>Participant: &#8358;{{ number_format($edition->registration_fee) }}<br>
                                                <small class="blue">
                                                    New Alumni: &#8358;{{ number_format($edition->new_alumni_registration_fee) }}<br>
                                                    Old Alumni: &#8358;{{ number_format($edition->alumni_registration_fee) }}<br>
                                                </small>
                                            </td>
                                            <td>Attempted: {{ $edition->attemptedPayments()->count() }}<br>
                                                <small>
                                                    Participants: {{ $edition->participantCount() }}<br>
                                                    Alumni: {{ $edition->alumniCount() }}<br>
                                                    Total: {{ $edition->payments->count() }}<br>
                                                </small>
                                            </td>
                                            <td>
                                                <a style="margin-bottom: 3px;" href="{{ route('show.conference.edition', $edition->id) }}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View</a> <br>
                                                <a style="margin-bottom: 3px;" href="{{ route('clone.conference.edition', $edition->id) }}" class="btn btn-info btn-sm"><i class="fa fa-copy"></i> Clone</a> <br>
                                                {{-- <a href="{{ route('edit.conference.edition', $edition->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a> <br> --}}
                                                <a style="margin-bottom: 3px;" href="{{ route('delete.conference.edition', $edition->id) }}" class="btn btn-danger btn-sm"><i class="fa fa-edit"></i> Delete</a>
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
    <!--/ Zero configuration table -->         
</div>
@endsection