@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('active')
<li class="breadcrumb-item">Dashboard</li>
@endsection
@section('content')
<div class="content-body">
    @include('includes.alerts')
    @if(auth()->user()->wallet >= $setting->min_payout_amount)
    <!-- Dashboard Ecommerce Starts -->
    <section id="dashboard-ecommerce">
        <div class="row">
            <!-- Greetings Content Starts -->
            <div class="col-md-12 col-12 dashboard-greetings">
                <div class="card">
                    <div class="card-header">
                        <h3 class="greeting-text">Congratulations {{ auth()->user()->name }}!</h3>
                        <p class="mb-0">You have reached withdrawal threshhold</p>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="dashboard-content-left">
                                    <h1 class="text-primary font-large-2 text-bold-500"></h1>
                                   <a @if(auth()->user()->registration_status == 'Pending')  href="{{ route('profile.edit', auth()->user()->id) }}"  onclick="return confirm('You have not completed your registration, would you like to complete now?');" @endif
                                    <button type="button" class="btn btn-primary glow">
                                    </button>
                                    </a> 
                                </div>
                                <div class="dashboard-content-right">
                                    <img src="{{ asset('app-assets/images/icon/cup.png') }}" height="220" width="80" class="img-fluid" alt="Dashboard" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>               
        </div>
    </section>
    @endif
     <!-- Dashboard Ecommerce Starts -->
    <section id="dashboard-ecommerce">
        <div class="row">
            <!-- Greetings Content Starts -->
            <div class="col-md-12 col-12 dashboard-greetings">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="dashboard-content-left">
                                    <h1 class="text-primary font-large-2 text-bold-500"></h1>
                                    <p>In wallet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>               
        </div>
    </section>

    <section id="dashboard-ecommerce">
    <div class="row  ">
        <!-- Statistics Cards Starts -->
        <div class="col-12">
            <div class="row">
                <div class="col-md-3 col-12 dashboard-users-success">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body py-1">
                                <div class="text-muted line-ellipsis">Submitted Posts</div>
                                <h3 class="mb-0"></h3>
                            </div>
                        </div>
                    </div>
                </div>
              
                <div class="col-md-4 col-12 dashboard-users-danger">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body py-1">
                                <div class="text-muted line-ellipsis">Posts Pending Approval</div>
                                <h3 class="mb-0">{{ $posts_pending_approval }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 activity-card">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body pt-1">
                                <div class="d-flex activity-content">
                                    <div class="avatar bg-rgba-primary m-0 mr-75">
                                        <div class="avatar-content">
                                            <i class="bx bx-bar-chart-alt-2 text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="activity-progress flex-grow-1">
                                        <small class="text-muted d-inline-block mb-50">Left to Withdrawal Request</small>
                                        <small class="float-right">{!! $setting->default_currency !!} {{ $setting->min_payout_amount - auth()->user()->wallet }}</small>
                                        <div class="progress progress-bar-primary progress-sm">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="{{ auth()->user()->wallet }}" style="width:{{ auth()->user()->wallet*100/$setting->min_payout_amount }}%"></div>
                                        </div>
                                    </div>
                                </div>
                              
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