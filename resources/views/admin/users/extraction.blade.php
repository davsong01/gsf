@extends('layouts.dashboard')

@section('title', 'Users Extraction')
@section('item')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Community Users</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Users Extraction</li>
@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/admin-user-extraction.css') }}">
@endsection

@section('content')
<div class="content-body extraction-workspace">
    <header class="extraction-hero">
        <div class="extraction-hero__copy">
            <span class="extraction-kicker"><i class="fa fa-book" aria-hidden="true"></i> Campus directory · record books</span>
            <h1>Users extraction</h1>
            <p>Approve campus record book pages, extract them on schedule, then review and add each record to the campus directory.</p>
        </div>
        <div class="extraction-hero__actions"><form method="POST" action="{{ route('users.extraction.process-approved') }}">@csrf<button class="extraction-hero__link" type="submit"><i class="fa fa-play-circle" aria-hidden="true"></i> Process approved queue</button></form><a class="extraction-hero__link" href="{{ route('users.index') }}"><i class="fa fa-users" aria-hidden="true"></i> Community users</a></div>
    </header>

    <section class="extraction-stats" aria-label="Record book page summary">
        <article class="extraction-stat"><span>All pages</span><strong>{{ number_format($totalUploads) }}</strong><i class="fa fa-files-o" aria-hidden="true"></i></article>
        <article class="extraction-stat extraction-stat--pending"><span>Awaiting approval</span><strong>{{ number_format($pendingCount) }}</strong><i class="fa fa-clock-o" aria-hidden="true"></i></article>
        <article class="extraction-stat extraction-stat--approved"><span>Approved queue</span><strong>{{ number_format($approvedCount) }}</strong><i class="fa fa-hourglass-half" aria-hidden="true"></i></article>
        <article class="extraction-stat extraction-stat--complete"><span>Ready for review</span><strong>{{ number_format($extractedCount) }}</strong><i class="fa fa-check-circle-o" aria-hidden="true"></i></article>
        <article class="extraction-stat extraction-stat--duplicate"><span>Completed</span><strong>{{ number_format($completedCount) }}</strong><i class="fa fa-check" aria-hidden="true"></i></article>
    </section>

    <section class="extraction-filter-panel" aria-labelledby="extraction-filter-heading">
        <div class="extraction-panel-heading"><div><span class="extraction-section-label">Find a record book page</span><h2 id="extraction-filter-heading">Filters</h2><p>Combine location, type, status, and upload date filters.</p></div></div>
        <form class="extraction-filters" method="GET" action="{{ route('users.extraction.index') }}">
            <div class="extraction-filter-grid">
                <div class="extraction-field extraction-field--search"><label for="extraction-search">Search</label><input class="form-control" id="extraction-search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Campus or stakeholder"></div>
                <div class="extraction-field"><label for="extraction-chapter">Chapter</label><select class="form-control" id="extraction-chapter" name="chapter_id"><option value="">All chapters</option>@foreach($chapters as $chapter)<option value="{{ $chapter->id }}" @selected(($filters['chapter_id'] ?? '') == $chapter->id)>{{ $chapter->name }}</option>@endforeach</select></div>
                <div class="extraction-field"><label for="extraction-zone">Zone</label><select class="form-control" id="extraction-zone" name="zone_id"><option value="">All zones</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected(($filters['zone_id'] ?? '') == $zone->id)>{{ $zone->name }}</option>@endforeach</select></div>
                <div class="extraction-field"><label for="extraction-field">Field</label><select class="form-control" id="extraction-field" name="field_id"><option value="">All fields</option>@foreach($fields as $field)<option value="{{ $field->id }}" @selected(($filters['field_id'] ?? '') == $field->id)>{{ $field->name }}</option>@endforeach</select></div>
                <div class="extraction-field"><label for="extraction-type">User type</label><select class="form-control" id="extraction-type" name="record_type"><option value="">All types</option><option value="member" @selected(($filters['record_type'] ?? '') === 'member')>Member</option><option value="alumni" @selected(($filters['record_type'] ?? '') === 'alumni')>Alumni</option></select></div>
                <div class="extraction-field"><label for="extraction-status">Status</label><select class="form-control" id="extraction-status" name="status"><option value="">All statuses</option>@foreach(['pending', 'approved', 'processing', 'extracted', 'completed', 'rejected', 'duplicate'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                <div class="extraction-field"><label for="extraction-from">Uploaded from</label><input class="form-control" id="extraction-from" type="date" name="from" value="{{ $filters['from'] ?? '' }}"></div>
                <div class="extraction-field"><label for="extraction-to">Uploaded to</label><input class="form-control" id="extraction-to" type="date" name="to" value="{{ $filters['to'] ?? '' }}"></div>
            </div>
            <div class="extraction-filter-actions"><span>Filters apply together to the page list below.</span><div><button class="extraction-button extraction-button--apply" type="submit"><i class="fa fa-filter" aria-hidden="true"></i> Apply filters</button><a class="extraction-button extraction-button--reset" href="{{ route('users.extraction.index') }}">Reset</a></div></div>
        </form>
    </section>

    <section class="extraction-results" aria-labelledby="extraction-results-heading">
        <div class="extraction-panel-heading extraction-results__heading">
            <div><span class="extraction-section-label">Uploaded images</span><h2 id="extraction-results-heading">Record book pages</h2><p>{{ number_format($uploads->total()) }} matching {{ $uploads->total() === 1 ? 'page' : 'pages' }}</p></div>
            <div class="extraction-results__page">Page {{ $uploads->currentPage() }} of {{ max(1, $uploads->lastPage()) }}</div>
        </div>
        <form id="extraction-bulk-form" class="extraction-bulk-form" method="POST" action="{{ route('users.extraction.bulk-action') }}">
            @csrf
            <span class="extraction-selected-count" data-extraction-selection>No pages selected</span>
            <div class="extraction-bulk-controls">
                <label class="sr-only" for="extraction-bulk-action">Bulk action</label>
                <select class="form-control" id="extraction-bulk-action" name="action" required>
                    <option value="">Choose bulk action</option>
                    <option value="approve">Approve selected</option>
                    <option value="approve_extract">Approve and extract selected</option>
                    <option value="reject">Reject selected</option>
                    <option value="delete">Delete selected</option>
                </select>
                <button class="extraction-button extraction-button--bulk" type="submit" disabled><i class="fa fa-bolt" aria-hidden="true"></i> Apply</button>
            </div>
        </form>
        <div class="extraction-table-wrap">
            <table class="extraction-table">
                <thead><tr><th class="extraction-check"><input type="checkbox" data-extraction-select-all aria-label="Select all pages on this page"></th><th>S/N</th><th>Page</th><th>Campus · zone · field</th><th>User type</th><th>Status</th><th>Records</th><th>Uploaded</th><th>Completed</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($uploads as $upload)
                        @php($imageUrl = route('admin.protected.download', ['file' => $upload->image_path]))
                        <tr>
                            <td class="extraction-check"><input type="checkbox" name="ids[]" value="{{ $upload->id }}" form="extraction-bulk-form" data-extraction-checkbox aria-label="Select uploaded page {{ $upload->id }}"></td>
                            <td class="extraction-serial">{{ ($uploads->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="extraction-page-cell"><button class="extraction-preview" type="button" data-toggle="modal" data-target="#extraction-preview-{{ $upload->id }}"><img class="extraction-thumb" src="{{ $imageUrl }}" alt=""><span><i class="fa fa-eye" aria-hidden="true"></i> Preview</span></button></td>
                            <td class="extraction-location"><strong>{{ $upload->chapter?->name ?? 'Chapter unavailable' }}</strong><small>{{ $upload->chapter?->zone?->name ?? 'No zone' }} · {{ $upload->chapter?->field?->name ?? $upload->chapter?->zone?->field?->name ?? 'No field' }}</small></td>
                            <td><span class="extraction-type">{{ $upload->record_type === 'alumni' ? 'Alumni' : 'Member' }}</span></td>
                            <td>
                                <span class="extraction-status extraction-status--{{ $upload->status }}">{{ ucfirst($upload->status) }}</span>
                                @if($upload->duplicate_of_upload_id)<small class="extraction-duplicate-note">Same as #{{ $upload->duplicate_of_upload_id }}</small>@endif
                                @if($upload->extraction_error)<small class="extraction-error" title="{{ $upload->extraction_error }}">{{ \Illuminate\Support\Str::limit($upload->extraction_error, 110) }}</small>@endif
                                @if($upload->extraction_response && !in_array($upload->status, ['extracted', 'completed'], true))<details class="extraction-record-preview"><summary>View response</summary><pre class="extraction-response-inline">{{ $upload->extraction_response }}</pre></details>@endif
                                @if(in_array($upload->status, ['extracted', 'completed'], true))
                                    <details class="extraction-record-preview"><summary>View extracted records</summary>
                                        @if(!empty($upload->extracted_data['page_notes']))<p class="extraction-page-notes">{{ $upload->extracted_data['page_notes'] }}</p>@endif
                                        <ol>
                                        @foreach(($upload->extracted_data['records'] ?? []) as $record)
                                            <li><strong>{{ $record['name'] ?? 'Name unreadable' }}</strong><span>{{ $record['email'] ?? 'No email' }} · {{ $record['phone'] ?? 'No phone' }}</span>@if(!empty($record['notes']))<small>{{ $record['notes'] }}</small>@endif</li>
                                        @endforeach
                                    </ol></details>
                                @endif
                            </td>
                            <td class="extraction-records">{{ in_array($upload->status, ['extracted', 'completed'], true) ? number_format($upload->extracted_records_count ?? 0) : '—' }}@if(in_array($upload->status, ['extracted', 'completed'], true))<a class="extraction-view-records" href="{{ route('users.extraction.records.index', $upload) }}">Review batch</a>@endif</td>
                            <td class="extraction-date">{{ $upload->created_at?->format('d M Y') }}<small>{{ $upload->created_at?->format('g:i A') }}</small></td>
                            <td class="extraction-date">{{ $upload->completed_at?->format('d M Y') ?? '—' }}@if($upload->completed_at)<small>{{ $upload->completed_at->format('g:i A') }}</small>@endif</td>
                            <td class="extraction-actions">
                                @if(in_array($upload->status, ['pending', 'duplicate', 'rejected'], true))
                                    <form class="extraction-extract-form" method="POST" action="{{ route('users.extraction.approve', $upload) }}">@csrf<button class="extraction-button extraction-button--approve" type="submit">Approve</button></form>
                                    <form class="extraction-extract-form" method="POST" action="{{ route('users.extraction.extract', $upload) }}" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').textContent = 'Extracting…';">@csrf<button class="extraction-button extraction-button--extract" type="submit">Approve &amp; extract</button></form>
                                @elseif($upload->status === 'approved')
                                    <form class="extraction-extract-form" method="POST" action="{{ route('users.extraction.extract', $upload) }}" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').textContent = 'Extracting…';">@csrf<button class="extraction-button extraction-button--extract" type="submit">Extract now</button></form>
                                @elseif($upload->status === 'processing')
                                    <span class="extraction-processing-note">Processing…</span>
                                @endif
                                @if(($upload->linked_user_records_count ?? 0) === 0)<form class="extraction-delete-form" method="POST" action="{{ route('users.extraction.destroy', $upload) }}" onsubmit="return confirm('Delete this uploaded image?')">@csrf @method('DELETE')<button class="extraction-delete" type="submit" aria-label="Delete uploaded image" title="Delete page"><i class="fa fa-trash" aria-hidden="true"></i></button></form>@else<small class="extraction-user-linked">Has linked users</small>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td class="extraction-empty" colspan="10"><i class="fa fa-inbox" aria-hidden="true"></i><strong>No record book pages found</strong><span>Try changing your filters or search for another campus.</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @foreach($uploads as $upload)
            <div class="modal fade extraction-preview-modal" id="extraction-preview-{{ $upload->id }}" tabindex="-1" role="dialog" aria-label="Record book page preview" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Record book page</h2>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body"><img src="{{ route('admin.protected.download', ['file' => $upload->image_path]) }}" alt="Record book page" loading="lazy"></div>
                    </div>
                </div>
            </div>
        @endforeach
        @if($uploads->hasPages())<div class="extraction-pagination">{{ $uploads->links() }}</div>@endif
    </section>
</div>
@endsection

@section('extra_scripts')
<script>
const extractionBulkForm = document.getElementById('extraction-bulk-form');
const extractionSelectAll = document.querySelector('[data-extraction-select-all]');
const extractionCheckboxes = Array.from(document.querySelectorAll('[data-extraction-checkbox]'));
const extractionBulkAction = document.getElementById('extraction-bulk-action');
const extractionApplyButton = extractionBulkForm.querySelector('button[type="submit"]');
const extractionSelectionLabel = document.querySelector('[data-extraction-selection]');

$(document).on('show.bs.modal', '.extraction-preview-modal', function () {
    $(this).appendTo(document.body);
});

function refreshExtractionSelection() {
    const selectedCount = extractionCheckboxes.filter(function (checkbox) { return checkbox.checked; }).length;
    extractionSelectionLabel.textContent = selectedCount ? selectedCount + (selectedCount === 1 ? ' page selected' : ' pages selected') : 'No pages selected';
    extractionApplyButton.disabled = selectedCount === 0;
    extractionSelectAll.checked = extractionCheckboxes.length > 0 && selectedCount === extractionCheckboxes.length;
    extractionSelectAll.indeterminate = selectedCount > 0 && selectedCount < extractionCheckboxes.length;
}

extractionSelectAll.addEventListener('change', function () {
    extractionCheckboxes.forEach(function (checkbox) { checkbox.checked = extractionSelectAll.checked; });
    refreshExtractionSelection();
});
extractionCheckboxes.forEach(function (checkbox) { checkbox.addEventListener('change', refreshExtractionSelection); });
extractionBulkForm.addEventListener('submit', function (event) {
    const selectedCount = extractionCheckboxes.filter(function (checkbox) { return checkbox.checked; }).length;
    if (!selectedCount || !extractionBulkAction.value) {
        event.preventDefault();
        alert('Select pages and choose a bulk action first.');
        return;
    }
    if (extractionBulkAction.value === 'delete' && !confirm('Permanently delete the selected uploaded pages?')) event.preventDefault();
    if (extractionBulkAction.value === 'reject' && !confirm('Reject the selected pages?')) event.preventDefault();
});

</script>
@endsection
