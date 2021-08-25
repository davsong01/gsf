@extends('layouts.app')
@section('title', 'Stakeholder Login')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- login page start -->
            <section id="auth-login" class="row flexbox-container">
                <div class="col-xl-8 col-11">
                    <div class="card bg-authentication mb-0">
                        <div class="row m-0">
                            <!-- left section-login -->
                            <div class="col-md-12 col-12 px-0">
                                <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                                    {{-- <div class="card-header pb-1">
                                        
                                    </div> --}}
                                    <div class="card-content">
                                        <div class="card-body">
                                           <div class="card-title">
                                                <div style="text-align: center;"><img style="width: 10%;" src="{{ asset('frontend/img/logo.png') }}"></div>
                                            </div>
                                            <div class="divider">
                                                <h5 style="color:red">ADMINISTRATOR ACCESS ONLY</h5>
                                                <h6 style="color:blue">REPORTS</h6>
                                                <div class="divider-text text-uppercase text-muted"><small>LOGIN AS A STAKEHOLDER</small>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="card-title">
                                                @include('includes.alerts')
                                            
                                                <form method="POST" action="{{ route('stakeholder.login') }}">
                                                @csrf
                                                <div class="form-group mb-50">
                                                    <label class="text-bold-600" for="email">Email</label>
                                                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email')  }}" required></div>
                                                <div class="form-group">
                                                    <label class="text-bold-600" for="password">Password</label>
                                                    <input type="password" class="form-control" id="password" value="{{ old('password')  }}" name="password" required>
                                                </div>
                                                <div class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                                                    <div class="text-right"><a herf="#" class="card-link"><small>Forgot Password? Contact the NPS to have your password reset</small></a></div>
                                                </div>
                                                <button type="submit" class="btn btn-primary glow w-100 position-relative">Login<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                                            </form>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- right section image -->
                           
                        </div>
                    </div>
                </div>
            </section>
            <!-- login page ends -->

        </div>
    </div>
</div>
@endsection