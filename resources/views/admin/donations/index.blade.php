@extends('layouts.conference')
@section('title', 'Donations')
@section('active')
<li class="breadcrumb-item">Conference Donations</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Donations for {{ $edition->conference_theme }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Amount Donated</th>
                                            <th>State</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($donations as $donation)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $donation->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $donation->name }}</td>
                                            <td>{{ $donation->email }}</td>
                                            <td>{{ $donation->phone }}</td>
                                            <td>&#8358;{{ $donation->amount }}</td>
                                            <td>{{ $donation->state }}</td>
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