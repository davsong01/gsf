@extends('layouts.dashboard')

@php($isEdit = isset($field))

@section('title', $isEdit ? 'Edit Field' : 'Create Field')
@section('item')
<li class="breadcrumb-item"><a href="{{ route('fields.index') }}">Fields</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">{{ $isEdit ? 'Update Field' : 'Create Field' }}</li>
@endsection

@section('content')
<style>
    .location-workspace { max-width:1440px; margin:0 auto; padding-bottom:30px; color:#172b4d; }
    .location-overview { position:relative; overflow:hidden; border:0; border-radius:20px; color:#fff; background:linear-gradient(118deg,#102a52 0%,#164c7d 62%,#147d91 100%); box-shadow:0 16px 38px rgba(20,48,83,.16); }
    .location-overview::after { position:absolute; width:310px; height:310px; right:-100px; top:-185px; border:1px solid rgba(255,255,255,.13); border-radius:50%; box-shadow:0 0 0 38px rgba(255,255,255,.035),0 0 0 78px rgba(255,255,255,.025); content:""; pointer-events:none; }
    .location-overview .card-body { position:relative; z-index:1; padding:28px; }
    .location-overview__top { display:flex; justify-content:space-between; align-items:flex-start; gap:22px; margin-bottom:24px; }
    .location-overview__eyebrow { margin-bottom:7px; color:#9fd7e7; font-size:10px; font-weight:800; letter-spacing:.16em; text-transform:uppercase; }
    .location-overview h2 { margin:0 0 7px; color:#fff; font-size:clamp(23px,3vw,32px); font-weight:700; letter-spacing:-.03em; }
    .location-overview__location { color:rgba(255,255,255,.7); font-size:13px; }
    .location-overview__actions { display:flex; flex-wrap:wrap; gap:9px; }
    .location-overview__actions .btn { min-height:42px; padding:10px 14px; border-radius:10px; font-size:12px; font-weight:700; }
    .location-overview__actions .btn-light { color:#12345c; }
    .location-overview__grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
    .location-overview__tile { min-width:0; min-height:92px; padding:15px 16px; border:1px solid rgba(255,255,255,.14); border-radius:13px; background:rgba(255,255,255,.085); }
    .location-overview__tile-label { display:block; margin-bottom:9px; color:rgba(255,255,255,.67); font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
    .location-overview__tile-value { display:block; color:#fff; font-size:15px; font-weight:700; line-height:1.35; overflow-wrap:anywhere; }
    .location-overview__tile-value--count { font-size:26px; letter-spacing:-.04em; }
    .location-overview__tile a { color:#fff; text-decoration:underline; text-decoration-color:rgba(255,255,255,.4); text-underline-offset:3px; }
    .location-overview__tile .is-unassigned { color:rgba(255,255,255,.55); font-size:13px; font-weight:500; }
    .location-overview__pastor { display:flex; align-items:center; gap:10px; min-width:0; }
    .location-overview__pastor img { width:38px!important; height:38px!important; border:1px solid rgba(255,255,255,.25); }
    .location-form-card { overflow:hidden; border:1px solid #e5ebf2; border-radius:18px; box-shadow:0 10px 28px rgba(26,52,80,.06); }
    .location-form__header { display:flex; justify-content:space-between; align-items:center; gap:16px; padding:22px 26px; border-bottom:1px solid #edf1f5; }
    .location-form__header h3 { margin:0 0 4px; color:#183251; font-size:18px; font-weight:700; }
    .location-form__header p { margin:0; color:#8090a3; font-size:12px; }
    .location-form__body { padding:25px 26px 10px; }
    .location-form__body .form-group { margin-bottom:20px; }
    .location-form__body label { display:block; margin-bottom:7px; color:#344b66; font-size:12px; font-weight:700; }
    .location-form__body .form-control { min-height:42px; padding:10px 12px; border:1px solid #dce4ed; border-radius:9px; color:#243b53; background-color:#fff; box-shadow:none; font-size:13px; }
    .location-form__body .form-control:focus { border-color:#2787a5; box-shadow:0 0 0 3px rgba(39,135,165,.12); }
    .location-form__body .form-text { margin-top:6px; color:#8492a5; font-size:11px; }
    .location-form__footer { display:flex; justify-content:flex-end; gap:10px; padding:17px 26px; border-top:1px solid #edf1f5; background:#fbfcfd; }
    .location-form__footer .btn { min-width:145px; min-height:42px; border-radius:9px; font-size:12px; font-weight:700; }
    @media(max-width:767.98px) { .location-overview .card-body{padding:21px}.location-overview__top{flex-direction:column}.location-overview__grid{grid-template-columns:1fr 1fr}.location-form__header,.location-form__body{padding-right:18px;padding-left:18px}.location-form__footer{padding-right:18px;padding-left:18px} }
    @media(max-width:420px) { .location-overview__grid{grid-template-columns:1fr}.location-overview__actions{width:100%}.location-overview__actions .btn{flex:1 1 100%}.location-form__footer{flex-direction:column-reverse}.location-form__footer .btn{width:100%} }
</style>

<div class="content-body location-workspace">
    @include('includes.alerts')
    @if($isEdit)
        <section class="card location-overview mb-2">
            <div class="card-content"><div class="card-body">
                <div class="location-overview__top">
                    <div><div class="location-overview__eyebrow">Field workspace</div><h2>{{ $field->name }}</h2><div class="location-overview__location">National field administration</div></div>
                    <div class="location-overview__actions"><a href="{{ route('fields.index') }}" class="btn btn-light"><i class="fa fa-arrow-left mr-50" aria-hidden="true"></i>All fields</a></div>
                </div>
                <div class="location-overview__grid">
                    <div class="location-overview__tile"><span class="location-overview__tile-label">Zones</span><strong class="location-overview__tile-value location-overview__tile-value--count">{{ number_format($field->zones->count()) }}</strong></div>
                    <div class="location-overview__tile"><span class="location-overview__tile-label">Chapters</span><strong class="location-overview__tile-value location-overview__tile-value--count">{{ number_format($field->chapters->count()) }}</strong></div>
                    <div class="location-overview__tile"><span class="location-overview__tile-label">Field pastor</span>@if($field->fieldCord)<div class="location-overview__pastor">{!! renderAvatar($field->fieldCord, 38) !!}<a class="location-overview__tile-value" href="{{ route('stakeholderpersonnel.edit', $field->fieldCord->id) }}">{{ $field->fieldCord->name }}</a></div>@else<span class="location-overview__tile-value is-unassigned">Not assigned</span>@endif</div>
                </div>
            </div></div>
        </section>
    @endif

    <section id="basic-input">
        <div class="card location-form-card">
            <div class="card-content">
                <div class="location-form__header"><div><h3>{{ $isEdit ? 'Field details' : 'New field' }}</h3><p>Set the field name and assign its field pastor.</p></div><span class="text-muted small">{{ $isEdit ? 'ID '.$field->id : 'Setup' }}</span></div>
                <form action="{{ $isEdit ? route('fields.update', $field->id) : route('fields.store') }}" method="POST">
                    @csrf
                    @if($isEdit) @method('PATCH') @endif
                    <div class="location-form__body"><div class="row">
                        <fieldset class="form-group col-md-6"><label for="name">Field name</label><input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $field->name ?? '') }}" placeholder="Enter field name" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</fieldset>
                        <fieldset class="form-group col-md-6"><label for="stakeholder_id">Field pastor</label><select class="form-control @error('stakeholder_id') is-invalid @enderror" name="stakeholder_id" id="stakeholder_id" required><option value="">Select a field pastor</option>@foreach($pastors as $pastor)<option value="{{ $pastor->id }}" {{ (int) old('stakeholder_id', $field->fieldCord->id ?? null) === (int) $pastor->id ? 'selected' : '' }}>{{ $pastor->name }}{{ $pastor->email ? ' · '.$pastor->email : '' }}</option>@endforeach</select><small class="form-text">Choose the person responsible for this field.</small>@error('stakeholder_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</fieldset>
                    </div></div>
                    <div class="location-form__footer"><a href="{{ route('fields.index') }}" class="btn btn-light">Cancel</a><button class="btn btn-primary" type="submit">{{ $isEdit ? 'Save field changes' : 'Create field' }}</button></div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
