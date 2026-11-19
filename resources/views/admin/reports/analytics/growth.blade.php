@extends($isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard')

@section('title', 'Growth Analysis')
@section('item')
<li class="breadcrumb-item"><a href="{{ route($isAdmin ? 'reports.analytics' : 'stakeholders.reports.analytics') }}">Report Analytics</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Growth Analysis</li>
@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ asset('assets/css/report-analytics.css') }}">
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
                <div class="analytics-filter__field"><label for="growthLevel">Compare</label><select id="growthLevel" name="level" class="form-select"><option value="chapter">Campuses</option><option value="zone">Zones</option><option value="field">Fields</option></select></div>
                <div class="analytics-filter__field growth-filter-grid__measure"><label for="growthMeasure">Growth measure</label><select id="growthMeasure" name="measure_id" class="form-select">@foreach($measures as $measure)<option value="{{ $measure['id'] }}">{{ $measure['label'] }}{{ $measure['kind'] === 'rating' ? ' (1–5 rating)' : '' }}</option>@endforeach</select></div>
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
            <div class="analytics-card__head"><div><h2 id="growth-chart-title">Monthly trend</h2><p id="growthChartDescription">Loading measurable growth…</p></div><span id="growthMeasureBadge" class="analytics-card__badge"></span></div>
            <div class="growth-chart-wrap"><canvas id="growthChart" role="img" aria-label="Monthly growth comparison"></canvas></div>
            <div id="growthChartEmpty" class="analytics-empty" hidden>No values were reported for this selection.</div>
            <p id="growthChartNote" class="growth-chart-note" role="status"></p>
        </section>
        <aside class="analytics-card growth-selector" aria-labelledby="growth-selector-title">
            <div class="analytics-card__head"><div><h2 id="growth-selector-title">Entities on chart</h2><p>Uncheck any line to focus the comparison.</p></div></div>
            <div class="growth-selector__tools"><input id="growthEntitySearch" type="search" placeholder="Search entities" aria-label="Search entities"><div><button id="growthSelectAll" type="button">Select all</button><button id="growthClearAll" type="button">Clear all</button></div></div>
            <div id="growthEntityList" class="growth-selector__list" role="group" aria-label="Entities shown on chart"></div>
        </aside>
    </div>

    <section class="analytics-card growth-table-card" aria-labelledby="growth-table-title">
        <div class="analytics-card__head"><div><h2 id="growth-table-title">Growth by fellowship</h2><p>Click a row to inspect the measures behind its progress. First and latest compare reported months within your range.</p></div><span id="growthRowCount" class="analytics-card__badge">—</span></div>
        <div class="growth-table-scroll"><table class="growth-table"><thead><tr><th scope="col">Entity</th><th scope="col">First</th><th scope="col">Latest</th><th scope="col">Change</th><th scope="col">Change %</th><th scope="col">Months reported</th><th scope="col">Trend</th></tr></thead><tbody id="growthTableBody"></tbody></table></div>
    </section>

    <section class="analytics-card growth-aspects-card" aria-labelledby="growth-aspects-title">
        <div class="analytics-card__head"><div><h2 id="growth-aspects-title">Aspects of growth</h2><p id="growthAspectsDescription">Select an entity to see the questions where it is growing or declining.</p></div></div>
        <div class="growth-table-scroll"><table class="growth-table"><thead><tr><th scope="col">Question</th><th scope="col">First</th><th scope="col">Latest</th><th scope="col">Change</th><th scope="col">Trend</th></tr></thead><tbody id="growthAspectsBody"></tbody></table></div>
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

    const number = value => value == null ? '—' : Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 });
    const signed = value => value == null ? '—' : (value > 0 ? '+' : '') + number(value);
    const statusText = { growing: 'Growing', declining: 'Declining', stable: 'Unchanged', insufficient: 'Insufficient data' };
    const monthLabel = month => new Date(month + '-01T00:00:00').toLocaleDateString(undefined, { month: 'short', year: 'numeric' });
    function element(tag, className, value) { const node = document.createElement(tag); if (className) node.className = className; if (value != null) node.textContent = value; return node; }
    function showError(message) { alertBox.textContent = message; alertBox.hidden = !message; }
    function syncFilters() {
        const level = form.level.value;
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
        if (form.level.value === 'field') { query.delete('field_id'); query.delete('zone_id'); }
        if (form.level.value === 'zone') query.delete('zone_id');
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
        document.getElementById('growthChartDescription').textContent = result.measure.label + ' · ' + result.months.length + ' months · ' + result.series.length + ' selected ' + (result.level === 'chapter' ? 'campuses' : result.level === 'zone' ? 'zones' : 'fields');
        document.getElementById('growthMeasureBadge').textContent = result.measure.kind === 'rating' ? 'Average rating / 5' : result.level === 'chapter' ? 'Reported count' : 'Average per campus';
        document.getElementById('growthRowCount').textContent = result.series.length + ' shown';
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
            data: { labels: result.months.map(monthLabel), datasets: result.series.map((row, index) => ({ label: row.name, data: row.values, contributors: row.contributors, borderColor: palette[index % palette.length], backgroundColor: palette[index % palette.length], borderWidth: row.id === focusId ? 3 : 2, pointRadius: reportedCounts[index] === 1 ? 4 : result.months.length > 18 ? 0 : 2, pointHoverRadius: 5, tension: .22, showLine: true, spanGaps: true, segment: { borderDash: context => context.p1DataIndex - context.p0DataIndex > 1 ? [6, 4] : [] } })) },
            options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'nearest', intersect: false }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: context => context.dataset.label + ': ' + number(context.parsed.y) + (result.level === 'chapter' ? '' : ' · ' + context.dataset.contributors[context.dataIndex] + ' contributing campuses') } } }, scales: { x: { grid: { display: false }, ticks: { maxTicksLimit: 12 } }, y: { beginAtZero: true, title: { display: true, text: result.measure.kind === 'rating' ? 'Average rating (1–5)' : result.level === 'chapter' ? 'Reported count' : 'Average per reporting campus' }, suggestedMax: result.measure.kind === 'rating' ? 5 : undefined } } }
        });
    }
    function renderTable(result) {
        const body = document.getElementById('growthTableBody'); body.replaceChildren();
        const rows = [...result.series].sort((a, b) => ({ declining: 0, insufficient: 1, stable: 2, growing: 3 }[a.status] - { declining: 0, insufficient: 1, stable: 2, growing: 3 }[b.status] || a.name.localeCompare(b.name)));
        rows.forEach(row => {
            const tr = element('tr', row.id === focusId ? 'is-focused' : ''); tr.tabIndex = 0; tr.title = 'View growth aspects for ' + row.name;
            [row.name, number(row.first), number(row.latest), signed(row.change), row.percent == null ? '—' : signed(row.percent) + '%', row.reported_months + ' / ' + result.months.length].forEach(value => tr.append(element('td', '', value)));
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
        items.forEach(item => { const tr = element('tr'); [item.name + (item.kind === 'rating' ? ' · rating' : ''), number(item.first), number(item.latest), signed(item.change)].forEach(value => tr.append(element('td', '', value))); const td = element('td'); td.append(element('span', 'growth-status growth-status--' + item.status, statusText[item.status])); tr.append(td); body.append(tr); });
        if (!items.length) { const tr = element('tr'); const td = element('td', 'growth-table-empty', 'No measured aspects are available for this entity and period.'); td.colSpan = 5; tr.append(td); body.append(tr); }
    }
    form.addEventListener('submit', event => { event.preventDefault(); load(); });
    form.level.addEventListener('change', () => { selectedIds = null; focusId = null; syncFilters(); load(); });
    form.field_id.addEventListener('change', () => { selectedIds = null; focusId = null; syncFilters(); load(); });
    form.zone_id.addEventListener('change', () => { selectedIds = null; focusId = null; load(); });
    document.getElementById('growthEntitySearch').addEventListener('input', () => { if (currentResult) renderSelector(currentResult); });
    document.getElementById('growthSelectAll').addEventListener('click', () => { if (!currentResult) return; selectedIds = currentResult.entities.map(entity => entity.id); load(); });
    document.getElementById('growthClearAll').addEventListener('click', () => { selectedIds = []; focusId = null; load(); });
    document.getElementById('growthExport').addEventListener('click', () => { window.location.href = endpoint + '?' + params('excel').toString(); });
    syncFilters(); load();
});
</script>
@endsection
