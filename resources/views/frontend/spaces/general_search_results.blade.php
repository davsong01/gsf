@extends('frontend.spaces.layouts.app')
@section('content')
      <section class="section-header bg-primary pb-9 pb-md-11 mb-4 mb-lg-6 text-white">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-10 col-lg-8 text-center">
            <h1 class="display-2 mb-3">{{ $searchMember->count() }} result(s) found</h1>
            {{-- <p class="lead">
                @if($searchMember->count() > 0)
                <h5><a href="#student">{{ $searchMember->count() }} Student(s)</a></h4>
                @endif
                @if($searchAlumni->count() > 0) 
                <h5> <a href="#alumni">{{ $searchAlumni->count() }} Alumni</a> 
                </h4>@endif
            </p> --}}
          </div>
        </div>
      </div>
    </section>
    <section class="section section-lg pt-0">
      <div class="container mt-n8 mt-lg-n10 z-2">
        <div class="row mt-7">
            @if($searchMember->count() > 0)
                @foreach($searchMember as $user)
                    @include('frontend.spaces.includes.user_block')
                @endforeach
            @endif
        </div>
      </div>
    </section>
    <section class="section section-lg pt-0">
      <div class="col mt-3 d-flex justify-content-center">
        {{-- {{$searchMember->links()}} --}}
      </div>
    </section>

@endsection