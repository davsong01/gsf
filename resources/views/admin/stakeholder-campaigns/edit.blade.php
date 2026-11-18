@extends('layouts.dashboard')

@section('title', $campaign->exists ? 'Edit Campaign' : 'Create Campaign')

@section('item')
<li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ $campaign->exists ? 'Edit Campaign' : 'Create Campaign' }}</li>
@endsection

@section('content')
@php
    $selectedQuestions = old('question_ids', $campaign->exists ? $campaign->questions->pluck('id')->all() : []);
    $dateValue = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i') : '';
@endphp

<style>
    .campaign-editor-page { max-width:1420px; margin:0 auto; }
    .campaign-editor-hero { display:flex; align-items:center; justify-content:space-between; gap:18px; margin-bottom:18px; padding:21px 25px; border-radius:13px; color:#fff; background:linear-gradient(120deg,#152c52,#1d4778 74%,#3479a4); box-shadow:0 10px 26px rgba(25,55,95,.12); }
    .campaign-editor-hero__kicker { margin:0 0 4px; color:#b9d7ee; font-size:10px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
    .campaign-editor-hero h1 { margin:0; color:#fff; font-size:23px; font-weight:750; }
    .campaign-editor-hero p { margin:4px 0 0; color:rgba(255,255,255,.76); font-size:12px; }
    .campaign-editor-hero__status { padding:7px 11px; border:1px solid rgba(255,255,255,.24); border-radius:20px; color:#fff; background:rgba(255,255,255,.09); font-size:10px; font-weight:750; text-transform:uppercase; }
    .campaign-editor-form .card { margin-bottom:16px; overflow:hidden; border:1px solid #e6ebf1; border-radius:12px; box-shadow:0 5px 18px rgba(31,49,75,.045); }
    .campaign-editor-form .card-header { padding:16px 20px; border-bottom:1px solid #e9edf2; background:#fff; }
    .campaign-editor-form .card-body { padding:20px; }
    .campaign-editor-section-title { display:flex; align-items:flex-start; gap:11px; }
    .campaign-editor-section-title__number { display:grid; width:30px; height:30px; flex:0 0 30px; place-items:center; border-radius:9px; color:#2d65a7; background:#edf4ff; font-size:12px; font-weight:800; }
    .campaign-editor-section-title h2 { margin:0; color:#29384d; font-size:14px; font-weight:750; }
    .campaign-editor-section-title p { margin:3px 0 0; color:#8190a3; font-size:11px; }
    .campaign-editor-form .form-label { display:block; margin:0 0 6px; color:#52627a; font-size:11px; font-weight:750; }
    .campaign-editor-form .form-control,.campaign-editor-form .form-select { min-height:42px; border-color:#dfe5ec; border-radius:8px; color:#33445d; font-size:12px; }
    .campaign-editor-form textarea.form-control { min-height:84px; resize:vertical; }
    .campaign-editor-field { min-width:0; }
    .campaign-editor-error { display:block; margin-top:5px; color:#bf4545; font-size:11px; }
    .campaign-option-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; }
    .campaign-option-card { display:flex; align-items:center; gap:9px; min-height:48px; margin:0; padding:10px 12px; border:1px solid #e5eaf0; border-radius:9px; background:#fbfcfe; cursor:pointer; }
    .campaign-option-card:hover { border-color:#b9cde7; background:#f6f9ff; }
    .campaign-option-card > .form-check-input { position:static; float:none!important; flex:0 0 16px; width:16px; height:16px; margin:0!important; padding:0; }
    .campaign-option-card > .form-check-label { display:block; margin:0; padding:0; color:#4a5b72; font-size:11px; font-weight:650; }
    .campaign-question-list { margin-top:14px; }
    .campaign-question-add { display:flex; justify-content:flex-end; margin-top:12px; }
    .campaign-question-row { display:grid; grid-template-columns:38px minmax(0,1fr) 38px; gap:10px; align-items:center; margin-bottom:9px; padding:10px; border:1px solid #e5eaf0; border-radius:9px; background:#fbfcfe; }
    .campaign-question-row.is-dragging { opacity:.45; }
    .campaign-question-row.is-drop-target { border-top:3px solid #4e8fe8; }
    .campaign-question-drag { border:0; color:#75849a; background:transparent; cursor:grab; }
    .campaign-question-remove { width:36px; height:36px; border:1px solid #e5c7c7; border-radius:7px; color:#b94444; background:#fff; }
    .campaign-question-empty { padding:25px 16px; border:1px dashed #cbd4df; border-radius:9px; color:#778397; background:#fbfcfe; text-align:center; font-size:12px; }
    .campaign-editor-actions { display:flex; align-items:center; justify-content:flex-end; gap:9px; margin-top:18px; }
    @media(max-width:900px) { .campaign-option-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:600px) { .campaign-editor-hero { align-items:flex-start; flex-direction:column; padding:19px; } .campaign-editor-form .card-body { padding:15px; } .campaign-option-grid { grid-template-columns:1fr; } .campaign-editor-actions { justify-content:stretch; } .campaign-editor-actions .btn { flex:1; } }
</style>

<div class="content-body">
    <section class="campaign-editor-page">
        @include('includes.alerts')
        <header class="campaign-editor-hero">
            <div><p class="campaign-editor-hero__kicker">Campaign manager</p><h1>{{ $campaign->exists ? 'Edit campaign' : 'Create campaign' }}</h1><p>Set up the public form, submission window, and campaign questions.</p></div>
            @if($campaign->exists)<span class="campaign-editor-hero__status">{{ ucfirst($campaign->status) }}</span>@endif
        </header>

        <form class="campaign-editor-form" method="POST" action="{{ $campaign->exists ? route('campaigns.update', $campaign) : route('campaigns.store') }}">
            @csrf
            @if($campaign->exists) @method('PUT') @endif

            <section class="card">
                <div class="card-header"><div class="campaign-editor-section-title"><span class="campaign-editor-section-title__number">01</span><div><h2>Campaign details</h2><p>Name and public facing information.</p></div></div></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-6 campaign-editor-field">
                            <label class="form-label" for="title">Campaign title</label>
                            <input id="title" name="title" class="form-control" value="{{ old('title', $campaign->title) }}" required>
                            @error('title')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-lg-6 campaign-editor-field">
                            <label class="form-label" for="slug">Public link slug</label>
                            <input id="slug" name="slug" class="form-control" value="{{ old('slug', $campaign->slug) }}" placeholder="Generated from title when blank">
                            @error('slug')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-12 campaign-editor-field">
                            <label class="form-label" for="description">Description <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea id="description" name="description" rows="2" class="form-control" placeholder="A short introduction shown above the campaign instructions">{{ old('description', $campaign->description) }}</textarea>
                            @error('description')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-12 campaign-editor-field">
                            <label class="form-label" for="instructions">Instructions</label>
                            <textarea id="instructions" name="instructions" rows="3" class="form-control" placeholder="Explain how participants should complete this form" required>{{ old('instructions', $campaign->instructions) }}</textarea>
                            @error('instructions')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-header"><div class="campaign-editor-section-title"><span class="campaign-editor-section-title__number">02</span><div><h2>Schedule and status</h2><p>Choose when responses are accepted and when the event takes place.</p></div></div></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-6 campaign-editor-field">
                            <label class="form-label" for="open_from">Open from</label>
                            <input type="datetime-local" id="open_from" name="open_from" class="form-control" value="{{ old('open_from', $dateValue($campaign->open_from)) }}" required>
                            @error('open_from')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-lg-6 campaign-editor-field">
                            <label class="form-label" for="open_until">Open until</label>
                            <input type="datetime-local" id="open_until" name="open_until" class="form-control" value="{{ old('open_until', $dateValue($campaign->open_until)) }}" required>
                            @error('open_until')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-lg-6 campaign-editor-field">
                            <label class="form-label" for="event_at">Event date and time</label>
                            <input type="datetime-local" id="event_at" name="event_at" class="form-control" value="{{ old('event_at', $dateValue($campaign->event_at)) }}" required>
                            @error('event_at')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-lg-6 campaign-editor-field">
                            <label class="form-label" for="status">Status</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="draft" @selected(old('status', $campaign->status ?? 'draft') === 'draft')>Draft</option>
                                <option value="active" @selected(old('status', $campaign->status ?? 'draft') === 'active')>Active</option>
                                <option value="closed" @selected(old('status', $campaign->status ?? 'draft') === 'closed')>Closed</option>
                            </select>
                            @error('status')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-header"><div class="campaign-editor-section-title"><span class="campaign-editor-section-title__number">03</span><div><h2>Frontend options</h2><p>Add optional placement fields to the public form.</p></div></div></div>
                <div class="card-body">
                    <div class="campaign-option-grid">
                        @foreach([
                            'enable_field_options' => 'Field options',
                            'enable_zone_options' => 'Zone options',
                            'enable_chapter_option' => 'Chapter option',
                            'enable_designation_options' => 'Designation options',
                        ] as $optionName => $optionLabel)
                            <label class="campaign-option-card">
                                <input class="form-check-input" type="checkbox" name="{{ $optionName }}" value="1" @checked(old($optionName, $campaign->{$optionName} ?? false))>
                                <span class="form-check-label">Enable {{ $optionLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <div class="campaign-editor-section-title"><span class="campaign-editor-section-title__number">04</span><div><h2>Question items</h2><p>Choose and order the questions shown on the public form.</p></div></div>
                </div>
                <div class="card-body">
                    <small class="text-muted">Drag the grip to change the public order. Each question can only be used once.</small>
                    <div id="campaignQuestionList" class="campaign-question-list"></div>
                    <div class="campaign-question-add">
                        <button type="button" id="addCampaignQuestion" class="btn btn-sm btn-outline-primary"><i class="fa fa-plus me-1"></i> Add question</button>
                    </div>
                    <script type="application/json" id="campaignQuestionData">@json($questions->map(fn ($question) => ['id' => $question->id, 'label' => $question->label, 'slug' => $question->slug])->values())</script>
                    <script type="application/json" id="selectedCampaignQuestionData">@json(array_values($selectedQuestions))</script>
                    @error('question_ids')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                    @error('question_ids.*')<small class="campaign-editor-error">{{ $message }}</small>@enderror
                    @if($questions->isEmpty())<small class="text-warning d-block mt-2">No campaign question items are available yet. Add items with module type Campaign first.</small>@endif
                </div>
            </section>

            <div class="campaign-editor-actions">
                <a href="{{ route('campaigns.index') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">{{ $campaign->exists ? 'Save changes' : 'Create campaign' }}</button>
            </div>
        </form>
    </section>
</div>
@endsection

@section('extra_scripts')
<style>
    .campaign-question-row { display:grid; grid-template-columns:38px minmax(0,1fr) 38px; gap:10px; align-items:center; margin-bottom:9px; padding:10px; border:1px solid #e5eaf0; border-radius:9px; background:#fbfcfe; }
    .campaign-question-row.is-dragging { opacity:.45; }
    .campaign-question-row.is-drop-target { border-top:3px solid #4e8fe8; }
    .campaign-question-drag { border:0; color:#75849a; background:transparent; cursor:grab; }
    .campaign-question-remove { width:36px; height:36px; border:1px solid #e5c7c7; border-radius:7px; color:#b94444; background:#fff; }
    .campaign-question-empty { padding:25px 16px; border:1px dashed #cbd4df; border-radius:9px; color:#778397; background:#fbfcfe; text-align:center; font-size:12px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const questionData = JSON.parse(document.getElementById('campaignQuestionData').textContent || '[]');
    const initialIds = JSON.parse(document.getElementById('selectedCampaignQuestionData').textContent || '[]');
    const list = document.getElementById('campaignQuestionList');
    const addButton = document.getElementById('addCampaignQuestion');
    let draggedRow = null;

    function refreshSelectOptions() {
        const selects = Array.from(list.querySelectorAll('.campaign-question-select'));
        const values = selects.map(select => select.value);

        selects.forEach((select, index) => {
            const keep = values[index];
            const occupiedElsewhere = new Set(values.filter((value, otherIndex) => value && otherIndex !== index));
            select.innerHTML = '<option value="">Search and select a question...</option>';
            questionData.forEach(question => {
                if (!occupiedElsewhere.has(String(question.id)) || String(question.id) === keep) {
                    const option = document.createElement('option');
                    option.value = question.id;
                    option.textContent = question.label + ' (' + question.slug + ')';
                    select.appendChild(option);
                }
            });
            select.value = keep;
            if (window.jQuery && jQuery.fn.select2) jQuery(select).trigger('change.select2');
        });
    }

    function addRow(questionId = '') {
        const row = document.createElement('div');
        row.className = 'campaign-question-row';
        row.draggable = true;
        row.innerHTML = '<button type="button" class="campaign-question-drag" aria-label="Drag to reorder" title="Drag to reorder"><i class="fa fa-bars"></i></button>' +
            '<select name="question_ids[]" class="form-select campaign-question-select" required></select>' +
            '<button type="button" class="campaign-question-remove" aria-label="Remove question" title="Remove question"><i class="fa fa-minus"></i></button>';
        list.appendChild(row);
        refreshSelectOptions();

        const select = row.querySelector('select');
        if (questionId) select.value = String(questionId);
        if (window.jQuery && jQuery.fn.select2) jQuery(select).select2({ width: '100%' });

        select.addEventListener('change', refreshSelectOptions);
        row.querySelector('.campaign-question-remove').addEventListener('click', function () {
            if (window.jQuery && jQuery.fn.select2 && jQuery(select).hasClass('select2-hidden-accessible')) jQuery(select).select2('destroy');
            row.remove();
            refreshSelectOptions();
        });
        row.addEventListener('dragstart', function (event) {
            draggedRow = row;
            row.classList.add('is-dragging');
            event.dataTransfer.effectAllowed = 'move';
        });
        row.addEventListener('dragend', function () {
            row.classList.remove('is-dragging');
            list.querySelectorAll('.is-drop-target').forEach(target => target.classList.remove('is-drop-target'));
            draggedRow = null;
        });
        row.addEventListener('dragover', function (event) {
            event.preventDefault();
            if (draggedRow && draggedRow !== row) row.classList.add('is-drop-target');
        });
        row.addEventListener('dragleave', function () { row.classList.remove('is-drop-target'); });
        row.addEventListener('drop', function (event) {
            event.preventDefault();
            row.classList.remove('is-drop-target');
            if (!draggedRow || draggedRow === row) return;
            const bounds = row.getBoundingClientRect();
            list.insertBefore(draggedRow, event.clientY < bounds.top + bounds.height / 2 ? row : row.nextSibling);
        });
        refreshSelectOptions();
    }

    addButton.addEventListener('click', function () {
        list.querySelector('.campaign-question-empty')?.remove();
        addRow();
    });

    initialIds.forEach(id => addRow(id));
    if (initialIds.length === 0) list.innerHTML = '<div class="campaign-question-empty">No question items added. Click “Add Question” to begin.</div>';
});
</script>
@endsection
