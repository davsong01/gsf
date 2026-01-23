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
                        @if(auth()->user()->conference_role == 'superadmin')
                        <a href="{{ route('conferenceeditions.create') }}" class="btn btn-primary mt-1">Add conference edition</a>
                        @endif
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Ministry</th>
                                            <th>Theme</th>
                                            <th>Dates</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($editions)
                                        @foreach($editions as $edition)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ optional($edition->ministry)->name ?? 'N/A' }} <br>
                                                <strong>{{ optional($edition->ministry)->code ?? '-' }}</strong>
                                            </td>

                                            <td>
                                                {{ $edition->conference_theme }} <br>
                                                <p class="btn btn-{{ $edition->status == 'active'?'primary':'danger' }} btn-sm" readonly>{{ $edition->status }}</p>
                                            </td>
                                            <td>{{ $edition->level }}
                                                <span style="color:red">{{ \Carbon\Carbon::parse($edition->start_date)->format('F j, Y') }}</span> -
                                                <span style="color:green">{{ \Carbon\Carbon::parse($edition->end_date)->format('F j, Y') }}</span> <br>

                                                <small class="blue">Start Reg: {{ \Carbon\Carbon::parse($edition->start_registration)->format('F j, Y') }}</small> <br>
                                                <small class="blue">Close Reg: {{ \Carbon\Carbon::parse($edition->close_registration)->format('F j, Y') }}</small>
                                            </td>


                                            <td>
                                                <a style="margin-bottom: 3px;" href="{{ route('show.conference.edition', $edition->id) }}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> View</a>
                                                @if(auth()->user()->conference_role == 'superadmin')
                                                <br>
                                                <a style="margin-bottom: 3px;" href="{{ route('clone.conference.edition', $edition->id) }}" class="btn btn-info btn-sm"><i class="fa fa-copy"></i> Clone</a> <br>
                                                <a style="margin-bottom: 3px;" href="{{ route('delete.conference.edition', $edition->id) }}" class="btn btn-danger btn-sm"><i class="fa fa-edit"></i> Delete</a>
                                                @endif
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
    <!--/ Zero configuration table -->
</div>
@endsection
