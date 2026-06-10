@props(['route' => null, 'level' => null, 'type' => null])

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let currentGroup = 'chapter';
    let downloadType = null;

    const compareBtn = document.getElementById('compareBtn');
    const compareModalEl = document.getElementById('compareModal');
    const compareModal = compareModalEl ? new bootstrap.Modal(compareModalEl) : null;
    const compareRadios = document.querySelectorAll('.compare-radio');

    let chart = null;
    let fullGraphData = null;

    const ctx = document.getElementById('reportGraph')?.getContext('2d');

    // -----------------------------
    // Compare group handling
    // -----------------------------
    function updateCompareBtnText() {
        if (!compareBtn) return;
        const span = compareBtn.querySelector('span');
        if (span) {
            span.textContent = currentGroup.charAt(0).toUpperCase() + currentGroup.slice(1);
        }
    }

    compareBtn?.addEventListener('click', () => {
        compareRadios.forEach(r => r.checked = r.value === currentGroup);
        compareModal?.show();
    });

    compareRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (!this.checked) return;

            currentGroup = this.value;
            updateCompareBtnText();

            fetchGraph();
            compareModal?.hide();
        });
    });

    updateCompareBtnText();

    // reset group on normal filter submit
    document.querySelectorAll('.graph-submit button[type="submit"]:not([name="filter_type"])')
        .forEach(btn => {
            btn.addEventListener('click', function () {
                currentGroup = 'chapter';
                updateCompareBtnText();
                compareRadios.forEach(r => r.checked = r.value === currentGroup);
            });
        });

    // -----------------------------
    // Download detection (FIXED)
    // -----------------------------
    document.querySelectorAll('.graph-submit button[name="filter_type"]')
        .forEach(btn => {
            btn.addEventListener('click', function () {
                downloadType = this.value; // pdf | excel
            });
        });

    // -----------------------------
    // AJAX + Export handler
    // -----------------------------
    function collectFilters() {
        return {
            _token: "{{ csrf_token() }}",
            from_date: $('input[name="from_date"]').val(),
            to_date: $('input[name="to_date"]').val(),
            zones: $('select[name="zones[]"]').val() || [],
            fields: $('select[name="fields[]"]').val() || [],
            submission_status: $('select[name="submission_status"]').val() || null,
            group_by: currentGroup,
            filter_type: downloadType
        };
    }

    function fetchGraph() {

        const data = collectFilters();

        // EXPORT FLOW
        if (downloadType) {

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route($route, $type) }}";

            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="filter_type" value="${downloadType}">
            `;

            Object.entries(data).forEach(([key, value]) => {
                if (value === null || key === 'filter_type') return;

                if (Array.isArray(value)) {
                    value.forEach(v => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key + '[]';
                        input.value = v;
                        form.appendChild(input);
                    });
                } else {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }
            });

            document.body.appendChild(form);
            form.submit();
            form.remove();

            downloadType = null;
            return;
        }

        // AJAX FLOW
        $('#graph-loader').removeClass('d-none');

        $.post("{{ route($route, $type) }}", data, function (res) {
            fullGraphData = res;
            renderChart();
            $('#graph-loader').addClass('d-none');
        });
    }

    // -----------------------------
    // Chart rendering
    // -----------------------------
    function getColor(i, total) {
        const hue = Math.round((i / total) * 360);
        return `hsl(${hue}, 60%, 65%)`;
    }

    function renderChart() {
        if (!fullGraphData || !ctx) return;

        const { labels, datasets, status_levels } = fullGraphData;

        datasets.forEach((ds, i) => {
            const color = getColor(i, datasets.length);
            ds.borderColor = color;
            ds.backgroundColor = color;
        });

        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: datasets.map(ds => ({
                    label: ds.label,
                    data: ds.data,
                    borderColor: ds.borderColor,
                    backgroundColor: ds.backgroundColor,
                    fill: false,
                    tension: ds.tension || 0.3,
                    borderWidth: 1,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => status_levels[v] || v
                        }
                    }
                }
            }
        });

        buildLegend(chart);
    }

    // -----------------------------
    // Legend system
    // -----------------------------
    function buildLegend(chart) {

        const legendContainer = document.getElementById('customLegend');
        const searchInput = document.getElementById('legendSearch');
        const selectAll = document.getElementById('legendSelectAllCheckbox');

        if (!legendContainer) return;

        const STORAGE_KEY = "legendState";

        function saveState() {
            const state = {};
            chart.data.datasets.forEach((ds, i) => {
                state[ds.label] = !chart.getDatasetMeta(i).hidden;
            });
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        }

        function render(filter = '') {

            legendContainer.innerHTML = '';

            chart.data.datasets.forEach((ds, i) => {

                if (filter && !ds.label.toLowerCase().includes(filter.toLowerCase())) return;

                const meta = chart.getDatasetMeta(i);

                const row = document.createElement('div');
                row.style.display = 'flex';
                row.style.alignItems = 'center';
                row.style.gap = '6px';
                row.style.cursor = 'pointer';
                row.style.fontSize = '11px';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = !meta.hidden;

                const box = document.createElement('span');
                box.style.width = '12px';
                box.style.height = '12px';
                box.style.background = ds.borderColor;

                const label = document.createElement('span');
                label.textContent = ds.label;

                checkbox.addEventListener('change', function () {
                    meta.hidden = !this.checked;
                    chart.update();
                    saveState();
                    updateSelectAll();
                });

                row.appendChild(checkbox);
                row.appendChild(box);
                row.appendChild(label);

                legendContainer.appendChild(row);
            });
        }

        function updateSelectAll() {
            const allVisible = chart.data.datasets.every((d, i) => !chart.getDatasetMeta(i).hidden);
            const noneVisible = chart.data.datasets.every((d, i) => chart.getDatasetMeta(i).hidden);

            selectAll.checked = allVisible;
            selectAll.indeterminate = !allVisible && !noneVisible;
        }

        selectAll?.addEventListener('change', function () {
            chart.data.datasets.forEach((d, i) => {
                chart.getDatasetMeta(i).hidden = !this.checked;
            });

            chart.update();
            saveState();
            render(searchInput?.value || '');
            updateSelectAll();
        });

        searchInput?.addEventListener('input', function () {
            render(this.value);
        });

        render();
        updateSelectAll();
    }

    // -----------------------------
    // Init
    // -----------------------------
    fetchGraph();

    document.querySelector('.graph-submit')?.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchGraph();
    });

});
</script>

<style>
#reportGraph { height: 450px; }
</style>