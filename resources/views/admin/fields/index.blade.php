@extends('layouts.dashboard')
@section('title', 'Fields')
@section('active')
<li class="breadcrumb-item">Fields</li>
@endsection

@section('content')
<style>
    .location-index { max-width:1500px; margin:0 auto; color:#172b4d; }
    .location-index__card { overflow:hidden; border:1px solid #e5ebf2; border-radius:18px; box-shadow:0 10px 28px rgba(26,52,80,.055); }
    .location-index__header { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:24px 26px 20px; border-bottom:1px solid #edf1f5; }
    .location-index__eyebrow { margin-bottom:6px; color:#21809a; font-size:10px; font-weight:850; letter-spacing:.15em; text-transform:uppercase; }
    .location-index__header h2 { margin:0 0 5px; color:#183251; font-family:Georgia,serif; font-size:25px; font-weight:650; }
    .location-index__header p { margin:0; color:#8090a3; font-size:12px; }
    .location-index__header .btn { min-height:40px; padding:10px 14px; border-radius:9px; font-size:12px; font-weight:750; white-space:nowrap; }
    .location-index__table { border-collapse:separate; border-spacing:0; margin:0; }
    .location-index__table thead th { padding:13px 15px; border-bottom:1px solid #edf1f5; color:#718096; background:#fbfcfd; font-size:10px; font-weight:850; letter-spacing:.07em; text-transform:uppercase; white-space:nowrap; }
    .location-index__table tbody td { padding:14px 15px; border-bottom:1px solid #f0f3f6; color:#596b80; font-size:12px; vertical-align:middle; }
    .location-index__table tbody tr { transition:background .15s; }
    .location-index__table tbody tr:hover { background:#f8fafc; }
    .location-index__table tbody tr:last-child td { border-bottom:0; }
    .location-index__number { color:#96a2b0!important; font-variant-numeric:tabular-nums; }
    .location-index__name { display:block; color:#1a3150; font-size:13px; font-weight:750; }
    .location-index__subtext { display:block; margin-top:4px; color:#8a97a7; font-size:10px; }
    .location-index__person { display:flex; align-items:center; gap:9px; min-width:180px; }
    .location-index__person > img { width:34px!important; height:34px!important; border:1px solid #e4ebf1; }
    .location-index__person-name { display:block; color:#35526d; font-size:11px; font-weight:700; }
    .location-index__person-email { display:block; max-width:230px; margin-top:3px; overflow:hidden; color:#8a97a7; font-size:10px; text-overflow:ellipsis; white-space:nowrap; }
    .location-index__muted { color:#9aa5b2; }
    .location-index__count { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:28px; padding:0 8px; border:1px solid #e4ebf1; border-radius:8px; color:#284b6d; background:#f7fafc; font-size:12px; font-weight:800; }
    .location-index__actions { display:flex; justify-content:flex-end; gap:5px; white-space:nowrap; }
    .location-index__action { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border:1px solid transparent; border-radius:8px; color:#42718d; background:transparent; }
    .location-index__action:hover { border-color:#e2eaf0; color:#174f78; background:#f1f6f9; }
    .location-index__action--delete:hover { border-color:#f3dddd; color:#bd4d4d; background:#fff5f5; }
    .location-index__empty { padding:38px 20px!important; color:#8794a4!important; text-align:center; }
    .location-index__empty i { display:block; margin-bottom:9px; color:#a8bac9; font-size:23px; }
    @media(max-width:650px) { .location-index__header { align-items:flex-start; flex-direction:column; padding:20px; } .location-index__header .btn { width:100%; justify-content:center; } }
</style>

<div class="content-body location-index">
    @include('includes.alerts')
    <section class="location-index__card" id="basic-datatable">
        <header class="location-index__header">
            <div><div class="location-index__eyebrow">GSF structure</div><h2>All Fields</h2><p>Manage field pastors and review zone and chapter coverage.</p></div>
            <a href="{{ route('fields.create') }}" class="btn btn-primary"><i class="bx bx-plus mr-50" aria-hidden="true"></i> Add new field</a>
        </header>
        <div class="card-content"><div class="card-body p-0"><div class="table-responsive">
            <table class="table zero-configuration location-index__table">
                <thead><tr><th class="pl-4" style="width:64px">S/N</th><th>Field</th><th>Field pastor</th><th>Zones</th><th>Chapters</th><th class="text-right pr-4" style="min-width:95px">Actions</th></tr></thead>
                <tbody>
                    @forelse($fields as $field)
                        <tr>
                            <td class="pl-4 location-index__number">{{ $loop->iteration }}</td>
                            <td><span class="location-index__name">{{ $field->name }}</span><span class="location-index__subtext">Field #{{ $field->id }}</span></td>
                            <td>
                                @if($field->fieldCord)
                                    <div class="location-index__person">{!! renderAvatar($field->fieldCord, 34) !!}<span><a class="location-index__person-name" href="{{ route('stakeholderpersonnel.edit', $field->fieldCord->id) }}">{{ $field->fieldCord->name }}</a><span class="location-index__person-email">{{ $field->fieldCord->email ?: 'No email on file' }}</span></span></div>
                                @else<span class="location-index__muted">Not assigned</span>@endif
                            </td>
                            <td><span class="location-index__count">{{ $field->zones->count() }}</span></td>
                            <td><span class="location-index__count">{{ $field->chapters->count() }}</span></td>
                            <td class="text-right pr-4"><div class="location-index__actions"><a class="location-index__action" data-toggle="tooltip" title="View or update field" aria-label="Edit {{ $field->name }}" href="{{ route('fields.edit', $field->id) }}"><i class="fa fa-edit"></i></a><a class="location-index__action location-index__action--delete" data-toggle="tooltip" onclick="return confirm('Are you really sure you want to delete this field?');" title="Delete field" aria-label="Delete {{ $field->name }}" href="{{ route('fields.delete', $field->id) }}"><i class="fa fa-trash"></i></a></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="location-index__empty"><i class="fa fa-sitemap" aria-hidden="true"></i>No fields yet. Add a field to start organizing zones.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div></div>
    </section>
</div>
@endsection
