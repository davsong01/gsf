@extends('frontend.main.mainlayout')
@section('title', 'Member search result')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>GSF Community - Member search results</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
<div class="container mb0">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
            <h2>{{ $results->count() }} result(s) found for <i>{{ $name }}</i></h2>
        </div>
    </div><!--/row-->
</div>

@if($results->count() < 1)
<div class="container mb0">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
            <a href="{{ route('people.students') }}"><button class="btn btn-primary lg">Go back</button></a>
        </div>
    </div><!--/row-->
</div>
@endif
<div class="">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb40">
                <div class="row">
                    @foreach($results as $user)
                    @include('includes.user_block')
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