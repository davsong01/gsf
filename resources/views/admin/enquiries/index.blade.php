@extends('layouts.dashboard')
@section('title', 'Enquiries')
@section('active')<li class="breadcrumb-item">Enquiries</li>@endsection
@section('content')
<div class="content-body">
    <section id="basic-datatable">
        <div class="row"><div class="col-12"><div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div><h4 class="card-title mb-0">Contact Us enquiries</h4><small class="text-muted">{{ $enquiries->total() }} total</small></div>
                <form method="GET" action="{{ route('enquiries.index') }}" class="d-flex mt-1" role="search">
                    <input class="form-control mr-1" name="search" value="{{ $search }}" placeholder="Search name, email or message" aria-label="Search enquiries">
                    <button class="btn btn-primary" type="submit">Search</button>
                    @if($search)<a href="{{ route('enquiries.index') }}" class="btn btn-outline-secondary ml-1">Clear</a>@endif
                </form>
            </div>
            <div class="card-content"><div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Campus</th><th>Name</th><th>Email</th><th>Phone</th><th>Enquiry</th></tr></thead>
                        <tbody>
                            @forelse($enquiries as $enquiry)
                                <tr>
                                    <td class="text-nowrap">{{ $enquiry->created_at?->format('d M Y, g:i A') }}</td>
                                    <td>{{ $enquiry->chapter?->name ?: 'National GSF' }}</td>
                                    <td>{{ $enquiry->name }}</td>
                                    <td><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></td>
                                    <td>{{ $enquiry->phone }}</td>
                                    <td style="min-width: 320px; white-space: pre-line">{{ $enquiry->message }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-3">{{ $search ? 'No enquiries match your search.' : 'No Contact Us enquiries have been received yet.' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $enquiries->links() }}
            </div></div>
        </div></div></div>
    </section>
</div>
@endsection
