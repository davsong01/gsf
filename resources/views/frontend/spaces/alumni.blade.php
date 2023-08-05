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
        </div>
      </div>
    </div>
  </div>
  <div class="section section-lg pt-6">
    <div id="spaces-container" class="container">
      <div class="row mb-5">
        <div class="col-12">
            <div class="card p-md-2">
                <div class="card-body p-2 p-md-0" style="margin-bottom:20px">
                    <form autocomplete="off" class="row" method="GET" action="{{ route('alumni.search') }}">
                        <div class="col-12 col-lg-5">
                            <div class="form-group form-group-lg mb-lg-0">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><span class="fas fa-user"></span></span></div><input id="name" name="name" type="text" class="form-control" placeholder="Type a name" required>
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