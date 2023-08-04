<header class="header-global">
    <nav style="background-color: @yield('nav-background') " id="navbar-main" class="navbar navbar-main navbar-theme-primary navbar-expand-lg headroom py-lg-3 px-lg-6 navbar-dark navbar-transparent navbar-theme-primary">
        <div class="container">
            <a class="navbar-brand @@logo_classes" href="{{url('/')}}">
                <x-logo width="auto" height="70px" class="navbar-brand-dark common"/>
                <img class="navbar-brand-dark common" src="" height="35">
            </a>
            <div class="navbar-collapse collapse" id="navbar_global">
                <div class="navbar-collapse-header">
                    <div class="row">
                        <div class="col-6 collapse-brand">
                            <a href=""><x-logo width="auto" height="35px"/></a>
                        </div>
                        <div class="col-6 collapse-close"><a href="#navbar_global" role="button" class="fas fa-times" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation"></a></div>
                    </div>
                </div>
                <ul class="navbar-nav navbar-nav-hover justify-content-center">
                    @foreach(menu() as $m)
                    <li class="nav-item">
                        <a href="{{route($m['route'])}}" id="dashboardPagesDropdown" class="nav-link" aria-expanded="false">
                            <span class="nav-link-inner-text mr-1">{{$m['name']}}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @guest
            <div class="d-none d-lg-block @@cta_button_classes">
                <a href="{{route('login')}}" target="_blank" class="btn btn-md btn-outline-white animate-up-2 mr-3">
                    <i class="fas fa-user mr-1"></i> 
                    <span class="d-none d-xl-inline">Login</span>
                </a> 
                <a href="{{ route('newalumni') }}" target="_blank" class="btn btn-md btn-secondary animate-up-2"><i class="fas fa-plus"></i>
                </a>
            </div>
            @endguest
            @auth
            <a href="{{ route('account') }}" target="_blank" class="btn btn-md btn-secondary animate-up-2"><i class="fa-user-circle-o"></i>Account
            </a>
            @endauth
            <div class="d-flex d-lg-none align-items-center"><button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button></div>
        </div>
    </nav>
</header>