@extends('frontend.spaces.layouts.app')
@section('title', 'Stakeholder Login')
@section('ogtitle', 'Stakeholder Login')
@section('ogdescription')
@section('content')
  <section class="section section-header bg-primary overlay-primary text-white pb-2" style="margin-top:-50px" !important>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-8 text-center">
            <h1 class="display-2 mb-4">Login</h1>
          </div>
        </div>
      </div>
    </section>
    <section class="min-vh-80 d-flex align-items-center" style="margin-top:20px">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="text-center text-md-center mb-5 mt-md-0 text-white">
                <h5 style="color:red">ADMINISTRATOR ACCESS ONLY</h5>
                <p style="color:blue">
                    LOGIN AS AN ADMINISTRATOR
                </p>
                {{-- <h6 style="color:blue">REPORTS</h6> --}}
                <div class="divider-text text-uppercase text-muted"><small></small>
                </div>
            </div>
          </div>
          <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
            <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                @include('includes.alerts')

                <form action="{{ route('stakeholder.login') }}" method="POST">
                  @csrf
                    <div class="form-group"><label for="email">Email</label>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend"><span class="input-group-text"><span
                            class="fas fa-envelope"></span></span></div>
                            <input class="form-control" required id="email" name="email" value="{{old('email')}}"
                        placeholder="Enter email" type="text" aria-label="Email">
                    </div>
                    </div>
                    <div class="form-group">
                    <div class="form-group"><label for="password">Password</label>
                        <div class="input-group mb-4">
                        <div class="input-group-prepend"><span class="input-group-text"><span
                                class="fas fa-unlock-alt"></span></span></div><input class="form-control" id="password"
                            placeholder="Password" type="password" name="password" aria-label="Password" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div><a href="#" class="small text-right">Forgot Password? Contact the NPS to have your password reset</a></div>
                    </div>
                    </div>
                    <button type="submit" class="btn btn-block btn-primary">Sign in</button>
                </form>
               
            </div>
          </div>
        </div>
      </div>
    </section>
    
@endsection