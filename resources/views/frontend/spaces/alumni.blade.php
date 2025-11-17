<?php 
  use App\Models\Chapter;
  $chapters = Chapter::where('id', '!=', 86)->get();
  $portfolios = app('App\Http\Controllers\Controller')->getCommunityPortfolios();
  $sessions = range(date('Y'), date('1982'));

?>
@extends('frontend.spaces.layouts.app')
@section('title', 'Alumni')

@section('css')
<style>
    .list-image {
      border-radius: 50% !important;
      display: flex;
      margin: auto;
      width: 150px;
      height: 150px;
      border: solid 1px red;
    }
    .list-image2{
      width: 150px;
      height: 150px;
      margin: auto;
      display: flex;
      border-radius: 50% !important;
      padding: 20px;
      border: solid 1px red;
    }
    .typeahead.dropdown-menu {
      width: 100%;
      left: 0px !important;
    }

    .alert-success{
      margin:auto !important;
    }
</style>
@endsection
@section('content')
  <div class="section section-header section-image bg-tertiary overlay-primary text-white overflow-hidden pb-6"
    data-background="../assets/img/new-york-hero.jpg">
    <div class="container z-2">
      <div class="row justify-content-center pt-3">
        <div class="col-12 text-center">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-transparent justify-content-center mb-4">
              <li class="breadcrumb-item text-secondary"><a href="/">Home</a>
              </li>
              <li class="breadcrumb-item text-muted active" aria-current="page">Alumni</li>
            </ol>
          </nav>
          <h1 class="mb-5">Find a GSF Alumni <span class="font-weight-bolder"></span></h1>
          <div>
            <a style="margin-bottom:10px" href="" data-toggle="modal" data-target="#modal-form-single" class="btn btn-md btn-outline-white animate-up-2">
              <i class="fas fa-user mr-1"></i><span class="d-xl-inline">Submit my details</span>
            </a>
            <a href="{{ route('newdonation') }}" style="margin-bottom:10px" target="_blank" class="btn btn-md btn-outline-white animate-up-2">
             <i class="fas fa-donate mr-1"></i> 
            <span class="d-xl-inline">Make Donation</span>
            </a>

            {{-- <a href=""  data-toggle="modal" data-target="#modal-form-multiple" style="margin-bottom:10px" target="_blank" class="btn btn-md btn-outline-white animate-up-2">
                      <i class="fas fa-upload mr-1"></i> 
                      <span class="d-xl-inline">Upload Multiple Alumni</span>
            </a> --}}

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="section section-lg pt-6">
    <div id="spaces-container" class="container">
      <div class="row mb-5">
        @include('includes.alerts')
        <div class="col-12">
            <div class="card p-md-2">
                <div class="card-body p-2 p-md-0" style="margin-bottom:20px">
                    <form autocomplete="off" class="row" method="GET" action="{{ route('search.alumni') }}">
                        <div class="col-12 col-lg-5">
                            <div class="form-group form-group-lg mb-lg-0">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><span class="fas fa-user"></span></span></div><input id="name" name="name" type="text" class="form-control" placeholder="Type a name">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="form-group form-group-lg mb-lg-0">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><span class="fas fa-university"></span></span></div><input id="school" name="school" type="text" class="form-control" placeholder="Type school name or leave empty">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-2"><button class="btn btn-lg btn-primary btn-block animate-up-2" type="submit">Search</button></div>
                    </form>
                </div>
            </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-12 col-lg-12 order-lg-1">
          <div class="justify-content-between align-items-center d-none d-md-flex" style="margin-bottom:120px">
            <div class="mr-3">
              <h2 class="h5 mb-3 mb-md-0">Total: <span class="counter font-weiht-bolder text-primary">{{ $alumnis->count() }}</span>
              </h2>
            </div>
          </div>
          <div class="tab-content mt-4 mt-lg-4" id="tabcontentexample-5">
            <div class="tab-pane fade show active" id="link-example-14" role="tabpanel"
              aria-labelledby="tab-link-example-14">
              <div class="row">
                @foreach($alumnis as $user)
                  @include('frontend.spaces.includes.user_block')
                @endforeach
              </div>
            </div>
          </div>
          <div>
          </div>
        </div>
      </div>
      <div>
        {{ $alumnis->links()}}
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal-form-single" tabindex="-1" role="dialog" aria-labelledby="modal-form" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content"><div class="modal-body p-0">
            <div class="card shadow-md border-0">
                <div class="card-body position-relative">
                    <button type="button" class="close mb-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <form class="mt-3" action="{{ route('upload-alumni', 'single') }}" method="POST" enctype="multipart/form-data">
                      @csrf
                      <div class="row">
                          <div class="col-md-12 col-sm-12">
                            <h4 style="text-align: center;margin-bottom:40px">Can't find your details in the GSF alumni database? Submit this form and get added.</h4>
                          </div>
                      </div>
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
                                <label for="gender">Gender</label>
                                <select class="form-control" name="gender" id="gender" required>
                                      <option value="">--Select Gender--</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : ''}}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : ''}}>Female</option>
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
    </div>
  </div>

  <div class="modal fade" id="modal-form-multiple" tabindex="-1" role="dialog" aria-labelledby="modal-form" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content"><div class="modal-body p-0">
            <div class="card shadow-md border-0">
                <div class="card-body position-relative">
                    <button type="button" class="close mb-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <form class="mt-3" action="{{ route('upload-alumni', 'single') }}" method="POST" enctype="multipart/form-data">
                      @csrf
                      <div class="row">
                          <div class="col-md-12 col-sm-12">
                            <h4 style="text-align: center;margin-bottom:40px">Can't find your details in the GSF alumni database? Submit this form and get added.</h4>
                          </div>
                      </div>
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
                                  <label for="gender">Gender</label>
                                  <select class="form-control" name="gender" id="gender" required>
                                        <option value="">--Select Gender--</option>
                                      <option value="Male" {{ old('gender') == 'Male' ? 'selected' : ''}}>Male</option>
                                      <option value="Female" {{ old('gender') == 'Female' ? 'selected' : ''}}>Female</option>
                                  </select>
                              </fieldset>
                              <input type="hidden" name="status" value="1">
                              
                          
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
                            <fieldset class="form-group">
                                <label for="open_to_work">Open to work?</label>
                                <select class="form-control" name="open_to_work" id="open_to_work" required>
                                    <option value="1" {{ old('open_to_work') == 1 ? 'selected' : ''}}>Yes</option>
                                    <option value="0" {{ old('open_to_work') == 0 ? 'selected' : ''}}>No</option>
                                </select>
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
    </div>
  </div>

@endsection
@section('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
  <script type="text/javascript">
    var path = "{{ route('alumni.suggestions') }}";
    $('#name').typeahead({
        source: function (query, process) {
            return $.get(path, {
                query: query
            }, function (data) {
                return process(data);
            });
        }
    });

    var path2 = "{{ route('campus.suggestions') }}";
    $('#school').typeahead({
        source: function (query, process) {
            return $.get(path2, {
                query: query
            }, function (data) {
                return process(data);
            });
        }
    });
</script>
@endsection