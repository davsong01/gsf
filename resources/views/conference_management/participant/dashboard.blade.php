
@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('active')
<li class="breadcrumb-item">Dashboard</li>
@endsection
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
                        <h4 class="card-title">Website Analytics</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body pb-1">
                            <div class="d-flex justify-content-around align-items-center flex-wrap">
                                <div class="user-analytics">
                                    <i class="bx bx-user mr-25 align-middle"></i>
                                    <span class="align-middle text-muted">Users</span>
                                    <div class="d-flex">
                                        <div id="radial-success-chart"></div>
                                        <h3 class="mt-1 ml-50">61K</h3>
                                    </div>
                                </div>
                                <div class="sessions-analytics">
                                    <i class="bx bx-trending-up align-middle mr-25"></i>
                                    <span class="align-middle text-muted">Sessions</span>
                                    <div class="d-flex">
                                        <div id="radial-warning-chart"></div>
                                        <h3 class="mt-1 ml-50">92K</h3>
                                    </div>
                                </div>
                                <div class="bounce-rate-analytics">
                                    <i class="bx bx-pie-chart-alt align-middle mr-25"></i>
                                    <span class="align-middle text-muted">Bounce Rate</span>
                                    <div class="d-flex">
                                        <div id="radial-danger-chart"></div>
                                        <h3 class="mt-1 ml-50">72.6%</h3>
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
                <!-- Greetings Content Starts -->
                <div class="col-xl-4 col-md-6 col-12 dashboard-greetings">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="greeting-text">Congratulations John!</h3>
                            <p class="mb-0">Best seller of the month</p>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div class="dashboard-content-left">
                                        <h1 class="text-primary font-large-2 text-bold-500">$89k</h1>
                                        <p>You have done 57.6% more sales today.</p>
                                        <button type="button" class="btn btn-primary glow">View Sales</button>
                                    </div>
                                    <div class="dashboard-content-right">
                                        <img src="app-assets/images/icon/cup.png" height="220" width="220" class="img-fluid" alt="Dashboard Ecommerce" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Multi Radial Chart Starts -->
                <div class="col-xl-4 col-md-6 col-12 dashboard-visit">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Visits of 2019</h4>
                            <i class="bx bx-dots-vertical-rounded font-medium-3 cursor-pointer"></i>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div id="multi-radial-chart"></div>
                                <ul class="list-inline d-flex justify-content-around mb-0">
                                    <li> <span class="bullet bullet-xs bullet-primary mr-50"></span>Target</li>
                                    <li> <span class="bullet bullet-xs bullet-danger mr-50"></span>Mart</li>
                                    <li> <span class="bullet bullet-xs bullet-warning mr-50"></span>Ebay</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-12 dashboard-users">
                    <div class="row  ">
                        <!-- Statistics Cards Starts -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-sm-6 col-12 dashboard-users-success">
                                    <div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body py-1">
                                                <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                                    <i class="bx bx-briefcase-alt font-medium-5"></i>
                                                </div>
                                                <div class="text-muted line-ellipsis">New Products</div>
                                                <h3 class="mb-0">1.2k</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12 dashboard-users-danger">
                                    <div class="card text-center">
                                        <div class="card-content">
                                            <div class="card-body py-1">
                                                <div class="badge-circle badge-circle-lg badge-circle-light-danger mx-auto mb-50">
                                                    <i class="bx bx-user font-medium-5"></i>
                                                </div>
                                                <div class="text-muted line-ellipsis">New Users</div>
                                                <h3 class="mb-0">45.6k</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-6 col-12 dashboard-revenue-growth">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center pb-0">
                                            <h4 class="card-title">Revenue Growth</h4>
                                            <div class="d-flex align-items-end justify-content-end">
                                                <span class="mr-25">$25,980</span>
                                                <i class="bx bx-dots-vertical-rounded font-medium-3 cursor-pointer"></i>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body pb-0">
                                                <div id="revenue-growth-chart"></div>
                                            </div>
                                        </div>
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

    <!-- Dashboard Analytics end -->
        <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Users</h4>
                        <button class="btn btn-primary">Add new</button>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Email</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><i class="bx bxs-circle success font-small-1 mr-50"></i>Approved</td>
                                            <td>Tiger Nixon</td>
                                            <td>System Architect</td>
                                            <td>Edinburgh</td>
                                            
                                        </tr>
                                        <tr>
                                            <td><i class="bx bxs-circle danger font-small-1 mr-50"></i><span>Rejected</span></td>
                                            <td>Accountant</td>
                                            <td>Accountant</td>
                                            <td>Tokyo</td>
                                            
                                        </tr>
                                        
                                        <tr>
                                                <td><i class="bx bxs-circle danger font-small-1 mr-50"></i><span>Rejected</span></td>
                                            
                                            <td>Donna Snider</td>
                                            <td>Customer Support</td>
                                            <td>New York</td>
                                            
                                        </tr>
                                    </tbody>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Zero configuration table -->         
</div>
@endsection