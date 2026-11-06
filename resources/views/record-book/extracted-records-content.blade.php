<div class="content-body extraction-workspace">
    <header class="extraction-hero">
        <div class="extraction-hero__copy"><span class="extraction-kicker"><i class="fa fa-list-alt" aria-hidden="true"></i> Record book review · {{ $recordType === 'alumni' ? 'Alumni' : 'Members' }}</span><h1>Extracted records</h1><p>{{ $upload->chapter?->name }} · {{ $upload->original_name }}. Review each extracted entry before adding it to the campus directory.</p></div>
        <a class="extraction-hero__link" href="{{ $isAdmin ? route('users.extraction.index') : $memberIndexUrl }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
    </header>

    @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if($upload->extracted_data['page_notes'] ?? false)<div class="extraction-review-note"><strong>Page notes</strong><p>{{ $upload->extracted_data['page_notes'] }}</p></div>@endif
    @if($upload->extraction_response)<details class="extraction-response"><summary><i class="fa fa-code" aria-hidden="true"></i> View extraction response</summary><pre>{{ $upload->extraction_response }}</pre></details>@endif

    <section class="extraction-review-panel">
        <div class="extraction-panel-heading"><div><span class="extraction-section-label">Batch #{{ $upload->id }} · {{ ucfirst($upload->status) }}</span><h2>Review this page</h2><p>{{ $upload->extractedRecords()->where('status', 'pending')->count() }} awaiting approval · {{ $upload->extractedRecords()->where('status', 'approved')->count() }} added to users.</p></div><a class="extraction-button extraction-button--reset" href="{{ $isAdmin ? route('admin.protected.download', ['file' => $upload->image_path]) : route('protected.download', ['file' => $upload->image_path]) }}" target="_blank" rel="noopener"><i class="fa fa-picture-o"></i> View source image</a></div>

        <form id="extracted-record-bulk-form" method="POST" action="{{ route($routeNames['bulk'], $upload) }}" class="extraction-bulk-form">
            @csrf
            <span class="extraction-selected-count">Select records for bulk actions</span>
            <div class="extraction-bulk-controls"><select name="action" required><option value="">Bulk actions</option><option value="edit">Edit selected</option><option value="delete">Delete selected</option><option value="approve">Approve selected</option></select><button class="extraction-button extraction-button--bulk" type="submit"><i class="fa fa-check"></i> Apply</button></div>
        </form>

        <div class="extraction-table-wrap"><table class="extraction-table extraction-records-table"><thead><tr><th class="extraction-check"><input type="checkbox" data-extraction-select-all aria-label="Select all pending records"></th><th>Name and contact</th><th>Fellowship details</th><th>Status</th><th>Actions</th></tr></thead><tbody>
            @forelse($records as $record)
                @php($data = $record->data ?? [])
                <tr>
                    <td class="extraction-check">@if($record->status === 'pending')<input type="checkbox" name="ids[]" value="{{ $record->id }}" form="extracted-record-bulk-form" data-extraction-checkbox aria-label="Select {{ $data['name'] ?? 'record' }}">@endif</td>
                    <td><strong class="extraction-review-name">{{ $data['name'] ?? 'Name missing' }}</strong><small>{{ $data['email'] ?? 'No email' }} · {{ $data['phone'] ?? 'No phone' }}</small>@if($data['notes'] ?? false)<small class="extraction-error">{{ $data['notes'] }}</small>@endif</td>
                    <td class="extraction-review-details"><span>{{ $data['gender'] ?? 'Gender unknown' }} · {{ $data['dob'] ?? 'DOB unknown' }}</span><span>{{ $data['program'] ?? 'Program unknown' }} · {{ $data['course'] ?? 'Course unknown' }}</span><span>Matric {{ $data['matric_year'] ?? '—' }} · Graduated {{ $data['graduation_year'] ?? '—' }}</span></td>
                    <td><span class="extraction-status extraction-status--{{ $record->status }}">{{ ucfirst($record->status) }}</span>@if($record->user_id)<small class="d-block">User #{{ $record->user_id }}</small>@endif</td>
                    <td class="extraction-actions">
                        @if($record->status === 'pending')
                            <details class="extraction-inline-edit"><summary>Edit</summary><form method="POST" action="{{ route($routeNames['update'], [$upload, $record]) }}">@csrf @method('PUT')
                                @foreach(['name'=>'Name','email'=>'Email','phone'=>'Phone','gender'=>'Gender','dob'=>'Date of birth','matric_year'=>'Matric year','graduation_year'=>'Graduation year','program'=>'Program','course'=>'Course','course_duration'=>'Course duration','notes'=>'Notes'] as $field=>$label)<label>{{ $label }}<input class="form-control" name="{{ $field }}" value="{{ $data[$field] ?? '' }}" @if($field==='name') required @endif></label>@endforeach
                                <button class="extraction-button extraction-button--apply" type="submit">Save changes</button>
                            </form></details>
                            <form method="POST" action="{{ route($routeNames['approve'], [$upload, $record]) }}" onsubmit="return confirm('Add this person to campus users?')">@csrf<button class="extraction-button extraction-button--approve" type="submit">Approve &amp; add</button></form>
                            <form method="POST" action="{{ route($routeNames['destroy'], [$upload, $record]) }}" onsubmit="return confirm('Delete this extracted record?')">@csrf @method('DELETE')<button class="extraction-delete" type="submit" title="Delete record"><i class="fa fa-trash"></i></button></form>
                        @else
                            <span class="extraction-user-linked">Added to users</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="extraction-empty"><strong>No extracted records in this batch</strong><span>You can add a record below if the image contains a person not detected by extraction.</span></td></tr>
            @endforelse
        </tbody></table></div>
        @if($records->hasPages())<div class="extraction-pagination">{{ $records->links() }}</div>@endif
    </section>

    <section class="extraction-review-panel extraction-add-panel"><div class="extraction-panel-heading"><div><span class="extraction-section-label">Manual entry</span><h2>Add a record to this batch</h2><p>The record will stay in review until an admin or campus stakeholder approves it. A valid email is required before creating the user.</p></div></div>
        <form class="extraction-add-form" method="POST" action="{{ route($routeNames['store'], $upload) }}">@csrf
            @foreach(['name'=>'Name','email'=>'Email','phone'=>'Phone','gender'=>'Gender','dob'=>'Date of birth','matric_year'=>'Matric year','graduation_year'=>'Graduation year','program'=>'Program','course'=>'Course','course_duration'=>'Course duration','notes'=>'Notes'] as $field=>$label)<label>{{ $label }}<input class="form-control" name="{{ $field }}" @if($field==='name') required @endif></label>@endforeach
            <button class="extraction-button extraction-button--apply" type="submit"><i class="fa fa-plus"></i> Add to batch</button>
        </form>
    </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.querySelector('[data-extraction-select-all]');
    const checkboxes = Array.from(document.querySelectorAll('[data-extraction-checkbox]'));
    if (!selectAll) return;
    selectAll.addEventListener('change', function () {
        checkboxes.forEach(function (checkbox) { checkbox.checked = selectAll.checked; });
    });
    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            selectAll.checked = checkboxes.length > 0 && checkboxes.every(function (item) { return item.checked; });
        });
    });
});
</script>
