@extends('layouts.stakeholderdashboard')

@section('extra_styles')
<style>
    .header-section {
        background-color: #004080;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        margin-top: 30px;
    }

    .sub-section {
        color: #004080;
        margin-top: 10px;
    }

    label {
        margin-top: 20px !important;
    }
</style>
@endsection

@section('title', 'Add/Edit Report')

@section('item')
<li class="breadcrumb-item"> <a href="{{ route('stakeholders.dashboard') }}">Report</a></li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($report) ? 'Edit Report' : 'Add New Report' }}</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ isset($report) ? route('stakeholders.reports.update', $report->id) : route('stakeholders.reports.store') }}"
                                method="POST" enctype="multipart/form-data"
                                onsubmit="return confirm('You are about to submit this report, action is irreversible');">
                                @csrf
                                @if(isset($report))
                                @method('PUT')
                                @endif

                                @php
                                $currentSection = null;
                                $currentSubSection = null;
                                @endphp

                                <div class="row">
                                    @foreach($sections as $section)
                                    {{-- Section Header --}}
                                    <div class="col-12">
                                        <h3 class="header-section">{{ $section->name }}</h3>
                                    </div>

                                    @foreach($section->subsections as $subsection)
                                    {{-- Sub-Section Header --}}
                                    <div class="col-12">
                                        <h5 class="sub-section">{{ $subsection->name }}</h5>
                                    </div>

                                    @foreach($subsection->questions as $question)
                                    @php
                                    $value = old('responses.' . $question->slug)
                                    ?? ($prefillData[$question->slug] ?? '');
                                    @endphp

                                    <div class="{{ $question->width_class ?? 'col-md-6' }} ">
                                        <label for="{{ $question->slug }}" class="form-label">
                                            {{ $question->label }}
                                            @if($question->is_required) <span class="text-danger">*</span> @endif
                                        </label>

                                        @switch($question->type)
                                        @case('text')
                                        @case('year')
                                        @case('month')
                                        @case('number')
                                        @case('date')
                                        <input type="{{ $question->type }}"
                                            class="form-control"
                                            id="{{ $question->slug }}"
                                            name="responses[{{ $question->slug }}]"
                                            value="{{ $value }}"
                                            @if($question->is_required) required @endif>
                                        @break

                                        @case('textarea')
                                        <textarea class="form-control"
                                            id="{{ $question->slug }}"
                                            name="responses[{{ $question->slug }}]"
                                            rows="3"
                                            @if($question->is_required) required @endif>{{ $value }}</textarea>
                                        @break

                                        @case('select')
                                        <select class="form-select"
                                            id="{{ $question->slug }}"
                                            name="responses[{{ $question->slug }}]"
                                            @if($question->is_required) required @endif>
                                            <option value="">Select...</option>
                                            @foreach($question->options as $optKey => $optLabel)
                                            <option value="{{ $optKey }}" {{ $value == $optKey ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @break

                                        @case('dynamic_table')
                                        <table class="table table-bordered dynamic-table" data-slug="{{ $question->slug }}">
                                            <thead>
                                                <tr>
                                                    @foreach($question->options as $col)
                                                    <th>{{ $col['label'] ?? $col }}</th>
                                                    @endforeach
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                $rows = old('responses.' . $question->slug, $prefillData[$question->slug] ?? [[]]);
                                                @endphp
                                                @foreach($rows as $row)
                                                <tr>
                                                    @foreach($question->options as $col)
                                                    @php $colLabel = $col['label'] ?? $col; @endphp
                                                    <td>
                                                        <input type="{{ $col['type'] ?? 'text' }}"
                                                            name="responses[{{ $question->slug }}][][{{ $colLabel }}]"
                                                            class="form-control"
                                                            value="{{ $row[$colLabel] ?? '' }}"
                                                            @if(!empty($col['required'])) required @endif>
                                                    </td>
                                                    @endforeach
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-success add-row">+</button>
                                                        <button type="button" class="btn btn-sm btn-danger remove-row">-</button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @break
                                        @endswitch
                                    </div>
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100" type="submit">{{ isset($report) ? 'Update Report' : 'Send Report' }}</button>
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

<script>
    $(document).on('click', '.add-row', function() {
        let table = $(this).closest('table');
        let newRow = table.find('tbody tr:first').clone();
        newRow.find('input').val('');
        table.find('tbody').append(newRow);
    });

    $(document).on('click', '.remove-row', function() {
        let table = $(this).closest('table');
        if (table.find('tbody tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });
</script>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Add row for dynamic tables
        document.querySelectorAll('.dynamic-table').forEach(function(table) {
            table.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-row')) {
                    let tbody = table.querySelector('tbody');
                    let firstRow = tbody.querySelector('tr:first-child');
                    let newRow = firstRow.cloneNode(true);

                    // Clear inputs in the cloned row
                    newRow.querySelectorAll('input').forEach(input => input.value = '');

                    tbody.appendChild(newRow);
                }

                // Remove row
                if (e.target.classList.contains('remove-row')) {
                    let tbody = table.querySelector('tbody');
                    if (tbody.querySelectorAll('tr').length > 1) {
                        e.target.closest('tr').remove();
                    } else {
                        alert('At least one row is required.');
                    }
                }
            });
        });

    });
</script>