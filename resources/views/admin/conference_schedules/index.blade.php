@extends('layouts.conference')
@section('title', 'Conference Schedules')

@section('content2')
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">All Conference Schedules</h4>
            <a href="{{ route('conference_schedule.create', ['edition' => $edition]) }}" class="btn btn-primary">Add New Schedule</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Day</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $index => $schedule)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $schedule->day }}</td>
                                <td>{{ $schedule->date }}</td>
                                <td>
                                    @if($schedule->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                
                                <td>
                                    <a href="{{ route('conference_schedule.edit', ['edition' => $edition, 'conferenceSchedule' => $schedule->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('conference_schedule.destroy', ['edition' => $edition, 'conferenceSchedule' => $schedule->id]) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure you want to delete this schedule?')" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No conference schedules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
