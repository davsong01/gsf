@extends('layouts.dashboard')
@section('title', 'Conference FAQs')

@section('content')
<div class="content-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">All Conference FAQs</h4>
            <a href="{{ route('conference_faqs.create') }}" class="btn btn-primary">Add New FAQ</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Order</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $index => $faq)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $faq->display_order }}</td>
                                <td class="fw-semibold text-dark">{{ $faq->question }}</td>
                                <td>{{ Str::limit($faq->answer, 80) }}</td>
                                <td>
                                    @if($faq->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('conference_faqs.edit', $faq->id) }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                                    <form action="{{ route('conference_faqs.destroy', $faq->id) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure you want to delete this FAQ?')" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No FAQs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
