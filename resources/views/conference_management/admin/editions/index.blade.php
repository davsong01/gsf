@extends('layouts.dashboard')

@section('title', 'Conference Management')
@section('active')
<li class="breadcrumb-item">Conference Management</li>
@endsection

@section('content')
<style>
    .conference-hub { max-width: 1600px; margin: 0 auto; color: #24314a; }
    .conference-hub__hero { position: relative; display: flex; align-items: center; justify-content: space-between; gap: 24px; overflow: hidden; margin-bottom: 24px; padding: 30px 34px; border-radius: 20px; color: #fff; background: linear-gradient(120deg, #091a3d 0%, #153f78 68%, #2366b1 100%); box-shadow: 0 18px 42px rgba(15,39,77,.18); }
    .conference-hub__hero::after { content: ''; position: absolute; right: -70px; bottom: -110px; width: 280px; height: 280px; border: 1px solid rgba(255,255,255,.13); border-radius: 50%; box-shadow: 0 0 0 42px rgba(255,255,255,.035), 0 0 0 86px rgba(255,255,255,.025); }
    .conference-hub__hero > * { position: relative; z-index: 1; }
    .conference-hub__eyebrow { margin-bottom: 7px; color: #e2c46f; font-size: 10px; font-weight: 850; letter-spacing: .16em; text-transform: uppercase; }
    .conference-hub__hero h2 { margin: 0 0 7px; color: #fff; font-family: Georgia, serif; font-size: 31px; font-weight: 500; letter-spacing: -.025em; }
    .conference-hub__hero p { max-width: 650px; margin: 0; color: rgba(255,255,255,.72); font-size: 14px; line-height: 1.7; }
    .conference-hub__add { flex: 0 0 auto; padding: 11px 17px; border: 0; border-radius: 10px; color: #12274d; background: #e2c46f; font-size: 13px; font-weight: 800; box-shadow: 0 8px 20px rgba(0,0,0,.13); }
    .conference-hub__add:hover { color: #12274d; background: #f0d98f; }
    .conference-hub__panel { overflow: hidden; border: 1px solid #e2e8f0; border-radius: 18px; background: #fff; box-shadow: 0 12px 34px rgba(15,39,77,.065); }
    .conference-hub__panel-head { padding: 22px 25px; border-bottom: 1px solid #edf1f5; }
    .conference-hub__panel-head h3 { margin: 0 0 4px; color: #12274d; font-family: Georgia, serif; font-size: 21px; font-weight: 600; }
    .conference-hub__panel-head p { margin: 0; color: #7a879b; font-size: 12px; }
    .conference-hub__table { margin: 0; }
    .conference-hub__table thead th { padding: 13px 16px; border: 0; color: #627087; background: #f7f9fc; font-size: 10px; font-weight: 850; letter-spacing: .08em; text-transform: uppercase; }
    .conference-hub__table tbody td { padding: 17px 16px; border-top: 1px solid #edf1f5; vertical-align: middle; color: #34435d; font-size: 13px; }
    .conference-hub__number { display: grid; place-items: center; width: 31px; height: 31px; border-radius: 9px; color: #174a83; background: #edf5fc; font-weight: 800; }
    .conference-hub__ministry strong, .conference-hub__theme strong { display: block; color: #172641; font-size: 14px; }
    .conference-hub__ministry span { color: #7a879b; font-size: 11px; }
    .conference-hub__status { display: inline-flex; margin-top: 6px; padding: 4px 8px; border-radius: 999px; color: #8a4d14; background: #fff3dc; font-size: 9px; font-weight: 850; letter-spacing: .05em; text-transform: uppercase; }
    .conference-hub__status.is-active { color: #087052; background: #e5f6ef; }
    .conference-hub__dates strong { display: block; margin-bottom: 4px; color: #172641; font-size: 12px; }
    .conference-hub__dates span { display: block; color: #7a879b; font-size: 10px; line-height: 1.65; }
    .conference-hub__actions { display: flex; flex-wrap: wrap; gap: 7px; }
    .conference-hub__actions .btn { margin: 0 !important; padding: 8px 11px; border-radius: 8px; font-size: 11px; font-weight: 750; }
    .conference-hub__actions .btn i { position: static !important; }
    .conference-hub__empty { padding: 50px 20px !important; color: #7a879b !important; text-align: center; }
    .conference-hub .dataTables_wrapper { padding: 0 20px 18px; }
    .conference-hub .dataTables_wrapper .table-responsive { margin-inline: -20px; }
    @media (max-width: 767px) {
        .conference-hub__hero { align-items: flex-start; flex-direction: column; padding: 24px; border-radius: 15px; }
        .conference-hub__hero h2 { font-size: 26px; }
        .conference-hub__add { width: 100%; text-align: center; }
        .conference-hub__table { min-width: 780px; }
    }
</style>

<div class="content-body conference-hub">
    <section class="conference-hub__hero">
        <div>
            <div class="conference-hub__eyebrow">GSF Conference Administration</div>
            <h2>Conference Management</h2>
            <p>Manage conference editions, registration periods, participants, transactions and programme settings from one place.</p>
        </div>
        @if(auth()->user()->conference_role == 'superadmin')
            <a href="{{ route('conferenceeditions.create') }}" class="conference-hub__add"><i class="fa fa-plus mr-1"></i> New conference edition</a>
        @endif
    </section>

    @include('includes.alerts')

    <section class="conference-hub__panel">
        <header class="conference-hub__panel-head">
            <h3>Conference editions</h3>
            <p>Select an edition to open its dashboard and management tools.</p>
        </header>
        <div class="table-responsive">
            <table class="table zero-configuration conference-hub__table">
                <thead><tr><th>#</th><th>Ministry</th><th>Conference</th><th>Dates</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($editions as $edition)
                        <tr>
                            <td><span class="conference-hub__number">{{ $loop->iteration }}</span></td>
                            <td class="conference-hub__ministry"><strong>{{ optional($edition->ministry)->name ?? 'Not assigned' }}</strong><span>{{ optional($edition->ministry)->code ?? '—' }}</span></td>
                            <td class="conference-hub__theme"><strong>{{ $edition->conference_theme }}</strong><span class="conference-hub__status {{ $edition->status === 'active' ? 'is-active' : '' }}">{{ $edition->status }}</span></td>
                            <td class="conference-hub__dates">
                                <strong>{{ $edition->start_date ? \Carbon\Carbon::parse($edition->start_date)->format('d M Y') : 'Date pending' }} — {{ $edition->end_date ? \Carbon\Carbon::parse($edition->end_date)->format('d M Y') : 'Date pending' }}</strong>
                                <span>Registration: {{ $edition->start_registration ? \Carbon\Carbon::parse($edition->start_registration)->format('d M Y') : '—' }} to {{ $edition->close_registration ? \Carbon\Carbon::parse($edition->close_registration)->format('d M Y') : '—' }}</span>
                            </td>
                            <td><div class="conference-hub__actions">
                                <a href="{{ route('show.conference.edition', $edition->id) }}" class="btn btn-primary"><i class="fa fa-eye mr-1"></i> Open dashboard</a>
                                @if(auth()->user()->conference_role == 'superadmin')
                                    <a href="{{ route('clone.conference.edition', $edition->id) }}" class="btn btn-outline-secondary"><i class="fa fa-copy mr-1"></i> Clone</a>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="conference-hub__empty">No conference editions are available yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
