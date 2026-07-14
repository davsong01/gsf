@php
    $layout = $isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard';
@endphp

@extends($layout)

@section('title', 'Compliance Analytics')

@if($isAdmin)
    @section('item')
        <li class="breadcrumb-item">
            <a href="{{ route('reports.analytics') }}">Monthly Reports</a>
        </li>
    @endsection
@else
    @section('active')
        <li class="breadcrumb-item">Monthly Reports</li>
    @endsection
@endif

@section('content')
<style>
    .report-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: separate; /* IMPORTANT for sticky headers */
        border-spacing: 0;
    }

    /* cells */
    .report-table th,
    .report-table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    /* sticky header */
    .report-table thead th {
        position: sticky;
        top: 0;
        z-index: 100;
        background: #f8f9fa; /* match table-light */
        box-shadow: 0 2px 2px rgba(0,0,0,0.05);
    }

    /* hover expansion */
    .report-table td:hover {
        white-space: normal;
        position: relative;
        z-index: 50;
        background: #fff;
        box-shadow: 0 0 5px rgba(0,0,0,0.15);
    }
</style>
<div class="row mb-2">
    <div class="col-12">
        @include('admin.reports.analytics.filter')
    </div>
</div>
<div class="row mb-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                @forelse($reports as $month => $sheet)
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="mb-0">{{ $month }}</h6>
                        </div>

                        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                            <table class="table table-bordered table-sm mb-0 report-table">
                                <thead class="table-light">
                                    <tr>
                                        @foreach($sheet['headers'] as $header)
                                            <th>{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($sheet['rows'] as $row)
                                        <tr>
                                            @foreach($sheet['headers'] as $header)
                                                <td>{{ $row[$header] ?? '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($sheet['headers']) }}"
                                                class="text-center text-muted">
                                                No records found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        No records found
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
$(document).ready(function () {

    const questions = @json($questions ?? []);
    const initiallySelectedQuestions = @json(array_map('strval', (array) request('questions', [])));

    function loadQuestions(sectionIds = [], subSectionIds = [], selected = []) {
        const questionSelect = $('.questions-select');
        const sectionValues = (sectionIds || []).map(String);
        const subSectionValues = (subSectionIds || []).map(String);
        const selectedSections = sectionValues.includes('all') ? [] : sectionValues;
        const selectedSubSections = subSectionValues.includes('all') ? [] : subSectionValues;

        questionSelect.empty();

        questions
            .filter(function (question) {
                const sectionMatches = selectedSections.length === 0 || selectedSections.includes(String(question.section_id));
                const subSectionMatches = selectedSubSections.length === 0 || selectedSubSections.includes(String(question.sub_section_id));
                return sectionMatches && subSectionMatches;
            })
            .forEach(function (question) {
                const id = String(question.id);
                questionSelect.append(new Option(question.label, id, selected.includes(id), selected.includes(id)));
            });

        questionSelect.trigger('change.select2');
    }

    function loadSubSections(sectionIds = [], selected = [], selectedQuestions = []) {

        let subSelect = $('.sub-sections-select');

        subSelect.prop('disabled', true).empty();

        if (!sectionIds || sectionIds.length === 0 || sectionIds.includes('all')) {
            subSelect.prop('disabled', false);
            return;
        }

        $.ajax({
            url: "{{ route('get.sub-sections') }}",
            type: "GET",
            dataType: "json",
            data: {
                sections: sectionIds || []
            },
            success: function (res) {

                subSelect.empty();

                if (!Array.isArray(res)) {
                    subSelect.prop('disabled', false);
                    return;
                }

                res.forEach(function (item) {

                    subSelect.append(
                        new Option(
                            item.name,
                            item.id,
                            (selected || []).includes(String(item.id)),
                            (selected || []).includes(String(item.id))
                        )
                    );

                });

                subSelect.prop('disabled', false);
                loadQuestions(sectionIds, subSelect.val() || [], selectedQuestions);
            },
            error: function (xhr) {
                console.error('Subsection load failed:', xhr.responseText);
                subSelect.prop('disabled', false);
            }
        });
    }

    let sectionSelect = $('.sections-select');

    sectionSelect.on('change', function () {
        let selected = $(this).val() || [];
        loadSubSections(selected, [], $('.questions-select').val() || []);
    });

    $('.sub-sections-select').on('change', function () {
        loadQuestions(sectionSelect.val() || [], $(this).val() || [], $('.questions-select').val() || []);
    });

    // initial load
    loadSubSections(
        sectionSelect.val() || [],
        @json(request('sub_sections', []))
    );

    loadQuestions(
        sectionSelect.val() || [],
        @json(request('sub_sections', [])),
        initiallySelectedQuestions
    );

});
</script>
@endsection
