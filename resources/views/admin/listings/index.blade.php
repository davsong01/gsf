@extends('layouts.dashboard')

@section('title', 'Pending Listings')
@section('active')
    <li class="breadcrumb-item">Pending Listings</li>
@endsection

@section('extra_styles')
    <link rel="stylesheet" href="{{ versionedAsset('assets/css/admin-pending-listings.css') }}">
@endsection

@section('content')
<div class="content-body pending-workspace">
    <header class="pending-hero">
        <div class="pending-hero__copy">
            <span class="pending-eyebrow"><i class="fa fa-user-clock" aria-hidden="true"></i> Member approvals</span>
            <h1>Pending listings</h1>
            <p>Review new member submissions and approve, reject, or remove each application.</p>
        </div>
        <div class="pending-hero__count"><span>Awaiting review</span><strong>{{ number_format($pending->count()) }}</strong></div>
    </header>

    @include('includes.alerts')

    <section class="pending-panel" aria-labelledby="pending-table-heading">
        <div class="pending-panel__head">
            <div>
                <span class="pending-section-label">Review queue</span>
                <h2 id="pending-table-heading">Member applications</h2>
                <p>Search names, contact details, or chapter to find a submission.</p>
            </div>
        </div>
        <div class="pending-filters">
            <label class="pending-search" for="pending-search">
                <i class="fa fa-search" aria-hidden="true"></i>
                <span class="sr-only">Search by applicant name, email, or phone</span>
                <input id="pending-search" type="search" placeholder="Search name, email, or phone" autocomplete="off">
            </label>
            <label class="pending-chapter-filter" for="pending-chapter-filter">
                <span>Chapter</span>
                <select id="pending-chapter-filter">
                    <option value="">All chapters</option>
                    @foreach($chapters as $chapter)
                        <option value="{{ $chapter->id }}">{{ $chapter->name }}</option>
                    @endforeach
                    <option value="unavailable">Chapter unavailable</option>
                </select>
            </label>
            <button type="button" class="pending-filter-reset" id="pending-filter-reset">Reset filters</button>
        </div>

        <div class="pending-table-wrap">
            <form id="pending-bulk-form" action="{{ route('users.pending.bulk-action') }}" method="POST">
                @csrf
                <div class="pending-bulkbar">
                    <label class="pending-select-all"><input type="checkbox" id="pending-select-all"> <span>Select all</span></label>
                    <div class="pending-bulkbar__controls">
                        <span id="pending-selected-count">0 selected</span>
                        <select name="action" id="pending-bulk-action" aria-label="Bulk action" required>
                            <option value="">Choose bulk action</option>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button type="submit" class="pending-bulk-apply" disabled>Apply</button>
                    </div>
                </div>
            <table class="pending-table" id="pending-listings" style="width:100%">
                <thead>
                    <tr><th class="pending-check-column"><input type="checkbox" id="pending-page-select" aria-label="Select visible applications"></th><th>S/N</th><th>Applicant</th><th>Contact</th><th>Submitted</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($pending as $user)
                        @php
                            $applicantName = trim((string) ($user->name ?? '')) ?: 'Applicant name unavailable';
                            if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])[A-Za-z0-9]{18,}$/', $applicantName)) {
                                $applicantName = 'Applicant name unavailable';
                            }
                            $avatarPath = $user->passport ? (\Illuminate\Support\Str::startsWith($user->passport, ['http://', 'https://']) ? $user->passport : asset($user->passport)) : null;
                        @endphp
                        <tr data-chapter-id="{{ $user->chapter ?: 'unavailable' }}" data-applicant-name="{{ $applicantName }}" data-applicant-email="{{ $user->email }}" data-applicant-phone="{{ $user->phone }}">
                            <td class="pending-check-column"><input type="checkbox" class="pending-row-check" name="selected_ids[]" value="{{ $user->id }}" aria-label="Select {{ $applicantName }}"></td>
                            <td class="pending-index">{{ $loop->iteration }}</td>
                            <td>
                                <div class="pending-person">
                                    @if($avatarPath)
                                        <img src="{{ $avatarPath }}" alt="" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                                        <span class="pending-avatar-fallback" hidden>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($applicantName, 0, 1)) }}</span>
                                    @else
                                        <span class="pending-avatar-fallback">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($applicantName, 0, 1)) }}</span>
                                    @endif
                                    <span><strong>{{ $applicantName }}</strong><small>{{ $user->campus?->name ?? 'Chapter unavailable' }}</small><small>{{ $user->gender ?? $user->sex ?? 'Gender not provided' }}</small></span>
                                </div>
                            </td>
                            <td class="pending-contact"><a href="mailto:{{ $user->email }}">{{ $user->email ?: 'No email provided' }}</a><a href="tel:{{ $user->phone }}">{{ $user->phone ?: 'No phone provided' }}</a></td>
                            <td class="pending-date">{{ $user->created_at?->format('d M Y') ?? '—' }}<small>{{ $user->created_at?->format('g:i A') }}</small></td>
                            <td>
                                <div class="pending-actions">
                                    <button type="button" class="pending-action pending-action--view" data-toggle="modal" data-target="#application-{{ $user->id }}"><i class="fa fa-eye" aria-hidden="true"></i><span>View</span></button>
                                    <a class="pending-action pending-action--approve" href="{{ route('user.single.approve', $user->id) }}" onclick="return confirm('Approve this member application?')"><i class="fa fa-check" aria-hidden="true"></i><span>Approve</span></a>
                                    <a class="pending-action pending-action--reject" href="{{ route('user.single.reject', $user->id) }}" onclick="return confirm('Reject this member application?')"><i class="fa fa-times" aria-hidden="true"></i><span>Reject</span></a>
                                    <a class="pending-action pending-action--delete" href="{{ route('user.single.delete', $user->id) }}" onclick="return confirm('Delete this application permanently?')" aria-label="Delete application"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </form>
        </div>
    </section>

    @foreach($pending as $user)
        @php
            $modalApplicantName = trim((string) ($user->name ?? '')) ?: 'Applicant name unavailable';
            if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])[A-Za-z0-9]{18,}$/', $modalApplicantName)) {
                $modalApplicantName = 'Applicant name unavailable';
            }
        @endphp
        <div class="modal fade pending-modal" id="application-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="application-title-{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div><span class="pending-section-label">Application profile</span><h2 class="modal-title" id="application-title-{{ $user->id }}">{{ $modalApplicantName }}</h2></div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="pending-detail-grid">
                            <div><span>Email</span><strong>{{ $user->email ?: 'Not provided' }}</strong></div>
                            <div><span>Phone</span><strong>{{ $user->phone ?: 'Not provided' }}</strong></div>
                            <div><span>Gender</span><strong>{{ $user->gender ?? $user->sex ?? 'Not provided' }}</strong></div>
                            <div><span>Chapter</span><strong>{{ $user->campus?->name ?? 'Chapter unavailable' }}</strong></div>
                            <div><span>Program</span><strong>{{ $user->program ?: 'Not provided' }}</strong></div>
                            <div><span>Course</span><strong>{{ $user->course ?: 'Not provided' }}</strong></div>
                            <div><span>Matriculation year</span><strong>{{ $user->matriculation_year ?: 'Not provided' }}</strong></div>
                            <div><span>Graduation year</span><strong>{{ $user->graduation_year ?: 'Not provided' }}</strong></div>
                            <div><span>Open to work</span><strong>{{ isset($user->open_to_work) ? ($user->open_to_work ? 'Yes' : 'No') : 'Not provided' }}</strong></div>
                            <div><span>Submitted</span><strong>{{ $user->created_at?->format('d M Y, g:i A') ?? '—' }}</strong></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="pending-action pending-action--approve" href="{{ route('user.single.approve', $user->id) }}" onclick="return confirm('Approve this member application?')"><i class="fa fa-check" aria-hidden="true"></i> Approve</a>
                        <a class="pending-action pending-action--reject" href="{{ route('user.single.reject', $user->id) }}" onclick="return confirm('Reject this member application?')"><i class="fa fa-times" aria-hidden="true"></i> Reject</a>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('extra_scripts')
<script>
$(function () {
    // Keep the modal outside the dashboard's positioned content wrappers.
    $(document).on('show.bs.modal', '.pending-modal', function () {
        $(this).appendTo(document.body);
    });

    var table = $('#pending-listings').DataTable({
        processing: true,
        ordering: false,
        autoWidth: false,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'All']],
        dom: "t<'pending-table-footer'lip>",
        columnDefs: [{ targets: [0, 1, 5], orderable: false, searchable: false }],
        language: {
            emptyTable: 'There are no applications waiting for review.',
            zeroRecords: 'No applications match your search.',
            processing: 'Loading applications…'
        }
    });
    var delay;
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'pending-listings') return true;
        var row = $(table.row(dataIndex).node());
        var query = $.trim($('#pending-search').val()).toLowerCase();
        var chapterId = $('#pending-chapter-filter').val();
        var searchable = [row.data('applicant-name'), row.data('applicant-email'), row.data('applicant-phone')].join(' ').toLowerCase();
        var matchesSearch = !query || searchable.indexOf(query) !== -1;
        var matchesChapter = !chapterId || String(row.data('chapter-id')) === chapterId;
        return matchesSearch && matchesChapter;
    });
    $('#pending-search').on('input', function () {
        clearTimeout(delay);
        delay = setTimeout(function () { table.draw(); }, 200);
    });
    $('#pending-chapter-filter').on('change', function () { table.draw(); });
    $('#pending-filter-reset').on('click', function () {
        $('#pending-search').val('');
        $('#pending-chapter-filter').val('');
        table.draw();
    });
    function updatePendingSelection() {
        var selected = $('.pending-row-check:checked').length;
        $('#pending-selected-count').text(selected + (selected === 1 ? ' selected' : ' selected'));
        $('#pending-bulk-action').prop('disabled', selected === 0);
        $('.pending-bulk-apply').prop('disabled', selected === 0);
        $('#pending-select-all').prop('checked', selected > 0 && selected === $('.pending-row-check').length);
        var visibleChecks = table.rows({ search: 'applied', page: 'current' }).nodes().to$().find('.pending-row-check');
        $('#pending-page-select').prop('checked', visibleChecks.length > 0 && visibleChecks.filter(':checked').length === visibleChecks.length);
    }
    $('#pending-select-all').on('change', function () {
        $('.pending-row-check').prop('checked', this.checked);
        updatePendingSelection();
    });
    $('#pending-page-select').on('change', function () {
        table.rows({ search: 'applied', page: 'current' }).nodes().to$().find('.pending-row-check').prop('checked', this.checked);
        updatePendingSelection();
    });
    $('#pending-listings').on('change', '.pending-row-check', updatePendingSelection);
    table.on('draw', updatePendingSelection);
    $('#pending-bulk-form').on('submit', function (event) {
        if (!$('.pending-row-check:checked').length || !$('#pending-bulk-action').val()) {
            event.preventDefault();
            return;
        }
        var action = $('#pending-bulk-action').val();
        var verb = { approve: 'Approve', reject: 'Reject', delete: 'Delete' }[action] || 'Apply action to';
        if (!window.confirm(verb + ' ' + $('.pending-row-check:checked').length + ' selected application(s)?')) event.preventDefault();
    });
});
</script>
@endsection
