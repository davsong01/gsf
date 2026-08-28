@extends('frontend.main.mainlayout')
@section('title', 'Alumni search result')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>GSF Community - Alumni search results</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
<div class="container mb0">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
            <h2>{{ $results->count() }} result(s) found</h2>
        </div>
    </div><!--/row-->
</div>

@if($results->count() < 1)
<div class="container mb0">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
            <a href="{{ route('people.alumni') }}"><button class="btn btn-primary lg">Go back</button></a>
        </div>
    </div><!--/row-->
</div>
@endif
<div class="">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb40">
                <div class="row">
                    @foreach($results as $alumni)
                    <div class="col-sm-4 mb30">
                        <div class="team-card">
                            {!! renderAvatar($alumni, 96, 'img-responsive alumni-img') !!}
                            <div class="team-overlay">
                                <ul class="list-inline">
                                    <li><a href="{{ $alumni->facebook }}" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="{{ $alumni->twitter }}" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                </ul>
                            </div>                        
                        </div>
                        <div class="team-content">
                            <a href="{{ route('alumni.single', $alumni->slug) }}">{{ ucfirst($alumni->name) }}</a><br>
                            <em>{{ isset($alumni->campus) ? $alumni->campus->name : ''}}</em> <br>
                            <em>{{ $alumni->session }}</em> <br>
                            <a href="{{ route('alumni.single', $alumni->slug) }}"><button class="btn btn-info view-campus-details">View details</button></a>
                        </div>
                    </div><!--/col-->
                    @endforeach
                </div>
                
            </div>
        
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  
      $(document).ready(function () {
          $('#chapter').keyup(function () {
              var data = $(this).val();
              if (data != '') {
                //   var _token = $('input[name="_token"]').val();
                  $.ajax({
                      url: "{{ route('campus.suggestions') }}",
                      method: "GET",
                      data: {data: data},
                      success: function (data) {
                          $('#productList').fadeIn();
                          $('#productList').html(data);
                      }
                  });
              }
          });
          $(document).on('click', 'option', function () {
            $('#chapter').val($(this).text());
              $('#productList').fadeOut();
              $('#chapter_id').val($(this).val());
          });
      });
  </script>
@endsection
