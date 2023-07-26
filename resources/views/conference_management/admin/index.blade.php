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
        <div class="col-12">

        </div>
        <div class="col-sm-4 col-12 dashboard-users-success">
            <div class="card text-center">
                <a href="{{ route('conference.participants',['type'=>'Participant', 'edition'=>$edition->id]) }}">
                    <div class="card-content">
                        <div class="card-body py-1">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                {{ $registered_participants }}
                            </div>
                            <div class="text-muted line-ellipsis">Registered Participants</div>
                            <h3 class="mb-0"></h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-sm-4 col-12 dashboard-users-success">
            <div class="card text-center">
                <a href="{{ route('users.index') }}">
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
        <div class="col-sm-4 col-12 dashboard-users-success">
            <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body py-1">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                &#8358;{{ $total }}
                            </div>
                            <div class="text-muted line-ellipsis">Total Payment</div>
                            <h3 class="mb-0"></h3>
                        </div>
                    </div>
                    
            </div>
        </div>
        <div class="col-sm-4 col-12 dashboard-users-success">
            <div class="card text-center">
                    <a href="{{ route('donations.index',['edition'=>$edition->id]) }}">
                
                    <div class="card-content">
                        <div class="card-body py-1">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                &#8358;{{ $donations }}
                            </div>
                            <div class="text-muted line-ellipsis">Total Donations</div>
                            <h3 class="mb-0"></h3>
                        </div>
                    </div>
                    </a>
            </div>
        </div>
        <div class="col-sm-4 col-12 dashboard-users-success">
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
    </div>
</div>
@endsection