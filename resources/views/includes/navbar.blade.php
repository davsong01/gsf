<!-- Nav Bar Start -->
    <div class="navbar navbar-expand-lg bg-dark navbar-dark" >
        <div class="container-fluid">
            <a href="/" class="navbar-brand"> 
                 <x-logo width="auto" height="70px"/>
            </a><b>GSF NATIONAL CONFERENCE {{ $conference_year}}</b>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ml-auto">
                    <a href="https://www.gsfnational.org/" class="nav-item nav-link active">GSF HOME</a>
                    @if($setting->close_registration >= date('Y-m-d'))
                        <a href="#register" class="nav-item nav-link">REGISTER</a>
                    @endif
                    @auth
                    <a href="/account" class="nav-item nav-link">MY ACCOUNT</a>
                    <a class="nav-item nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off mr-50"></i>{{ __('Logout') }}</a> 
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>  
                    @endauth
                    @guest
                         <a href="/login" class="nav-item nav-link">LOGIN</a>
                    @endguest 
                </div>
            </div>
        </div>
    </div>
 
<!-- Nav Bar End -->