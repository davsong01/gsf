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
                <li class=" nav-item {{ Request::is('users*') ? 'active' : '' }}"><a href="{{ route('users.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">My Participants</span></a>
                </li>
                <li class=" nav-item {{ Request::is('materials*') ? 'active' : '' }}"><a href="{{ route('materials.index') }}"><img width="22px" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zOnN2Z2pzPSJodHRwOi8vc3ZnanMuY29tL3N2Z2pzIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTIiIHhtbDpzcGFjZT0icHJlc2VydmUiIGNsYXNzPSIiPjxnPjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgaWQ9IlNvbGlkIj48cGF0aCBkPSJtMjM5LjAyOSAzODQuOTdhMjQgMjQgMCAwIDAgMzMuOTQyIDBsOTAuNTA5LTkwLjUwOWEyNCAyNCAwIDAgMCAwLTMzLjk0MSAyNCAyNCAwIDAgMCAtMzMuOTQxIDBsLTQ5LjUzOSA0OS41Mzl2LTI2Mi4wNTlhMjQgMjQgMCAwIDAgLTQ4IDB2MjYyLjA1OWwtNDkuNTM5LTQ5LjUzOWEyNCAyNCAwIDAgMCAtMzMuOTQxIDAgMjQgMjQgMCAwIDAgMCAzMy45NDF6IiBmaWxsPSIjOGY5ZGFmIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBzdHlsZT0iIiBjbGFzcz0iIj48L3BhdGg+PHBhdGggZD0ibTQ2NCAyMzJhMjQgMjQgMCAwIDAgLTI0IDI0djE4NGgtMzY4di0xODRhMjQgMjQgMCAwIDAgLTQ4IDB2MTkyYTQwIDQwIDAgMCAwIDQwIDQwaDM4NGE0MCA0MCAwIDAgMCA0MC00MHYtMTkyYTI0IDI0IDAgMCAwIC0yNC0yNHoiIGZpbGw9IiM4ZjlkYWYiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIHN0eWxlPSIiIGNsYXNzPSIiPjwvcGF0aD48L2c+PC9nPjwvc3ZnPg==" /><span>&nbsp;&nbsp;&nbsp;&nbsp;Conference Materials</span></a>
                </li>
                @endif
                @if(auth::user()->level == 'Admin')
                <li class=" nav-item {{ Request::is('users*') ? 'active' : '' }}"><a href="{{ route('users.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Participants</span></a>
                </li>
                <li class=" nav-item {{ Request::is('choir*') ? 'active' : '' }}"><a href="{{ route('user.choir') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Choristers</span></a>
                </li>
                <li class=" nav-item {{ Request::is('medical*') ? 'active' : '' }}"><a href="{{ route('user.medical') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Medics</span></a>
                </li>
                <li class=" nav-item {{ Request::is('moderators*') ? 'active' : '' }}"><a href="{{ route('moderators.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="user"></i><span class="menu-title" data-i18n="User">All Moderators</span></a>
                </li>
                <li class=" nav-item {{ Request::is('alumni*') ? 'active' : '' }}"><a href="{{ route('alumni.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Alumni Participants</span></a>
                </li>
                <li class=" nav-item {{ Request::is('nec*') ? 'active' : '' }}"><a href="{{ route('user.nec') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All NEC Participants</span></a>
                </li>
                <li class=" nav-item {{ Request::is('official*') ? 'active' : '' }}"><a href="{{ route('user.official') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Officials</span></a>
                </li>
                <li class=" nav-item {{ Request::is('hostels*') ? 'active' : '' }}"><a href="{{ route('hostels.index') }}"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">Hostel Management</span></a>
                </li>
                <li class=" nav-item {{ Request::is('foods*') ? 'active' : '' }}"><a href="{{ route('foods.index') }}"><span class="menu-title" data-i18n="User"><img style="width: 22px;" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zOnN2Z2pzPSJodHRwOi8vc3ZnanMuY29tL3N2Z2pzIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgdmlld0JveD0iMCAwIDEyOCAxMjgiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTIiIHhtbDpzcGFjZT0icHJlc2VydmUiIGNsYXNzPSIiPjxnPjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTEyMS41LDI1Ljk0MmExLjc1LDEuNzUsMCwwLDAsMS43NS0xLjc1VjYuNWExLjc1LDEuNzUsMCwwLDAtMS43NS0xLjc1SDYuNUExLjc1LDEuNzUsMCwwLDAsNC43NSw2LjVWMjQuMTkyYTEuNzUsMS43NSwwLDAsMCwxLjc1LDEuNzVIMTcuNTFWNjIuMjVINi41QTEuNzUsMS43NSwwLDAsMCw0Ljc1LDY0djZBMS43NSwxLjc1LDAsMCwwLDYuNSw3MS43NWg0Ljc1VjEyMS41QTEuNzUsMS43NSwwLDAsMCwxMywxMjMuMjVIMTE1YTEuNzUsMS43NSwwLDAsMCwxLjc1LTEuNzVWNzEuNzVoNC43NUExLjc1LDEuNzUsMCwwLDAsMTIzLjI1LDcwVjY0YTEuNzUsMS43NSwwLDAsMC0xLjc1LTEuNzVIMTEwLjQ5VjI1Ljk0MlpNOC4yNSw4LjI1aDExMS41VjIyLjQ0Mkg4LjI1Wm01NC41LDU0VjUyQTEuNzUsMS43NSwwLDAsMCw2MSw1MC4yNUg1NUExLjc1LDEuNzUsMCwwLDAsNTMuMjUsNTJWNjIuMjVINTAuNDU4VjUyYTEuNzQ5LDEuNzQ5LDAsMCwwLTEuNzUtMS43NWgtMTJBMS43NSwxLjc1LDAsMCwwLDM0Ljk1OCw1MlY2Mi4yNUgyNy4wMVYyNS45NDJoNzMuOThWNjIuMjVabS0zLjUsMGgtMi41di04LjVoMi41Wm0tMTIuMjkyLTZoLTguNXYtMi41aDguNVptLTguNSwzLjVoOC41djIuNWgtOC41Wk0yMS4wMSwyNS45NDJoMi41VjYyLjI1aC0yLjVaTTExMy4yNSw5OC41SDE0Ljc1VjkwaDk4LjVabS05OC41LDIxLjI1VjEwMmg5OC41djE3Ljc1Wm05OC41LTMzLjI1SDE0Ljc1VjcxLjc1aDk4LjVabTYuNS0xOC4yNUg4LjI1di0yLjVoMTExLjVabS0xMi43Ni02aC0yLjVWMjUuOTQyaDIuNVoiIGZpbGw9IiM5ZmFjYmEiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIHN0eWxlPSIiIGNsYXNzPSIiPjwvcGF0aD48cGF0aCBkPSJNMjMuOTA2LDE0LjI1YTEuNzUsMS43NSwwLDAsMCwwLTMuNWgtN2ExLjc1LDEuNzUsMCwxLDAsMCwzLjVaIiBmaWxsPSIjOWZhY2JhIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBzdHlsZT0iIiBjbGFzcz0iIj48L3BhdGg+PC9nPjwvZz48L3N2Zz4=" />&nbsp;&nbsp;&nbsp;&nbsp;Food stand Management</span></a>
                </li>
                <li class=" nav-item {{ Request::is('materials*') ? 'active' : '' }}"><a href="{{ route('materials.index') }}"><img width="22px" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zOnN2Z2pzPSJodHRwOi8vc3ZnanMuY29tL3N2Z2pzIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTIiIHhtbDpzcGFjZT0icHJlc2VydmUiIGNsYXNzPSIiPjxnPjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgaWQ9IlNvbGlkIj48cGF0aCBkPSJtMjM5LjAyOSAzODQuOTdhMjQgMjQgMCAwIDAgMzMuOTQyIDBsOTAuNTA5LTkwLjUwOWEyNCAyNCAwIDAgMCAwLTMzLjk0MSAyNCAyNCAwIDAgMCAtMzMuOTQxIDBsLTQ5LjUzOSA0OS41Mzl2LTI2Mi4wNTlhMjQgMjQgMCAwIDAgLTQ4IDB2MjYyLjA1OWwtNDkuNTM5LTQ5LjUzOWEyNCAyNCAwIDAgMCAtMzMuOTQxIDAgMjQgMjQgMCAwIDAgMCAzMy45NDF6IiBmaWxsPSIjOGY5ZGFmIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBzdHlsZT0iIiBjbGFzcz0iIj48L3BhdGg+PHBhdGggZD0ibTQ2NCAyMzJhMjQgMjQgMCAwIDAgLTI0IDI0djE4NGgtMzY4di0xODRhMjQgMjQgMCAwIDAgLTQ4IDB2MTkyYTQwIDQwIDAgMCAwIDQwIDQwaDM4NGE0MCA0MCAwIDAgMCA0MC00MHYtMTkyYTI0IDI0IDAgMCAwIC0yNC0yNHoiIGZpbGw9IiM4ZjlkYWYiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIHN0eWxlPSIiIGNsYXNzPSIiPjwvcGF0aD48L2c+PC9nPjwvc3ZnPg==" /><span>&nbsp;&nbsp;&nbsp;&nbsp;Conference Materials</span></a>
                </li>
                @endif
                             
                @if(auth::user()->level == 'Admin')
                
                <li class=" nav-item {{ Request::is('settings') ? 'active' : '' }}"><a href="{{ route('settings.index') }}"><i class="menu-livicon" data-icon="wrench"></i><span class="menu-title" data-i18n="Account Settings">Settings</span></a>
                </li>
                @endif
        </div>
    </div>