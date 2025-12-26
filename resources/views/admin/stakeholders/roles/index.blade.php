@extends('layouts.dashboard')
@section('title', 'Stakeholder Roles')
@section('active')
<li class="breadcrumb-item">Stakeholder Roles</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Roles</h4>
                        <a href="{{ route('stakeholderroles.create') }}" class="btn btn-primary mt-1">Add New Role</a>
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
                                            <th>Permissions</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($roles as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>{{ $role->slug }}</td>
                                            <td>
                                                @if($role->permissions->isNotEmpty())
                                                    <ul class="mb-0">
                                                        @foreach($role->permissions as $perm)
                                                            <li>{{ $perm->name }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <em>No permissions assigned</em>
                                                @endif
                                            </td>
                                            
                                            <td>
                                                <a href="{{ route('stakeholderroles.edit', $role->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                <form action="{{ route('stakeholderroles.destroy', $role->id) }}" method="POST" style="display:inline-block">
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
