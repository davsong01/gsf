@extends($isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard')

@section('title', 'Growth Analysis')
@section('item')
<li class="breadcrumb-item"><a href="{{ route($isAdmin ? 'reports.analytics' : 'stakeholders.reports.analytics') }}">Report Analytics</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Growth Analysis</li>
@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ asset('assets/css/report-analytics.css') }}?v={{ filemtime(public_path('assets/css/report-analytics.css')) }}">
@endsection

@section('content')
<div class="content-body analytics-workspace growth-workspace">
    <header class="analytics-hero">
        <div>
            <span class="analytics-eyebrow"><i class="fa fa-area-chart" aria-hidden="true"></i> Fellowship progress</span>
            <h1>Growth analysis</h1>
            <p>Explore measurable progress across campuses, zones and fields. Choose one measure to compare like with like.</p>
        </div>
        <a class="analytics-hero__link" href="{{ route($isAdmin ? 'reports.analytics' : 'stakeholders.reports.analytics') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> All analytics</a>
    </header>

    <section class="analytics-filter" aria-labelledby="growth-filter-title">
        <div class="analytics-filter__head"><span class="analytics-filter__icon"><i class="fa fa-sliders"></i></span><div><h2 id="growth-filter-title">Filter the analysis</h2><p>Choose the reporting level, measure and months to compare.</p></div></div>
        <form id="growthFilters" class="growth-filter-form">
            <div class="analytics-filter__body growth-filter-grid">
                <div class="growth-level-control"><span class="growth-level-control__label">Compare</span><div class="growth-level-switch" role="group" aria-label="Comparison level"><label class="growth-level-option"><input type="radio" name="level" value="chapter" checked><span>Campuses</span></label><label class="growth-level-option"><input type="radio" name="level" value="zone"><span>Zones</span></label><label class="growth-level-option"><input type="radio" name="level" value="field"><span>Fields</span></label></div></div>
                <div class="analytics-filter__field growth-filter-grid__measure"><label for="growthMeasure">Growth measure</label><select id="growthMeasure" name="measure_id" class="form-select">@foreach($measures as $measure)<option value="{{ $measure['id'] }}">{{ $measure['label'] }}{{ $measure['kind'] === 'rating' ? ' (1–5 rating)' : '' }}</option>@endforeach</select><span id="growthMeasureHint" class="growth-filter-hint">Counts scale to the highest reported value; ratings scale to 5. Answered questions contribute equally.</span></div>
                <div class="analytics-filter__field"><label for="growthFrom">From month</label><input id="growthFrom" name="from_month" type="month" class="form-control" value="{{ now()->startOfYear()->format('Y-m') }}"></div>
                <div class="analytics-filter__field"><label for="growthTo">To month</label><input id="growthTo" name="to_month" type="month" class="form-control" value="{{ now()->format('Y-m') }}"></div>
                <div class="analytics-filter__field" id="growthFieldWrap"><label for="growthField">Field</label><select id="growthField" name="field_id" class="form-select"><option value="">All fields</option>@foreach($fields as $field)<option value="{{ $field->id }}">{{ $field->name }}</option>@endforeach</select></div>
                <div class="analytics-filter__field" id="growthZoneWrap"><label for="growthZone">Zone</label><select id="growthZone" name="zone_id" class="form-select"><option value="">All zones</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" data-field="{{ $zone->field_id }}">{{ $zone->name }}</option>@endforeach</select></div>
            </div>
            <div class="analytics-filter__actions"><p>Months when a school was not in session are excluded. Zone and field values average reporting campuses.</p><div class="analytics-filter__buttons"><button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply filters</button><button id="growthExport" type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export Excel</button></div></div>
        </form>
    </section>

    <div id="growthAlert" class="growth-alert" role="alert" hidden></div>
    <div class="growth-summary" aria-label="Growth summary">
        <div class="growth-stat growth-stat--up"><span>Growing</span><strong id="growthGrowing">—</strong></div>
        <div class="growth-stat growth-stat--down"><span>Declining</span><strong id="growthDeclining">—</strong></div>
        <div class="growth-stat growth-stat--flat"><span>Unchanged</span><strong id="growthStable">—</strong></div>
        <div class="growth-stat growth-stat--unknown"><span>Insufficient data</span><strong id="growthInsufficient">—</strong></div>
    </div>

    <div class="growth-main">
        <section class="analytics-card growth-chart-card" aria-labelledby="growth-chart-title">
            <div class="analytics-card__head"><div><h2 id="growth-chart-title">Monthly trend</h2><p id="growthChartDescription">Loading measurable growth…</p></div><div class="growth-chart-actions"><span id="growthMeasureBadge" class="analytics-card__badge"></span><div class="growth-period-switch" role="group" aria-label="Trend period"><button type="button" data-period="month" aria-pressed="true">Monthly</button><button type="button" data-period="year" aria-pressed="false">Yearly</button></div></div></div>
            <div class="growth-chart-wrap"><canvas id="growthChart" role="img" aria-label="Monthly growth comparison"></canvas></div>
            <div id="growthChartEmpty" class="analytics-empty" hidden>No values were reported for this selection.</div>
            <p id="growthChartNote" class="growth-chart-note" role="status"></p>
        </section>
        <aside class="analytics-card growth-selector" aria-labelledby="growth-selector-title">
            <div class="analytics-card__head"><div><h2 id="growth-selector-title">Campuses on chart</h2><p>Uncheck any line to focus the comparison.</p></div></div>
            <div class="growth-selector__tools"><input id="growthEntitySearch" type="search" placeholder="Search entities" aria-label="Search entities"><div><button id="growthSelectAll" type="button">Select all</button><button id="growthClearAll" type="button">Clear all</button></div></div>
            <div id="growthEntityList" class="growth-selector__list" role="group" aria-label="Entities shown on chart"></div>
        </aside>
    </div>

    <section class="analytics-card growth-table-card" aria-labelledby="growth-table-title">
        <div class="analytics-card__head"><div><h2 id="growth-table-title">Growth by campus</h2><p id="growthTableDescription">Click a row to inspect its measures. Change compares its latest two months with valid values in your selected range.</p></div><span id="growthRowCount" class="analytics-card__badge">—</span></div>
        <div class="growth-table-scroll"><table class="growth-table"><thead><tr><th scope="col">Entity</th><th scope="col">Previous report</th><th scope="col">Latest report</th><th scope="col">Change</th><th scope="col">Change %</th><th id="growthCoverageHeading" scope="col">Months reported</th><th scope="col">Trend</th></tr></thead><tbody id="growthTableBody"></tbody></table></div>
    </section>

    <section class="analytics-card growth-aspects-card" aria-labelledby="growth-aspects-title">
        <div class="analytics-card__head"><div><h2 id="growth-aspects-title">Aspects of growth</h2><p id="growthAspectsDescription">Select an entity to see the questions where it is growing or declining.</p></div></div>
        <div class="growth-table-scroll"><table class="growth-table"><thead><tr><th scope="col">Question</th><th scope="col">Previous report</th><th scope="col">Latest report</th><th scope="col">Change</th><th scope="col">Trend</th></tr></thead><tbody id="growthAspectsBody"></tbody></table></div>
    </section>
