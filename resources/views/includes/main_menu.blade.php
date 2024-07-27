<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="/">
                    <div class="brand-logo"></div>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i><i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary" data-ticon="bx-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div><a class="navbar-brand" href="/"><img style="text-align: center; width:30%; display: block; margin-left: 80px;margin-right: auto;" class="logo" src="{{ asset('frontend/img/logo.png') }}"></a></div>
    
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">              
            @if(Auth::user()->isSwitchingUser() )
            <li class="sidebar-item hide-menu"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                style="color:yellow !important; font-weight:bolder" href="{{ route('stop.switchuser') }}" aria-expanded="false"><i class="fa fa-arrow-left"></i><span
                    class="hide-menu">BACK TO ADMIN</span></a></li>
            @endif
            @if(auth::user()->role == 1)
            {{-- Admin menus --}}
                @include('includes.adminmenu')
            @elseif(auth::user()->role == 3)
            {{-- Publicity sec menu --}}
                @include('includes.subadminmenu')
            @endif
                
            @if(isset($edition) && $edition->status == 'active')
                @if(auth::user()->role != 1)
                    @include('includes.membermenu')
                @endif
            @else
            @endif

           
        </ul> 
    </div>
</div>