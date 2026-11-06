@extends('layouts.dashboard')

@section('title', 'Enquiries')
@section('active')<li class="breadcrumb-item">Enquiries</li>@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/admin-enquiries.css') }}">
@endsection

@section('content')
<div class="content-body enquiries-workspace">
    <header class="enquiries-hero">
        <div>
            <span class="enquiries-eyebrow"><i class="fa fa-envelope-o" aria-hidden="true"></i> GSF contact centre</span>
            <h1>Enquiries</h1>
            <p>Review messages from the national website and campus contact forms. Use the filters to find a specific enquiry.</p>
        </div>
        <div class="enquiries-count"><strong>{{ number_format($totalEnquiries) }}</strong><span>matching enquiries</span></div>
    </header>

    <section class="enquiries-metrics" aria-label="Enquiry summary">
        <div class="enquiries-metric"><span>All enquiries</span><strong>{{ number_format($totalEnquiries) }}</strong><i class="fa fa-inbox" aria-hidden="true"></i></div>
        <div class="enquiries-metric"><span>Website</span><strong>{{ number_format($websiteCount) }}</strong><i class="fa fa-globe" aria-hidden="true"></i></div>
        <div class="enquiries-metric enquiries-metric--campus"><span>Campus</span><strong>{{ number_format($campusCount) }}</strong><i class="fa fa-university" aria-hidden="true"></i></div>
    </section>

    <section class="enquiries-filter" aria-labelledby="enquiry-filter-title">
        <div class="enquiries-filter__head"><div><h2 id="enquiry-filter-title">Filter enquiries</h2><p>Choose a campus, location, enquiry status and date range.</p></div></div>
        <form method="GET" action="{{ route('enquiries.index') }}">
            <div class="enquiries-filter__body">
                <div class="enquiries-field enquiries-field--search"><label for="enquiry-search">Search</label><input id="enquiry-search" class="form-control" name="search" value="{{ $search }}" placeholder="Name, email, message or chapter"></div>
                <div class="enquiries-field"><label for="enquiry-chapter">Chapter</label><select id="enquiry-chapter" class="form-control" name="chapter_id"><option value="">All chapters</option>@foreach($chapters as $chapter)<option value="{{ $chapter->id }}" @selected((string)request('chapter_id') === (string)$chapter->id)>{{ $chapter->name }}</option>@endforeach</select></div>
                <div class="enquiries-field"><label for="enquiry-zone">Zone</label><select id="enquiry-zone" class="form-control" name="zone_id"><option value="">All zones</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected((string)request('zone_id') === (string)$zone->id)>{{ $zone->name }}</option>@endforeach</select></div>
                <div class="enquiries-field"><label for="enquiry-field">Field</label><select id="enquiry-field" class="form-control" name="field_id"><option value="">All fields</option>@foreach($fields as $field)<option value="{{ $field->id }}" @selected((string)request('field_id') === (string)$field->id)>{{ $field->name }}</option>@endforeach</select></div>
                <div class="enquiries-field"><label for="enquiry-status">Status</label><select id="enquiry-status" class="form-control" name="status"><option value="">All statuses</option><option value="website" @selected(request('status') === 'website')>Website enquiry</option><option value="campus" @selected(request('status') === 'campus')>Campus enquiry</option></select></div>
                <div class="enquiries-field"><label for="enquiry-from">Date from</label><input id="enquiry-from" class="form-control" type="date" name="from_date" value="{{ request('from_date') }}"></div>
                <div class="enquiries-field"><label for="enquiry-to">Date to</label><input id="enquiry-to" class="form-control" type="date" name="to_date" value="{{ request('to_date') }}"></div>
            </div>
            <div class="enquiries-filter__actions"><small>Website and campus are the enquiry types currently recorded by the contact forms.</small><div class="enquiries-filter__buttons"><button class="btn btn-primary" type="submit"><i class="fa fa-filter" aria-hidden="true"></i> Apply filters</button><a class="btn btn-outline-secondary" href="{{ route('enquiries.index') }}">Reset</a></div></div>
        </form>
    </section>

    <section class="enquiries-panel" aria-labelledby="enquiry-list-title">
        <div class="enquiries-panel__head"><div><h2 id="enquiry-list-title">Contact messages</h2><p>Showing {{ $enquiries->firstItem() ?? 0 }}–{{ $enquiries->lastItem() ?? 0 }} of {{ number_format($enquiries->total()) }} results.</p></div></div>
        <div class="enquiries-table-wrap">
            <table class="enquiries-table">
                <thead><tr><th>Date</th><th>Enquiry</th><th>Chapter</th><th>Field / zone</th><th>Status</th><th>Message</th></tr></thead>
                <tbody>
                    @forelse($enquiries as $enquiry)
                        <tr>
                            <td class="enquiries-table__date">{{ $enquiry->created_at?->format('d M Y') }}<br>{{ $enquiry->created_at?->format('g:i A') }}</td>
                            <td class="enquiries-person"><strong>{{ $enquiry->name ?: 'Unknown sender' }}</strong><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a>@if($enquiry->phone)<br><a href="tel:{{ $enquiry->phone }}">{{ $enquiry->phone }}</a>@endif</td>
                            <td class="enquiries-place">{{ $enquiry->chapter?->name ?: 'National GSF' }}</td>
                            <td class="enquiries-place">{{ $enquiry->chapter?->field?->name ?? '—' }}<br><span class="text-muted">{{ $enquiry->chapter?->zone?->name ?? '—' }}</span></td>
                            <td><span class="enquiries-status {{ $enquiry->type === 'campus' ? 'enquiries-status--campus' : '' }}">{{ $enquiry->type === 'campus' ? 'Campus enquiry' : 'Website enquiry' }}</span></td>
                            <td class="enquiries-message"><p>{{ str($enquiry->message)->limit(220) }}</p>@if(mb_strlen((string) $enquiry->message) > 220)<details><summary>Read full message</summary><p>{{ $enquiry->message }}</p></details>@endif</td>
                        </tr>
                    @empty
                        <tr><td class="enquiries-empty" colspan="6"><i class="fa fa-inbox" aria-hidden="true"></i>{{ $search || collect($filters)->filter()->isNotEmpty() ? 'No enquiries match these filters.' : 'No enquiries have been received yet.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($enquiries->hasPages())<div class="enquiries-pagination">{{ $enquiries->links() }}</div>@endif
    </section>
</div>
@endsection
