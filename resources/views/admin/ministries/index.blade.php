@extends('layouts.dashboard')
@section('title', 'Ministries')
@section('content')
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Ministries</h4>
            <a href="{{ route('ministry.create') }}" class="btn btn-primary mt-1">Add New Ministry</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ministries as $index => $ministry)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ministry->name }}</td>
                            <td>{{ $ministry->code }}</td>
                            <td>{{ ucfirst($ministry->status) }}</td>
                            <td>
                                <a href="{{ route('ministry.edit', $ministry->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <a href="{{ route('ministryfield.index', $ministry->id) }}" class="btn btn-sm btn-info">Reg. Fields</a>
                                <form action="{{ route('ministry.destroy', $ministry->id) }}" method="POST" style="display:inline-block">
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
@endsection
