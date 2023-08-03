@extends('frontend.spaces.layouts.app')
@section('title', 'Upload details')
@section('ogtitle', 'Upload details')
@section('ogdescription')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
  <section class="section section-header bg-primary overlay-primary text-white pb-2" style="margin-top:-50px" !important>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-8 text-center">
            <h1 class="display-2 mb-4">Upload </h1>
          </div>
        </div>
      </div>
    </section>
    <section class="min-vh-80 d-flex align-items-center" style="margin-top:20px">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="text-center text-md-center mb-5 mt-md-0 text-white">
              <h1 class="mb-0 h3" style="color:black">Welcome, upload new listing to GSF database</h1>
            </div>
          </div>
          <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
            <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-900">
                @include('includes.alerts')
                <form action="{{ route('newalumni.save') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-4">
                        <label for="name">Name</label>
                        <input class="form-control" required id="name" name="name" value="{{old('name')}}" placeholder="Enter name" type="text" aria-label="Name">
                      </div>
                      <div class="mb-4">
                        <label for="phone">Phone</label>
                        <input class="form-control" required id="phone" name="phone" value="{{old('phone')}}" placeholder="Enter phone" type="text" aria-label="phone">
                      </div>

                      <div class="mb-4">
                          <label for="campus">Campus</label>
                          <select name="campus" id="campus" class="form-control select2" required aria-label="Campus" >
                            @foreach(\App\Models\Chapter::where('id','<>',86)->get() as $chapter)
                              <option value="{{$chapter->id}}" {{ old('campus') == $chapter->id ? 'selected' : '' }}>{{ $chapter->name }}</option>
                            @endforeach
                          </select>
                      </div>
                      

                      <div class="mb-4">
                          <label for="portfolio">Portfolio</label>
                          <select name="portfolio" id="portfolio" class="form-control" required aria-label="Portfolio" >
                            <option value="" selected>Select...</option>
                            <option value="Exco" {{ old('portfolio') == 'Exco' ? 'selected' : '' }}>Exco</option>
                            <option value="Member" {{ old('portfolio') == 'Member' ? 'selected' : '' }}>Member</option>
                          </select>
                      </div>
                      <div class="mb-4" id="office-div" style="display:none"> 
                          <label for="office">Office</label> <br>
                          <select name="office" id="office" class="form-control select2">
                            <option value="">Select Office--</option>
                            @foreach($portfolios as $portfolio)
                                @if(!in_array($portfolio, ['Admin','Worker','Member'])) 
                                    <option value="{{ $portfolio }}" {{ old('portfolio') == $portfolio ? 'selected' : '' }}>{{ $portfolio }}</option>
                                @endif
                            @endforeach 
                          </select>
                      </div>
                      
                    </div>

                    <div class="col-md-6">
                      <div class="mb-4">
                        <label for="email">Email</label>
                        <input class="form-control" required id="email" name="email" value="{{old('email')}}" placeholder="Enter email" type="text" aria-label="Email">
                      </div>

                      <div class="mb-4">
                          <label for="gender">Gender</label>
                          <select name="gender" id="gender" class="form-control" required aria-label="Gender" >
                            <option value="">Select...</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                          </select>
                      </div>
                      <div class="mb-4">
                        <label for="matriculation_year">Year of Matriculation</label>
                        <select class="form-control select2" name="matriculation_year" id="matriculation_year">
                            @foreach($sessions as $session=>$value)
                            <option value="{{ $value . '/' . ($value + 1) }}" {{ old('matriculation_year') == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                        @endforeach                                
                        </select>
                      </div>
                      <div class="mb-4">
                        <label for="graduation_year">Year of graduation</label>
                        <select class="form-control select2" name="graduation_year" id="graduation_year">
                            @foreach($sessions as $session=>$value)
                            <option value="{{ $value . '/' . ($value + 1) }}" {{ old('graduation_year') == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                        @endforeach                                
                        </select>
                      </div>

                      
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-block btn-primary">Submit details</button>
                    </div>
                  </div>
                  
                </form>
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
@section('js')
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.select2').select2({
         width: 'resolve',
         theme: "bootstrap"
      });
  });
  </script>
    <script>
      $('#portfolio').change(function (e) {
        if(this.value == 'Exco'){
          $('#office-div').show();
          $('#office').attr('required', true);
        }else{
          $('#office-div').hide();
          $('#office').attr('required', false);
        }
      });
    </script>
@endsection
