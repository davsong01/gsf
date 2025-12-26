@extends('layouts.dashboard')
@section('title', 'Stakeholder Permissions')
@section('active')
<li class="breadcrumb-item">Stakeholder Permissions</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Permissions</h4>
                        <a href="{{ route('stakeholderpermissions.create') }}" class="btn btn-primary mt-1">Add New Peermission</a>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>Roles</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($permissions as $permission)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $permission->name }}</td>
                                            <td>{{ $permission->slug }}</td>
                                            <td>
                                                @if($permission->roles->isNotEmpty())
                                                    <ul class="mb-0">
                                                        @foreach($permission->roles as $role)
                                                            <li>{{ $role->name }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <em>No role assigned</em>
                                                @endif
                                            </td>
                                            
                                            <td>
                                                <a href="{{ route('stakeholderpermissions.edit', $permission->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                <form action="{{ route('stakeholderpermissions.destroy', $permission->id) }}" method="POST" style="display:inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
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
