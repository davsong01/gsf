@extends('frontend.spaces.layouts.app')
@section('title', 'Login')
@section('ogtitle', 'Login')
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
              <h1 class="mb-0 h3" style="color:black">Login to your account</h1>
            </div>
          </div>
          <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
            <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                @include('includes.falerts')

                <form action="{{ route('login') }}" method="POST">
                  @csrf
                    <div class="form-group"><label for="email">Family ID</label>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend"><span class="input-group-text"><span
                            class="fas fa-users"></span></span></div>
                            <input class="form-control" required id="family_id" name="family_id" value="{{old('family_id')}}"
                        placeholder="GSF-******" type="text" aria-label="Family Id">
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
                            <div class="form-check"><input class="form-check-input" type="checkbox" value id="rm" name="remember_me"> <label
                                class="form-check-label" for="remember">Remember me</label></div>
                            <div><a href="password/reset" class="small text-right">Forgot password?</a></div>
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
