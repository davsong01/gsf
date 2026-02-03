@props(['route' => null, 'level' => null, 'type' => null, 'allowProductCollapse' => false])

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simplebar@6/dist/simplebar.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const ctx = document.getElementById('reportGraph').getContext('2d');
    let chart = null;
    let fullGraphData = null;

    function getSelectedLegends() {
        return Array.from(document.querySelectorAll('.product-checkbox:checked'))
            .map(cb => cb.value);
    }

    function updateLegendColors(datasets) {
        datasets.forEach(ds => {
            const dot = document.querySelector(`.legend-color[data-id="${ds.legend_id}"]`);
            if (dot) dot.style.background = ds.borderColor;
        });
    }

    function formatLabels(labels) {
        return labels.map(l => {
            const [year, month] = l.split('-');
            const date = new Date(year, month - 1, 1);
            return date.toLocaleString('default', { month: 'short', year: 'numeric' });
        });
    }

    function assignColors(datasets) {
        const count = datasets.length;
        datasets.forEach((ds, i) => {
            if (!ds.borderColor) {
                const hue = Math.round((i / count) * 360);
                const color = `hsl(${hue}, 70%, 50%)`;
                ds.borderColor = color;
                ds.backgroundColor = color;
            }
        });
    }

    function renderChart() {
        if (!fullGraphData) return;

        const selected = getSelectedLegends();
        const datasets = fullGraphData.datasets.filter(ds =>
            selected.includes(String(ds.legend_id))
        );

        assignColors(datasets);

        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formatLabels(fullGraphData.labels),
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                elements: { point: { radius: 5 }, line: { tension: 0.3 } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: v => v === 1 ? 'Submitted' : 'Not'
                        },
                        title: { display: true, text: 'Submission Status' }
                    },
                    x: { title: { display: true, text: 'Month' } }
                }
            }
        });

        updateLegendColors(datasets);
    }

    function fetchGraph() {
        $('#graph-loader').removeClass('d-none');

        $.post("{{ route($route, $type) }}", {
            _token: "{{ csrf_token() }}",
            from_date: $('input[name="from_date"]').val(),
            to_date: $('input[name="to_date"]').val()
        }, function (res) {
            fullGraphData = res;
            renderChart();
            $('#graph-loader').addClass('d-none');
        });
    }

    // Legend click toggles checkbox
    document.querySelectorAll('.legend-color, .product-checkbox + label').forEach(el => {
        el.addEventListener('click', e => {
            const checkbox = e.target.closest('div')?.querySelector('.product-checkbox') ||
                             e.target.previousElementSibling;
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                renderChart();
            }
        });
    });

    // Checkbox events
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.addEventListener('change', renderChart);
    });

    // Check/uncheck all
    document.getElementById('checkAllProducts').addEventListener('change', function () {
        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
        renderChart();
    });

    // Filter submit
    document.querySelector('.graph-submit').addEventListener('submit', function(e){
        e.preventDefault();
        fetchGraph();
    });

    // Initial load
    fetchGraph();

});
</script>
