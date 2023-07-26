@extends('layouts.stakeholderdashboard')
@section('title', 'Add new proof of payment')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('stakeholder.dashboard') }}">Proof of Payment</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Add New proof of payment</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add New proof of payment</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('stakeholderpayment.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                            
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="report">Select Associated Report</label>
                                        <select class="form-control" name="report"
                                            id="report">
                                            <option value="">Not Applicable</option>
                                            @foreach($reports as $report)
                                            <option value="{{ $report->id }}" {{ old('report') == $report->id ? 'selected' : '' }}>{{ date("F", mktime(0, 0, 0, $report->month, 10)) . ' ' . $report->year }}'s report</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="report">Description</label>
                                        <select class="form-control" name="description"
                                            id="description">
                                            <option value="">Not Applicable</option>
                                            @foreach($descriptions as $description)
                                            <option value="{{ $description}}" {{ old('description') == $description ? 'selected' : '' }}>{{ $description }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="image">Amount</label>
                                        <input type="number" value="{{ old('amount') }}" class="form-control" name="amount" step=".01"
                                        id="amount" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="report">Is school in session?</label>
                                        <select class="form-control" name="insession"
                                            id="insession" required>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                            <label for="image">Upload proof of payment</label>
                                            <input type="file" value="{{ old('image') }}" class="form-control" name="image"
                                            id="image" required>
                                    </fieldset>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <button class="btn btn-primary" style="width:100%" type="submit">Send Report</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
<!-- Basic Inputs end -->
</div>
@endsection