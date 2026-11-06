@extends('layouts.stakeholderdashboard')

@section('title', $isAlumni ? 'Chapter Alumni' : 'Chapter Members')
@section('active')<li class="breadcrumb-item">{{ $isAlumni ? 'Chapter Alumni' : 'Chapter Members' }}</li>@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/stakeholder-members.css') }}">
@endsection

@section('content')
<div class="content-body members-workspace">
    <header class="members-hero">
        <div class="members-hero__copy">
            <span class="members-eyebrow"><i class="fa fa-users" aria-hidden="true"></i> {{ $chapter?->name ?: 'Campus fellowship' }}</span>
            <h1>{{ $isAlumni ? 'Chapter alumni' : 'Chapter members' }}</h1>
            <p>{{ $isAlumni ? 'Stay connected with graduates who remain part of your fellowship family.' : 'Keep your campus directory current and your student community connected.' }}</p>
        </div>
        <div class="members-hero__actions">
            @unless($isAlumni)
                @if($canUploadRecordBook ?? false)
                    <a class="members-button members-button--light" href="{{ $routes['recordBook'] }}" title="Upload your record book, also known as the Book of Life, and we will scan it and extract the records." data-toggle="tooltip" data-placement="bottom" aria-label="Upload your record book, also known as the Book of Life, and we will scan it and extract the records."><i class="fa fa-book" aria-hidden="true"></i> Upload Record Book</a>
                @endif
                <a class="members-button members-button--light" href="{{ $routes['import'] }}"><i class="fa fa-upload" aria-hidden="true"></i> Import members</a>
            @endunless
            <a class="members-button members-button--gold" href="{{ $routes['create'] }}"><i class="fa fa-plus" aria-hidden="true"></i> Add {{ $isAlumni ? 'alumnus' : 'member' }}</a>
        </div>
    </header>

    <section class="members-summary" aria-label="{{ $isAlumni ? 'Alumni' : 'Member' }} overview">
        <div class="members-summary__card"><span>{{ $isAlumni ? 'Alumni profiles' : 'Member profiles' }}</span><strong>{{ number_format($memberCount) }}</strong><small>Registered with this campus</small><i class="fa {{ $isAlumni ? 'fa-graduation-cap' : 'fa-users' }}" aria-hidden="true"></i></div>
        <div class="members-summary__card"><span>Active accounts</span><strong>{{ number_format($activeCount) }}</strong><small>Currently active in the portal</small><i class="fa fa-check-circle-o" aria-hidden="true"></i></div>
        <div class="members-summary__card members-summary__card--hint"><span>Keep the directory current</span><strong>Share their story</strong><small>Update contact and campus details when they change.</small><i class="fa fa-address-card-o" aria-hidden="true"></i></div>
    </section>

    <section class="members-panel" aria-labelledby="members-list-heading">
        <div class="members-panel__head">
            <div><span class="members-section-label">Campus directory</span><h2 id="members-list-heading">{{ $isAlumni ? 'Your alumni' : 'Your members' }}</h2><p>Search by name, email or phone number.</p></div>
            <label class="members-search" for="members-search"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search {{ $isAlumni ? 'alumni' : 'members' }}</span><input id="members-search" type="search" placeholder="Search directory" autocomplete="off"></label>
        </div>
        <div class="members-table-wrap">
            <table class="members-table" id="stakeholder-members-table" style="width:100%">
                <thead><tr><th>S/N</th><th>Login ID</th><th>Photo</th><th>Member details</th><th>Standing</th><th>Designation</th><th>Role</th><th>Action</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@section('extra_scripts')
<script>
$(function () {
    var table = $('#stakeholder-members-table').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ordering: false,
        autoWidth: false,
        pageLength: 20,
        lengthMenu: [[20, 30, 50, 100], [20, 30, 50, 100]],
        dom: "t<'members-table-footer'lip>",
        language: {
            emptyTable: 'No {{ $isAlumni ? 'alumni' : 'members' }} have been added yet.',
            zeroRecords: 'No profiles match that search.',
            processing: 'Loading profiles…'
        },
        ajax: {
            url: @json($routes['all']),
            type: 'POST',
            data: { _token: @json(csrf_token()), isAdmin: false }
        },
        columns: [
            { data: 'S/N' },
            { data: 'family_id' },
            { data: 'avatar' },
            { data: 'details' },
            { data: 'is_graduated' },
            { data: 'designation' },
            { data: 'role' },
            { data: 'actions' }
        ]
    });
    var searchDelay;
    $('#members-search').on('input', function () {
        var query = this.value;
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function () { table.search(query).draw(); }, 250);
    });
});
</script>
@endsection


