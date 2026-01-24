@extends('frontend.conference.template1.index')
@section('css')
<style>
    .form-control {
        padding-left: 10px !important;
    }

    .select2-selection__rendered {
        line-height: 31px !important;
    }

    .select2-container .select2-selection--single {
        height: 35px !important;
    }

    .select2-selection__arrow {
        height: 34px !important;
    }
    .close{
        background: red !important;
        color: white !important;
        border-radius: unset !important;
        box-shadow: unset !important;
        padding: 6px !important;
        border: unset !important;
        height: 38px !important;
        width: 38px !important;
    }
</style>
@endsection
@section('sec-content')
<section class="bg-100 py-7" id="register">
    <div class="container-lg small-container">

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5 text-center mb-3">
                <h2>Login</h2>
            </div>
        </div>
        @include('includes.alerts')
        <div class="row h-100 justify-content-center">
            <div class="contact-form">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="control-group">
                        <label>Login ID</label>
                        <input type="text" class="form-control" id="family_id" name="family_id" placeholder="Enter your login ID" required="required" value="{{ old('family_id') }}">
                    </div>
                    <div class="control-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required="required">

                    </div>

                    <div class="form-group-icon mb15 mt-3">
                        <small onclick="newFunction()" style="cursor: pointer;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="check">
                                <label class="form-check-label" for="check">
                                    Show password
                                </label>
                            </div>
                        </small>
                        <small>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="rm">
                                <label class="form-check-label" for="rm" name="remember_me">
                                    Remember Me
                                </label>
                            </div>
                            {{-- <input type="checkbox" name="remember_me" id="rm">  --}}
                        </small>
                    </div>
                    <input style="margin-top:20px" type="submit" value="Login" h
                        class="btn btn-danger hover-top btn-glow rounded-pill border-0">
                </form>
                <div class="text-right"><a href="password/reset" class="card-link"><small>Forgot Password?</small></a>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>

@endsection
@section('script')
<script>
    function newFunction() {
        var x = document.getElementById("password");
        var c = document.getElementById("check");

        if (x.type === "password") {
            x.type = "text";
            c.checked = "checked";
        } else {
            x.type = "password";
            c.checked = "";
        }

    }
</script>
@endsection
