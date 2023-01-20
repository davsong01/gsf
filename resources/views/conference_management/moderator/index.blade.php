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
                        <h4 class="card-title">Moderator Analytics</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body pb-1">
                            <div class="d-flex justify-content-around align-items-center flex-wrap">
                                <div class="user-analytics">
                                    <i class="bx bx-user mr-25 align-middle"></i>
                                    <span class="align-middle text-muted">{{ auth()->user()->slot  }} Slot(s) paid for</span>
                                    <div class="d-flex">
                                        <div id="radial-success-chart"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                                <div class="sessions-analytics">
                                    <i class="bx bx-trending-down align-middle mr-25" style="color:red"></i>
                                    <span class="align-middle text-muted">{{ auth()->user()->slot_filled }} Slot(s) used</span>
                                    <div class="d-flex">
                                        <div id="radial-warning-chart-down"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                                <div class="sessions-analytics">
                                    <i class="bx bx-trending-up align-middle mr-25" style="color:green"></i>
                                    <span class="align-middle text-muted">{{ auth()->user()->slot - auth()->user()->slot_filled }} Slot(s) remaining</span>
                                    <div class="d-flex">
                                        <div id="radial-warning-chart"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                                <div class="bounce-rate-analytics">
                                    <i class="bx bx-pie-user align-middle mr-25"></i>
                                    <span class="align-middle text-muted">{{ $pending_registration->count() }} Pending Registration</span>
                                    <div class="d-flex">
                                        <div id="radial-danger-chart"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                                <div class="bounce-rate-analytics">
                                    <i class="fa fa-registered align-middle mr-25"></i>
                                    <span class="align-middle text-muted">{{ $completed_registration->count() }} Complete Registration</span>
                                    <div class="d-flex">
                                        <div id="radial-danger-chart"></div>
                                        <h3 class="mt-1 ml-50"></h3>
                                    </div>
                                </div>
                            </div>
                            <div id="analytics-bar-chart"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
        <!-- Dashboard Ecommerce Starts -->
        <section id="dashboard-ecommerce">
            <div class="row">
                <div class="col-md-12 dashboard-users">
                    <div class="row  ">
                        <!-- Statistics Cards Starts -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-sm-4 col-12 dashboard-users-success">
                                    <div class="card text-center">
                                        <a href="{{ route('payouts.index') }}">
                                            <div class="card-content">
                                                <div class="card-body py-1">
                                                    <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                                        <i class="bx bx-briefcase-alt font-medium-5"></i>
                                                    </div>
                                                    <div class="text-muted line-ellipsis">Pending Payouts</div>
                                                    <h3 class="mb-0"></h3>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-12 dashboard-users-danger">
                                    <div class="card text-center">
                                        <a href="{{ route('payouts.index') }}">
                                            <div class="card-content">
                                                <div class="card-body py-1">
                                                    <div class="badge-circle badge-circle-lg badge-circle-light-warning mx-auto mb-50">
                                                        <i class="bx bx-dollar font-medium-5"></i>
                                                    </div>
                                                    <div class="text-muted line-ellipsis">Completed Payouts</div>
                                                    <h3 class="mb-0"></h3>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-12 dashboard-users-danger">
                                    <div class="card text-center">
                                        <a href="{{ route('users.index') }}">
                                            <div class="card-content">
                                                <div class="card-body py-1">
                                                    <div class="badge-circle badge-circle-lg badge-circle-light-danger mx-auto mb-50">
                                                        <i class="bx bx-user font-medium-5"></i>
                                                    </div>
                                                    <div class="text-muted line-ellipsis">Eligible for Payout</div>
                                                    <h3 class="mb-0"></h3>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Revenue Growth Chart Starts -->
                    </div>
                </div>
            </div> 
        </section>
        <!-- Dashboard Ecommerce ends -->

       
</div>
@endsection