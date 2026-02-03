@props(['route' => null, 'level' => null, 'type' => null, 'allowProductCollapse'=>false])
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simplebar@6/dist/simplebar.min.js"></script>

<script>
const GRAPH_ROUTE_TEMPLATE = @json(route($route, ['type'=>$type, 'level'=>'__LEVEL__']));
let isProductCollapsed = false;
const ALLOW_PRODUCT_COLLAPSE = @json($allowProductCollapse);
let fullGraphData = null;
let myChart = null;
let selectedLegends = [];
let allProductIds = [];

/* --------------------------
 * HELPER FUNCTIONS
 * ------------------------*/
function graphRoute(level) {
    return GRAPH_ROUTE_TEMPLATE.replace('__LEVEL__', level);
}

function initProductCheckboxes() {
    selectedLegends = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
}

function initAllProductIds() {
    allProductIds = Array.from(document.querySelectorAll('.product-checkbox')).map(cb => cb.value);
}

function syncLegendsWithChart(chart) {
    const legendItems = document.querySelectorAll('.legend-item');
    legendItems.forEach((item, index) => {
        if (!chart.data.datasets[index]) return;
        const color = chart.data.datasets[index].borderColor || chart.data.datasets[index].backgroundColor || '#000';
        item.querySelector('.legend-color').style.background = color;
    });
}

/* --------------------------
 * FETCH GRAPH DATA
 * ------------------------*/
function fetchGraph(level = "{{ $level }}", fromCollapseToggle = false) {
    $('#graph-loader').show();

    const payload = {
        _token: "{{ csrf_token() }}",
        legends: isProductCollapsed ? [] : selectedLegends,
        collapse_products: isProductCollapsed ? 1 : 0,
        date_from: $('#date_from').val(),
        date_to: $('#date_to').val(),
        email: $('input[name="email"]').val(),
        phone: $('input[name="phone"]').val(),
        table: $('select[name="table"]').val(),
        domain: $('input[name="domain"]').val(),
        business_developer_id: $('select[name="business_developer_id"]').val(),
        channel: $('select[name="channel"]').val(),
        platform: $('select[name="platform"]').val(),
        role_id: $('select[name="role_id"]').val(),
        category_id: $('select[name="category_id"]').val(),
    };

    $.post(graphRoute(level), payload, function(res){
        fullGraphData = res;
        renderChart();
        $('#graph-loader').hide();
    });
}

/* --------------------------
 * RENDER CHART
 * ------------------------*/
function renderChart() {
    if (!fullGraphData) return;

    const graphType = fullGraphData.graph_type || 'multi';
    let datasetsToRender = [];

    if (graphType === 'multi') {
        datasetsToRender = fullGraphData.datasets.filter(ds => selectedLegends.includes(String(ds.product_id)));
    } else {
        datasetsToRender = fullGraphData.datasets.map(ds => ({
            ...ds,
            borderColor: ds.borderColor || '#36A2EB',
            backgroundColor: ds.backgroundColor || '#36A2EB'
        }));
    }

    if (myChart) myChart.destroy();

    const ctx = document.getElementById('reportGraph').getContext('2d');

    myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: fullGraphData.labels,
            datasets: datasetsToRender
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'nearest',
                    intersect: true
                }
            },
            interaction: { mode: 'nearest', intersect: true },
            scales: { y: { beginAtZero: true } },
            elements: { line: { borderWidth: 1.5 }, point: { radius: 3, hitRadius: 8 } }
        }
    });

    // Sync legend colors
    syncLegendsWithChart(myChart);
}

/* --------------------------
 * DOCUMENT READY
 * ------------------------*/
$(document).ready(function(){
    initProductCheckboxes();
    initAllProductIds();

    // Toggle collapse
    if(ALLOW_PRODUCT_COLLAPSE){
        $('#toggleProductCollapse').on('click', function(){
            isProductCollapsed = !isProductCollapsed;
            $(this).attr('data-collapsed', isProductCollapsed ? '1':'0');
            $(this).text(isProductCollapsed ? 'Uncollapse':'Collapse');
            fetchGraph("{{ $level }}", true);
        });
    }

    // Checkbox logic
    $('#checkAllProducts').on('change', function(){
        const checked = this.checked;
        document.querySelectorAll('.product-checkbox').forEach((cb, i)=>{
            cb.checked = checked;
            if(myChart?.data?.datasets[i]) myChart.data.datasets[i].hidden = !checked;
        });
        myChart?.update();
        initProductCheckboxes();
    });

    $(document).on('change', '.product-checkbox', function(){
        const index = [...document.querySelectorAll('.product-checkbox')].indexOf(this);
        if(myChart?.data?.datasets[index]) myChart.data.datasets[index].hidden = !this.checked;
        myChart?.update();
        initProductCheckboxes();

        // Update "Check All" status
        const allChecked = Array.from(document.querySelectorAll('.product-checkbox')).every(cb => cb.checked);
        $('#checkAllProducts').prop('checked', allChecked);
    });

    // Customize modal
    $('#openLevelModal').on('click', function(){
        $('#periodModal').modal('show');
    });

    $('.level-option').on('change', function(){
        const newLevel = $(this).val();
        $('#graph-period').text(newLevel.charAt(0).toUpperCase() + newLevel.slice(1));
        $('input[name="filter_period"]').val(newLevel);
        fetchGraph(newLevel);
        $('#periodModal').modal('hide');
    });

    // Initial fetch
    fetchGraph("{{ $level }}");
});
</script>
