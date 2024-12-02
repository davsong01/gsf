@extends('frontend.spaces.layouts.app')
@section('title', 'GSF - Campus Tracker')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="section section-header section-image bg-primary overlay-primary text-white overflow-hidden pb-6"
    data-background="{{ asset($chapter->banner ?? 'main/images/chapters/coming-soon.png') }}">
    <div class="container z-2">
        <div class="row justify-content-center pt-3">
            <div class="col-12 text-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-transparent justify-content-center mb-4">
                        <li class="breadcrumb-item text-secondary"><a href="/">Home</a></li>
                        <li class="breadcrumb-item text-secondary"><a href="{{ route('people.campuses') }}">GSF - Campuses</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">GSF - Campus Tracker</li>
                    </ol>
                </nav>
                
            </div>
        </div>
    </div>
</div>
<div class="section section-lg pt-5" style="padding-bottom: 0rem !important">
    <div class="container">
        @include('includes.alerts')
        <div class="row">
            <div class="col-md-12 mt-3 mt-lg-0">
                <div class="card border-light mt-4 p-3">
                    <h5 class="font-weight-normal">Select GSF campus and click view details</h5>
                    {{-- <form class="mt-3" action="{{ route('campus.single') }}" method="GET">
                        @csrf
                        <div class="form-group">
                            <select name="chapter" class="form-control select2 chapter" required>
                                <option value="">--Select Campus</option>
                                @foreach($chapters as $chapter)
                                <option value="{{ $chapter->id }}" {{ old('chapter') == $chapter->id ? 'selected' : ''}}>
                                    {{ $chapter->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary submitregistration" type="submit" style="width:100%">View details</button>
                    </form> --}}
                    <form class="mt-3" action="{{ route('campus.single') }}" method="GET">
                        {{-- @csrf <!-- This is unnecessary for GET requests, but you can keep it if it's part of a blade component --> --}}
                        <div class="form-group">
                            <select name="chapter" class="form-control select2 chapter" required>
                                <option value="">--Select Campus--</option>
                                @foreach($chapters as $chapter)
                                    <option value="{{ $chapter->id }}" {{ request('chapter') == $chapter->id ? 'selected' : '' }}>
                                        {{ $chapter->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary submitregistration" type="submit" style="width:100%">View details</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.chapter').select2();
        });
    </script>
@endsection