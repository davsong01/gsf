@extends('layouts.dashboard')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('conference.participants',['type'=>'Participant', 'edition'=>$edition->id]) }}">Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Import Participant</li>
@endsection
@section('content')
<div class="content-body">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pb-0">
                        <h3 class="mb-1">Import {{ $type ?? null }}</h3>
                        <p class="text-muted mb-0">Upload your participant list using the required Excel format</p>
                    </div>

                    <div class="card-body">

                        {{-- ================== GUIDELINES ================== --}}
                        <div class="p-1 mb-1 rounded" style="background:#f9f9f9; border:1px solid #e3e3e3;">
                            <h5 class="mb-1">
                                <i class="fa fa-info-circle text-primary"></i> Guidelines
                            </h5>

                            <ul class="pl-1">
                                <li>Only Excel formats are supported: <strong>.csv, .xlsv, .xls, .xlsx</strong>.</li>
                                <li>Do not include empty rows at the bottom of the file.</li>

                                @if($fields)
                                    <hr class="my-3">

                                    <h6 class="font-weight-bold">Required Excel Headers</h6>
                                    <p class="text-muted mb-2">Your file must contain the following:</p>

                                    {{-- MOBILE-FRIENDLY TABLE (scrollable on small screens) --}}
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
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
                                @endif

                                @if(getRegistrationUserLevel(['Moderator'], $edition))
                                    <li class="text-danger mt-3">
                                        <strong>You may upload a maximum of {{ $transaction->slot - $transaction->slot_filled }} participants.</strong>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        {{-- ================== DOWNLOAD SAMPLE ================== --}}
                        <div class="text-left mb-4">
                            <a class="btn btn-primary"
                                href="{{ route('conference.usersexport.sample',['type'=>$type, 'edition' => $edition->id]) }}">
                                <i class="fa fa-download"></i> Download Sample
                            </a>
                        </div>

                        {{-- ================== UPLOAD FORM ================== --}}
                        <form action="{{ route('conferenceusers.import',['edition'=>$edition->id]) }}"
                              method="POST"
                              enctype="multipart/form-data">

                            @csrf

                            <div class="form-group">
                                <label class="font-weight-bold">Select Excel File</label>
                                <input type="file"
                                       name="file"
                                       class="form-control"
                                       accept=".csv,.xls,.xlsx"
                                       required>
                            </div>

                            <input type="hidden" name="import_level" value="Participant">

                            @error('file')
                            <div class="alert alert-danger alert-dismissible fade show mt-2">
                                {{ $message }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                            @enderror

                            <button class="btn btn-success px-4 mt-3">
                                <i class="fa fa-upload"></i> Import File
                            </button>

                        </form>

                    </div> {{-- card-body --}}
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
