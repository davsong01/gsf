@extends($isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard')

@section('title', $isAdmin ? 'Reports Analytics' : 'Reports')

@section($isAdmin ? 'item' : 'active')
    <li class="breadcrumb-item">
        @if($isAdmin)
            <a href="{{ route('stakeholderreports.index') }}">Monthly Reports</a>
        @else
            Reports
        @endif
    </li>
@endsection

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
.monitor-card { margin-bottom: 20px !important; }

/* Sleeker cards */
.card {
    border-radius: 0.75rem !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
}

/* Smaller shadow for inner cards like field/zone avatars */
.card-body .card {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
}

.border-left-primary { border-left: 4px solid #0028FF; }
.border-left-success { border-left: 4px solid #28a745; }

.hover-card { transition: transform 0.2s, box-shadow 0.2s; }
.hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }

/* Optional: text color overrides for small text */
.card small { color: rgba(255, 255, 255, 0.85); }
</style>

<div class="row">
    <div class="col-md-3 col-sm-6 mb-2">
        <a href="{{ route($isAdmin ? 'reports.analytics.type' : 'stakeholders.reports.analytics.type', 'compliance') }}" class="text-decoration-none">
            <div class="card h-100 shadow border-0 text-white bg-primary hover-card">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <div class="mb-2"><i class="fa fa-chart-line fa-2x"></i></div>
                    <h6 class="mb-1">Compliance Trends</h6>
                    <small>View chapter compliance over time</small>
                </div>
            </div>
        </a>
    </div>

    {{-- <div class="col-md-3 col-sm-6 mb-2">
        <a href="{{ route('reports.analytics.type', 'compliance') }}" class="text-decoration-none">
            <div class="card h-100 shadow border-0 text-white bg-success hover-card">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <div class="mb-2"><i class="fa fa-calendar-alt fa-2x"></i></div>
                    <h6 class="mb-1">Monthly Trend</h6>
                    <small>Reports submitted per month</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <a href="{{ route('reports.analytics.type', 'compliance') }}" class="text-decoration-none">
            <div class="card h-100 shadow border-0 text-white bg-warning hover-card">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <div class="mb-2"><i class="fa fa-balance-scale fa-2x"></i></div>
                    <h6 class="mb-1">Question Performance</h6>
                    <small>Compare question responses</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <a href="{{ route('reports.analytics.type', 'compliance') }}" class="text-decoration-none">
            <div class="card h-100 shadow border-0 text-white bg-info hover-card">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <div class="mb-2"><i class="fa fa-trophy fa-2x"></i></div>
                    <h6 class="mb-1">Chapter Performance</h6>
                    <small>Top & lowest performing chapters</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <a href="{{ route('reports.analytics.type', 'compliance') }}" class="text-decoration-none">
            <div class="card h-100 shadow border-0 text-white bg-danger hover-card">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <div class="mb-2"><i class="fa fa-clock fa-2x"></i></div>
                    <h6 class="mb-1">Late Submissions</h6>
                    <small>Track delayed reports</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <a href="{{ route('reports.analytics.type', 'compliance') }}" class="text-decoration-none">
            <div class="card h-100 shadow border-0 text-white bg-secondary hover-card">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <div class="mb-2"><i class="fa fa-layer-group fa-2x"></i></div>
                    <h6 class="mb-1">Zone vs Zone Trend</h6>
                    <small>Compare performance by structure</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6 mb-2">
        <a href="{{ route('reports.analytics.type', 'compliance') }}" class="text-decoration-none">
            <div class="card h-100 shadow border-0 text-white bg-secondary hover-card">
                <div class="card-body d-flex flex-column align-items-center text-center">
                    <div class="mb-2"><i class="fa fa-layer-group fa-2x"></i></div>
                    <h6 class="mb-1">Field vs Field Trend</h6>
                    <small>Compare performance by structure</small>
                </div>
            </div>
        </a>
    </div> --}}
</div>
@endsection
