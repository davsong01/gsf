@extends('layouts.dashboard')
@section('title', isset($chapter) ? 'Update Chapter' : 'Add Chapter')

@section('item')
<li class="breadcrumb-item"> <a href="{{ route('chapters.index') }}">Chapters</a></li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($chapter) ? (auth()->user()->isAdmin() ? 'Update Chapter' : $chapter->name) : 'Add Chapter' }}</li>
@endsection

@section('extra_styles')
<style>
    .chapter-workspace { max-width: 1440px; margin: 0 auto; padding-bottom: 30px; color: #172b4d; }
    .chapter-overview { position: relative; overflow: hidden; border: 0; border-radius: 20px; color: #fff; background: linear-gradient(118deg, #102a52 0%, #164c7d 62%, #147d91 100%); box-shadow: 0 16px 38px rgba(20, 48, 83, .16); }
    .chapter-overview::after { position: absolute; width: 310px; height: 310px; right: -100px; top: -185px; border: 1px solid rgba(255,255,255,.13); border-radius: 50%; box-shadow: 0 0 0 38px rgba(255,255,255,.035), 0 0 0 78px rgba(255,255,255,.025); content: ""; pointer-events: none; }
    .chapter-overview .card-body { position: relative; z-index: 1; padding: 28px; }
    .chapter-overview__top { display: flex; justify-content: space-between; align-items: flex-start; gap: 22px; margin-bottom: 25px; }
    .chapter-overview__eyebrow { margin-bottom: 7px; color: #9fd7e7; font-size: 10px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
    .chapter-overview h2 { margin: 0 0 7px; color: #fff; font-size: clamp(23px, 3vw, 32px); font-weight: 700; letter-spacing: -.03em; }
    .chapter-overview__location { color: rgba(255,255,255,.7); font-size: 13px; }
    .chapter-overview__slug { display: inline-flex; margin-top: 12px; padding: 5px 10px; border: 1px solid rgba(255,255,255,.2); border-radius: 999px; color: #e4f5fa; background: rgba(255,255,255,.08); font-size: 11px; }
    .chapter-overview__actions { display: flex; flex-wrap: wrap; gap: 9px; }
    .chapter-overview__actions .btn { min-height: 42px; padding: 10px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; }
    .chapter-overview__actions .btn-light { color: #12345c; }
    .chapter-overview__actions .btn-outline-light:hover { color: #12345c; }
    .chapter-overview__grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; }
    .chapter-overview__tile { min-width: 0; min-height: 92px; padding: 15px 16px; border: 1px solid rgba(255,255,255,.14); border-radius: 13px; background: rgba(255,255,255,.085); }
    .chapter-overview__tile--metric { min-height: 76px; padding: 11px 13px; }
    .chapter-overview__tile--stakeholder { grid-column: span 2; }
    .chapter-overview__tile-label { display: block; margin-bottom: 9px; color: rgba(255,255,255,.67); font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .chapter-overview__tile-value { display: block; color: #fff; font-size: 15px; font-weight: 700; line-height: 1.35; }
    .chapter-overview__tile-value--count { font-size: 26px; letter-spacing: -.04em; }
    .chapter-overview__tile a { color: #fff; text-decoration: underline; text-decoration-color: rgba(255,255,255,.4); text-underline-offset: 3px; }
    .chapter-overview__tile .is-unassigned { color: rgba(255,255,255,.52); font-size: 13px; font-weight: 500; }
    .chapter-person { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .chapter-person img, .chapter-person .avatar-initials { width: 42px !important; height: 42px !important; border: 1px solid rgba(255,255,255,.28); }
    .chapter-person__details { min-width: 0; }
    .chapter-person__copy-row { display: flex; align-items: center; gap: 5px; width: 100%; min-width: 0; }
    .chapter-person__copy-row a { display: block; flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .chapter-person__copy-row > small { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .chapter-person__details small { display: block; overflow: hidden; margin-top: 3px; color: rgba(255,255,255,.67); font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
    .chapter-contact-details { display: grid; gap: 3px; margin-top: 7px; }
    .chapter-contact-details .chapter-person__copy-row small { flex: 1 1 auto; min-width: 0; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .chapter-copy-button { display: inline-flex; flex: 0 0 auto; align-items: center; justify-content: center; width: 26px; height: 26px; padding: 0; border: 1px solid rgba(255,255,255,.2); border-radius: 7px; color: rgba(255,255,255,.82); background: rgba(255,255,255,.08); cursor: pointer; }
    .chapter-copy-button:hover, .chapter-copy-button:focus { color: #fff; background: rgba(255,255,255,.18); outline: none; }
    .chapter-copy-button i { font-size: 14px; }
    .chapter-copy-toast { position: fixed; z-index: 11000; right: 24px; bottom: 24px; padding: 13px 18px; border: 1px solid rgba(255,255,255,.12); border-radius: 11px; color: #fff; background: #173b5e; box-shadow: 0 12px 32px rgba(10,30,50,.24); font-size: 13px; font-weight: 700; opacity: 0; pointer-events: none; transform: translateY(10px); transition: opacity .18s ease, transform .18s ease; }
    .chapter-copy-toast.is-visible { opacity: 1; transform: translateY(0); }
    .chapter-copy-toast.is-error { background: #873b3b; }
    .chapter-form-card { overflow: hidden; border: 1px solid #e5ebf2; border-radius: 18px; box-shadow: 0 10px 28px rgba(26, 52, 80, .06); }
    .chapter-form-card .card-body { padding: 0; }
    .chapter-form__header { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 22px 26px; border-bottom: 1px solid #edf1f5; }
    .chapter-form__header h3 { margin: 0 0 4px; color: #183251; font-size: 18px; font-weight: 700; }
    .chapter-form__header p { margin: 0; color: #8090a3; font-size: 12px; }
    .chapter-form__body { padding: 25px 26px 18px; }
    .chapter-form__body .form-group { margin-bottom: 20px; }
    .chapter-form__body label { display: block; margin-bottom: 7px; color: #344b66; font-size: 12px; font-weight: 700; }
    .chapter-form__body .form-control { min-height: 42px; padding: 10px 12px; border: 1px solid #dce4ed; border-radius: 9px; color: #243b53; background-color: #fff; box-shadow: none; font-size: 13px; }
    .chapter-form__body .form-control:focus { border-color: #2787a5; box-shadow: 0 0 0 3px rgba(39,135,165,.12); }
    .chapter-form__body .form-control[readonly] { color: #738298; background: #f6f8fb; }
    .chapter-form__body .form-text { margin-top: 6px; color: #8492a5; font-size: 11px; }
    .chapter-form__body textarea.form-control { min-height: 100px; }
    .chapter-banner-field { display: flex; align-items: center; gap: 18px; padding: 15px; border: 1px dashed #cbd7e3; border-radius: 12px; background: #f8fafc; }
    .chapter-form__body .chapter-banner-preview { display: block; width: 170px; height: 104px; max-width: 40%; object-fit: cover; border: 1px solid #e2e9f0; border-radius: 10px; }
    .chapter-banner-upload { flex: 1; min-width: 0; }
    .chapter-banner-upload small { display: block; margin-bottom: 9px; color: #78889c; font-size: 11px; }
    .chapter-form__footer { display: flex; justify-content: flex-end; gap: 10px; padding: 17px 26px; border-top: 1px solid #edf1f5; background: #fbfcfd; }
    .chapter-form__footer .btn { min-width: 150px; min-height: 42px; border-radius: 9px; font-size: 12px; font-weight: 700; }
    @media (max-width: 1100px) { .chapter-overview__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 767.98px) {
        .chapter-overview .card-body { padding: 21px; }
        .chapter-overview__top { flex-direction: column; }
        .chapter-overview__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .chapter-form__header, .chapter-form__body { padding-right: 18px; padding-left: 18px; }
        .chapter-banner-field { align-items: flex-start; flex-direction: column; }
        .chapter-form__body .chapter-banner-preview { width: 100%; max-width: 100%; height: 150px; }
    }
    @media (max-width: 420px) { .chapter-overview__grid { grid-template-columns: 1fr; } .chapter-overview__tile--stakeholder { grid-column: span 1; } .chapter-overview__actions { width: 100%; } .chapter-overview__actions .btn { flex: 1 1 100%; } }
</style>
@endsection

@section('content')
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<div class="content-body chapter-workspace">
    <div class="chapter-copy-toast" id="chapter-copy-toast" role="status" aria-live="polite" aria-atomic="true"></div>
    <section id="basic-input">
        @if(isset($chapter))
        <div class="card chapter-overview mb-2">
            <div class="card-content">
                <div class="card-body">
                            <div class="chapter-overview__top">
                                <div>
                                    <div class="chapter-overview__eyebrow">Chapter workspace</div>
                                    <h2>{{ $chapter->name }}</h2>
                                    <div class="chapter-overview__location">{{ $chapter->zone?->name ?? 'No zone assigned' }} · {{ $chapter->zone?->field?->name ?? 'No field assigned' }}</div>
                                    <span class="chapter-overview__slug">/{{ strtolower($chapter->slug) }}</span>
                                </div>
                                <div class="chapter-overview__actions">
                                    <a target="_blank" rel="noopener" href="{{ route('campus.single', strtolower($chapter->slug)) }}" class="btn btn-light">
                                        <i class="bx bx-link-external mr-50"></i>Portal view
                                    </a>
                                    @if(auth()->user()?->isAdmin() && $chapter->stakeholder)
                                        <a href="{{ route('switchuser', ['id' => $chapter->stakeholder->id, 'type' => 'stakeholder']) }}" class="btn btn-outline-light">
                                            <i class="bx bx-log-in mr-50"></i>Login as stakeholder
                                        </a>
                                    @elseif(!auth()->user()?->isAdmin())
                                        <a href="{{ route('stakeholders.loginpage') }}" class="btn btn-outline-light">
                                            <i class="bx bx-log-in mr-50"></i>Stakeholder login
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="chapter-overview__grid">
                                <div class="chapter-overview__tile chapter-overview__tile--metric"><span class="chapter-overview__tile-label">Active members</span><strong class="chapter-overview__tile-value chapter-overview__tile-value--count">{{ number_format($chapter->members_count) }}</strong></div>
                                <div class="chapter-overview__tile chapter-overview__tile--metric"><span class="chapter-overview__tile-label">Active alumni</span><strong class="chapter-overview__tile-value chapter-overview__tile-value--count">{{ number_format($chapter->alumni_count) }}</strong></div>
                                <div class="chapter-overview__tile chapter-overview__tile--stakeholder">
                                    <span class="chapter-overview__tile-label">Chapter stakeholder</span>
                                    @if($chapter->stakeholder)
                                        <div class="chapter-person">
                                            {!! renderAvatar($chapter->stakeholder, 42, 'chapter-person__avatar') !!}
                                            <div class="chapter-person__details">
                                                <div class="chapter-person__copy-row">
                                                    <a class="chapter-overview__tile-value" href="{{ route('stakeholderpersonnel.edit', $chapter->stakeholder->id) }}">{{ $chapter->stakeholder->name }}</a>
                                                    <button type="button" class="chapter-copy-button" data-copy-value="{{ $chapter->stakeholder->name }}" aria-label="Copy stakeholder name" title="Copy name"><i class="bx bx-copy" aria-hidden="true"></i></button>
                                                </div>
                                                <div class="chapter-person__copy-row">
                                                    <small>{{ $chapter->stakeholder->email }}</small>
                                                    <button type="button" class="chapter-copy-button" data-copy-value="{{ $chapter->stakeholder->email }}" aria-label="Copy stakeholder email" title="Copy email"><i class="bx bx-copy" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="chapter-overview__tile-value is-unassigned">Not assigned</span>
                                    @endif
                                </div>
                                <div class="chapter-overview__tile">
                                    <span class="chapter-overview__tile-label">Zonal pastor</span>
                                    @if($chapter->zone?->zonalCord)
                                        <a class="chapter-overview__tile-value" href="{{ route('stakeholderpersonnel.edit', $chapter->zone->zonalCord->id) }}">{{ $chapter->zone->zonalCord->name }}</a>
                                        <div class="chapter-contact-details">
                                            @if(filled($chapter->zone->zonalCord->email))
                                                <div class="chapter-person__copy-row"><small>{{ $chapter->zone->zonalCord->email }}</small><button type="button" class="chapter-copy-button" data-copy-value="{{ $chapter->zone->zonalCord->email }}" aria-label="Copy zonal pastor email" title="Copy email"><i class="bx bx-copy" aria-hidden="true"></i></button></div>
                                            @endif
                                            @if(filled($chapter->zone->zonalCord->phone))
                                                <div class="chapter-person__copy-row"><small>{{ $chapter->zone->zonalCord->phone }}</small><button type="button" class="chapter-copy-button" data-copy-value="{{ $chapter->zone->zonalCord->phone }}" aria-label="Copy zonal pastor phone" title="Copy phone"><i class="bx bx-copy" aria-hidden="true"></i></button></div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="chapter-overview__tile-value is-unassigned">Not assigned</span>
                                    @endif
                                </div>
                                <div class="chapter-overview__tile">
                                    <span class="chapter-overview__tile-label">Field pastor</span>
                                    @if($chapter->zone?->field?->fieldCord)
                                        <a class="chapter-overview__tile-value" href="{{ route('stakeholderpersonnel.edit', $chapter->zone->field->fieldCord->id) }}">{{ $chapter->zone->field->fieldCord->name }}</a>
                                        <div class="chapter-contact-details">
                                            @if(filled($chapter->zone->field->fieldCord->email))
                                                <div class="chapter-person__copy-row"><small>{{ $chapter->zone->field->fieldCord->email }}</small><button type="button" class="chapter-copy-button" data-copy-value="{{ $chapter->zone->field->fieldCord->email }}" aria-label="Copy field pastor email" title="Copy email"><i class="bx bx-copy" aria-hidden="true"></i></button></div>
                                            @endif
                                            @if(filled($chapter->zone->field->fieldCord->phone))
                                                <div class="chapter-person__copy-row"><small>{{ $chapter->zone->field->fieldCord->phone }}</small><button type="button" class="chapter-copy-button" data-copy-value="{{ $chapter->zone->field->fieldCord->phone }}" aria-label="Copy field pastor phone" title="Copy phone"><i class="bx bx-copy" aria-hidden="true"></i></button></div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="chapter-overview__tile-value is-unassigned">Not assigned</span>
                                    @endif
                                </div>
                            </div>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card chapter-form-card">
                    <div class="card-content">
                        <div class="card-body">

                            <div class="chapter-form__header">
                                <div>
                                    <h3>{{ isset($chapter) ? 'Chapter details' : 'New chapter' }}</h3>
                                    <p>Keep the campus information accurate and easy to find.</p>
                                </div>
                                <span class="text-muted small">{{ isset($chapter) ? 'ID '.$chapter->id : 'Setup' }}</span>
                            </div>

                            <form action="{{ isset($chapter) ? route('chapters.update', $chapter->id) : route('chapters.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if(isset($chapter))
                                    @method('PATCH')
                                @endif

                                <div class="chapter-form__body">
                                    <div class="row">

                                        {{-- Name --}}
                                        <fieldset class="form-group col-md-6">
                                            <label for="name">Name</label>
                                            <input
                                                @if(isset($chapter) && !auth()->user()->isAdmin()) readonly @endif
                                                type="text"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                value="{{ old('name', $chapter->name ?? '') }}"
                                                placeholder="Enter name"
                                                required
                                            >
                                        </fieldset>

                                        @if(isset($chapter))
                                        <fieldset class="form-group col-md-6">
                                            <label for="slug">Campus URL slug</label>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $chapter->slug) }}" placeholder="Enter a unique URL slug" required>
                                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            <small class="form-text text-muted">The URL uses lowercase letters. Slugs must be unique.</small>
                                        </fieldset>
                                        @endif

                                        {{-- Field --}}
                                        @if(auth()->user()->isAdmin())
                                        <fieldset class="form-group col-md-6">
                                            <label for="field_id">Field</label>
                                            <select class="form-control" name="field_id" id="field_id" required>
                                                <option value="">--Select Field--</option>
                                                @foreach($fields as $field)
                                                    <option value="{{ $field->id }}"
                                                        {{ isset($chapter) && $chapter->zone->field_id == $field->id ? 'selected' : '' }}>
                                                        {{ $field->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                        @else
                                        <fieldset class="form-group col-md-6">
                                            <label for="field_id">Field</label>
                                            <input class="form-control" type="text" readonly
                                                value="{{ $chapter->zone->field->name ?? '' }}">
                                        </fieldset>
                                        @endif

                                        {{-- Zone --}}
                                        @if(auth()->user()->isAdmin())
                                        <fieldset class="form-group col-md-6">
                                            <label for="zone_id">Zone</label>
                                            <select class="form-control" name="zone_id" id="zone_id" required>
                                                <option value="">--Select Zone--</option>
                                                @if(isset($chapter) && $chapter->zone->field)
                                                    @foreach($chapter->zone->field->zones as $zone)
                                                        <option value="{{ $zone->id }}"
                                                            {{ $chapter->zone_id == $zone->id ? 'selected' : '' }}>
                                                            {{ $zone->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </fieldset>
                                        @else
                                        <fieldset class="form-group col-md-6">
                                            <label for="zone_id">Zone</label>
                                            <input class="form-control" type="text" readonly
                                                value="{{ $chapter->zone->name ?? '' }}">
                                        </fieldset>
                                        @endif


                                        {{-- Address, Email, Phone --}}
                                        <fieldset class="form-group col-md-6">
                                            <label for="address">Address (Optional)</label>
                                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $chapter->address ?? '') }}" placeholder="Enter address">
                                        </fieldset>

                                        <fieldset class="form-group col-md-6">
                                            <label for="state">State</label>
                                            <select class="form-control @error('state') is-invalid @enderror" id="state" name="state" required>
                                                <option value="">Select state</option>
                                                @foreach(array_keys(config('gsf_states')) as $state)
                                                    <option value="{{ $state }}" @selected(old('state', $chapter->state ?? '') === $state)>{{ $state }}</option>
                                                @endforeach
                                            </select>
                                            @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </fieldset>

                                        <fieldset class="form-group col-md-6">
                                            <label for="email">Email (Optional)</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $chapter->email ?? '') }}" placeholder="Enter email">
                                        </fieldset>

                                        <fieldset class="form-group col-md-6">
                                            <label for="phone">Phone (Optional)</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $chapter->phone ?? '') }}" placeholder="Enter phone">
                                        </fieldset>

                                        {{-- Social Links --}}
                                        <fieldset class="form-group col-md-6">
                                            <label for="facebook">Facebook Link (Optional)</label>
                                            <input type="text" class="form-control" id="facebook" name="facebook" value="{{ old('facebook', $chapter->facebook ?? '') }}" placeholder="Enter Facebook link">
                                        </fieldset>

                                        <fieldset class="form-group col-md-6">
                                            <label for="twitter">Twitter Link (Optional)</label>
                                            <input type="text" class="form-control" id="twitter" name="twitter" value="{{ old('twitter', $chapter->twitter ?? '') }}" placeholder="Enter Twitter link">
                                        </fieldset>

                                        {{-- About --}}
                                        <fieldset class="form-group position-relative has-icon-left col-12">
                                            <label for="about">About</label><small class="form-text text-muted">Write a brief biography of the chapter, including fellowship service periods if useful.</small>
                                            <textarea class="form-control" id="about" rows="3" name="about">{!! old('about', $chapter->about ?? '') !!}</textarea>
                                        </fieldset>

                                        {{-- Banner --}}
                                        <fieldset class="form-group col-12">
                                            <label for="chapter_banner">{{ isset($chapter) ? 'Change banner' : 'Upload banner' }}</label>
                                            <div class="chapter-banner-field">
                                                @if(isset($chapter))
                                                    <img src="{{ versionedAsset(campusImagePath($chapter)) }}" alt="Current campus image for {{ $chapter->name }}" class="chapter-banner-preview">
                                                @else
                                                    <div class="chapter-banner-preview d-flex align-items-center justify-content-center text-muted">No banner yet</div>
                                                @endif
                                                <div class="chapter-banner-upload">
                                                    <small>{{ isset($chapter) ? (filled($chapter->banner) ? 'Current custom banner. Choose a new image to replace it.' : 'Current campus image. Upload a custom banner to replace this image.') : 'Choose an image to use as the chapter banner.' }}</small>
                                                    <input type="file" accept="image/*" class="form-control" name="chapter_banner" id="chapter_banner">
                                                </div>
                                            </div>
                                        </fieldset>

                                    </div>
                                </div>

                                @include('includes.chapter_meetings', ['meetingRoutePrefix' => 'chapters.meetings'])

                                <div class="chapter-form__footer">
                                    <a href="{{ route('chapters.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button class="btn btn-primary" type="submit">
                                        {{ isset($chapter) ? 'Save changes' : 'Create chapter' }}
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    var copyToast = document.getElementById('chapter-copy-toast');
    var copyToastTimer;
    function showCopyFeedback(message, isError) {
        if (!copyToast) return;
        copyToast.textContent = message;
        copyToast.classList.toggle('is-error', Boolean(isError));
        copyToast.classList.add('is-visible');
        window.clearTimeout(copyToastTimer);
        copyToastTimer = window.setTimeout(function () {
            copyToast.classList.remove('is-visible');
        }, 2400);
    }

    document.querySelectorAll('[data-copy-value]').forEach(function (button) {
        button.addEventListener('click', async function () {
            var value = button.getAttribute('data-copy-value') || '';
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(value);
                } else {
                    var temporaryInput = document.createElement('textarea');
                    temporaryInput.value = value;
                    temporaryInput.style.position = 'fixed';
                    temporaryInput.style.opacity = '0';
                    document.body.appendChild(temporaryInput);
                    temporaryInput.select();
                    var copied = document.execCommand('copy');
                    temporaryInput.remove();
                    if (!copied) throw new Error('Copy failed');
                }
                var icon = button.querySelector('i');
                icon.classList.remove('bx-copy');
                icon.classList.add('bx-check');
                button.title = 'Copied';
                showCopyFeedback(button.getAttribute('aria-label').replace(/^Copy /, '') + ' copied', false);
                window.setTimeout(function () {
                    icon.classList.remove('bx-check');
                    icon.classList.add('bx-copy');
                    button.title = button.getAttribute('aria-label').replace('Copy stakeholder ', 'Copy ');
                }, 1400);
            } catch (error) {
                button.title = 'Unable to copy';
                showCopyFeedback('Could not copy this detail', true);
            }
        });
    });

    CKEDITOR.replace('about');
</script>
@if(auth()->user()->isAdmin())
<script>
$(document).ready(function() {
    $('#field_id').on('change', function() {
        var fieldId = $(this).val();
        var $zoneSelect = $('#zone_id');

        // Reset zone select
        $zoneSelect.html('<option value="">--Select Zone--</option>');

        if (!fieldId) return;

        $.ajax({
            url: '/ajax/field/' + fieldId + '/zones',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $.each(data, function(index, zone) {
                    $zoneSelect.append('<option value="' + zone.id + '">' + zone.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching zones:', error);
            }
        });
    });
});
</script>
@endif


@endsection
