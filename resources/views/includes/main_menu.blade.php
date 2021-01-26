 <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="/\">
                        <div class="brand-logo"></div>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i><i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary" data-ticon="bx-disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div><img style="text-align: center; width:30%; display: block; margin-left: 80px;margin-right: auto;" class="logo" src="{{ asset('frontend/img/logo.png') }}"></div>
        <br><br>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">              
                 @if(Auth::user()->isSwitchingUser() )
                <li class="sidebar-item hide-menu"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    style="color:yellow !important; font-weight:bolder" href="{{ route('stop.switchuser') }}" aria-expanded="false"><i class="fa fa-arrow-left"></i><span
                        class="hide-menu">BACK TO ADMIN</span></a></li>
                @endif
                <li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="menu-livicon" data-icon="grid"></i><span class="menu-title" data-i18n="Kanban">Dashboard</span></a>
                </li>
                @if(auth::user()->level == 'Moderator')
                <li class=" nav-item {{ Request::is('users*') ? 'active' : '' }}"><a href="{{ route('users.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Participants</span></a>
                </li>
                @endif
                @if(auth::user()->level == 'Admin')
                <li class=" nav-item {{ Request::is('users*') ? 'active' : '' }}"><a href="{{ route('users.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Participants</span></a>
                </li>
                <li class=" nav-item {{ Request::is('moderators*') ? 'active' : '' }}"><a href="{{ route('moderators.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="user"></i><span class="menu-title" data-i18n="User">All Moderators</span></a>
                </li>
                <li class=" nav-item {{ Request::is('alumni*') ? 'active' : '' }}"><a href="{{ route('alumni.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Alumni Participants</span></a>
                </li>
                <li class=" nav-item {{ Request::is('hostels*') ? 'active' : '' }}"><a href="{{ route('hostels.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">Hostel Management</span></a>
                </li>
                <li class=" nav-item {{ Request::is('foods*') ? 'active' : '' }}"><a href="{{ route('foods.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User"> Food stand Management</span></a>
                </li>
                
                @endif
             
                <li class=" nav-item {{ Request::is('payouts') ? 'active' : '' }}"><a href="{{ route('payouts.index') }}"><i class="menu-livicon" data-icon="notebook"></i><span class="menu-title" data-i18n="Invoice">Payment History</span></a>
                </li>
                
                @if(auth::user()->level == 'Admin')
                <li class=" nav-item {{ Request::is('settings') ? 'active' : '' }}"><a href="{{ route('settings.index') }}"><i class="menu-livicon" data-icon="wrench"></i><span class="menu-title" data-i18n="Account Settings">Settings</span></a>
                </li>
                @endif
        </div>
    </div>