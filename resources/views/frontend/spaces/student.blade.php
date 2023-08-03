@extends('frontend.spaces.layouts.app')
@section('title', 'Members')

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
              <li class="breadcrumb-item text-muted active" aria-current="page">Members</li>
            </ol>
          </nav>
          <h1 class="mb-5">Find a GSF Member <span class="font-weight-bolder"></span></h1>
        </div>
      </div>
    </div>
  </div>
  <div class="section section-lg pt-6">
    <div id="spaces-container" class="container">
      <div class="row mb-5">
        <div class="col-12">
            <div class="card p-md-2">
                <div class="card-body p-2 p-md-0">
                    <form autocomplete="off" class="row" method="GET" action="{{ route('member.search') }}">
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
          <div class="justify-content-between align-items-center d-none d-md-flex">
            <div class="mr-3">
              <h2 class="h5 mb-3 mb-md-0">Total: <span class="counter font-weiht-bolder text-primary">{{$alumnis->count()}}</span>
              </h2>
            </div>
            <div class="nav-wrapper position-relative p-0" style="padding-top: 20px; !important">
              <ul class="nav nav-pills small-pills" id="tab-34" role="tablist">
                <li class="nav-item pr-0"><a class="nav-link text-sm-center border-0 active" id="tab-link-example-14"
                    data-toggle="tab" href="#link-example-14" role="tab" aria-controls="link-example-14"
                    aria-selected="false"><span class="nav-link-icon d-block"><span
                        class="fas fa-th-large"></span></span></a></li>
                <li class="nav-item pr-0"><a class="nav-link text-sm-center border-0" id="tab-link-example-13"
                    data-toggle="tab" href="#link-example-13" role="tab" aria-controls="link-example-13"
                    aria-selected="true"><span class="nav-link-icon d-block"><span
                        class="fas fa-th-list"></span></span></a></li>
              </ul>
            </div>
          </div>
          <div class="tab-content mt-4 mt-lg-4" id="tabcontentexample-5">
            <div class="tab-pane fade" id="link-example-13" role="tabpanel" aria-labelledby="tab-link-example-13">
              <div class="row justify-content-center">
                @foreach($alumnis as $alumni)
                <div class="col-12 col-sm-10 col-md-4 col-lg-12 mb-4">
                  <div class="card border-light mb-4 animate-up-5">
                    <div class="row no-gutters align-items-center">
                      <div class="col-12 col-lg-6 col-xl-5"><a href="{{ route('user.single', $alumni->slug) }}" style="padding:20px"><img
                            src="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}" alt="{{ $alumni->passport }}"
                            alt="private office" class="card-img p-2 rounded-sm list-image"></a></div>
                      <div class="col-12 col-lg-6 col-xl-7">
                        <div class="card-body"><a href="{{ route('user.single', $alumni->slug) }}">
                            <h4 class="h5">{{ $alumni->name }}</h4>
                          </a>
                          <div class="d-flex my-3"><span class="star fas fa-star text-warning"></span> <span
                              class="star fas fa-star text-warning"></span> <span
                              class="star fas fa-star text-warning"></span> <span
                              class="star fas fa-star text-warning"></span> <span
                              class="star fas fa-star text-warning"></span> <span
                              class="badge badge-pill badge-primary ml-2">5.0</span></div>
                          <ul class="list-group mb-3">
                            <li class="list-group-item small p-0"><span class="fas fa-map-marker-alt mr-2"></span>New
                              York, Manhattan, USA</li>
                            <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Old Street
                              (2 mins walk)</li>
                          </ul>
                          <div class="d-flex justify-content-between">
                            <div class="col pl-0"><span class="text-muted font-small d-block">Monthly</span> <span
                                class="h6 text-dark font-weight-bold">500$</span></div>
                            <div class="col"><span class="text-muted font-small d-block">People</span> <span
                                class="h6 text-dark font-weight-bold">12</span></div>
                            <div class="col pr-0"><span class="text-muted font-small d-block">Sq.Ft</span> <span
                                class="h6 text-dark font-weight-bold">1200</span></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            <div class="tab-pane fade show active" id="link-example-14" role="tabpanel"
              aria-labelledby="tab-link-example-14">
              <div class="row">
                @foreach($alumnis as $alumni)
                <div class="col-12 col-md-4">
                  <div class="card border-light mb-4 animate-up-5"><a href="{{ route('user.single', $alumni->slug) }}"
                      class="position-relative" style="padding-top: 20px; !important"><img
                        src="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}" alt="{{ $alumni->passport }}"
                        class="card-img-top rounded-xl p-2 list-image2">
                      </a>
                    <div class="card-body" style="text-align: center">
                      <a href="{{ route('user.single', $alumni->slug) }}" >
                        <h4 class="h5">{{ $alumni->name }}</h4>
                      </a>
                      <ul class="list-group mb-3">
                        <li class="list-group-item small p-0"><span
                            class="fas fa-map-marker-alt mr-2"></span>California, USA</li>
                        <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Penny Market
                          Street (15 mins walk)</li>
                        <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Museum Street
                          (20 mins walk)</li>
                      </ul>
                    </div>
                    <div class="card-footer bg-soft border-top">
                      <div class="d-flex justify-content-between">
                        <div class="col pl-0"><span class="text-muted font-small d-block mb-2">Monthly</span> <span
                            class="h5 text-dark font-weight-bold">300$</span></div>
                        <div class="col"><span class="text-muted font-small d-block mb-2">People</span> <span
                            class="h5 text-dark font-weight-bold">24</span></div>
                        <div class="col pr-0"><span class="text-muted font-small d-block mb-2">Sq.Ft</span> <span
                            class="h5 text-dark font-weight-bold">2000</span></div>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            <div class="col mt-3 d-flex justify-content-center">
              {{$alumnis->links()}}
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