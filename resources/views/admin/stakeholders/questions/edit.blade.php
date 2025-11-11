@extends('layouts.dashboard')

@section('title', isset($question) ? 'Update Question' : 'Create Question')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholder.questions.index') }}">All Questions</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($question) ? 'Update' : 'Create' }} Question</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@include('includes.alerts')</div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ isset($question) ? route('stakeholder.questions.update', $question->id) : route('stakeholder.questions.store') }}" method="POST">
                                @csrf
                                @if(isset($question))
                                    @method('PATCH')
                                @endif

                                <div class="questions-wrapper">

                                    @php
                                        $questions = isset($questions) ? $questions : (isset($question) ? [$question] : [null]);
                                    @endphp

                                    @foreach($questions as $index => $q)
                                    <div class="question-row mb-2 border-bottom pb-2">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label>Label</label>
                                                    <input type="text" name="questions[{{ $index }}][label]" class="form-control" 
                                                        value="{{ old("questions.$index.label", $q->label ?? '') }}" required>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label>Slug</label>
                                                    <input type="text" name="questions[{{ $index }}][slug]" class="form-control" 
                                                        value="{{ old("questions.$index.slug", $q->slug ?? '') }}" required>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3">
                                                <fieldset class="form-group">
                                                    <label>Section</label>
                                                    <select name="questions[{{ $index }}][section]" class="form-control">
                                                        <option value="">--Select--</option>
                                                        @foreach($sections as $section)
                                                            <option value="{{ $section->name }}" {{ old("questions.$index.section", $q->section ?? '') == $section->name ? 'selected' : '' }}>
                                                                {{ $section->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-2">
                                                <fieldset class="form-group">
                                                    <label>Required?</label>
                                                    <select name="questions[{{ $index }}][is_required]" class="form-control">
                                                        <option value="0" {{ old("questions.$index.is_required", $q->is_required ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                                        <option value="1" {{ old("questions.$index.is_required", $q->is_required ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3">
                                                <fieldset class="form-group">
                                                    <label>Width Class</label>
                                                    <select name="questions[{{ $index }}][width_class]" class="form-control">
                                                        <option value="col-md-2" {{ old("questions.$index.width_class", $q->width_class ?? 'col-md-2') == 0 ? 'selected' : '' }}>Col md 2</option>
                                                        <option value="col-md-3" {{ old("questions.$index.width_class", $q->width_class ?? 'col-md-3') == 0 ? 'selected' : '' }}>Col md 3</option>
                                                        <option value="col-md-4" {{ old("questions.$index.width_class", $q->width_class ?? 'col-md-4') == 0 ? 'selected' : '' }}>Col md 4</option>
                                                        <option value="col-md-6" {{ old("questions.$index.width_class", $q->width_class ?? 'col-md-6') == 0 ? 'selected' : '' }}>Col md 6</option>
                                                        <option value="col-md-8" {{ old("questions.$index.width_class", $q->width_class ?? 'col-md-8') == 0 ? 'selected' : '' }}>Col md 8</option>
                                                        <option value="col-md-12" {{ old("questions.$index.width_class", $q->width_class ?? 'col-md-12') == 0 ? 'selected' : '' }}>Col md 12</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <fieldset class="form-group">
                                                    <label>Type</label>
                                                    <select name="questions[{{ $index }}][type]" class="form-control" required>
                                                        @foreach(['text', 'number', 'textarea', 'select', 'radio', 'checkbox', 'date', 'month', 'year', 'rating', 'dynamic_table'] as $type)
                                                            <option value="{{ $type }}" {{ old("questions.$index.type", $q->type ?? '') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2">
                                                <fieldset class="form-group">
                                                    <label>Order</label>
                                                    <input type="number" name="questions[{{ $index }}][order]" class="form-control" value="{{ old("questions.$index.order", $q->order ?? 0) }}">
                                                </fieldset>
                                            </div>

                                            {{-- <div class="col-md-1 text-center">
                                                <button type="button" class="btn btn-danger remove-row" style="margin-bottom:18px;">&times;</button>
                                            </div> --}}

                                        </div>
                                    </div>
                                    @endforeach

                                </div>

                                {{-- <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success" id="add-question">+ Add Question</button>
                                    </div>
                                </div> --}}

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary w-100">{{ isset($question) ? 'Update Question' : 'Save Question' }}</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('extra_scripts')
<script>
$(document).ready(function() {
    let questionIndex = {{ count($questions) }}; // Start from existing questions count

    $('#add-question').click(function() {
        const row = `
        <div class="question-row mb-2 border-bottom pb-2">
            <div class="row g-2 align-items-end">

                <div class="col-md-6">
                    <fieldset class="form-group">
                        <label>Label</label>
                        <input type="text" name="questions[${questionIndex}][label]" class="form-control" required>
                    </fieldset>
                </div>

                <div class="col-md-6">
                    <fieldset class="form-group">
                        <label>Slug</label>
                        <input type="text" name="questions[${questionIndex}][slug]" class="form-control" required>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="form-group">
                        <label>Section</label>
                        <select name="questions[${questionIndex}][section]" class="form-control">
                            <option value="">--Select--</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->name }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="form-group">
                        <label>Required?</label>
                        <select name="questions[${questionIndex}][is_required]" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="form-group">
                        <label>Type</label>
                        <select name="questions[${questionIndex}][type]" class="form-control" required>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Select</option>
                            <option value="radio">Radio</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="form-group">
                        <label>Order</label>
                        <input type="number" name="questions[${questionIndex}][order]" class="form-control" value="0">
                    </fieldset>
                </div>

                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-danger remove-row" style="margin-bottom:18px;">&times;</button>
                </div>

            </div>
        </div>`;

        $('.questions-wrapper').append(row);
        questionIndex++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.question-row').remove();
    });
});
</script>
@endsection
