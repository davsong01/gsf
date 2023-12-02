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
      .btn.btn-md.btn-outline-white {
        background: #0d1b48;
      }
    </style>
@endsection
@section('content')
  <section class="section section-header bg-primary overlay-primary text-white pb-2" style="margin-top:-50px" !important>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-8 text-center">
            <h1 class="display-2 mb-4">Upload data</h1>
          </div>
        </div>
      </div>
    </section>
    <section class="min-vh-80 d-flex align-items-center" style="margin-top:20px">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="text-center text-md-center mb-5 mt-md-0 text-white">
              <h1 class="mb-0 h3" style="color:black">Can't find Alumni of your Institution of higher learning.</h1>
              <a href="{{ route('newalumni') }}" style="margin-bottom:10px;background:bblad" class="btn btn-md btn-outline-white animate-up-2 donate-button">
                <i class="fas fa-donate mr-1"></i> 
                <span class="d-xl-inline">Upload Single</span>
              </a>
            </div>
          </div>
          <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
            
            <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-900">
                @include('includes.alerts')
                
                 <form class="mt-3" action="{{ route('upload-alumni', 'multiple') }}" method="POST" enctype="multipart/form-data">
                      @csrf
                      
                      <div class="row">
                        <div class="card-header">
                          <p>
                            Select an excel file to upload, please pay attention to the following: </p>
                          <ul>
                            <li>
                              Only Excel format is acceptable
                            </li>
                            <li>
                              <strong>Name, Email, Phone, Gender, Year of Matriculation, ear of graduation, Office, Program, Course of Study</strong> must be present
                            </li>
                            <li>There must be no spaces after the last line in the excel file to be imported</li>
                          </ul>

								        </div>
                        <div class="col-md-12 col-sm-12">  
                                                     
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
                              <label for="file">Upload File  <a href="{{ route('sample-listing') }}" style="margin-bottom:10px;background:bblad" target="_blank" class="btn btn-md btn-outline-white animate-up-2 donate-button">
                                <i class="fas fa-download mr-1"></i> 
                                <span class="d-xl-inline">Download sample File</span>
                              </a></label>
                              <input type="file" id="file" name="file" class="form-control" value="{{ old('file') }}" required>
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
