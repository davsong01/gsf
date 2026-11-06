@extends($isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard')

@section('title', 'Question Analysis')
@section('item')
<li class="breadcrumb-item"><a href="{{ route($isAdmin ? 'reports.analytics' : 'stakeholders.reports.analytics') }}">Report Analytics</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Question Analysis</li>
@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ asset('assets/css/report-analytics.css') }}">
@endsection

@section('content')
<div class="content-body analytics-workspace">
    <header class="analytics-hero">
        <div>
            <span class="analytics-eyebrow"><i class="fa fa-list-alt" aria-hidden="true"></i> Report responses</span>
            <h1>Question analysis</h1>
            <p>Compare chapter responses by month. Filter to a section or question to focus the comparison.</p>
        </div>
        <a class="analytics-hero__link" href="{{ route($isAdmin ? 'reports.analytics' : 'stakeholders.reports.analytics') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> All analytics</a>
    </header>
    @include('admin.reports.analytics.filter')
    <div class="analytics-section-heading"><h2>Monthly responses</h2><p>Scroll across to compare answers. The chapter name stays in view.</p></div>
    <div class="analytics-months">
        @forelse($reports as $month => $sheet)
            @php
                $visibleHeaders = array_values(array_filter($sheet['headers'], fn ($header) => strcasecmp(trim($header), 'Income Records') !== 0));
                $incomeColumns = [];
                $incomeRows = [];
                foreach ($sheet['rows'] as $recordRow) {
                    $income = $recordRow['_income_records'] ?? [];
                    if (is_string($income)) {
                        $income = json_decode($income, true) ?: [];
                    }
                    if (!is_array($income)) continue;
                    foreach ($income as $week => $values) {
                        if (!is_array($values)) continue;
                        $normalizedValues = [];
                        foreach ($values as $column => $value) {
                            $label = strcasecmp((string) $column, 'Remark') === 0 ? 'Remarks' : (string) $column;
                            $normalizedValues[$label] = $value;
                            if (!in_array($label, $incomeColumns, true)) $incomeColumns[] = $label;
                        }
                        $incomeRows[] = ['Chapter' => $recordRow['Chapter'] ?? '—', 'Week' => $week] + $normalizedValues;
                    }
                }
            @endphp
            <section class="analytics-card" aria-label="Responses for {{ $month }}">
                <div class="analytics-card__head">
                    <div><h2>{{ $month }}</h2><p>{{ count($sheet['rows']) }} {{ count($sheet['rows']) === 1 ? 'chapter' : 'chapters' }} with responses</p></div>
                    @if(count($sheet['rows']))
                        <label><span class="sr-only">Search chapters in {{ $month }}</span><input type="search" class="analytics-month__search" placeholder="Find a chapter" aria-label="Find a chapter in {{ $month }}"></label>
                    @else
                        <span class="analytics-card__badge"><i class="fa fa-inbox" aria-hidden="true"></i> No responses</span>
                    @endif
                </div>
                @if(count($sheet['rows']))
                    <div class="analytics-month__table-wrap">
                        <table class="analytics-table">
                            <thead><tr>@foreach($visibleHeaders as $header)<th scope="col">{{ $header }}</th>@endforeach</tr></thead>
                            <tbody>
                                @foreach($sheet['rows'] as $row)
                                    <tr>@foreach($visibleHeaders as $header)<td>{{ $row[$header] ?? '—' }}</td>@endforeach</tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="analytics-empty analytics-month__no-match" hidden>No chapters match this search.</div>
                    @if(count($incomeRows))
                        <div class="analytics-income">
                            <div class="analytics-income__head"><div><h3>Income records</h3><p>Weekly figures submitted by each chapter.</p></div><span class="analytics-card__badge">{{ count($incomeRows) }} weekly entries</span></div>
                            <div class="analytics-month__table-wrap">
                                <table class="analytics-table analytics-table--income">
                                    <thead><tr><th scope="col">Chapter</th><th scope="col">Week</th>@foreach($incomeColumns as $column)<th scope="col">{{ $column }}</th>@endforeach</tr></thead>
                                    <tbody>@foreach($incomeRows as $incomeRow)<tr><td>{{ $incomeRow['Chapter'] }}</td><td>{{ $incomeRow['Week'] }}</td>@foreach($incomeColumns as $column)<td>{{ $incomeRow[$column] ?? '—' }}</td>@endforeach</tr>@endforeach</tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="analytics-empty"><i class="fa fa-inbox" aria-hidden="true"></i>No responses found for this month and filter selection.</div>
                @endif
            </section>
        @empty
            <div class="analytics-card analytics-empty"><i class="fa fa-inbox" aria-hidden="true"></i>No responses found for this filter selection.</div>
        @endforelse
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
$(function () {
    const questions = @json($questions ?? []);
    const selectedSubSections = @json(array_map('strval', (array) request('sub_sections', [])));
    const selectedQuestions = @json(array_map('strval', (array) request('questions', [])));
    const sectionSelect = $('.sections-select');
    const subSectionSelect = $('.sub-sections-select');
    const questionSelect = $('.questions-select');

    function loadQuestions(sectionIds, subSectionIds, selected) {
        const sections = (sectionIds || []).map(String).filter(id => id !== 'all');
        const subSections = (subSectionIds || []).map(String).filter(id => id !== 'all');
        questionSelect.empty();
        questions.filter(question =>
            (!sections.length || sections.includes(String(question.section_id))) &&
            (!subSections.length || subSections.includes(String(question.sub_section_id)))
        ).forEach(question => {
            const id = String(question.id);
            questionSelect.append(new Option(question.label, id, selected.includes(id), selected.includes(id)));
        });
        questionSelect.trigger('change.select2');
    }

    function loadSubSections(sectionIds, selectedSub = [], selectedQuestion = []) {
        const sections = (sectionIds || []).map(String).filter(id => id !== 'all');
        subSectionSelect.empty();
        if (!sections.length) {
            subSectionSelect.prop('disabled', false).trigger('change.select2');
            loadQuestions(sectionIds, [], selectedQuestion);
            return;
        }
        subSectionSelect.prop('disabled', true);
        $.getJSON("{{ route('get.sub-sections') }}", { sections })
            .done(function (items) {
                (Array.isArray(items) ? items : []).forEach(item => {
                    const id = String(item.id);
                    subSectionSelect.append(new Option(item.name, id, selectedSub.includes(id), selectedSub.includes(id)));
                });
                subSectionSelect.prop('disabled', false).trigger('change.select2');
                loadQuestions(sectionIds, subSectionSelect.val() || [], selectedQuestion);
            })
            .fail(function () {
                subSectionSelect.prop('disabled', false).trigger('change.select2');
                loadQuestions(sectionIds, [], selectedQuestion);
            });
    }

    sectionSelect.on('change', function () { loadSubSections(sectionSelect.val() || [], [], questionSelect.val() || []); });
    subSectionSelect.on('change', function () { loadQuestions(sectionSelect.val() || [], subSectionSelect.val() || [], questionSelect.val() || []); });
    loadSubSections(sectionSelect.val() || [], selectedSubSections, selectedQuestions);

    $('.analytics-month__search').on('input', function () {
        const card = $(this).closest('.analytics-card');
        const query = this.value.trim().toLocaleLowerCase();
        let visible = 0;
        card.find('.analytics-table tbody tr').each(function () {
            const match = $(this).find('td:first').text().toLocaleLowerCase().includes(query);
            $(this).toggle(match);
            if (match) visible++;
        });
        card.find('.analytics-month__no-match').prop('hidden', visible > 0);
    });
});
</script>
@endsection
