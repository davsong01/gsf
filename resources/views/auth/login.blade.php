@extends('frontend.main.mainlayout')
@section('title', 'Login')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Sign In</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
<div class="container mb70">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="border-card">
                <h3 class="font300 mb0 text-center">Login to your account</h3> <hr>
                @include('includes.alerts')
                                            
                <form method="POST" action="{{ route('login') }}">
                @csrf
                
                    <div class='form-group-icon mb15'>
                        <i class='fa fa-users'></i>
                        <input type="family_id" class='form-control' name="family_id" value="{{ old('family_id') }}" placeholder="Family ID">
                    </div>
                    
                      
                    <div class='form-group-icon mb15'>
                        <i class='fa fa-lock'></i>
                        <input type="password" class='form-control' id="password" value="{{ old('password')  }}" name="password" placeholder="Password" required>
                        
                    </div>
                    
                    <div class="form-group-icon mb15">
                        <div id="check">
                            <small onclick="newFunction()" style="cursor: pointer;">
                                <input type="checkbox"> Show password
                            </small>
                        </div>
                    </div>

                    <div class="checkbox">
                        <input type="checkbox" name="remember_me" id="rm">
                        <label for="rm">
                             Remember Me
                        </label>
                    </div>
                    <input type="submit" value="Login" class='btn btn-default btn-lg btn-block'>
                </form>
                <div class="text-right"><a href="password/reset" class="card-link"><small>Forgot Password?</small></a></div>
            </div>
        </div>
    </div>
</div>
<script>
    function newFunction() {
        var x = document.getElementById("password");
        var c = document.getElementById("check");
        
        if (x.type === "password") {
            x.type = "text";
            c.checked="checked";
        } else {
            x.type = "password";
            c.checked="";
        }
        
    } 
</script>
@endsection