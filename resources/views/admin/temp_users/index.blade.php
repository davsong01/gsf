@extends('layouts.dashboard')
@section('title', 'Transactions')
@section('active')
<li class="breadcrumb-item">Attempted/Not Completed Transactions</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Attempted Transactions</h4>
                       
                        @include('includes.alerts')
                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Type</th>
                                            <th>Chapter</th>

                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $participant)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $participant->name }}</td>
                                            <td>{{ $participant->email }}</td>
                                            <td>{{ $participant->phone }}</td>
                                            <td>@if($participant->type == 1)Individual
                                                @elseif($participant->type == 2)Fellowship
                                                @elseif($participant->type == 3)Alumni
                                                @elseif($participant->type == 4)Nec
                                                @elseif($participant->type == 5)Donation
                                                @endif
                                            </td>
                                            <td>{{ isset($participant->campus) ?$participant->campus->name : 'N/A'}}</td>
                                                                 
                                            <td>{{ $participant->created_at->format('Y-m-d') }}</td>
                                                                                        
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete User" href="{{ route('tempusers.destroy', $participant->id) }}"> <i class="fa fa-trash"></i></
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