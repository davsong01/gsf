@extends('layouts.stakeholderdashboard')
@section('title', 'Payments')
@section('active')
<li class="breadcrumb-item">Payments</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All payments</h4>
                        @if(Auth::guard('stakeholder')->user()->role == 'President')
                        <a href="{{ route('stakeholderpayment.create') }}" class="btn btn-primary mt-1">Add proof of payment <strong></strong></a>
                        @endif
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            @if(Auth::guard('stakeholder')->user()->role == 'Field Pastor' || Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' || Auth::guard('stakeholder')->user()->role == 'Financial Secretary' || Auth::guard('stakeholder')->user()->role == 'Secretariat')
                                            <th>Chapter</th>
                                            @endif
                                            <th>Month/Year</th>
                                            <th>Date Uploaded</th>
                                            <th>Download File</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $count ++ }}</td>
                                            @if(Auth::guard('stakeholder')->user()->role == 'Field Pastor' || Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' || Auth::guard('stakeholder')->user()->role == 'Financial Secretary' || Auth::guard('stakeholder')->user()->role == 'Secretariat')
                                            <td>{{ $payment->report->chapter->name }}</td>
                                            @endif
                                            <td>{{ date("F", mktime(0, 0, 0, $payment->report->month, 10)) . ' ' . $payment->report->year }}</td>
                                                                                        
                                            <td>{{ $payment->created_at->format('d-m-Y:h-m-s') }}</td>
                                            <td><a onclick="return confirm('File will be downloaded to your computer?');" href="downloadpop/{{ $payment->id }}"><i class="fa fa-download"> Download</i></a></td>
                                            <td> 
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Payment" href="{{ route('stakeholderpayment.delete', $payment->id) }}"> <i class="fa fa-trash"></i></
                                                </a>
                                        </td>
                                        
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