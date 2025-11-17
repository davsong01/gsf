@extends('layouts.conference')
@section('title', 'Conference Plans')

@section('content2')
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">All Conference Plans</h4>
            <a href="{{ route('conference_plans.create', ['edition' => $edition]) }}" class="btn btn-primary">Add New Plan</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Level</th>
                        <th>Type</th>
                        <th>Price (₦)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $index => $plan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $plan->title }}</td>
                            <td>
                                @if($plan->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($plan->level) }}</td>
                            <td>{{ ucfirst($plan->type) }}</td>
                            <td>{{ number_format($plan->price, 2) }}</td>
                            
                            <td>
                                <a href="{{ route('conference_plans.edit', ['edition' => $edition, 'conferencePlan' => $plan->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('conference_plans.destroy',  ['edition' => $edition, 'conferencePlan' => $plan->id]) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Are you sure you want to delete this plan?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No conference plans found.</td>
                        </tr>
                    @endforelse
                </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
@endsection
