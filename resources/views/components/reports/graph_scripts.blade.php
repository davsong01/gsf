@props(['route' => null, 'level' => null, 'type' => null])

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('reportGraph').getContext('2d');
    let chart = null;
    let fullGraphData = null;
    let downloadRequested = false;

    // Detect download button click
    document.querySelectorAll('.graph-submit button[name="filter_type"]').forEach(btn => {
        btn.addEventListener('click', function() {
            downloadRequested = this.value === 'download';
        });
    });

    // Utility: generate soft color palette
    function getColor(i, total) {
        const hue = Math.round((i / total) * 360);
        return `hsl(${hue}, 60%, 65%)`;
    }

   function renderChart() {
        if (!fullGraphData) return;

        const { labels, datasets, status_levels } = fullGraphData;

        // Assign colors
        datasets.forEach((ds, i) => {
            const color = getColor(i, datasets.length);
            ds.borderColor = color;
            ds.backgroundColor = color;
        });

        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets.map(ds => ({
                    label: ds.label,
                    data: ds.data,
                    borderColor: ds.borderColor,
                    backgroundColor: ds.backgroundColor,
                    fill: false,
                    tension: ds.tension || 0.3,
                    borderWidth: 1,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        mode: 'index',
                        intersect: true,
                        callbacks: {
                            beforeBody: function(context) {
                                const monthIndex = context[0].dataIndex;
                                const status_levels = fullGraphData.status_levels;
                                const datasets = fullGraphData.datasets;

                                const counts = {};

                                // Count statuses for this month
                                datasets.forEach(ds => {
                                    const status = ds.data[monthIndex];
                                    counts[status] = (counts[status] || 0) + 1;
                                });

                                // Build summary lines
                                return Object.keys(counts).map(status => {
                                    return `${status_levels[status]}: ${counts[status]} chapter(s)`;
                                });
                            },
                            label: function(context) {
                                const dsIndex = context.datasetIndex;
                                const ptIndex = context.dataIndex;
                                const tooltipLabel =
                                    fullGraphData.datasets[dsIndex].tooltip?.[ptIndex] || context.raw;

                                return `${context.dataset.label}: ${tooltipLabel}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return status_levels[value] || value;
                            }
                        },
                        title: { display: true, text: 'Submission / Approval Status' }
                    },
                    x: {
                        title: { display: true, text: 'Month' }
                    }
                },
                layout: {
                    padding: { top: 10, bottom: 10 }
                }
            }
        });
    }


    function fetchGraph() {
        const postData = {
            _token: "{{ csrf_token() }}",
            from_date: $('input[name="from_date"]').val(),
            to_date: $('input[name="to_date"]').val(),
            zones: $('select[name="zones[]"]').val() || [],
            fields: $('select[name="fields[]"]').val() || [],
            submission_status: $('select[name="submission_status"]').val() || null,
            filter_type: downloadRequested ? 'download' : null
        };

        if (downloadRequested) {
            downloadRequested = false; // reset flag
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = "{{ route($route, $type) }}";

            // CSRF
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = "{{ csrf_token() }}";
            tempForm.appendChild(csrfInput);

            // Append filters
            Object.entries(postData).forEach(([key, value]) => {
                if (value !== null) {
                    if (Array.isArray(value)) {
                        value.forEach(v => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key + '[]';
                            input.value = v;
                            tempForm.appendChild(input);
                        });
                    } else {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        tempForm.appendChild(input);
                    }
                }
            });

            document.body.appendChild(tempForm);
            tempForm.submit();
            tempForm.remove();
            return;
        }

        // AJAX for chart update
        $('#graph-loader').removeClass('d-none');
        $.post("{{ route($route, $type) }}", postData, function(res) {
            fullGraphData = res;
            renderChart();
            $('#graph-loader').addClass('d-none');
        });
    }

    // Initial load
    fetchGraph();

    // Filter submit
    document.querySelector('.graph-submit').addEventListener('submit', function(e){
        e.preventDefault();
        fetchGraph();
    });
});
</script>

<style>
#reportGraph { height: 450px; } /* reduced height */
</style>
