@extends('layouts.dashboard')
@section('title', 'Trashed Users')
@section('active')
<li class="breadcrumb-item">Trashed users</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Deleted Users</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date Deleted</th>
                                            <th>ID</th>
                                            <th>Avatar</th>
                                            <th>Details</th>
                                            <th>Status</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $user->deleted_at->format('Y-m-d') }}</td>
                                            <td>{{ $user->family_id }}</td>
                                            <td><img class="mr-1" style="border-radius:50%" src="{{ asset($user->passport ? $user->passport : 'frontend/passports/avatar.jpg') }}" alt="avatar" height="40" width="40"></td>
                                            <td>
                                                <strong>{{ $user->name }}</strong> <br>
                                               <i class="fa fa-envelope"></i> {{ $user->email }} <br>
                                               <i class="fa fa-phone"></i> {{ $user->phone }} <br>
                                              <i class="fa fa-university"></i> {{ 'GSF, ' . $user->campus->name ?? '' }}
                                            </td>
                                            <td>
                                                @if($user->status == 0)
                                                Student @else Alumni
                                                @endif
                                            </td>
                                            <td>{{ $user->rolename }}  @if($user->rolename <> 'Admin' && $user->rolename <> 'Member')<em>{{ $user->portfolio_session }}</em>
                                               
                                                @endif
                                            </td>
                                                                                                        
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                               
                                                <a class="actions" onclick="return confirm('Are you sure?')" data-toggle="tooltip" title="Restore" href="{{ route('users.restore', $user->id) }}"> <i class="fa fa-undo"></i></
                                                </a>
                                                <a class="actions" onclick="return confirm('Are you sure?')" data-toggle="tooltip" title="Delete permanently" href="{{ route('users.delete', $user->id) }}"> <i class="fa fa-trash"></i></
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