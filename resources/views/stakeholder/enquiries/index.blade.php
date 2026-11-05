@extends('layouts.stakeholderdashboard')
@section('title', 'Campus Enquiries')
@section('active')<li class="breadcrumb-item">Enquiries</li>@endsection
@section('content')
<div class="content-body">
    <section>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div><h4 class="card-title mb-0">Campus enquiries</h4><small class="text-muted">{{ $chapter->name }} · {{ $enquiries->total() }} total</small></div>
            </div>
            <div class="card-content"><div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Message</th></tr></thead>
                        <tbody>
                            @forelse($enquiries as $enquiry)
                                <tr>
                                    <td class="text-nowrap">{{ $enquiry->created_at?->format('d M Y, g:i A') }}</td>
                                    <td>{{ $enquiry->name }}</td>
                                    <td><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></td>
                                    <td>{{ $enquiry->phone }}</td>
                                    <td style="min-width: 320px; white-space: pre-line">{{ $enquiry->message }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No enquiries have been received for this campus yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $enquiries->links() }}
            </div></div>
        </div>
    </section>
</div>
@endsection
