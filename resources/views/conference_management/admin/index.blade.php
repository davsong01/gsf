@extends('layouts.conference')
@section('title', 'Conference management')
@section('active')
<li class="breadcrumb-item">Conference Management</li>
@endsection
@section('content2')
<div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="card-title">Conference Analytics</h4>
</div>
<div class="card-content">
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
                <a href="{{ route('conference.participants',['type'=>'Participant', 'edition'=>$edition->id]) }}">
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
        <div class="col-sm-12 col-12 dashboard-users-success">
            <div class="card text-center">
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <canvas id="multiBarChart"></canvas>
                <script>
                    $(document).ready(function () {
                        const ctx = document.getElementById('multiBarChart').getContext('2d');
                        let chartInstance = null;

                        function fetchChartData() {
                            $.ajax({
                                url: '/chart-data/' + "{{$edition->id}}",
                                method: 'GET',
                                dataType: 'json',
                                success: function (response) {
                                    if (!response.labels || !response.datasets) {
                                        // alert('Failed to load chart data properly.');
                                        return;
                                    }

                                    updateChart(response.labels, response.datasets);
                                },
                                error: function (xhr, status, error) {
                                    alert('Failed to load chart data.');
                                }
                            });
                        }

                        function updateChart(labels, datasets) {
                            if (chartInstance) {
                                chartInstance.destroy();
                            }

                            chartInstance = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: datasets
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        },
                                        x: {
                                            stacked: false,
                                            ticks: {
                                                autoSkip: false,
                                                maxRotation: 90,
                                                minRotation: 0
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top'
                                        }
                                    }
                                }
                            });
                        }

                        // Fetch and load chart data on page load
                        fetchChartData();
                    });
                </script>

            </div>
        </div>
        @endif
    </div>
</div>
@endsection
