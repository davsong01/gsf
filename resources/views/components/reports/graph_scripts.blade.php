@props(['route' => null, 'level' => null, 'type' => null])

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let downloadType = null;
    let chart = null;
    let fullGraphData = null;
    const ctx = document.getElementById('reportGraph')?.getContext('2d');

    function formatDate(date) {
        return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
    }
    const today = new Date();
    const fromInput = document.querySelector('input[name="from_date"]');
    const toInput = document.querySelector('input[name="to_date"]');
    const dueThrough = new Date(today.getFullYear(), today.getMonth(), 1);
    if (today.getDate() < 25) dueThrough.setMonth(dueThrough.getMonth() - 1);
    if (fromInput && !fromInput.value) fromInput.value = formatDate(new Date(dueThrough.getFullYear(), dueThrough.getMonth() - 11, 1));
    if (toInput && !toInput.value) toInput.value = formatDate(new Date(dueThrough.getFullYear(), dueThrough.getMonth() + 1, 0));

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
            group_by: 'chapter',
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

    function renderChart() {
        if (!fullGraphData || !ctx) return;

        const { labels, datasets, status_levels } = fullGraphData;
        const colors = ['#9ca3af', '#f59e0b', '#60a5fa', '#ef4444', '#34d399', '#dc2626', '#10b981', '#b91c1c', '#047857'];
        const chapterCount = Number(fullGraphData.chapter_count) || datasets.length;
        const statusCodes = Object.keys(status_levels).map(Number).sort((a, b) => a - b);
        const statusDatasets = statusCodes.map((code, index) => ({
            label: status_levels[code],
            data: labels.map((_, monthIndex) => datasets.reduce((count, chapter) => (
                count + (Number(chapter.data[monthIndex]) === code ? 1 : 0)
            ), 0)),
            backgroundColor: colors[index % colors.length],
            borderColor: '#fff',
            borderWidth: 1,
            stack: 'monthly-status'
        }));

        const summary = document.getElementById('complianceChartSummary');
        if (summary) {
            summary.textContent = chapterCount
                ? 'Monthly chapter counts across ' + chapterCount + ' selected chapters. Reports in edit mode count as not submitted.'
                : 'No chapters match the selected filters.';
        }

        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: statusDatasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const count = Number(context.raw) || 0;
                                const percent = chapterCount ? (100 * count / chapterCount).toFixed(1) : '0.0';
                                return context.dataset.label + ': ' + count + ' chapters (' + percent + '%)';
                            },
                            footer: function (items) {
                                return 'Total chapters: ' + items.reduce((total, item) => total + (Number(item.raw) || 0), 0);
                            }
                        }
                    }
                },
                scales: {
                    x: { stacked: true, title: { display: true, text: 'Report month' } },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: { display: true, text: 'Number of chapters' },
                        ticks: { precision: 0 }
                    }
                }
            }
        });
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
.compliance-chart-card{padding:20px;background:#fff;border:1px solid rgba(40,45,55,.1);border-radius:14px;box-shadow:0 5px 18px rgba(20,25,35,.06)}
.compliance-chart-heading{margin-bottom:12px}.compliance-chart-heading h2{margin:0 0 4px;font-size:1.1rem}.compliance-chart-heading p{margin:0;color:#747b88;font-size:.85rem}
.compliance-chart-wrap{position:relative;height:520px}
@media(max-width:767px){.compliance-chart-card{padding:14px}.compliance-chart-wrap{height:430px}}
</style>
