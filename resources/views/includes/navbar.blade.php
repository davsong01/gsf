<!-- Nav Bar Start -->

    <div class="navbar navbar-expand-lg bg-dark navbar-dark" >
     
        <div class="container-fluid">
            <a href="/" class="navbar-brand"> <img src="{{ asset('frontend/img/logo.png') }}" alt="logo"></a><b>GSF NATIONAL CONFERENCE {{ $conference_year}}</b>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ml-auto">
                    <a href="https://www.gsfnational.org/" class="nav-item nav-link active">GSF HOME</a>
                    @if($setting->close_registration >= date('Y-m-d'))
                        <a href="#register" class="nav-item nav-link">REGISTER</a>
                    @endif
                    
                    <a href="/login" class="nav-item nav-link">LOGIN</a>
                    
                </div>
            </div>
        </div>
    </div>
 
<!-- Nav Bar End -->