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
                        @if(Auth::guard('stakeholder')->user()->role == 'Financial Secretary' || Auth::guard('stakeholder')->user()->role == 'Secretariat')
                        <div class="card">
                            <div class="card-header" style="padding: 10px 10px 0 0;">
                                <h6>Export options</h6>
                            </div>
                             <form action="{{ route('pop.export') }}" method="POST">
                                @csrf
                            <div class="body">
                                <div class="row">

                                    <div class="col-md-5 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="campus">Campus</label>

                                            <select class="form-control" name="campus" id="campus" required>
                                            <option value="all">--All--</option>
                                            @foreach( \App\Chapter::all() as $chapter)
                                            <option value="{{ $chapter->id ?? old('chapter')}}" {{ old('campus') == $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>
                                            @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="month">Month</label>
                                            <select class="form-control" name="month"
                                                id="month" required>
                                                <option value="all">--All--</option>
                                                @foreach($months as $month=>$value)
                                                <option value="{{ $value }}" {{ old('month') == $value ? 'selected' : '' }}>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-2 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="year">Year</label>
                                            <select class="form-control" name="year" id="year" required>
                                                <option value="all">--All--</option>
                                                @foreach($years as $key => $value)
                                                <option value="{{ $value }}" {{ old('year') == $value ? 'selected' : '' }}>{{ $key }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>


                                    <div class="col-md-2 col-sm-12">
                                        <fieldset class="form-group"> <br>
                                        <input class="btn btn-primary" value="Export" type="submit">
                                        </fieldset>
                                    </div>

                                </div>
                            </div>
                        </form>
                        </div>

                        @endif
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
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Date Uploaded</th>

                                            <th>Download File</th>
                                            @if (Auth::guard('stakeholder')->user()->role == 'Secretariat' || Auth::guard('stakeholder')->user()->role == 'President' || Auth::guard('stakeholder')->user()->role == 'Financial Secretary')
                                            <th>Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $count ++ }}</td>
                                            @if(Auth::guard('stakeholder')->user()->role == 'Field Pastor' || Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' || Auth::guard('stakeholder')->user()->role == 'Financial Secretary' || Auth::guard('stakeholder')->user()->role == 'Secretariat')
                                            <td>{{ $payment->report->chapter->name ?? $payment->chapter->name  ?? 'N/A'}}</td>
                                            @endif
                                            <td>@if(!is_null($payment->report))
                                                {{ date("F", mktime(0, 0, 0, $payment->report->month, 10)) . ' ' . $payment->report->year }}
                                                @else
                                                {{ date("F", mktime(0, 0, 0, $payment->month, 10)) . ' ' . $payment->year }}
                                                @endif
                                            </td>
                                            <td>{{ $payment->description ?? 'N/A' }}</td>
                                            <td>{!! currency_symbol() !!}{{ number_format($payment->amount, 2) }}</td>

                                            <td>{{ $payment->created_at->format('d-m-Y:h-m-s') }}</td>
                                            <td><a onclick="return confirm('File will be downloaded to your computer?');" href="downloadpop/{{ $payment->id }}"><i class="fa fa-download"> Download</i></a></td>
                                            @if (Auth::guard('stakeholder')->user()->role == 'Secretariat' || Auth::guard('stakeholder')->user()->role == 'President' || Auth::guard('stakeholder')->user()->role == 'Financial Secretary')
                                            <td>
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Payment" href="{{ route('stakeholderpayment.delete', $payment->id) }}"> <i class="fa fa-trash actions"></i></
                                                </a>
                                            </td>
                                            @endif
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
