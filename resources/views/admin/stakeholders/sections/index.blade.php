@extends('layouts.dashboard')
@section('title', 'Stakeholder Sections')
@section('active')
<li class="breadcrumb-item">Stakeholder Sections</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Sections</h4>
                        <a href="{{ route('stakeholderreportsection.create') }}" class="btn btn-primary mt-1">Add Section</a>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Roles with Access</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sections as $section)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $section->name }}</td>
                                            <td>{{ $section->status ? 'Active' : 'Inactive' }}</td>
                                            <td>
                                                @if(!empty($section->access_roles) && count($section->access_roles) > 0)
                                                    <ul class="mb-0">
                                                        @foreach($section->access_roles as $roleId)
                                                            @php
                                                                $role = \App\Models\StakeholderRole::find($roleId);
                                                            @endphp
                                                            @if($role)
                                                                <li>{{ $role->name }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <em>No roles assigned</em>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('stakeholderreportsection.edit', $section->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                <form action="{{ route('stakeholderreportsection.destroy', $section->id) }}" method="POST" style="display:inline-block">
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
