@extends('frontend.main.mainlayout')
@section('title', 'Upload details')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>GSF National Alumni Database</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
<div class="container mb70">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="border-card">
                <h3 class="font300 mb0 text-center">Welcome, upload your details to GSF Alumni database</h3> <hr>
                @include('includes.alerts')
                                            
                <form method="POST" action="{{ route('newalumni.save') }}" enctype="multipart/form-data">
                @csrf
                
                    <div class='form-group-icon mb15'>
                        <label for="name">Your name</label>
                        <input type="text" class='form-control pl-0' name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class='form-group-icon mb15'>
                        <label for="email">Your email</label>
                        <input type="email" class='form-control pl-0' name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class='form-group-icon mb15'>
                        <label for="phone">Your phone</label>
                        <input type="text" class='form-control pl-0' name="phone" value="{{ old('phone') }}" required>
                    </div>
                    <div class='form-group-icon mb15'>
                        <label for="gender">Select Gender</label><br>
                        <select name="gender" id="gender" class="form-control pl-0" required>
                            <option value="">Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class='form-group-icon mb15'>
                        <label for="chapter_id">Campus</label><br>
                        <input type="text" id="chapter" name="chapter" class="form-control pl-0"
                        placeholder="Type to search for a GSF chapter"/>
                        <input type="hidden" id="chapter_id" name="chapter_id" class="form-control input-lg" required/>
                        <div id="productList">
                        </div>
                    </div>
                    <div class='form-group-icon mb15'>
                        <label for="status">Portfolio</label><br>
                        <select name="status" id="status" class="form-control pl-0" required>
                            <option value="">Select...</option>
                            <option value="Exco">Exco</option>
                            <option value="Member">Member</option>
                        </select>
                    </div>  
                    <div class='form-group-icon mb15'>
                        <label for="portfolio">Office</label>
                       
                        <select class="form-control pl-0" name="role" id="role" required>
                            <option value="">Select Portfolio--</option>
                            @foreach($portfolios as $portfolio=>$value)
                                @if($value <> 1 && $value <> 2)  
                                    <option value="{{ $portfolio }}" {{ old('portfolio') == $portfolio ? 'selected' : '' }}>{{ $value }}</option>
                                @endif
                            @endforeach                               
                        </select>
                    </div>
                    <div class="form-group mb15">
                        <label for="matric_year">Year of Matriculation</label>
                        <select class="form-control js-example-basic-single" name="matric_year" id="matric_year">
                            @foreach($sessions as $session=>$value)
                            <option value="{{ $value . '/' . ($value + 1) }}" {{ old('matric_year') == $value . '/' . ($value + 1) ? 'selected' : '' }} required>{{ $value . '/' . ($value + 1) }}</option>
                        @endforeach                                
                        </select>
                    </div>
                    <div class="form-group mb15">
                        <label for="graduation_year">Year of graduation</label>
                        <select class="form-control year" name="graduation_year" id="graduation_year">
                            <option value="">Select...</option>
                            @foreach($sessions as $session=>$value)
                            <option value="{{ $value . '/' . ($value + 1) }}" {{ old('graduation_year') == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                        @endforeach                                
                        </select>
                    </div>
                </form>
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