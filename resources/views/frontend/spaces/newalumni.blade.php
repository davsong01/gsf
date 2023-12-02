@extends('frontend.spaces.layouts.app')
@section('title', 'Upload details')
@section('ogtitle', 'Upload details')
@section('ogdescription')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
      .donate-button:hover{
        color:white !important
      }
    </style>
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
              <h1 class="mb-0 h3" style="color:black">Can't find your details in the GSF alumni database? Submit this form and get added.</h1>
              <a href="{{ route('upload.multiple') }}" style="margin-bottom:10px;background:#125;color:white" class="btn btn-md btn-outline-white animate-up-2 donate-button">
                <i class="fas fa-upload mr-1"></i> 
                <span class="d-xl-inline">Upload Multiple</span>
              </a>
            </div>
          </div>
          <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
            
            <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-900">
                @include('includes.alerts')
                
                 <form class="mt-3" action="{{ route('upload-alumni', 'single') }}" method="POST" enctype="multipart/form-data">
                      @csrf
                      
                      <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <fieldset class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter name">
                            </fieldset>

                            <fieldset class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" placeholder="Enter email" class="form-control" value="{{ old('email') }}" required>
                            </fieldset>

                            <fieldset class="form-group">
                                <label for="phone">Phone</label>
                                <input type="phone" id="phone" placeholder="Enter phone number" name="phone" class="form-control" value="{{ old('phone') }}" required>
                            </fieldset>

                            <fieldset class="form-group">
                                <label for="sex">Gender</label>
                                <select class="form-control" name="sex" id="sex" required>
                                      <option value="">--Select Gender--</option>
                                    <option value="Male" {{ old('sex') == 'Male' ? 'selected' : ''}}>Male</option>
                                    <option value="Female" {{ old('sex') == 'Female' ? 'selected' : ''}}>Female</option>
                                </select>
                            </fieldset>
                            <input type="hidden" name="status" value="1">
                            
                          <fieldset class="form-group">
                              <label for="open_to_work">Open to work?</label>
                              <select class="form-control" name="open_to_work" id="open_to_work" required>
                                  <option value="1" {{ old('open_to_work') == 1 ? 'selected' : ''}}>Yes</option>
                                  <option value="0" {{ old('open_to_work') == 0 ? 'selected' : ''}}>No</option>
                              </select>
                          </fieldset>
                          <fieldset class="form-group @error('image')is-invalid @enderror">
                              <label for="image">Upload Passport</label>
                              <input type= "file"  accept="image/*" class="form-control" name="image" id="image">	
                          </fieldset>           
                            
                        </div>

                        <div class="col-md-6 col-sm-12">                               
                          <fieldset class="form-group">
                            <label for="chapter">Campus</label>
                            <select class="form-control" name="chapter" id="chapter" required>
                                {{-- //include chapter --}}
                                <option value="">--Select Campus--</option>
                                @foreach($chapters as $chapter)
                                <option value="{{ $chapter->id }}" {{ old('old') == $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>
                                @endforeach
                            </select>
                          </fieldset>
                          <fieldset class="form-group">
                            <label for="matriculation_year">Year of Matriculation</label>
                            <select class="form-control" name="matriculation_year" id="matriculation_year">
                                @foreach($sessions as $session=>$value)
                                <option value="{{ $value . '/' . ($value + 1) }}" {{ old('matriculation_year') == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                            @endforeach                                
                            </select>
                          </fieldset>
                      
                          <fieldset class="form-group">
                            <label for="graduation_year">Year of graduation</label>
                            <select class="form-control" name="graduation_year" id="graduation_year">
                                @foreach($sessions as $session=>$value)
                                <option value="{{ $value . '/' . ($value + 1) }}" {{ old('graduation_year') == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                            @endforeach                                
                            </select>
                          </fieldset>
                          <fieldset class="form-group">
                              <label for="portfolio">Portfolio</label>
                              <select class="form-control" name="role" id="role" required>
                                  <option value="">--Select Portfolio--</option>
                                  @foreach($portfolios as $portfolio=>$value)
                                      @if($portfolio <> 1)  
                                      <option value="{{ $portfolio }}" {{ old('portfolio') == $portfolio ? 'selected' : '' }}>{{ $value }}</option>
                                      @endif
                                  @endforeach                               
                              </select>
                          </fieldset>
                          <fieldset class="form-group">
                              <label for="program">Program</label>
                              <select class="form-control" name="program" required>
                                  <option value="">Select...</option>
                                  <option value="PHD"  {{ old('program')== 'PHD' ? 'selected' : '' }}>PHD</option>
                                  <option value="PGD"  {{ old('program')== 'PGD' ? 'selected' : '' }}>PGD</option>
                                  <option value="MSC"  {{ old('program')== 'MSC' ? 'selected' : '' }}>MSC</option>
                                  <option value="BSC"  {{ old('program')== 'BSC' ? 'selected' : '' }}>BSC</option>
                                  <option value="HND"  {{ old('program')== 'HND' ? 'selected' : '' }}>HND</option>
                                  <option value="OND"  {{ old('program')== 'OND' ? 'selected' : '' }}>OND</option>
                                  <option value="NCE"  {{ old('program')== 'NCE' ? 'selected' : '' }}>NCE</option>
                              </select>
                          </fieldset>
                          <fieldset class="form-group">
                              <label for="course">Course of study</label>
                              <input type="text" id="course" name="course" class="form-control" value="{{ old('course') }}" required>
                          </fieldset>
                    
                        </div>
                      </div>
                      <div class="row">
                          <div class="col-md-12 col-sm-12">
                              <button class="btn btn-primary" style="width:100%" type="submit">Submit</button>
                              </form>
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
