@extends('layouts.dashboard')
@section('title', 'Community Users')
@section('active')
<li class="breadcrumb-item">GSF Community users</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Pending Users</h4>
                        <div class="">
                            {{-- <a href="{{ route('users.create') }}" class="btn btn-primary mt-1">Add new</a>
                            <a href="{{ route('users.import.index') }}" class="btn btn-primary mt-1">Import</a> --}}
                            {{-- @if(auth()->user()->isAdmin())<a href="" class="btn btn-primary mt-1">Export</a>@endif --}}
                        </div>
                        @include('includes.alerts')
                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="users">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Avatar</th>
                                            <th>Details</th>
                                            <th>Chapter</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ( $pending as $user )
                                            <tr>
                                                <td>{{ $counter ++ }}</td>
                                                <td><img style="max-width:200px" src="{{ asset($user->passport) }}" alt=""></td>
                                                <td>
                                                    Name:  {{ $user->name }} <br>
                                                    Phone:  {{ $user->phone }} <br>
                                                    Gender:  {{ $user->sex }}
                                                </td>
                                                <td>{{ $user->campus->name }}</td>   
                                                <td>
                                                    <div class="button-group">
                                                        <button style="margin-bottom: 10px;" class="btn btn-info btn-sm" data-toggle="modal"  data-target="#myModal-{{$user->id}}"><i class="fa fa-eye"></i>View</button>
                                                        <a style="margin-bottom: 10px;" class="btn btn-primary btn-sm" href="{{ route('user.single.approve', $user->id) }}"><i class="fa fa-check"></i> Approve</a>
                                                        <a style="margin-bottom: 10px;" class="btn btn-warning btn-sm" href="{{ route('user.single.reject', $user->id) }}"><i class="fa fa-times"></i> Reject</a>
                                                        <a style="margin-bottom: 10px;" class="btn btn-danger btn-sm" href="{{ route('user.single.delete', $user->id) }}"><i class="fa fa-trash"></i> Delete</a>

                                                    </div>
                                                </td>
                                            </tr>

                                            <div class="modal" id="myModal-{{$user->id}}">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <!-- Modal body -->
                                                        <div class="modal-body">
                                                            @include('admin.listings.edit')
                                                        </div>

                                                        <!-- Modal footer -->
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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