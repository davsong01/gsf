@props(['route' => null, 'level' => null, 'type' => null])

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // At the top
    let currentGroup = 'chapter'; // default

    const compareBtn = document.getElementById('compareBtn');
    const compareModalEl = document.getElementById('compareModal');
    const compareModal = new bootstrap.Modal(compareModalEl);
    const compareRadios = document.querySelectorAll('.compare-radio');

    // Update button text
    function updateCompareBtnText() {
        compareBtn.querySelector('span').textContent =
            currentGroup.charAt(0).toUpperCase() + currentGroup.slice(1);
    }

    // Show modal and check current group
    compareBtn.addEventListener('click', () => {
        compareRadios.forEach(r => r.checked = r.value === currentGroup);
        compareModal.show();
    });

    // When a radio is selected, update chart immediately and close modal
    compareRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (!this.checked) return;

            currentGroup = this.value;
            updateCompareBtnText();

            const postData = {
                _token: "{{ csrf_token() }}",
                from_date: $('input[name="from_date"]').val(),
                to_date: $('input[name="to_date"]').val(),
                zones: $('select[name="zones[]"]').val() || [],
                fields: $('select[name="fields[]"]').val() || [],
                submission_status: $('select[name="submission_status"]').val() || null,
                group_by: currentGroup
            };

            $('#graph-loader').removeClass('d-none');
            $.post("{{ route($route, $type) }}", postData, function(res) {
                fullGraphData = res;
                renderChart();
                $('#graph-loader').addClass('d-none');
            });

            compareModal.hide();
        });
    });

    // Reset group to 'chapter' when filter button is clicked
    document.querySelector('.graph-submit button[type="submit"]:not([name="filter_type"])').addEventListener('click', function(e) {
        currentGroup = 'chapter';
        updateCompareBtnText();
        // Ensure radios also reflect the reset
        compareRadios.forEach(r => r.checked = r.value === currentGroup);
    });

    // Initialize button text
    updateCompareBtnText();

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

        // Create a div for HTML tooltip (once)
        let tooltipEl = document.getElementById('chartjs-tooltip');
        if (!tooltipEl) {
            tooltipEl = document.createElement('div');
            tooltipEl.id = 'chartjs-tooltip';
            tooltipEl.style.position = 'absolute';
            tooltipEl.style.background = 'rgba(0,0,0,0.8)';
            tooltipEl.style.color = 'white';
            tooltipEl.style.borderRadius = '4px';
            tooltipEl.style.padding = '6px';
            tooltipEl.style.fontSize = '10px';
            tooltipEl.style.pointerEvents = 'none';
            tooltipEl.style.maxHeight = '1000px';
            tooltipEl.style.overflowY = 'auto';
            tooltipEl.style.zIndex = 1000;
            document.body.appendChild(tooltipEl);
        }

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
                    pointHoverRadius: 6,
                    pointHitRadius: 10
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'nearest', intersect: true },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: false, // disable default canvas tooltip
                        external: function(context) {
                            const tooltipModel = context.tooltip;

                            // Hide if no tooltip
                            if (!tooltipModel.opacity) {
                                tooltipEl.style.opacity = 0;
                                return;
                            }

                            const monthIndex = tooltipModel.dataPoints[0].dataIndex;

                            // Count statuses for summary
                            const counts = {};
                            datasets.forEach(ds => {
                                if (!context.chart.getDatasetMeta(datasets.indexOf(ds)).hidden) {
                                    const status = ds.data[monthIndex];
                                    counts[status] = (counts[status] || 0) + 1;
                                }
                            });

                            // Build HTML for summary
                            let innerHtml = `<div style="font-weight:bold; margin-bottom:4px;">Summary:</div>`;
                            Object.keys(counts).forEach(status => {
                                innerHtml += `<div>${status_levels[status]}: ${counts[status]} chapter(s)</div>`;
                            });

                            // Add individual chapters
                            innerHtml += `<div style="margin-top:6px; font-weight:bold;">Chapters:</div>`;
                            datasets.forEach(ds => {
                                const meta = chart.getDatasetMeta(datasets.indexOf(ds));
                                if (!meta.hidden) {
                                    const chaptersForMonth = ds.tooltip?.[monthIndex] || [];
                                    chaptersForMonth.forEach(ch => {
                                        innerHtml += `<div>${ch.chapter_name}: ${ch.status_label}</div>`;
                                    });
                                }
                            });

                            tooltipEl.innerHTML = innerHtml;

                            // Position tooltip
                            const canvasRect = context.chart.canvas.getBoundingClientRect();
                            tooltipEl.style.opacity = 1;
                            tooltipEl.style.left = canvasRect.left + window.pageXOffset + tooltipModel.caretX + 'px';
                            tooltipEl.style.top = canvasRect.top + window.pageYOffset + tooltipModel.caretY + 'px';
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, callback: v => status_levels[v] || v },
                        title: { display: true, text: 'Submission / Approval Status' }
                    },
                    x: { title: { display: true, text: 'Month' } }
                }
            }
        });

        buildCustomLegend(chart);
    }


    function buildCustomLegend(chart) {
        const legendContainer = document.getElementById('customLegend');
        const searchInput = document.getElementById('legendSearch');
        const selectAll = document.getElementById('legendSelectAllCheckbox');

        const STORAGE_KEY = "legendState";

        // Load saved state
        const savedState = JSON.parse(localStorage.getItem(STORAGE_KEY) || "{}");

        chart.data.datasets.forEach((ds, i) => {
            if (savedState.hasOwnProperty(ds.label)) {
                chart.getDatasetMeta(i).hidden = !savedState[ds.label];
            }
        });

        function saveState() {
            const state = {};
            chart.data.datasets.forEach((ds, i) => {
                state[ds.label] = !chart.getDatasetMeta(i).hidden;
            });
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        }

        function getSortedDatasets() {
            return chart.data.datasets
                .map((ds, i) => {
                    const activeMonths = ds.data.filter(v => typeof v === "number" && v > 0).length;
                    const maxValue = Math.max(...ds.data.filter(v => typeof v === "number"));
                    return { ds, i, activeMonths, maxValue };
                })
                .sort((a, b) => {
                    if (b.activeMonths !== a.activeMonths) return b.activeMonths - a.activeMonths;
                    return b.maxValue - a.maxValue;
                });
        }

        function renderLegend(filterText = '') {
            legendContainer.innerHTML = '';
            const sorted = getSortedDatasets();

            sorted.forEach(({ ds, i }) => {
                if (!ds.label.toLowerCase().includes(filterText.toLowerCase())) return;

                const meta = chart.getDatasetMeta(i);
                const item = document.createElement('div');
                item.style.display = 'flex';
                item.style.alignItems = 'center';
                item.style.marginBottom = '4px';
                item.style.cursor = 'pointer';
                item.style.fontSize = '10px';

                // Checkbox for each item
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = !meta.hidden;
                checkbox.style.marginRight = '6px';

                // Color box (perfect square)
                const colorBox = document.createElement('span');
                colorBox.style.width = '14px';
                colorBox.style.height = '14px';
                colorBox.style.backgroundColor = ds.borderColor;
                colorBox.style.display = 'inline-block';
                colorBox.style.marginRight = '6px';
                colorBox.style.flex = '0 0 14px';

                // Chapter label
                const label = document.createElement('span');
                label.textContent = ds.label;

                function toggle() {
                    checkbox.checked = !checkbox.checked;
                    meta.hidden = !checkbox.checked;
                    chart.update();
                    saveState();
                    renderLegend(searchInput.value);
                    updateSelectAllCheckbox();
                }

                checkbox.addEventListener('change', function (e) {
                    meta.hidden = !this.checked;
                    chart.update();
                    saveState();
                    renderLegend(searchInput.value);
                    updateSelectAllCheckbox();
                    e.stopPropagation();
                });

                item.addEventListener('click', toggle);

                item.appendChild(checkbox);
                item.appendChild(colorBox);
                item.appendChild(label);
                legendContainer.appendChild(item);
            });
        }

        function updateSelectAllCheckbox() {
            const allVisible = chart.data.datasets.every((ds, i) => !chart.getDatasetMeta(i).hidden);
            const noneVisible = chart.data.datasets.every((ds, i) => chart.getDatasetMeta(i).hidden);

            selectAll.checked = allVisible;
            selectAll.indeterminate = !allVisible && !noneVisible;
        }

        selectAll.addEventListener('change', function () {
            const showAll = this.checked;
            chart.data.datasets.forEach((ds, i) => chart.getDatasetMeta(i).hidden = !showAll);
            chart.update();
            saveState();
            renderLegend(searchInput.value);
            updateSelectAllCheckbox();
        });

        // Initial render
        renderLegend();
        updateSelectAllCheckbox();

        // Search filter
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                renderLegend(this.value);
            });
        }
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

    const selectAll = document.getElementById('legendSelectAllCheckbox');

    function updateSelectAllCheckbox() {
        const allVisible = chart.data.datasets.every((ds, i) => !chart.getDatasetMeta(i).hidden);
        const noneVisible = chart.data.datasets.every((ds, i) => chart.getDatasetMeta(i).hidden);

        // Checked if all are visible, unchecked if all hidden
        // Indeterminate if mixed
        selectAll.checked = allVisible;
        selectAll.indeterminate = !allVisible && !noneVisible;
    }

    // Checkbox toggle for Select All / Clear All
    selectAll.addEventListener('change', function() {
        const showAll = this.checked;
        chart.data.datasets.forEach((ds, i) => chart.getDatasetMeta(i).hidden = !showAll);
        chart.update();
        saveState();
        renderLegend(searchInput.value);

        // Update checkbox in case some datasets get hidden manually
        updateSelectAllCheckbox();
    });

    // Call this whenever you render or toggle legend items
    function toggleLegendItem(meta) {
        meta.hidden = !meta.hidden;
        chart.update();
        saveState();
        renderLegend(searchInput.value);
        updateSelectAllCheckbox();
    }

    // Initial update
    updateSelectAllCheckbox();

});
</script>

<style>
#reportGraph { height: 450px; } /* reduced height */
</style>
