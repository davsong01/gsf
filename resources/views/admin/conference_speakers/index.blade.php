@extends('layouts.dashboard')
@section('title', 'Conference Speakers')

@section('content')
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">All Conference Speakers</h4>
            <a href="{{ route('conference_speakers.create') }}" class="btn btn-primary">Add New Speaker</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($speakers as $index => $speaker)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $speaker->name }}</td>
                                <td>{{ $speaker->title ?? '-' }}</td>
                                <td>
                                    @if($speaker->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($speaker->image)
                                        <img src="{{ asset('storage/' . $speaker->image) }}" alt="{{ $speaker->name }}" style="height:50px; width:auto;">
                                    @else
                                        <em>No Image</em>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('conference_speakers.edit', $speaker->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('conference_speakers.destroy', $speaker->id) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure you want to delete this speaker?')" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No conference speakers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
