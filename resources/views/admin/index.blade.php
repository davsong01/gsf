@extends('layouts.dashboard')
@section('title', 'Dashboard')

@section('content')
<div class="content-body">
    @include('includes.alerts')
    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics">
        <div class="row">
            <!-- Website Analytics Starts-->
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Conference Analytics</h4>
                    </div>
                    <div class="card-content">
                        <div class="row">
                            <div class="col-12">

                            </div>
                            <div class="col-sm-4 col-12 dashboard-users-success">
                                <div class="card text-center">
                                    <a href="{{ route('users.index') }}">
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
                                    
                                        <div class="card-content">
                                            <div class="card-body py-1">
                                                <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                                    &#8358;{{ $donations }}
                                                </div>
                                                <div class="text-muted line-ellipsis">Total Donations</div>
                                                <h3 class="mb-0"></h3>
                                            </div>
                                        </div>

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
                </div>

            </div>
        </div>
    </section>
       
</div>
@endsection