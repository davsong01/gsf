@extends('layouts.conference')
@section('title', 'Participant Import')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('conference.participants',['type'=>'Participant', 'edition'=>$edition->id]) }}">Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Add Participant</li>
@endsection
@section('content2')
<!-- Transitions Start-->

<div class="content-body">
	<div class="container">
		<div class="row">
            <div class="col-lg-12">
                <h3>Import {{ $type ?? '' }}</h3>
                @include('includes.alerts')
                <div class="row mb-3 px-1">
                    <a href="{{ route('conference.usersexport.sample', ['type' => $type]) }}" class="btn btn-primary mb-3">
                        <i class="fa fa-download"></i> Download Sample
                    </a>

                    <div class="card-header">
                        <p>Please select an Excel file to upload. Make sure to follow these rules:</p>
                        <ul>
                            <li>Only Excel formats (.csv, .xls, .xlsx) are accepted.</li>
                            <li><strong>Name</strong> and <strong>Email</strong> must be present in the file.</li>
                            <li>Ensure there are no extra blank rows at the end of the file.</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.conferenceuser.import', ['type' => $type, 'edition' => $edition->id]) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="@if($errors->any()) has-error @endif px-1">
                    @csrf

                    @if(auth()->user()->role == 1)
                        {{-- <div class="form-group">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">-- Select Type --</option>
                                <option value="0">Participant</option>
                                <option value="1">Alumni</option>
                            </select>
                        </div> --}}

                        <div class="form-group">
                            <label for="chapter_id">Campus</label>
                            <select name="chapter_id" id="chapter_id" class="form-control">
                                <option value="">-- Select Campus --</option>
                                @foreach($chapters as $chapter)
                                    <option value="{{ $chapter->id }}"
                                            {{ old('chapter_id') == $chapter->id ? 'selected' : '' }}>
                                        {{ $chapter->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="file">Upload Excel File</label>
                        <input type="file" name="file" class="form-control" accept=".csv, .xls, .xlsx" required>
                    </div>

                    <input type="hidden" name="import_level" value="{{ $type }}">
                    <input type="hidden" name="import_type" value="{{ $import_type }}">

                    @error('file')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
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
<!-- Transitions End-->
@endsection
