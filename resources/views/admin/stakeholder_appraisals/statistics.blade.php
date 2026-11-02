@extends('layouts.dashboard')

@section('title', 'Appraisal Statistics')

@section('active')
<li class="breadcrumb-item"><a href="{{ route('stakeholderappraisals.index') }}">Appraisals</a></li>
<li class="breadcrumb-item">Statistics Report</li>
@endsection

@section('content')
<div class="content-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="card-title mb-1">Report of the Appraisal - Statistics</h4>
                <small class="text-muted">Status and completion summary for the selected stakeholder population.</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('stakeholderappraisals.statistics.export', request()->query()) }}" class="btn btn-success btn-sm rounded-pill"><i class="fa fa-download me-1"></i> Download Excel</a>
                <a href="{{ route('stakeholderappraisals.index', request()->query()) }}" class="btn btn-outline-secondary btn-sm rounded-pill">Back to Appraisals</a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-light border mb-4">
                <strong>Filters applied:</strong>
                {{ request('search') ?: 'All stakeholders' }}
                @if(request('field_id')), field {{ $fields->firstWhere('id', request('field_id'))?->name }} @endif
                @if(request('zone_id')), zone {{ $zones->firstWhere('id', request('zone_id'))?->name }} @endif
                @if(request('self_status')), self status {{ request('self_status') }} @endif
                @if(request('evaluation_status')), evaluation status {{ request('evaluation_status') }} @endif
            </div>

            <div class="row g-3 mb-4">
                @foreach([
                    ['Total Stakeholders', $summary['total_stakeholders'], 'primary'],
                    ['Self Appraisals Published', $summary['self_published'], 'success'],
                    ['Self Completion', $summary['self_completion_percent'] . '%', 'info'],
                    ['Evaluations Published', $summary['evaluations_published'], 'warning'],
                    ['Evaluation Completion', $summary['evaluation_completion_percent'] . '%', 'secondary'],
                ] as [$label, $value, $color])
                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="card border-{{ $color }} h-100">
                            <div class="card-body">
                                <small class="text-muted d-block">{{ $label }}</small>
                                <h3 class="mb-0 text-{{ $color }}">{{ $value }}</h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row g-4">
                @foreach([['By Role', $byRole], ['By Field', $byField], ['By Zone', $byZone]] as [$title, $rows])
                    <div class="col-lg-4">
                        <div class="table-responsive">
                            <h5>{{ $title }}</h5>
                            <table class="table table-sm table-striped align-middle">
                                <thead><tr><th>Group</th><th>Total</th><th>Self</th><th>Eval.</th></tr></thead>
                                <tbody>
                                    @forelse($rows as $row)
                                        <tr><td>{{ $row['Group'] }}</td><td>{{ $row['Stakeholders'] }}</td><td>{{ $row['Self Published'] }} ({{ $row['Self Completion %'] }}%)</td><td>{{ $row['Evaluations Published'] }} ({{ $row['Evaluation Completion %'] }}%)</td></tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted">No data available.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
