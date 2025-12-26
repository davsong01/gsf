@extends('layouts.stakeholderdashboard')

@section('extra_styles')
<style>
    .section-card {
        background-color: #f9f9f9;
        border: 1px solid #d1d1d1;
        border-radius: 8px;
        margin-top: 25px;
        padding: 10px;
    }

    .add-row,.remove-row{
        padding: 0.167rem 0.5rem !important;
    }

    .header-section{
        background-color: #004080;
        color: #fff;
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 1.25rem;
    }

    .sub-section-card {
        border-left: 4px solid #004080;
        background-color: #ffffff;
        padding: 5px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .sub-section{
        color: #004080;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 15px;
    }

    label {
        margin-top: 15px !important;
        font-weight: 500;
    }

    .totals-row {
        font-weight: bold;
        background-color: #f2f2f2;
    }

    .dynamic-table input, .income-table input {
        height: calc(1.8em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
    }

    .dynamic-table th, .income-table th {
        background-color: #e9ecef;
        text-align: center;
    }

    .dynamic-table td, .income-table td {
        text-align: center;
        vertical-align: middle;
    }

    .table-responsive {
        overflow-x: auto;
        margin-bottom: 20px;
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
        <form action="{{ isset($report) ? route('stakeholders.reports.update', $report->id) : route('stakeholders.reports.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('You are about to submit this report, action is irreversible');">
            @csrf
            @if(isset($report))
                @method('PUT')
            @endif
            
            @foreach($sections as $section)
                @php $sectionAccess = app('App\Services\StakeholderRolePermissionService'::class)->sectionAccess($user, $section); @endphp
                @if($sectionAccess['view'])
                    <div class="section-card">
                        <h3 class="header-section">{{ $section->name }}</h3>

                        @foreach($section->subsections as $subsection)
                            @php $subAccess =  app('App\Services\StakeholderRolePermissionService'::class)->sectionAccess($user, $section); @endphp
                            @if($subAccess['view'])
                                <div class="sub-section-card">
                                    <h5 class="sub-section">{{ $subsection->name }}</h5>
                                    <div class="row">
                                        @foreach($subsection->questions as $question)
                                            @php
                                                $qAccess = app('App\Services\StakeholderRolePermissionService'::class)->questionAccess($user, $question);
                                                
                                                $value = old('responses.' . $question->slug);
                                                if(isset($report) && $report->answers) {
                                                    $answer = $report->answers->firstWhere('question_id', $question->id);
                                                    if($answer) {
                                                        $decoded = json_decode($answer->answer_value, true);
                                                        $value = $decoded ?? $answer->answer_value;
                                                    }
                                                }
                                                if(!$value && isset($prefillData[$question->slug])) {
                                                    $value = $prefillData[$question->slug];
                                                }

                                                $disabled = !$qAccess['edit'] ? 'disabled' : '';
                                            @endphp

                                            <div class="{{ $question->width_class ?? 'col-md-6' }} mb-1">
                                                <label for="{{ $question->slug }}">
                                                    {{ $question->label }}
                                                    @if($question->is_required && $qAccess['edit']) <span class="text-danger">*</span> @endif
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
                                                            {{ $disabled }}
                                                            @if($question->is_required && $qAccess['edit']) required @endif>
                                                        @break

                                                    @case('textarea')
                                                        <textarea class="form-control"
                                                                id="{{ $question->slug }}"
                                                                name="responses[{{ $question->slug }}]"
                                                                rows="3"
                                                                {{ $disabled }}
                                                                @if($question->is_required && $qAccess['edit']) required @endif>{{ $value }}</textarea>
                                                        @break

                                                    @case('select')
                                                        <select class="form-select"
                                                                id="{{ $question->slug }}"
                                                                name="responses[{{ $question->slug }}]"
                                                                {{ $disabled }}
                                                                @if($question->is_required && $qAccess['edit']) required @endif>
                                                            <option value="">Select...</option>
                                                            @foreach($question->options as $optKey => $optLabel)
                                                                <option value="{{ $optKey }}" {{ $value == $optKey ? 'selected' : '' }}>
                                                                    {{ $optLabel }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @break

                                                    @case('dynamic_table')
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered dynamic-table" data-slug="{{ $question->slug }}">
                                                                <thead>
                                                                    <tr>
                                                                        @foreach($question->options as $col)
                                                                            <th>{{ $col['label'] ?? $col }}</th>
                                                                        @endforeach
                                                                        @if($qAccess['edit'])
                                                                            <th>Action</th>
                                                                        @endif
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $rows = $value ?? [[]]; @endphp
                                                                    @foreach($rows as $rowIndex => $row)
                                                                        <tr>
                                                                            @foreach($question->options as $col)
                                                                                @php $colLabel = $col['label'] ?? $col; @endphp
                                                                                <td>
                                                                                    <input type="{{ $col['type'] ?? 'text' }}"
                                                                                        name="responses[{{ $question->slug }}][{{ $rowIndex }}][{{ $colLabel }}]"
                                                                                        class="form-control"
                                                                                        value="{{ $row[$colLabel] ?? '' }}"
                                                                                        {{ $disabled }}
                                                                                        @if(!empty($col['required']) && $qAccess['edit']) required @endif>
                                                                                </td>
                                                                            @endforeach
                                                                            @if($qAccess['edit'])
                                                                                <td>
                                                                                    <button type="button" class="btn btn-sm btn-success add-row">+</button>
                                                                                    <button type="button" class="btn btn-sm btn-danger remove-row">-</button>
                                                                                </td>
                                                                            @endif
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        @break

                                                    @case('income_table')
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered income-table" data-slug="{{ $question->slug }}">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Week</th>
                                                                        @foreach($question->options['columns'] as $col)
                                                                            <th>{{ $col }}</th>
                                                                        @endforeach
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($question->options['rows'] as $week)
                                                                        <tr>
                                                                            <td>{{ $week }}</td>
                                                                            @foreach($question->options['columns'] as $col)
                                                                                <td>
                                                                                    <input type="{{ $col == 'Remarks' ? 'text' : 'number' }}"
                                                                                        class="form-control numeric-input"
                                                                                        name="responses[{{ $question->slug }}][{{ $week }}][{{ $col }}]"
                                                                                        value="{{ $value[$week][$col] ?? '' }}"
                                                                                        {{ $disabled }}
                                                                                        @if($qAccess['edit'] && $col != 'Remarks') required min="1" @endif>
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr class="totals-row">
                                                                        <td>Totals</td>
                                                                        @foreach($question->options['columns'] as $col)
                                                                            @if($col != 'Remarks')
                                                                            <td class="total-cell" data-column="{{ $col }}">0</td>
                                                                            @endif
                                                                        @endforeach
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                        @break

                                                @endswitch
                                            </div>
                                            
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endforeach


            <div class="card mb-4">
                <div class="card-body">
                    <div class="form-check">
                        <input type="checkbox" name="confirm_information" value="1" class="form-check-input" id="confirmInfo" required>
                        <label style="margin-top: 0 !important" class="form-check-label fw-semibold" for="confirmInfo">
                            I hereby confirm that all information provided in this report is true, current, and accurate to the best of my knowledge.
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary w-100" type="submit">{{ isset($report) ? 'Update Report' : 'Submit' }}</button>
            </div>
        </form>
    </section>
</div>
@endsection

@section('extra_scripts')
<script>
    // Dynamic table row add/remove
    $(document).on('click', '.add-row', function(){
        let table = $(this).closest('table');
        let newRow = table.find('tbody tr:first').clone();
        newRow.find('input').val('');
        table.find('tbody').append(newRow);
        recalcTotals(table);
    });

    $(document).on('click', '.remove-row', function(){
        let table = $(this).closest('table');
        if(table.find('tbody tr').length > 1){
            $(this).closest('tr').remove();
            recalcTotals(table);
        }
    });

    // Income Table Totals
    function recalcTotals(table){
        if(table.hasClass('income-table')){
            table.find('tfoot td.total-cell').each(function(){
                let col = $(this).data('column');
                let sum = 0;
                table.find('tbody tr').each(function(){
                    let val = parseFloat($(this).find('input[name*="['+col+']"]').val()) || 0;
                    sum += val;
                });
                $(this).text(sum.toFixed(2));
            });
        }
    }

    $(document).on('input', '.income-table .numeric-input', function(){
        let table = $(this).closest('table');
        recalcTotals(table);
    });

    // Initialize totals on page load
    $('.income-table').each(function(){
        recalcTotals($(this));
    });
</script>
@endsection
