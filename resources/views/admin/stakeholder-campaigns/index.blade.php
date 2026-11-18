@extends('layouts.dashboard')

@section('title', 'Campaigns')

@section('active')
<li class="breadcrumb-item">Campaigns</li>
@endsection

@section('content')
<div class="content-body">
    <section>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-1">Campaigns</h4>
                    <small class="text-muted">Manage campaign forms and their opening dates.</small>
                </div>
                <a href="{{ route('campaigns.create') }}" class="btn btn-primary">Create Campaign</a>
            </div>
            <div class="card-body">
                @include('includes.alerts')
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr><th>Title</th><th>Slug</th><th>Public URL</th><th>Status</th><th>Questions</th><th>Responses</th><th>Event Date</th><th>Open From</th><th>Open Until</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $campaign)
                                <tr>
                                    <td class="fw-semibold">{{ $campaign->title }}</td>
                                    <td>{{ $campaign->slug }}</td>
                                    <td><a href="{{ route('campaigns.public.show', $campaign->slug) }}" target="_blank" rel="noopener"><code>{{ url('campaign/' . $campaign->slug) }}</code></a></td>
                                    <td><span class="badge {{ $campaign->status === 'active' ? 'bg-success' : ($campaign->status === 'closed' ? 'bg-secondary' : 'bg-warning text-dark') }}">{{ ucfirst($campaign->status) }}</span></td>
                                    <td>{{ $campaign->questions_count }}</td>
                                    <td><a href="{{ route('campaigns.responses', $campaign) }}">View</a> <small class="text-muted">({{ $campaign->responses_count }})</small></td>
                                    <td>{{ $campaign->event_at?->format('M j, Y g:i A') ?? 'Not set' }}</td>
                                    <td>{{ $campaign->open_from->format('M j, Y g:i A') }}</td>
                                    <td>{{ $campaign->open_until->format('M j, Y g:i A') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-sm btn-info">Edit</a>
                                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Delete this campaign?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted py-4">No campaigns yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $campaigns->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
