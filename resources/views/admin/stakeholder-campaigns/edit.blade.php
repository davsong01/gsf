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
<div class="content-body">
    <section>
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">{{ $campaign->exists ? 'Edit Campaign' : 'Create Campaign' }}</h4></div>
            <div class="card-body">
                @include('includes.alerts')
                <form method="POST" action="{{ $campaign->exists ? route('campaigns.update', $campaign) : route('campaigns.store') }}">
                    @csrf
                    @if($campaign->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="title">Title</label>
                            <input id="title" name="title" class="form-control" value="{{ old('title', $campaign->title) }}" required>
                            @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="slug">Slug</label>
                            <input id="slug" name="slug" class="form-control" value="{{ old('slug', $campaign->slug) }}" placeholder="Generated from title when blank">
                            @error('slug')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Description <span class="text-muted">(optional)</span></label>
                            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $campaign->description) }}</textarea>
                            @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="instructions">Instructions</label>
                            <textarea id="instructions" name="instructions" rows="5" class="form-control" required>{{ old('instructions', $campaign->instructions) }}</textarea>
                            @error('instructions')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="open_from">Open From</label>
                            <input type="datetime-local" id="open_from" name="open_from" class="form-control" value="{{ old('open_from', $dateValue($campaign->open_from)) }}" required>
                            @error('open_from')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="open_until">Open Until</label>
                            <input type="datetime-local" id="open_until" name="open_until" class="form-control" value="{{ old('open_until', $dateValue($campaign->open_until)) }}" required>
                            @error('open_until')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="status">Status</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="draft" @selected(old('status', $campaign->status ?? 'draft') === 'draft')>Draft</option>
                                <option value="active" @selected(old('status', $campaign->status ?? 'draft') === 'active')>Active</option>
                                <option value="closed" @selected(old('status', $campaign->status ?? 'draft') === 'closed')>Closed</option>
                            </select>
                            @error('status')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Question Items</label>
                                <button type="button" id="addCampaignQuestion" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-plus"></i> Add Question
                                </button>
                            </div>
                            <div id="campaignQuestionList" class="campaign-question-list"></div>
                            <script type="application/json" id="campaignQuestionData">@json($questions->map(fn ($question) => ['id' => $question->id, 'label' => $question->label, 'slug' => $question->slug])->values())</script>
                            <script type="application/json" id="selectedCampaignQuestionData">@json(array_values($selectedQuestions))</script>
                            <small class="text-muted">Add questions with module type Campaign, remove them with the minus button, and drag rows to set their public order.</small>
                            @error('question_ids')<small class="text-danger d-block">{{ $message }}</small>@enderror
                            @error('question_ids.*')<small class="text-danger d-block">{{ $message }}</small>@enderror
                            @if($questions->isEmpty())<small class="text-warning d-block mt-1">No campaign question items are available yet. Add items with module type Campaign first.</small>@endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">{{ $campaign->exists ? 'Save Changes' : 'Create Campaign' }}</button>
                        <a href="{{ route('campaigns.index') }}" class="btn btn-light border">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@section('extra_scripts')
<style>
    .campaign-question-row { display:grid; grid-template-columns:42px minmax(0,1fr) 42px; gap:10px; align-items:center; margin-bottom:10px; padding:10px; border:1px solid #e2e8ee; border-radius:9px; background:#fff; }
    .campaign-question-row.is-dragging { opacity:.45; }
    .campaign-question-row.is-drop-target { border-top:3px solid #4e8fe8; }
    .campaign-question-drag { border:0; color:#75849a; background:transparent; cursor:grab; }
    .campaign-question-remove { width:38px; height:38px; border:1px solid #e5c7c7; border-radius:7px; color:#b94444; background:#fff; }
    .campaign-question-empty { padding:16px; border:1px dashed #cbd4df; border-radius:9px; color:#778397; text-align:center; }
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