</div>
@endsection

@section('extra_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const endpoint = @json(route($isAdmin ? 'reports.analytics.growth' : 'stakeholders.reports.analytics.growth'));
    const zones = @json($zones->map(fn ($zone) => ['id' => $zone->id, 'name' => $zone->name, 'field_id' => $zone->field_id])->values());
    const form = document.getElementById('growthFilters');
    const list = document.getElementById('growthEntityList');
    const alertBox = document.getElementById('growthAlert');
    const chartCanvas = document.getElementById('growthChart');
    const palette = ['#256c89', '#cf8d3f', '#3d8875', '#a35d78', '#6a70ad', '#c16f53', '#537a52', '#7c678e', '#3b8792', '#a57836'];
    let chart = null;
    let selectedIds = null;
    let focusId = null;
    let currentResult = null;
    let requestNumber = 0;
    let period = 'month';
    let dateRangeEdited = false;
    const defaultFrom = form.from_month.value;

    const number = value => value == null ? '—' : Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 });
    const signed = value => value == null ? '—' : (value > 0 ? '+' : '') + number(value);
    const statusText = { growing: 'Growing', declining: 'Declining', stable: 'Unchanged', insufficient: 'Insufficient data' };
    const monthLabel = month => new Date(month + '-01T00:00:00').toLocaleDateString(undefined, { month: 'short', year: 'numeric' });
    const periodLabel = (key, unit) => unit === 'year' ? key : monthLabel(key);
    const datedValue = (value, key, unit) => value == null ? '—' : number(value) + ' · ' + periodLabel(key, unit);
    function element(tag, className, value) { const node = document.createElement(tag); if (className) node.className = className; if (value != null) node.textContent = value; return node; }
    function showError(message) { alertBox.textContent = message; alertBox.hidden = !message; }
    function selectedLevel() { return form.querySelector('input[name="level"]:checked').value; }
    function syncFilters() {
        const level = selectedLevel();
        document.getElementById('growthFieldWrap').hidden = level === 'field';
        document.getElementById('growthZoneWrap').hidden = level !== 'chapter';
        const fieldId = form.field_id.value;
        const zoneId = form.zone_id.value;
        form.zone_id.replaceChildren(new Option('All zones', ''));
        zones.filter(zone => !fieldId || String(zone.field_id) === fieldId).forEach(zone => form.zone_id.add(new Option(zone.name, zone.id)));
        form.zone_id.value = Array.from(form.zone_id.options).some(option => option.value === zoneId) ? zoneId : '';
    }
    function params(format) {
        const query = new URLSearchParams(new FormData(form));
        query.set('format', format);
        query.set('period', period);
        if (selectedLevel() === 'field') { query.delete('field_id'); query.delete('zone_id'); }
        if (selectedLevel() === 'zone') query.delete('zone_id');
        if (selectedIds !== null) (selectedIds.length ? selectedIds : [0]).forEach(id => query.append('entity_ids[]', id));
        if (focusId) query.set('focus_id', focusId);
        return query;
    }
    async function load() {
        const sequence = ++requestNumber;
        showError('');
        document.getElementById('growthChartDescription').textContent = 'Loading growth figures…';
        try {
            const response = await fetch(endpoint + '?' + params('json').toString(), { headers: { Accept: 'application/json' } });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || Object.values(result.errors || {}).flat().join(' ') || 'The analysis could not be loaded.');
            if (sequence !== requestNumber) return;
            currentResult = result;
            selectedIds = result.selected_ids;
            focusId = result.focus_id;
            render(result);
        } catch (error) { if (sequence === requestNumber) { showError(error.message); document.getElementById('growthChartDescription').textContent = 'Unable to load the chart.'; } }
    }
    function render(result) {
        document.getElementById('growthGrowing').textContent = result.summary.growing;
        document.getElementById('growthDeclining').textContent = result.summary.declining;
        document.getElementById('growthStable').textContent = result.summary.stable;
        document.getElementById('growthInsufficient').textContent = result.summary.insufficient;
        document.getElementById('growth-chart-title').textContent = result.period === 'year' ? 'Yearly trend' : 'Monthly trend';
        document.getElementById('growthChartDescription').textContent = result.measure.label + ' · ' + result.months.length + (result.period === 'year' ? ' years · ' : ' months · ') + result.series.length + ' selected ' + (result.level === 'chapter' ? 'campuses' : result.level === 'zone' ? 'zones' : 'fields') + (result.period === 'year' ? ' · yearly average of reported months' : '');
        document.getElementById('growth-selector-title').textContent = (result.level === 'chapter' ? 'Campuses' : result.level === 'zone' ? 'Zones' : 'Fields') + ' on chart';
        document.getElementById('growth-table-title').textContent = 'Growth by ' + (result.level === 'chapter' ? 'campus' : result.level);
        document.getElementById('growthTableDescription').textContent = 'Click a row to inspect its measures. Change compares its latest two ' + (result.period === 'year' ? 'years' : 'months') + ' with valid values in your selected range.';
        document.getElementById('growthMeasureBadge').textContent = result.measure.kind === 'index' ? 'Overall index / 100' : result.measure.kind === 'rating' ? 'Average rating / 5' : result.level === 'chapter' ? 'Reported count' : 'Average per campus';
        document.getElementById('growthMeasureHint').hidden = result.measure.kind !== 'index';
        document.getElementById('growthRowCount').textContent = result.series.length + ' shown';
        document.getElementById('growthCoverageHeading').textContent = result.period === 'year' ? 'Years reported' : 'Months reported';
        renderSelector(result);
        renderChart(result);
        renderTable(result);
        renderAspects(result);
    }
    function renderSelector(result) {
        const search = document.getElementById('growthEntitySearch').value.trim().toLowerCase();
        list.replaceChildren();
        result.entities.filter(entity => entity.name.toLowerCase().includes(search)).forEach(entity => {
            const label = element('label', 'growth-entity');
            const checkbox = element('input'); checkbox.type = 'checkbox'; checkbox.value = entity.id; checkbox.checked = selectedIds.includes(entity.id);
            checkbox.addEventListener('change', () => { selectedIds = result.entities.filter(item => item.id === entity.id ? checkbox.checked : selectedIds.includes(item.id)).map(item => item.id); if (!selectedIds.includes(focusId)) focusId = selectedIds[0] || null; load(); });
            label.append(checkbox, element('span', '', entity.name)); list.append(label);
        });
        if (!list.children.length) list.append(element('div', 'analytics-empty', 'No matching entities.'));
    }
    function renderChart(result) {
        if (chart) { chart.destroy(); chart = null; }
        const reportedCounts = result.series.map(row => row.values.filter(value => value !== null).length);
        const anyValues = reportedCounts.some(count => count > 0);
        const note = document.getElementById('growthChartNote');
        note.textContent = 'Growth measure: ' + result.measure.label;
        document.getElementById('growthChartEmpty').hidden = anyValues;
        chartCanvas.hidden = !anyValues;
        if (!anyValues) return;
        chart = new Chart(chartCanvas, {
            type: 'line',
            data: { labels: result.months.map(key => periodLabel(key, result.period)), datasets: result.series.map((row, index) => ({ label: row.name, data: row.values, contributors: row.contributors, borderColor: palette[index % palette.length], backgroundColor: palette[index % palette.length], borderWidth: row.id === focusId ? 3 : 2, pointRadius: reportedCounts[index] === 1 ? 4 : result.months.length > 18 ? 0 : 2, pointHoverRadius: 5, tension: .22, showLine: true, spanGaps: true, segment: { borderDash: context => context.p1DataIndex - context.p0DataIndex > 1 ? [6, 4] : [] } })) },
            options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'nearest', intersect: false }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: context => context.dataset.label + ': ' + number(context.parsed.y) + (result.period === 'year' ? ' · ' + context.dataset.contributors[context.dataIndex] + ' reported months' : result.level === 'chapter' ? '' : ' · ' + context.dataset.contributors[context.dataIndex] + ' contributing campuses') } } }, scales: { x: { grid: { display: false }, ticks: { maxTicksLimit: 12 } }, y: { beginAtZero: true, title: { display: true, text: result.measure.kind === 'index' ? 'Overall growth index (0–100)' : result.measure.kind === 'rating' ? 'Average rating (1–5)' : result.level === 'chapter' ? 'Reported count' : 'Average per reporting campus' }, suggestedMax: result.measure.kind === 'index' ? 100 : result.measure.kind === 'rating' ? 5 : undefined } } }
        });
    }
    function renderTable(result) {
        const body = document.getElementById('growthTableBody'); body.replaceChildren();
        const rows = [...result.series].sort((a, b) => ({ declining: 0, insufficient: 1, stable: 2, growing: 3 }[a.status] - { declining: 0, insufficient: 1, stable: 2, growing: 3 }[b.status] || a.name.localeCompare(b.name)));
        rows.forEach(row => {
            const tr = element('tr', row.id === focusId ? 'is-focused' : ''); tr.tabIndex = 0; tr.title = 'View growth aspects for ' + row.name;
            [row.name, datedValue(row.previous, row.previous_month, result.period), datedValue(row.latest, row.latest_month, result.period), signed(row.change), row.percent == null ? '—' : signed(row.percent) + '%', row.reported_months + ' / ' + result.months.length].forEach(value => tr.append(element('td', '', value)));
            const statusCell = element('td'); statusCell.append(element('span', 'growth-status growth-status--' + row.status, statusText[row.status])); tr.append(statusCell);
            const focus = () => { focusId = row.id; load(); document.getElementById('growth-aspects-title').scrollIntoView({ behavior: 'smooth', block: 'nearest' }); };
            tr.addEventListener('click', focus); tr.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); focus(); } }); body.append(tr);
        });
        if (!rows.length) { const tr = element('tr'); const td = element('td', 'growth-table-empty', 'Select at least one entity to see growth.'); td.colSpan = 7; tr.append(td); body.append(tr); }
    }
    function renderAspects(result) {
        const body = document.getElementById('growthAspectsBody'); body.replaceChildren();
        document.getElementById('growthAspectsDescription').textContent = result.focus_name ? result.focus_name + ' · Growth across quantifiable report questions. ' + result.aggregation + '.' : 'Select an entity to see its growth aspects.';
        const items = result.aspects.filter(item => item.reported_months).sort((a, b) => ({ growing: 0, declining: 1, stable: 2, insufficient: 3 }[a.status] - { growing: 0, declining: 1, stable: 2, insufficient: 3 }[b.status] || a.name.localeCompare(b.name)));
        items.forEach(item => { const tr = element('tr'); [item.name + (item.kind === 'rating' ? ' · rating' : ''), datedValue(item.previous, item.previous_month, result.period), datedValue(item.latest, item.latest_month, result.period), signed(item.change)].forEach(value => tr.append(element('td', '', value))); const td = element('td'); td.append(element('span', 'growth-status growth-status--' + item.status, statusText[item.status])); tr.append(td); body.append(tr); });
        if (!items.length) { const tr = element('tr'); const td = element('td', 'growth-table-empty', 'No measured aspects are available for this entity and period.'); td.colSpan = 5; tr.append(td); body.append(tr); }
    }
    form.addEventListener('submit', event => { event.preventDefault(); load(); });
    form.querySelectorAll('input[name="level"]').forEach(input => input.addEventListener('change', () => { selectedIds = null; focusId = null; syncFilters(); load(); }));
    form.measure_id.addEventListener('change', () => { document.getElementById('growthMeasureHint').hidden = form.measure_id.value !== '0'; });
    form.field_id.addEventListener('change', () => { selectedIds = null; focusId = null; syncFilters(); load(); });
    form.zone_id.addEventListener('change', () => { selectedIds = null; focusId = null; load(); });
    [form.from_month, form.to_month].forEach(input => input.addEventListener('change', () => { dateRangeEdited = true; }));
    document.querySelectorAll('.growth-period-switch button').forEach(button => button.addEventListener('click', () => {
        if (period === button.dataset.period) return;
        period = button.dataset.period;
        document.querySelectorAll('.growth-period-switch button').forEach(item => item.setAttribute('aria-pressed', String(item.dataset.period === period)));
        if (!dateRangeEdited) form.from_month.value = period === 'year' ? String(Number(defaultFrom.slice(0, 4)) - 3) + '-01' : defaultFrom;
        load();
    }));
    document.getElementById('growthEntitySearch').addEventListener('input', () => { if (currentResult) renderSelector(currentResult); });
    document.getElementById('growthSelectAll').addEventListener('click', () => { if (!currentResult) return; selectedIds = currentResult.entities.map(entity => entity.id); load(); });
    document.getElementById('growthClearAll').addEventListener('click', () => { selectedIds = []; focusId = null; load(); });
    document.getElementById('growthExport').addEventListener('click', () => { window.location.href = endpoint + '?' + params('excel').toString(); });
    syncFilters(); load();
});
</script>
@endsection
