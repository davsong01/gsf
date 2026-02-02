@extends('layouts.stakeholderdashboard')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    .monitor-card {
        margin-bottom: 20px !important;
    }

    .content-header {
        display: none !important;
    }

    /* Sleeker cards */
    .card {
        border-radius: 0.75rem !important; /* smoother edges */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important; /* subtle shadow */
    }

    /* Smaller shadow for inner cards like field/zone avatars */
    .card-body .card {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
    }

    .border-left-primary {
        border-left: 4px solid #0028FF;
    }

    .border-left-success {
        border-left: 4px solid #28a745;
    }

    .hover-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    /* Optional: text color overrides for small text */
    .card small {
        color: rgba(255, 255, 255, 0.85);
    }

</style>
<div class="content-body">
    @if(in_array($user->role_id, [1, 2, 3, 4, 5]))
    {{-- CHAPTER HEADER --}}
        @php
            $bgStyle = $chapter?->banner
                ? "background-image: url('" . asset($chapter->banner) . "');"
                : "background: linear-gradient(135deg, #0d6efd, #0b5ed7);";

            $fieldCord = $chapter->field->fieldCord ?? $user->field->fieldCord ?? null;
            $zoneCords = $chapter->zone->zonalCords ?? $user->zone->zonalCords ?? null;
            $field = $chapter->field ?? $user->field ?? null;
            $zone = $chapter->zone ?? $user->zone ?? null;
            $heroName = $chapter->name ?? $user->zone->name ?? $user->field->name ?? 'Secretariat';

            if($user->role_id == 5){
                $dashboardName = 'Chapter Dashboard';
            }

            if($user->role_id == 4){
                $dashboardName = 'Zone Dashboard';
            }

            if($user->role_id == 3){
                $dashboardName = 'Field Dashboard';
            }

            if(in_array($user->role_id, [1,2])){
                $dashboardName = 'Secretariat Dashboard';
            }

        @endphp

        <div class="card shadow-sm mb-1 border-0"
            style="{{ $bgStyle }} background-size:cover; background-position:center; position:relative;">

            {{-- Dark Overlay --}}
            <div style="
                position:absolute;
                inset:0;
                background: rgba(0, 0, 0, 0.71);
                border-radius: .75rem;
            "></div>

            <div class="card-body text-center py-4 position-relative text-white">
                <h2 class="fw-bold mb-1" style="color:white">
                    {{ $heroName }}
                </h2>

                <small class="opacity-75">
                    Welcome back, {{ Auth::guard('stakeholder')->user()->name }}
                </small> <br>
                <span class="badge bg-light text-dark mt-2">
                   {{$dashboardName}}
                </span>
            </div>
        </div>

        {{-- FIELD & ZONE CARDS --}}
        <div class="row mb-1">
            @if($fieldCord)
            {{-- FIELD CARD --}}
            <div class="col-12 col-md-6 mb-1">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">

                            {{-- Avatar --}}
                            <div class="col-12 col-md-5 text-center mb-1 mb-md-0">
                                <img
                                    src="{{ asset(optional($fieldCord)->avatar ?? 'images/avatar.png') }}"
                                    class="rounded-circle mb-2"
                                    style="width:100px;height:100px;object-fit:cover;"
                                >
                                <h6 class="mb-0 d-block text-break" style="word-wrap: break-word; overflow-wrap: anywhere;">
                                    {{ optional($fieldCord)->name ?? 'N/A' }}
                                </h6>
                                <small class="text-muted d-block">Field Pastor</small>
                            </div>

                            {{-- Info --}}
                            <div class="col-12 col-md-7 text-break" style="word-wrap: break-word; overflow-wrap: anywhere;">
                                <h6 class="text-primary mb-2">
                                    <i class="fa fa-map-marker"></i> Field Information
                                </h6>

                                <p class="mb-1" style="word-wrap: break-word; overflow-wrap: anywhere;"><strong>Field:</strong> {{ $field->name ?? '' }}</p>
                                <p class="mb-1" style="word-wrap: break-word; overflow-wrap: anywhere;">
                                    <i class="fa fa-envelope text-muted"></i> {{ optional($fieldCord)->email ?? 'N/A' }}
                                </p>
                                <p class="mb-1" style="word-wrap: break-word; overflow-wrap: anywhere;">
                                    <i class="fa fa-phone text-muted"></i> {{ optional($fieldCord)->phone ?? 'N/A' }}
                                </p>

                                @if($fieldCord && $fieldCord->day && $fieldCord->month)
                                    <span class="badge badge-light-primary d-block mt-1 w-100 text-break"
                                        style="background-color:#0028FF;color:white !important; word-wrap: break-word; overflow-wrap: anywhere;">
                                        🎉 <strong>{{ \Carbon\Carbon::create(null, $fieldCord->month, $fieldCord->day)->format('F jS') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if($zoneCords)
            {{-- ZONE CARD --}}
            <div class="col-12 col-md-6 mb-1">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-primary mb-2">
                            <i class="fa fa-globe"></i> Zone Information
                        </h6>

                        <p class="mb-2" style="word-wrap: break-word; overflow-wrap: anywhere;"><strong>Zone:</strong> {{ $zone->name ?? 'N/A' }}</p>
                        <div class="row">
                            @forelse($zoneCords ?? collect() as $cord)
                                <div class="col-12 mb-1">
                                    <div class="row align-items-center">

                                        {{-- Avatar --}}
                                        <div class="col-3 col-md-2 text-center mb-1 mb-md-0">
                                            <img
                                                src="{{ asset($cord->avatar ?? 'images/avatar.png') }}"
                                                class="rounded-circle"
                                                style="width:50px;height:50px;object-fit:cover;"
                                            >
                                        </div>

                                        {{-- Info --}}
                                        <div class="col-9 col-md-10 text-break" style="word-wrap: break-word; overflow-wrap: anywhere;">
                                            <strong class="d-block text-break">{{ $cord->name }}</strong>

                                            <span class="d-block text-muted small" style="word-wrap: break-word; overflow-wrap: anywhere;">
                                                <i class="fa fa-envelope"></i> {{ $cord->email ?? 'N/A' }}
                                            </span>

                                            <span class="d-block text-muted small" style="word-wrap: break-word; overflow-wrap: anywhere;">
                                                <i class="fa fa-phone"></i> {{ $cord->phone ?? 'N/A' }}
                                            </span>

                                            @if($cord && $cord->day && $cord->month)
                                                <span class="badge badge-light-primary d-block mt-1 w-100 text-break"
                                                    style="background-color:#3B50C4;color:white !important; word-wrap: break-word; overflow-wrap: anywhere;">
                                                    🎉 <strong>{{ \Carbon\Carbon::create(null, $cord->month, $cord->day)->format('F jS') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                    <hr class="my-2">
                                </div>
                            @empty
                                <div class="col-12 text-muted">
                                    No zonal coordinators assigned
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @endif
    @if(in_array($user->role_id, [5]))
        {{-- SISTER CAMPUSES ROW --}}
        <div class="row">

            {{-- ZONE SISTER CAMPUSES --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h5 class="text-primary mb-3">
                            <i class="fa fa-globe"></i> Sister Campuses in Same Zone
                        </h5>

                        <div class="row">

                            @php
                                $zoneCampuses = $chapter->relatedByZone()
                                    ->where('id', '!=', $chapter->id)
                                    ->get();
                            @endphp

                            @forelse($zoneCampuses as $campus)
                                <div class="col-12 col-md-4 mb-2">
                                    <div class="card h-100 border-left-primary shadow-sm">
                                        <div class="card-body">

                                            <h6 class="mb-1 text-break">
                                                <i class="fa fa-map-marker text-primary"></i>
                                                <span style="color:#29166f">{{ $campus->name }}</span>
                                            </h6>

                                            <p class="mb-1 small text-muted text-break">
                                                {{ $campus->address ?? 'Address not available' }}
                                            </p>

                                            <p class="mb-0 small">
                                                <i class="fa fa-phone text-success"></i>
                                                {{ $campus->phone ?? 'N/A' }}
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted text-center py-3">
                                    No sister campuses found in this Zone.
                                </div>
                            @endforelse

                        </div>

                    </div>
                </div>
            </div>


            {{-- FIELD SISTER CAMPUSES --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h5 class="text-primary mb-3">
                            <i class="fa fa-map"></i> Sister Campuses in Same Field
                        </h5>

                        <div class="row">

                            @php
                                $fieldCampuses = $chapter->relatedByField()
                                    ->where('id', '!=', $chapter->id)
                                    ->get();
                            @endphp

                            @forelse($fieldCampuses as $campus)
                                <div class="col-12 col-md-4 mb-2">
                                    <div class="card h-100 border-left-success shadow-sm">
                                        <div class="card-body">

                                            <h6 class="mb-1 text-break">
                                                <i class="fa fa-building text-success" style="color:#29166F !important"></i>
                                                <span style="color:#29166F">{{ $campus->name }}</strong> ({{$campus->zone->name}})
                                            </h6>

                                            <p class="mb-1 small text-muted text-break">
                                                {{ $campus->address ?? 'Address not available' }}
                                            </p>

                                            <p class="mb-0 small">
                                                <i class="fa fa-phone text-primary"></i>
                                                {{ $campus->phone ?? 'N/A' }}
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted text-center py-3">
                                    No sister campuses found in this Field.
                                </div>
                            @endforelse

                        </div>

                    </div>
                </div>
            </div>

        </div>
    @endif
    <div class="row g-3 mb-4">

        {{-- All Reports --}}
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('stakeholders.reports.index') }}" class="text-decoration-none">
                <div class="card h-100 shadow border-0 text-white bg-primary hover-card">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                        <div class="mb-2">
                            <i class="fa fa-file fa-2x"></i>
                        </div>
                        <h6 class="mb-1">All Reports</h6>
                        <small>View submitted reports</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Submit Report --}}
        @if(canAddReport(Auth::guard('stakeholder')->user()->chapter_id)['eligible'])
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('stakeholders.reports.create') }}" class="text-decoration-none">
                <div class="card h-100 shadow border-0 text-white bg-success hover-card">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                        <div class="mb-2">
                            <i class="fa fa-plus fa-3x"></i>
                        </div>
                        <h6 class="mb-1">Submit Report</h6>
                        <small>Create a new monthly report</small>
                    </div>
                </div>
            </a>
        </div>
        @endif

        {{-- Pending Zonal Reviews --}}
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'pending']) }}" class="text-decoration-none">
                <div class="card h-100 shadow border-0 text-white hover-card" style="background-color:#f6c23e;"> {{-- Lighter yellow --}}
                    <div class="card-body d-flex flex-column align-items-center text-center">
                        <div class="mb-2">
                            <i class="fas fa-clock fa-3x"></i>
                        </div>
                        <h6 class="mb-1">Pending Zonal Reviews</h6>
                        <small>Reports awaiting action from zonal Pastor</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Pending Field Reviews --}}
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'pending']) }}" class="text-decoration-none">
                <div class="card h-100 shadow border-0 text-white hover-card" style="background-color:#f4b619;"> {{-- Medium yellow --}}
                    <div class="card-body d-flex flex-column align-items-center text-center">
                        <div class="mb-2">
                            <i class="fa fa-clock fa-3x"></i>
                        </div>
                        <h6 class="mb-1">Pending Field Reviews</h6>
                        <small>Reports awaiting action from field pastor</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Pending National Reviews --}}
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'pending']) }}" class="text-decoration-none">
                <div class="card h-100 shadow border-0 text-white hover-card" style="background-color:#e09c00;"> {{-- Darker yellow/orange --}}
                    <div class="card-body d-flex flex-column align-items-center text-center">
                        <div class="mb-2">
                            <i class="fa fa-clock fa-3x"></i>
                        </div>
                        <h6 class="mb-1">Pending National Reviews</h6>
                        <small>Reports awaiting action from the national</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Approved Reports --}}
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'approved']) }}" class="text-decoration-none">
                <div class="card h-100 shadow border-0 text-white bg-info hover-card">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                        <div class="mb-2">
                            <i class="fa fa-check-circle fa-3x"></i>
                        </div>
                        <h6 class="mb-1">Approved Reports</h6>
                        <small>Completed approvals</small>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
