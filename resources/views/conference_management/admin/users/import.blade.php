@extends('layouts.conference')
@section('title', 'Participant Import')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('conference.participants',['type'=>'Participant', 'edition'=>$edition->id]) }}">Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Import Participant</li>
@endsection
@section('content2')
<!-- Transitions Start-->

<div class="content-body">
    <div class="container">

        <div class="row">

            <!-- LEFT COLUMN: Guidelines + Download -->
            <div class="col-md-6">
                <h3 class="ml-2">Import {{ $type ?? '' }}</h3>

                <div class="card">
                    <div class="card-body">
                        <p>Please select an Excel file to upload. Make sure to follow these rules:</p>
                        <ul>
                            <li>Only Excel formats are supported: <strong>.csv, .xls, .xlsx</strong>.</li>
                            <li>Do not include empty rows at the bottom of the file.</li>
                        </ul>

                        <h6 class="font-weight-bold">Required Excel Headers</h6>
                        <p class="text-muted mb-2">Your file must contain the following:</p>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-3">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Field</th>
                                        <th>Description</th>
                                        <th>Requirement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fields as $plan)
                                        <tr>
                                            <td>{{ $plan->name }}</td>
                                            <td>{{ $plan->label }}</td>
                                            <td>{{ $plan->required ? 'Mandatory' : 'Optional' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <a href="{{ route('conference.usersexport.sample', ['type' => $type, 'import_type' => $import_type, 'edition' => $edition->id]) }}"
                           class="btn btn-primary">
                            <i class="fa fa-download"></i> Download Sample
                        </a>
                    </div>
                </div>
            </div>


            <!-- RIGHT COLUMN: Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">

                        <form action="{{ route('conferenceusers.import', ['type' => $type, 'edition' => $edition->id]) }}"

                              method="POST"
                              enctype="multipart/form-data"
                              class="@if($errors->any()) has-error @endif">
                            @csrf

                            <div class="form-group">
                                <label for="file">Upload Excel File</label>
                                <input type="file" name="file" class="form-control" accept=".csv, .xls, .xlsx" required>
                            </div>

                            <input type="hidden" name="import_level" value="{{ $type }}">
                            <input type="hidden" name="import_type" value="{{ $import_type }}">

                            @error('file')
                                <div class="alert alert-danger alert-dismissible fade show">
                                    {{ $message }}
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            @enderror

                            <button type="submit" class="btn btn-success">Import File</button>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Transitions End-->
@endsection
