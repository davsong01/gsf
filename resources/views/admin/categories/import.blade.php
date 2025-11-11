@extends('dashboard')
@section('content')
<div class="income-order-visit-user-area mg-t-40">
    <div class="container">

    </div>
</div>
<!-- Transitions Start-->
<div class="transition-world-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="transition-world-list shadow-reset">
                    <div class="sparkline7-list mg-b-40">
                        <div class="sparkline7-hd">
                            <div class="main-spark7-hd">
                                <h1>Import Questions Category</h1>
                                @include('includes.alerts')
                            </div>

                        </div>
                        <div class="sparkline7-graph">
                            <div class="row">
															<a class="btn btn-primary" href="{{ route('categories.export') }}"><i class="fa fa-download"></i> Download sample</a>
                                <div class="card-header">
                                    <p>Select an excel file to upload, please pay attention to the following: </p>
                                        <ul>
                                            <li>
                                                Only Excel format is acceptable
                                            </li>
                                            <li>
                                                Company name must be present
                                            </li>
                                        </ul>
                             
                                </div>

                                <form action="{{ url('importcategories') }}" method="POST" name="importform"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="file" class="form-control" accept=".csv, .xlsv, .xls, .xlsx" required>
                                    <br>
                                    @if ($errors->has('file'))
                                    <div class="alert alert-danger" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                          {{ $errors->first('file') }}
                                    </div>
                                    @endif
                                    <button class="btn btn-success submit-button">Import File</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Transitions End-->
@endsection