@extends('layouts.dashboard')
@section('title', 'Donations')
@section('active')
<li class="breadcrumb-item">Donations</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Donations</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date</th>
                                            <th>Donator</th>
                                            <th>Amount Donated</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($donations as $donation)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $donation->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                {{ $donation->name }} <br>
                                                {{ $donation->email }} <br>
                                                {{ $donation->phone }}

                                            </td>
                                            <td>{!! currency_symbol() !!}{{ $donation->amount }}</td>
                                            <td>{{ ucfirst($donation->type) }}</td>
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
</div>
@endsection
