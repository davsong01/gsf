@extends('layouts.dashboard')
@section('title', 'Payout')
@section('active')
<li class="breadcrumb-item">My Payouts</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Payout History</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Amount Requested</th>
                                            <th>Date Requested</th>
                                            <th>Status</th>
                                            <th>Date Paid</th>
                                             <th>Amount Paid</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payouts as $payout)
                                        <tr>
                                            <td>{{ $i ++ }}</td>
                                            <td>{!! $setting->default_currency !!} {{ $payout->amount_requested }}</td>
                                            <td>{{ $payout->created_at }}</td>
                                            <td><a href="#" class="btn btn-primary mt-1">{{ $payout->status }}</a></td>
                                            <td>{{ $payout->paid_at }}</td>
                                            <td>{!! $setting->default_currency !!} {{ $payout->amount_paid }}</td>
                                        </tr>
                                      
                                        @endforeach
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