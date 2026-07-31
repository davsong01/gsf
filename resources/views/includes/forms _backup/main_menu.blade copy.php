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
                <li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="fa fa-bars"></i><span class="menu-title" data-i18n="Kanban">Dashboard</span></a>
                </li>
                <li class="nav-item {{ Request::is('conferencemanagement*') ? 'active' : '' }}"><a href="{{ route('conferencemanagement.index') }}"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="Dashboard">Conference</span></a>
                </li>

                
                @if((isAdmin()['status'] ?? false))
                
                <li class=" nav-item {{ Request::is('staff') ? 'active' : '' }}"><a href="{{ route('staff.index') }}"><i class="fa fa-group"></i><span class="menu-title">Staff</span></a>
                </li>

                <li class=" nav-item {{ Request::is('users*') ? 'active' : '' }}"><a href="{{ route('users.index') }}"><i class="fa fa-user"></i><span class="menu-title">Users</span></a></li>
                <li class="nav-item {{ Request::is('trashedusers') ? 'active' : '' }}"><a href="{{ route('users.trashed') }}"><i class="fa fa-trash-o"></i><span class="menu-title">Trashed Users</span></a></li>
                <li class="nav-item {{ Request::is('events') ? 'active' : '' }}"><a href="{{ route('events.index') }}"><i class="fa fa-calendar" aria-hidden="true"></i><span class="menu-title">Events</span></a></li> 
                <li class="nav-item {{ Request::is('fields') ? 'active' : '' }}"><a href="{{ route('fields.index') }}"><i class="fa fa-globe" aria-hidden="true"></i><span class="menu-title">Fields</span></a>
                </li>
                <li class="nav-item {{ Request::is('zones') ? 'active' : '' }}"><a href="{{ route('zones.index') }}"><i class="fa fa-flag" aria-hidden="true"></i><span class="menu-title">Zones</span></a>
                </li>
                <li class="nav-item {{ Request::is('chapters') ? 'active' : '' }}"><a href="{{ route('chapters.index') }}"><i class="fa fa-thumb-tack" aria-hidden="true"></i><span class="menu-title">Chapters</span></a>
                </li>

                <li class=" nav-item {{ Request::is('useremails') ? 'active' : '' }}"><a href="{{ route('useremails.index') }}"><i class="fa fa-envelope"></i><span class="menu-title" >Emails</span></a>
                </li>
               
                

                
                
                @endif
        </div>
    </div>
