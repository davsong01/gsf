@extends('frontend.main.mainlayout')
@section('title', 'Alumni')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>GSF Community - Alumni</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
<div class="container mb0">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
            <h2>Our Alumni</h2>
            <p>
                List most recent places are submitted by our users. It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.
            </p>
        </div>
    </div><!--/row-->
</div>
<div class="container mb70">
    <div class="row">
        <form action="{{ route('alumni.search') }}" method="POST">
            @csrf
            <div class="col-md-4 col-sm-6">
                <div class="form-group">                           
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Type alumni name... minimum of 4 characters" minlength="4" required>
                </div>
            </div>
            <div class="col-md-7 col-sm-6">
                <div class="form-group">
                    <input type="text" id="chapter" name="chapter" class="form-control"
                    placeholder="Start typing to search for a GSF chapter"/>
                    <input type="hidden" id="chapter_id" name="chapter_id" class="form-control input-lg" required/>
                    <div id="productList">
                    </div>
                </div>
            </div>
            
            <div class="col-md-1 col-sm-6">
                <input type="submit" class="btn btn-primary btn-block" value="Search">
            </div>
        </form>
    </div>
</div>
<div class="">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb40">
                <div class="row">
                    @foreach($alumnis as $user)
                    @include('includes.user_block')
                    @endforeach
                </div>
                
                <div class="text-right mb30">
                    {{ $alumnis->links() }}
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