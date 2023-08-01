@extends('frontend.spaces.layouts.app')
@section('css')
<style>
    .list-image {
      border-radius: 50% !important;
      display: flex;
      margin: auto;
      width: 300px;
      height: 300px;
      border: solid;
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
        <div class="row">
          <div class="col-12 mb-4">
            <form autocomplete="off" class="row" action="#">
              <div class="col-12 col-lg-4">
                <div class="form-group mb-lg-0">
                  <div class="input-group input-group-md mb-lg-0">
                    <div class="input-group-prepend"><span class="input-group-text"><span
                          class="fas fa-search"></span></span></div><input id="search-location" type="text"
                      class="form-control autocomplete" placeholder="Search location" tabindex="1" required>
                  </div>
                </div>
              </div>
              <div class="col-12 col-lg-3">
                <div class="input-group input-group-md mb-3 mb-lg-0">
                  <div class="input-group-prepend"><span class="input-group-text"><span
                        class="far fa-calendar-alt"></span></span></div><input class="form-control datepicker"
                    placeholder="Select date" type="text" required>
                </div>
              </div>
              <div class="col-12 col-lg-2"><button class="btn btn-primary btn-block mb-3 mt-md-0 animate-up-2"
                  type="submit">Find a desk</button></div>
              <div class="col-12 col-lg-3"><a href="#modal-map" data-toggle="modal" data-target="#map-listings"
                  class="card bg-soft border-light p-3 mb-lg-3 flex-row align-items-center justify-content-center text-primary"><span
                    class="fas fa-map-marker-alt mr-2"></span> <span>Map View</span></a></div>
            </form>
          </div>
          <div class="col-12 d-lg-none"><span class="d-block font-small text-primary mt-2 text-right"
              id="show-filters-button">Show filters</span></div>
        </div>
        <div class="row">
          <aside id="filters-container" class="col-12 col-lg-3 mt-3 mt-lg-0 z-2 order-lg-2">
            <div id="filters-sidebar" class="d-none d-lg-block">
              <form action="#" method="get" class="sidebar-inner">
                <div class="card border-light p-3"><a href="#" data-target="#price"
                    class="accordion-panel-header w-100 d-flex align-items-center justify-content-between"
                    data-toggle="collapse" role="button" aria-expanded="false" aria-controls="price"><span
                      class="icon-title h6 mb-0 font-weight-bold">Price range</span> <span class="icon icon-xs"><span
                        class="fas fa-plus"></span></span></a>
                  <div id="price" class="collapse">
                    <div class="pt-5">
                      <div id="input-slider-range" data-range-value-min="100" data-range-value-max="500"></div>
                      <div class="row d-none">
                        <div class="col-6"><span class="range-slider-value value-low" data-range-value-low="200"
                            id="input-slider-range-value-low"></span></div>
                        <div class="col-6 text-right"><span class="range-slider-value value-high"
                            data-range-value-high="400" id="input-slider-range-value-high"></span></div>
                      </div><span class="font-xs text-gray">*Prices are in USD</span>
                    </div>
                  </div>
                </div>
                <div class="card border-light mt-4 p-3"><a href="#" data-target="#capacity"
                    class="accordion-panel-header w-100 d-flex align-items-center justify-content-between"
                    data-toggle="collapse" role="button" aria-expanded="false" aria-controls="capacity"><span
                      class="icon-title h6 mb-0 font-weight-bold">Capacity</span> <span class="icon icon-xs"><span
                        class="fas fa-plus"></span></span></a>
                  <div id="capacity" class="collapse pt-4">
                    <div class="form-group"><label for="people" class="pt-2">People</label> <select
                        class="custom-select custom-select-sm" id="people">
                        <option>1 Person</option>
                        <option>2-5 Persons</option>
                        <option>10-20 Persons</option>
                        <option>20-40 Persons</option>
                        <option>50+ Persons</option>
                      </select></div>
                    <div class="form-group"><label for="size">Size</label> <select
                        class="custom-select custom-select-sm" id="size">
                        <option>10 Sq. Ft - 20 Sq. Ft</option>
                        <option>20 Sq. Ft - 50 Sq. Ft</option>
                        <option>50 Sq. Ft - 100 Sq. Ft</option>
                        <option>100 Sq. Ft - 200 Sq. Ft</option>
                        <option>200+ Sq. Ft</option>
                      </select></div>
                  </div>
                </div>
                <div class="card border-light mt-3 p-3"><a href="#" data-target="#reviews"
                    class="accordion-panel-header w-100 d-flex align-items-center justify-content-between"
                    data-toggle="collapse" role="button" aria-expanded="false" aria-controls="reviews"><span
                      class="icon-title h6 mb-0 font-weight-bold">Rating</span> <span class="icon"><span
                        class="fas fa-plus"></span></span></a>
                  <ul id="reviews" class="collapse list-group list-group-flush pt-4 border-0">
                    <li
                      class="list-group-item border-0 py-1 pt-2 px-0 d-flex align-items-center justify-content-between">
                      <div class="form-check"><input class="form-check-input" type="checkbox" value id="defaultCheck1">
                        <label class="form-check-label" for="defaultCheck1"><span class="d-flex"><i
                              class="star fas fa-star text-warning"></i> <i class="star fas fa-star text-warning"></i>
                            <i class="star fas fa-star text-warning"></i> <i class="star fas fa-star text-warning"></i>
                            <i class="star fas fa-star text-warning"></i> <span
                              class="small font-weight-normal ml-2">(12)</span></span></label></div>
                    </li>
                    <li class="list-group-item border-0 py-1 px-0 d-flex align-items-center justify-content-between">
                      <div class="form-check"><input class="form-check-input" type="checkbox" value id="defaultCheck2">
                        <label class="form-check-label" for="defaultCheck2"><span class="d-flex"><i
                              class="star fas fa-star text-warning"></i> <i class="star fas fa-star text-warning"></i>
                            <i class="star fas fa-star text-warning"></i> <i class="star fas fa-star text-warning"></i>
                            <i class="star far fa-star text-dark"></i> <span
                              class="small font-weight-normal ml-2">(22)</span></span></label></div>
                    </li>
                    <li class="list-group-item border-0 py-1 px-0 d-flex align-items-center justify-content-between">
                      <div class="form-check"><input class="form-check-input" type="checkbox" value id="defaultCheck3">
                        <label class="form-check-label" for="defaultCheck3"><span class="d-flex"><i
                              class="star fas fa-star text-warning"></i> <i class="star fas fa-star text-warning"></i>
                            <i class="star fas fa-star text-warning"></i> <i class="star far fa-star text-dark"></i> <i
                              class="star far fa-star text-dark"></i> <span
                              class="small font-weight-normal ml-2">(32)</span></span></label></div>
                    </li>
                    <li class="list-group-item border-0 py-1 px-0 d-flex align-items-center justify-content-between">
                      <div class="form-check"><input class="form-check-input" type="checkbox" value id="defaultCheck4">
                        <label class="form-check-label" for="defaultCheck4"><span class="d-flex"><i
                              class="star fas fa-star text-warning"></i> <i class="star fas fa-star text-warning"></i>
                            <i class="star far fa-star text-dark"></i> <i class="star far fa-star text-dark"></i> <i
                              class="star far fa-star text-dark"></i> <span
                              class="small font-weight-normal ml-2">(9)</span></span></label></div>
                    </li>
                    <li class="list-group-item border-0 py-1 px-0 d-flex align-items-center justify-content-between">
                      <div class="form-check"><input class="form-check-input" type="checkbox" value id="defaultCheck5">
                        <label class="form-check-label" for="defaultCheck5"><span class="d-flex"><i
                              class="star fas fa-star text-warning"></i> <i class="star far fa-star text-dark"></i> <i
                              class="star far fa-star text-dark"></i> <i class="star far fa-star text-dark"></i> <i
                              class="star far fa-star text-dark"></i> <span
                              class="small font-weight-normal ml-2">(4)</span></span></label></div>
                    </li>
                  </ul>
                </div>
                <div class="card border-light mt-4 p-3"><a href="#" data-target="#amenities-1"
                    class="accordion-panel-header w-100 d-flex align-items-center justify-content-between"
                    data-toggle="collapse" role="button" aria-expanded="false" aria-controls="amenities-1"><span
                      class="icon-title h6 mb-0 font-weight-bold">Amenities</span> <span class="icon"><i
                        class="fas fa-plus"></i></span></a>
                  <ul id="amenities-1" class="collapse list-group list-group list-group-flush pt-4 border-0">
                    <li class="list-group-item border-0 py-1 px-0 d-flex align-items-center justify-content-between">
                      <div class="form-check"><input class="form-check-input" type="checkbox" value id="defaultCheck6">
                        <label class="form-check-label" for="defaultCheck6">Kitchen</label></div>
                    </li>
                    <li class="list-group-item border-0 py-1 px-0 d-flex align-items-center justify-content-between">
                      <div class="form-check"><input class="form-check-input" type="checkbox" value id="defaultCheck7">
                        <label class="form-check-label" for="defaultCheck7">Conference Room</label></div>
                    </li>
                    <li class="list-group-item border-0 py-1 px-0 d-flex align-items-center justify-content-between">
                      <div class="form-check"><input class="form-check-input" type="checkbox" value id="defaultCheck8">
                        <label class="form-check-label" for="defaultCheck8">Coffee & Drinks</label></div>
                    </li>
                    <li class="list-group-item border-0 py-1 px-0 d-flex align-items-center justify-content-between">
                      <div class="form-check"><input class="form-check-input" type="checkbox" value id="defaultCheck9">
                        <label class="form-check-label" for="defaultCheck9">Bike Parking</label></div>
                    </li>
                  </ul>
                </div><button class="btn btn-sm btn-block btn-primary animate-up-2 mt-4" type="submit">Apply
                  filters</button>
              </form>
            </div>
          </aside>
          <div class="col-12 col-lg-9 order-lg-1">
            <div class="justify-content-between align-items-center d-none d-md-flex">
              <div class="mr-3">
                <h2 class="h5 mb-3 mb-md-0">Total Alumni: <span class="font-weiht-bolder text-primary">{{$alumnis->count()}}</span>
                </h2>
              </div>
              <div class="nav-wrapper position-relative p-0">
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

                  <div class="col-12 col-sm-10 col-md-6 col-lg-12 mb-4">
                    <div class="card border-light mb-4 animate-up-5">
                      <div class="row no-gutters align-items-center">
                        <div class="col-12 col-lg-6 col-xl-5"><a href="single-space.html" ><img
                              src="https://demo.themesberg.com/spaces/assets/img/private-office.jpg"
                              alt="private office" class="card-img p-2 rounded-sm list-image"></a></div>
                        <div class="col-12 col-lg-6 col-xl-7">
                          <div class="card-body"><a href="single-space.html">
                              <h4 class="h5">Collaborative Workspace</h4>
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

                </div>
              </div>
              <div class="tab-pane fade show active" id="link-example-14" role="tabpanel"
                aria-labelledby="tab-link-example-14">
                <div class="row">
                  <div class="col-12 col-md-6">
                    <div class="card border-light mb-4 animate-up-5"><a href="single-space.html"
                        class="position-relative"><img
                          src="https://demo.themesberg.com/spaces/assets/img/image-office.jpg"
                          class="card-img-top rounded-xl p-2 " alt="themesberg office"></a>
                      <div class="card-body"><a href="single-space.html">
                          <h4 class="h5">Coworking Workspace</h4>
                        </a>
                        <div class="d-flex my-4"><span class="star fas fa-star text-warning"></span> <span
                            class="star fas fa-star text-warning"></span> <span
                            class="star fas fa-star text-warning"></span> <span
                            class="star fas fa-star text-warning"></span> <span
                            class="star fas fa-star text-warning"></span> <span
                            class="badge badge-pill badge-primary ml-2">5.0</span></div>
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

                  <div class="col-12 col-md-6">
                    <div class="card border-light mb-4 animate-up-5"><a href="single-space.html"
                        class="position-relative"><img
                          src="https://demo.themesberg.com/spaces/assets/img/cowork-office.jpg"
                          class="card-img-top rounded-xl p-2" alt="developer desk"></a>
                      <div class="card-body"><a href="single-space.html">
                          <h4 class="h5">Coworking Workspace</h4>
                        </a>
                        <div class="d-flex my-4"><span class="star fas fa-star text-warning"></span> <span
                            class="star fas fa-star text-warning"></span> <span
                            class="star fas fa-star text-warning"></span> <i class="star fas fa-star text-light"></i> <i
                            class="star fas fa-star text-light"></i> <span
                            class="badge badge-pill badge-primary ml-2">3.0</span></div>
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

                  
                </div>
              </div>
              <div class="col mt-3 d-flex justify-content-center">
                <nav aria-label="Page navigation example">
                  <ul class="pagination">
                    <li class="page-item disabled"><a class="page-link" tabindex="-1" href="#">Previous</a></li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item active"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item"><a class="page-link" href="#">5</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection