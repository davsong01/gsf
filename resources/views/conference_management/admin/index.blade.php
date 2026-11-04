@extends('layouts.conference')
@section('title', 'Conference management')
@section('active')
<li class="breadcrumb-item">Conference Management</li>
@endsection
@section('content2')
<style>
    .conference-analytics-shell { overflow:hidden; border:0; border-radius:20px; box-shadow:0 16px 42px rgba(20,43,72,.08); }
    .conference-analytics-heading { display:flex; align-items:center; justify-content:space-between; gap:24px; padding:28px 30px 24px; color:#fff; background:linear-gradient(120deg,#102f55 0%,#1b527d 58%,#2a8a8b 100%); }
    .conference-analytics-heading__eyebrow { display:block; margin-bottom:7px; color:#b9e8dc; font-size:10px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
    .conference-analytics-heading h4 { margin:0; color:#fff; font-size:25px; font-weight:750; }
    .conference-analytics-heading p { max-width:620px; margin:7px 0 0; color:rgba(255,255,255,.76); font-size:13px; }
    .conference-analytics-heading__button { display:inline-flex; align-items:center; gap:8px; flex:none; padding:11px 15px; border:1px solid rgba(255,255,255,.45); border-radius:9px; color:#fff; background:rgba(255,255,255,.12); font-size:12px; font-weight:750; text-decoration:none; }
    .conference-analytics-heading__button:hover { color:#102f55; background:#fff; text-decoration:none; }
    .conference-analytics-body { padding:24px 26px 30px; background:#f6f8fb; }
    .conference-analytics-body > .row { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:16px; margin:0; }
    .conference-analytics-body .dashboard-users-success { width:auto; max-width:none; padding:0; margin:0; }
    .conference-analytics-body .dashboard-users-success:not(.conference-registration-chart-column) { grid-column:span 1; }
    .conference-analytics-body .conference-registration-chart-column { grid-column:1 / -1; }
    .conference-analytics-body .dashboard-users-success > .card { height:100%; margin:0; border:1px solid #e5ebf3; border-radius:16px; background:#fff; box-shadow:0 8px 22px rgba(26,53,87,.045); transition:transform .2s ease,box-shadow .2s ease; }
    .conference-analytics-body .dashboard-users-success > .card:hover { transform:translateY(-2px); box-shadow:0 13px 28px rgba(26,53,87,.09); }
    .conference-analytics-body .dashboard-users-success > .card > a { display:block; height:100%; color:inherit; text-decoration:none; }
    .conference-analytics-body .dashboard-users-success .card-content { height:100%; }
    .conference-analytics-body .dashboard-users-success .card-body { min-height:112px; padding:14px 15px; text-align:left; }
    .conference-analytics-body .dashboard-users-success .badge-circle { width:36px; height:36px; margin:0 0 12px!important; border-radius:10px; font-size:13px; font-weight:800; }
    .conference-analytics-body .dashboard-users-success .text-muted { color:#728198!important; font-size:10px; font-weight:700; line-height:1.35; }
    .conference-analytics-body .dashboard-users-success .card-body > span { color:#7b6eac!important; font-size:10px!important; line-height:1.8; }
    .conference-analytics-body .dashboard-users-success .card-body > span strong { color:#403b70; }
    .conference-analytics-body .conference-registration-card { margin-top:8px!important; }
    @media (max-width:1199px) { .conference-analytics-body > .row { grid-template-columns:repeat(3,minmax(0,1fr)); } }
    @media (max-width:767px) { .conference-analytics-heading { align-items:flex-start; flex-direction:column; padding:23px 20px; } .conference-analytics-body { padding:18px 16px 24px; } .conference-analytics-body > .row { grid-template-columns:repeat(2,minmax(0,1fr)); gap:11px; } }
    @media (max-width:480px) { .conference-analytics-body > .row { grid-template-columns:1fr; } }
</style>
<div class="conference-analytics-heading">
    <div><span class="conference-analytics-heading__eyebrow">Conference command centre</span><h4>Conference Analytics</h4><p>Monitor registrations, payments, donations and operational resources for this conference edition.</p></div>
    @if($edition->slug)<a class="conference-analytics-heading__button" href="{{ route('conferences.show', $edition->slug) }}" target="_blank" rel="noopener"><i class="fa fa-external-link" aria-hidden="true"></i> View conference page</a>@endif
</div>
<div class="card-content conference-analytics-body">
    <div class="row">
        <div class="col-md-3 col-12 dashboard-users-success">
            <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body py-1">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                {!! currency_symbol() !!}{{ $total }}
                            </div>
                            <div class="text-muted line-ellipsis">Total Payments</div>
                            <h3 class="mb-0"></h3>
                        </div>
                    </div>

            </div>
        </div>

        @if($plans)
            @foreach($plans as $plan)
            {{-- {{dd($plan->slug)}} --}}
            <div class="col-md-3 col-12 dashboard-users-success">
                <div class="card text-center">
                    <a href="{{ route('conference.participants',['type'=>$plan->level, 'edition'=>$edition->id, 'slug' => $plan->slug]) }}">
                        <div class="card-content">
                            <div class="card-body py-1">
                                <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                    {{ $plan->registered->where('status','Complete')->count() }}
                                </div>
                                <div class="text-muted line-ellipsis">{{ $plan->title }}</div>
                                <h3 class="mb-0"></h3>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        @endif
        <div class="col-md-3 col-12 dashboard-users-success">
            <div class="card text-center">
                <a href="{{ route('conference.participants',['type'=>'Participant', 'edition'=>$edition->id, 'registration_user_type' => 'moderator']) }}">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                {{ $registered_moderators_count }}
                            </div>
                            <div class="text-muted line-ellipsis">Moderators</div>
                            <span style="font-size: 10px;">
                                Total Slots: <strong>{{$total_slots}}</strong><br>
                                Allocated: <strong>{{$slots_filled}}</strong><br>
                                Unallocated: <strong>{{$unallocated_slots}}</strong><br>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-3 col-12 dashboard-users-success">
                <div class="card text-center">
                    <a href="{{ route('conference.transactions', ['edition'=>$edition->id, 'status'=>'pending']) }}">
                    <div class="card-content">
                        <div class="card-body py-1">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                {{ $pending_registration }}
                            </div>
                            <div class="text-muted line-ellipsis">Pending Registration</div>
                            <h3 class="mb-0"></h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-md-3 col-12 dashboard-users-success">
            <div class="card text-center">
                    <a href="{{ route('donations.index',['edition'=>$edition->id]) }}">

                    <div class="card-content">
                        <div class="card-body py-1">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                {!! currency_symbol() !!}{{ $donations }}
                            </div>
                            <div class="text-muted line-ellipsis">Total Donations</div>
                            <h3 class="mb-0"></h3>
                        </div>
                    </div>
                    </a>
            </div>
        </div>
        <div class="col-md-3 col-12 dashboard-users-success">
            <div class="card text-center">
                <a href="{{ route('materials.index') }}">
                    <div class="card-content">
                        <div class="card-body py-1">
                            <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                {{ $materials }}
                            </div>
                            <div class="text-muted line-ellipsis">Conference Materials</div>
                            <h3 class="mb-0"></h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @if(strtoupper($edition->ministry->code) == 'GSF')
        <div class="col-sm-12 col-12 dashboard-users-success conference-registration-chart-column">
            <div class="card conference-registration-card">
                <style>
                    .conference-registration-card { overflow:hidden; border:1px solid #e4eaf2; border-radius:16px; box-shadow:0 10px 28px rgba(26,53,87,.06); }
                    .conference-registration-card__head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:22px 24px 16px; border-bottom:1px solid #edf1f6; background:linear-gradient(135deg,#f9fbff,#fff); }
                    .conference-registration-card__eyebrow { display:block; margin-bottom:5px; color:#6480a0; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
                    .conference-registration-card__title { margin:0; color:#17365f; font-size:20px; font-weight:750; }
                    .conference-registration-card__hint { margin:5px 0 0; color:#718096; font-size:12px; }
                    .conference-registration-card__stat { min-width:92px; padding:10px 13px; border:1px solid #dce7f5; border-radius:11px; background:#f3f8ff; text-align:right; }
                    .conference-registration-card__stat span { display:block; color:#718096; font-size:10px; font-weight:700; text-transform:uppercase; }
                    .conference-registration-card__stat strong { display:block; margin-top:2px; color:#1d5ca8; font-size:22px; line-height:1.1; }
                    .conference-registration-card__body { padding:18px 24px 24px; }
                    .conference-registration-card__chart { position:relative; height:360px; }
                    .chapter-registration-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(175px,1fr)); gap:10px; }
                    .chapter-registration-tile { min-width:0; padding:13px 14px; border:1px solid rgba(29,92,168,.16); border-radius:11px; background:#f5f8fd; }
                    .chapter-registration-tile__top { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:7px; }
                    .chapter-registration-tile__rank { color:#7b8da5; font-size:9px; font-weight:800; letter-spacing:.08em; }
                    .chapter-registration-tile__count { color:#174d89; font-size:18px; font-weight:850; line-height:1; }
                    .chapter-registration-tile__name { display:block; min-height:27px; color:#31445f; font-size:10px; font-weight:700; line-height:1.35; overflow-wrap:anywhere; }
                    .conference-registration-card__status { margin:10px 0 0; color:#8795a8; font-size:11px; }
                    @media (max-width:600px) { .conference-registration-card__head { flex-direction:column; } .conference-registration-card__stat { text-align:left; } .conference-registration-card__chart { height:280px; } }
                </style>
                <div class="conference-registration-card__head">
                    <div><span class="conference-registration-card__eyebrow">Registration overview</span><h4 id="multiBarChartTitle" class="conference-registration-card__title">Participants by chapter</h4><p id="multiBarChartHint" class="conference-registration-card__hint">Completed registrations grouped by chapter.</p></div>
                    <div class="conference-registration-card__stat"><span>Total shown</span><strong id="multiBarChartTotal">—</strong></div>
                </div>
                <div class="conference-registration-card__body">
                <div class="btn-group btn-group-sm mb-3" role="group" aria-label="Registration breakdown">
                    <button type="button" class="btn btn-primary" data-breakdown="chapters">Chapters</button>
                    <button type="button" class="btn btn-outline-primary" data-breakdown="zones">Zones</button>
                    <button type="button" class="btn btn-outline-primary" data-breakdown="fields">Fields</button>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <div class="conference-registration-card__chart"><div id="chapterRegistrationGrid" class="chapter-registration-grid" hidden></div><canvas id="multiBarChart"></canvas></div>
                <div id="multiBarChartStatus" class="conference-registration-card__status" role="status" aria-live="polite">Loading registration data…</div>
                <script>
                    $(document).ready(function () {
                        const ctx = document.getElementById('multiBarChart').getContext('2d');
                        const chartCanvas = document.getElementById('multiBarChart');
                        const chapterGrid = document.getElementById('chapterRegistrationGrid');
                        const chartContainer = document.querySelector('.conference-registration-card__chart');
                        let chartInstance = null;
                        let breakdowns = {};
                        let activeBreakdown = 'chapters';
                        const palette = ['#1d5ca8', '#d59a2b', '#2b9b82', '#805ad5', '#e76f51', '#3b82b6'];

                        function fetchChartData() {
                            $.ajax({
                                url: '/chart-data/' + "{{$edition->id}}",
                                method: 'GET',
                                dataType: 'json',
                                success: function (response) {
                                    if (!response.labels || !response.datasets) {
                                        document.getElementById('multiBarChartStatus').textContent = 'No registration data is available yet.';
                                        return;
                                    }

                                    breakdowns = response.breakdowns || { chapters: { labels: response.labels, datasets: response.datasets } };
                                    renderBreakdown(activeBreakdown);
                                },
                                error: function (xhr, status, error) {
                                    alert('Failed to load chart data.');
                                }
                            });
                        }

                        function renderBreakdown(key) {
                            const breakdown = breakdowns[key];
                            if (!breakdown) return;
                            activeBreakdown = key;
                            const sourceLabels = breakdown.labels || [];
                            const sourceDatasets = breakdown.datasets || [];
                            const labels = sourceLabels.slice();
                            const datasets = sourceDatasets.map(function (dataset) { return Object.assign({}, dataset, { data: (dataset.data || []).slice() }); });
                            document.getElementById('multiBarChartTitle').textContent = 'Participants by ' + key.slice(0, -1);
                            document.getElementById('multiBarChartHint').textContent = key === 'chapters'
                                ? 'All chapters ranked by completed registrations.'
                                : 'Completed registrations grouped by ' + key.slice(0, -1) + '.';
                            if (chartInstance) {
                                chartInstance.destroy();
                                chartInstance = null;
                            }

                            const total = datasets.reduce(function (sum, dataset) {
                                return sum + (dataset.data || []).reduce(function (datasetTotal, value) {
                                    return datasetTotal + (Number(value) || 0);
                                }, 0);
                            }, 0);
                            document.getElementById('multiBarChartTotal').textContent = total.toLocaleString();
                            document.getElementById('multiBarChartStatus').textContent = sourceLabels.length + ' ' + key + ' represented';
                            const isChapterView = key === 'chapters';
                            if (isChapterView) {
                                chartCanvas.hidden = true;
                                chapterGrid.hidden = false;
                                chartContainer.style.height = 'auto';
                                chapterGrid.replaceChildren();
                                const values = datasets[0] ? datasets[0].data : [];
                                const maximum = Math.max.apply(null, values.concat([1]));
                                labels.forEach(function (label, index) {
                                    const count = Number(values[index]) || 0;
                                    const tile = document.createElement('article');
                                    const strength = count / maximum;
                                    tile.className = 'chapter-registration-tile';
                                    tile.style.backgroundColor = 'rgba(29,92,168,' + (0.045 + strength * 0.2) + ')';
                                    tile.style.borderColor = 'rgba(29,92,168,' + (0.14 + strength * 0.24) + ')';
                                    const top = document.createElement('div');
                                    top.className = 'chapter-registration-tile__top';
                                    const rank = document.createElement('span');
                                    rank.className = 'chapter-registration-tile__rank';
                                    rank.textContent = '#' + (index + 1);
                                    const value = document.createElement('strong');
                                    value.className = 'chapter-registration-tile__count';
                                    value.textContent = count.toLocaleString();
                                    const name = document.createElement('span');
                                    name.className = 'chapter-registration-tile__name';
                                    name.textContent = label;
                                    name.title = label;
                                    top.append(rank, value);
                                    tile.append(top, name);
                                    chapterGrid.appendChild(tile);
                                });
                                return;
                            }

                            chapterGrid.hidden = true;
                            chartCanvas.hidden = false;
                            chartContainer.style.height = '360px';

                            chartInstance = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: datasets.map(function (dataset, index) {
                                        return Object.assign({}, dataset, {
                                            backgroundColor: palette[index % palette.length],
                                            borderColor: palette[index % palette.length],
                                            borderWidth: 0,
                                            borderRadius: 7,
                                            maxBarThickness: 34,
                                        });
                                    })
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    indexAxis: 'x',
                                    interaction: { mode: 'index', intersect: false },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            border: { display: false },
                                            grid: { color: '#edf1f6' },
                                            ticks: { precision: 0, color: '#718096', font: { size: 10 } }
                                        },
                                        x: {
                                            border: { display: false },
                                            grid: { display: false },
                                            ticks: { autoSkip: false, maxRotation: 25, minRotation: 0, color: '#718096', font: { size: 10, weight: '600' } }
                                        }
                                    },
                                    plugins: {
                                        tooltip: { backgroundColor: '#17365f', padding: 12, displayColors: true, cornerRadius: 8 },
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        }

                        document.querySelectorAll('[data-breakdown]').forEach(function (button) {
                            button.addEventListener('click', function () {
                                document.querySelectorAll('[data-breakdown]').forEach(function (item) {
                                    item.classList.toggle('btn-primary', item === button);
                                    item.classList.toggle('btn-outline-primary', item !== button);
                                });
                                renderBreakdown(button.dataset.breakdown);
                            });
                        });

                        // Fetch and load chart data on page load
                        fetchChartData();
                    });
                </script>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
