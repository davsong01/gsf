@extends('frontend.spaces.layouts.app')
@section('title', 'Chapters')
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
  <div class="section section-header section-image bg-tertiary overlay-primary text-white overflow-hidden pb-9"
      data-background="../assets/img/new-york-hero.jpg">
      <div class="container z-2">
        <div class="row justify-content-center pt-3">
          <div class="col-12 text-center">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb breadcrumb-transparent justify-content-center mb-4">
                <li class="breadcrumb-item text-secondary"><a href="/">Home</a>
                </li>
                <li class="breadcrumb-item text-muted active" aria-current="page">Our Campuses</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
  </div>
  <div class="section section-md pt-0">
    <div class="container mt-n7">
      <div class="row">
        <div class="col-12">
          <div class="card border-light p-md-2">
            <div class="card-body p-4">
              <form autocomplete="off" method="get" action="#">
                <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col">
                        <div class="form-group form-group-lg mb-lg-0">
                          <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><span
                                  class="fas fa-map-marker-alt"></span></span></div><input name="product_name" id="product_name"
                              type="text" class="form-control autocomplete" placeholder="Start typing to search for a GSF chapter"
                              tabindex="1" required>
                          </div>
                          <div id="productList">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  {{-- <div class="col-md-2 mb-3 mb-lg-0">
                    <button class="btn btn-primary btn-block animate-up-2"
                      type="submit">Search</button>
                  </div> --}}
                </div>
                {{ csrf_field() }}
                
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="section section-lg pt-0">
    <div class="container">
      <div class="row">
        <div class="col-12 mb-5">
          <div class="tab-content mt-4 mt-lg-4" id="tabcontentexample-5">
            <div class="tab-pane fade show active" id="link-example-14" role="tabpanel"
              aria-labelledby="tab-link-example-14">
              <div class="row">
                @foreach($chapters as $chapter)
                @include('frontend.spaces.includes.campus_block')
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('js')
  <script>
    $(document).ready(function () {
        $('#product_name').keyup(function () {
            var data = $(this).val();
            if (data != '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('campus.autocomplete') }}",
                    method: "POST",
                    data: {data: data, _token: _token},
                    success: function (data) {
                        $('#productList').fadeIn();
                        $('#productList').html(data);
                    }
                });
            }else{
              $('#productList').fadeOut();
            }
        });
        $(document).on('click', 'li', function () {
            $('#product_name').val($(this).text());
            $('#productList').fadeOut();
        });
    });
</script>
@endsection