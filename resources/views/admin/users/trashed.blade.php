@extends('layouts.dashboard')

@section('title', 'Trashed Users')
@section('active')<li class="breadcrumb-item">Trashed Users</li>@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/stakeholder-members.css') }}">
<style>
.trashed-user-type { display:inline-flex; padding:5px 9px; border-radius:20px; color:#286e64; background:#e8f5ef; font-size:10px; font-weight:800; }
.members-action--restore { color:#276c67!important; background:#eef7f2!important; }
.members-table .dataTables_empty { padding:45px 20px!important; color:#758396!important; text-align:center; }
#trashed-users-table tbody td:nth-child(6) { min-width:250px; overflow-wrap:anywhere; }
#trashed-users-table tbody td:nth-child(6) strong { color:#24374f; font-size:13px; }
.trashed-bulk { display:flex; flex-wrap:wrap; align-items:center; gap:9px; padding:16px 20px; border-bottom:1px solid #e8edf3; }
.trashed-bulk__count { margin-right:auto; color:#758396; font-size:13px; font-weight:700; }
.trashed-bulk .members-button:disabled { opacity:.5; cursor:not-allowed; }
</style>
@endsection

@section('content')
@php
    $studentCount = $users->where('is_graduated', 0)->count();
    $alumniCount = $users->where('is_graduated', 1)->count();
@endphp
<div class="content-body members-workspace">
    <header class="members-hero">
        <div class="members-hero__copy">
            <span class="members-eyebrow"><i class="fa fa-trash-o" aria-hidden="true"></i> Directory recovery</span>
            <h1>Trashed users</h1>
            <p>Restore deleted profiles or permanently remove accounts that are no longer needed.</p>
        </div>
        <div class="members-hero__actions">
            <a class="members-button members-button--light" href="{{ route('users.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Community users</a>
        </div>
    </header>

    <section class="members-summary" aria-label="Trashed user summary">
        <div class="members-summary__card"><span>Deleted profiles</span><strong>{{ number_format($users->count()) }}</strong><small>Currently in the trash</small><i class="fa fa-trash-o" aria-hidden="true"></i></div>
        <div class="members-summary__card"><span>Students</span><strong>{{ number_format($studentCount) }}</strong><small>Deleted student profiles</small><i class="fa fa-graduation-cap" aria-hidden="true"></i></div>
        <div class="members-summary__card members-summary__card--hint"><span>Alumni</span><strong>{{ number_format($alumniCount) }}</strong><small>Deleted alumni profiles</small><i class="fa fa-users" aria-hidden="true"></i></div>
    </section>

    <section class="members-panel" aria-labelledby="trashed-users-heading">
        <div class="members-panel__head">
            <div><span class="members-section-label">Deleted directory profiles</span><h2 id="trashed-users-heading">Recently deleted</h2><p>Search by name, email, phone, login ID, or chapter.</p></div>
            <label class="members-search" for="trashed-users-search"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search trashed users</span><input id="trashed-users-search" type="search" placeholder="Search deleted users" autocomplete="off"></label>
        </div>
        <div class="members-table-wrap">
            <form id="trashed-users-bulk-form" method="POST" action="{{ route('users.trashed.bulk-action') }}">
                @csrf
                <div class="trashed-bulk">
                    <span class="trashed-bulk__count"><span id="trashed-users-selected-count">0</span> selected</span>
                    <button class="members-button members-button--light" type="submit" name="action" value="restore" disabled><i class="fa fa-undo" aria-hidden="true"></i> Restore selected</button>
                    <button class="members-button members-button--light" type="submit" name="action" value="delete" disabled><i class="fa fa-trash" aria-hidden="true"></i> Delete selected forever</button>
                </div>
            <table class="members-table" id="trashed-users-table" style="width:100%">
                <thead><tr><th><input type="checkbox" id="trashed-users-select-all" aria-label="Select all deleted users"></th><th>S/N</th><th>Date deleted</th><th>Login ID</th><th>Photo</th><th>Member details</th><th>Type</th><th>Role</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><input type="checkbox" class="trashed-user-select" name="user_ids[]" value="{{ $user->id }}" aria-label="Select {{ $user->name }}"></td>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-nowrap">{{ $user->deleted_at?->format('d M Y') ?? '—' }}<small class="d-block text-muted">{{ $user->deleted_at?->format('g:i A') }}</small></td>
                            <td>{{ $user->family_id ?: '—' }}</td>
                            <td>{!! renderAvatar($user, 42) !!}</td>
                            <td><strong>{{ $user->name }}</strong><br><i class="fa fa-envelope" aria-hidden="true"></i> {{ $user->email ?: '—' }}<br><i class="fa fa-phone" aria-hidden="true"></i> {{ $user->phone ?: '—' }}<br><i class="fa fa-university" aria-hidden="true"></i> {{ $user->campus?->name ?? 'Chapter unavailable' }}</td>
                            <td><span class="trashed-user-type">{{ (int) $user->is_graduated === 1 ? 'Alumni' : 'Student' }}</span></td>
                            <td>{{ $user->rolename }}@if($user->rolename !== 'Admin' && $user->rolename !== 'Member' && $user->portfolio_session)<small class="d-block text-muted">{{ $user->portfolio_session }}</small>@endif</td>
                            <td>
                                <div class="members-actions">
                                    <a class="btn btn-sm members-action members-action--restore" onclick="return confirm('Restore this user to the community directory?')" href="{{ route('users.restore', $user->id) }}"><i class="fa fa-undo" aria-hidden="true"></i><span>Restore</span></a>
                                    <a class="btn btn-sm members-action members-action--delete" onclick="return confirm('Permanently delete this user and their profile data? This cannot be undone.')" href="{{ route('users.delete', $user->id) }}"><i class="fa fa-trash" aria-hidden="true"></i><span>Delete forever</span></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
            </form>
        </div>
    </section>
</div>
@endsection

@section('extra_scripts')
<script>
$(function () {
    var table = $('#trashed-users-table').DataTable({
        searching: true,
        ordering: false,
        autoWidth: false,
        pageLength: 100,
        lengthMenu: [[100, 250, 500, -1], [100, 250, 500, 'All']],
        dom: "t<'members-table-footer'lip>",
        language: { emptyTable: 'The trash is empty. Deleted user profiles will appear here.', zeroRecords: 'No deleted profiles match that search.' }
    });
    function updateBulkControls() {
        var checked = $('#trashed-users-table tbody .trashed-user-select:checked').length;
        $('#trashed-users-selected-count').text(checked);
        $('#trashed-users-bulk-form button[type="submit"]').prop('disabled', checked === 0);
        var visible = table.rows({ search: 'applied', page: 'current' }).nodes().to$().find('.trashed-user-select');
        $('#trashed-users-select-all').prop('checked', visible.length > 0 && visible.filter(':checked').length === visible.length);
    }
    $('#trashed-users-table').on('change', '.trashed-user-select', updateBulkControls);
    $('#trashed-users-select-all').on('change', function () {
        table.rows({ search: 'applied', page: 'current' }).nodes().to$().find('.trashed-user-select').prop('checked', this.checked);
        updateBulkControls();
    });
    table.on('draw', updateBulkControls);
    $('#trashed-users-bulk-form').on('submit', function (event) {
        var action = event.originalEvent.submitter.value;
        var count = $('#trashed-users-table tbody .trashed-user-select:checked').length;
        var message = action === 'delete'
            ? 'Permanently delete ' + count + ' selected user(s) and their profile data? This cannot be undone.'
            : 'Restore ' + count + ' selected user(s) to the community directory?';
        if (!count || !window.confirm(message)) event.preventDefault();
    });
    var searchDelay;
    $('#trashed-users-search').on('input', function () {
        var query = this.value;
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function () { table.search(query).draw(); }, 200);
    });
    updateBulkControls();
});
</script>
@endsection
