@extends('layouts.dashboard')

@section('title', 'Community Members')
@section('active')
<li class="breadcrumb-item">Community Members</li>
@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/stakeholder-members.css') }}">
@endsection

@section('content')
<div class="content-body members-workspace">
    <header class="members-hero">
        <div class="members-hero__copy">
            <span class="members-eyebrow"><i class="fa fa-users" aria-hidden="true"></i> {{ $isAdmin ? 'National directory' : 'Campus directory' }}</span>
            <h1>Community members</h1>
            <p>{{ $isAdmin ? 'Manage member profiles across the GSF network and keep the national directory up to date.' : 'Manage member profiles for your campus fellowship.' }}</p>
        </div>
        <div class="members-hero__actions">
            <a class="members-button members-button--light" href="{{ $routes['import'] }}"><i class="fa fa-upload" aria-hidden="true"></i> Import members</a>
            @if($isAdmin)
                <a class="members-button members-button--light" href="{{ $routes['export'] }}"><i class="fa fa-download" aria-hidden="true"></i> Export</a>
            @endif
            <a class="members-button members-button--gold" href="{{ $routes['create'] }}"><i class="fa fa-plus" aria-hidden="true"></i> Add member</a>
        </div>
    </header>

    <section class="members-summary" aria-label="Community overview">
        <div class="members-summary__card"><span>Students</span><strong>{{ number_format($studentCount) }}</strong><small>Current student profiles</small><i class="fa fa-graduation-cap" aria-hidden="true"></i></div>
        <div class="members-summary__card"><span>Alumni</span><strong>{{ number_format($alumniCount) }}</strong><small>Graduated member profiles</small><i class="fa fa-users" aria-hidden="true"></i></div>
        <div class="members-summary__card members-summary__card--hint"><span>Total profiles</span><strong>{{ number_format($memberCount) }}</strong><small>Students and alumni in this directory.</small><i class="fa fa-address-card-o" aria-hidden="true"></i></div>
    </section>

    <section class="members-panel" aria-labelledby="community-members-heading">
        <div class="members-panel__head">
            <div><span class="members-section-label">GSF directory</span><h2 id="community-members-heading">All members</h2><p>Search by member name, email or phone number.</p></div>
            <label class="members-search" for="members-search"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search members</span><input id="members-search" type="search" placeholder="Search directory" autocomplete="off"></label>
        </div>
        <div class="members-filters" aria-label="Member filters">
            <label for="user-type-filter"><span>User type</span><select id="user-type-filter"><option value="">All user types</option><option value="0">Students</option><option value="1">Alumni</option></select></label>
            <label for="chapter-filter"><span>Chapter</span><select id="chapter-filter"><option value="">All chapters</option>@foreach($chapters as $chapter)<option value="{{ $chapter->id }}">{{ $chapter->name }}</option>@endforeach</select></label>
            <button type="button" class="members-filter-reset" id="reset-member-filters">Reset filters</button>
        </div>
        <div class="members-table-wrap">
            <table class="members-table" id="users" style="width:100%">
                <thead><tr><th>S/N</th><th>Login ID</th><th>Photo</th><th>Member details</th><th>Standing</th><th>Designation</th><th>Role</th>@if($isAdmin)<th>Source</th>@endif<th>Date created</th><th>Actions</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@section('extra_scripts')
<script>
$(function () {
    var table = $('#users').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ordering: false,
        autoWidth: false,
        pageLength: 100,
        lengthMenu: [[100, 250, 500, 1000, -1], [100, 250, 500, 1000, 'All']],
        dom: "t<'members-table-footer'lip>",
        language: {
            emptyTable: 'No community members have been added yet.',
            zeroRecords: 'No profiles match that search.',
            processing: 'Loading profiles…'
        },
        ajax: {
            url: @json($routes['all']),
            type: 'POST',
            data: function (data) {
                data._token = @json(csrf_token());
                data.user_type = $('#user-type-filter').val();
                data.chapter_id = $('#chapter-filter').val();
            }
        },
        columns: [
            { data: 'S/N' },
            { data: 'family_id' },
            { data: 'avatar' },
            { data: 'details' },
            { data: 'is_graduated' },
            { data: 'designation' },
            { data: 'role' },
            @if($isAdmin){ data: 'source' },@endif
            { data: 'created_at' },
            { data: 'actions' }
        ]
    });
    var searchDelay;
    $('#members-search').on('input', function () {
        var query = this.value;
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function () { table.search(query).draw(); }, 250);
    });
    $('#user-type-filter, #chapter-filter').on('change', function () { table.ajax.reload(null, true); });
    $('#reset-member-filters').on('click', function () {
        $('#user-type-filter, #chapter-filter').val('');
        $('#members-search').val('');
        table.search('').ajax.reload(null, true);
    });
});
</script>
@endsection
