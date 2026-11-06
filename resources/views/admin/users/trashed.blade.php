@extends('layouts.dashboard')

@section('title', 'Trashed Users')
@section('active')<li class="breadcrumb-item">Trashed Users</li>@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/stakeholder-members.css') }}">
<style>
.trashed-user-type { display:inline-flex; padding:5px 9px; border-radius:20px; color:#286e64; background:#e8f5ef; font-size:10px; font-weight:800; }
.members-action--restore { color:#276c67!important; background:#eef7f2!important; }
.members-table .dataTables_empty { padding:45px 20px!important; color:#758396!important; text-align:center; }
#trashed-users-table tbody td:nth-child(5) { min-width:250px; overflow-wrap:anywhere; }
#trashed-users-table tbody td:nth-child(5) strong { color:#24374f; font-size:13px; }
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
            <table class="members-table" id="trashed-users-table" style="width:100%">
                <thead><tr><th>S/N</th><th>Date deleted</th><th>Login ID</th><th>Photo</th><th>Member details</th><th>Type</th><th>Role</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
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
    var searchDelay;
    $('#trashed-users-search').on('input', function () {
        var query = this.value;
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function () { table.search(query).draw(); }, 200);
    });
});
</script>
@endsection
