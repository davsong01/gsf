@extends('frontend.main.mainlayout')
@section('title', 'All search results')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>GSF Community - Search results</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
<div class="container mb0">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center center-heading">
            <h3>{{ $searchMember->count() + $searchAlumni->count() }} result(s) found</h3>
            @if($searchMember->count() > 0)
            <h4><a href="#student">{{ $searchMember->count() }} Student(s)</a></h4>
            @endif
            @if($searchAlumni->count() > 0) 
            <h4> <a href="#alumni">{{ $searchAlumni->count() }} Alumni</a> 
            </h4>@endif
        </div>
    </div><!--/row-->
</div>
@if($searchMember->count() + $searchAlumni->count() > 0)
    @if($searchMember->count() > 0)
        <div class="container" id="student">
            <div class="row">
                <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
                                <h3>Active student(s)</h3>
                            </div>
                            @foreach($searchMember as $user)
                            @include('includes.user_block')
                            @endforeach
                        </div>
                </div>
            </div>
        </div>
    @endif

    @if($searchAlumni->count() > 0)
    <div class="container" id="alumni">
        <div class="row">
            <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
                            <h3>Alumni</h3>
                        </div>
                        @foreach($searchAlumni as $user)
                        @include('includes.user_block')
                        @endforeach
                    </div>
            </div>
        </div>
    </div>
    @endif
@else
<div class="container">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
            <a href="{{ route('home.index') }}"><button class="btn btn-primary lg">Go back</button></a>
        </div>
    </div><!--/row-->
</div>
@endif
@endsection