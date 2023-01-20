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
        <br><br>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">              
                <li class=" nav-item {{ Request::is('reports*') ? 'active' : '' }}"><a href="{{ route('stakeholder.dashboard') }}"><i class="fa fa-file" aria-hidden="true"></i><span class="menu-title" data-i18n="User">Manage Reports</span></a>
                </li>
                
                
                <li class=" nav-item {{ Request::is('stakeholderpayment*') ? 'active' : '' }}"><a href="{{ route('stakeholderpayment.index') }}"><i class="fa fa-money"></i>Proof of Payment</span></a>
                </li>
                @if(Auth::guard('stakeholder')->user()->role == 'President')
                <li class="nav-item"><a href="{{ route('stakeholder.profile') }}"><i class="fa fa-signature" aria-hidden="true"><img style="width:20px" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyIDUxMjsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik0yNTYuMDUyLDYxLjc3M0wxNDUuOTU5LDE2LjE2OWMtMy42NzUtMS41MjEtNy44MDUtMS41MjEtMTEuNDgsMGMtMy42NzUsMS41MjItNi41OTYsNC40NDItOC4xMTgsOC4xMThMODEuNjI3LDEzMi4yODUNCgkJCUwzLjQ1NCwyMjYuNTM5Yy0zLjE1NSwzLjgwNS00LjIzMyw4LjkxOS0yLjg4NCwxMy42NzNsNDIuNDM2LDE0OS40NTdjMS4yNTIsNC40MTEsNC40NTMsOC4wMDcsOC42ODksOS43NjINCgkJCWMxLjg0MywwLjc2NCwzLjc5NCwxLjE0Miw1Ljc0LDEuMTQyYzIuNTI2LDAsNS4wNDQtMC42MzgsNy4zMDctMS44OTlMMjAwLjQzLDMyM2M0LjMxNi0yLjQwNyw3LjE3LTYuNzg2LDcuNjI5LTExLjcwNw0KCQkJbDExLjM3Ni0xMjEuOTI2TDI2NC4xNyw4MS4zNzJDMjY3LjM0LDczLjcxOSwyNjMuNzA2LDY0Ljk0NCwyNTYuMDUyLDYxLjc3M3ogTTE3OC45MjEsMzAwLjY0NGwtOTEuMTIsNTAuODE3bDM4LjgyOC05My43NA0KCQkJYzMuMTctNy42NTMtMC40NjQtMTYuNDI5LTguMTE4LTE5LjU5OWMtNy42NTMtMy4xNy0xNi40MjksMC40NjQtMTkuNTk5LDguMTE4bC0zOC44MjksOTMuNzQyTDMxLjU4NiwyMzkuNjEzTDk5LjEsMTU4LjIxMg0KCQkJbDQ0LjgyMywxOC41NjVsNDQuODIzLDE4LjU2NkwxNzguOTIxLDMwMC42NDR6IE0xOTYuNTkxLDE2Ni4xMmwtNTguNjI2LTI0LjI4MmwtMjMuNzUtOS44MzdsMzQuMTIxLTgyLjM3NWw4Mi4zNzYsMzQuMTIyDQoJCQlMMTk2LjU5MSwxNjYuMTJ6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik00OTcsMjc0LjE3MkgzMzYuMzM0Yy0zNC44NDksMC02My4yMDEsMjguMzUyLTYzLjIwMSw2My4yMDFjMCwzNC44NDgsMjguMzUyLDYzLjE5OSw2My4yMDEsNjMuMTk5DQoJCQljMTguMzA2LDAsMzMuMTk5LDE0Ljg5MywzMy4xOTksMzMuMTk5YzAsMTguMzA4LTE0Ljg5MywzMy4yMDEtMzMuMTk5LDMzLjIwMUgxNWMtOC4yODQsMC0xNSw2LjcxNi0xNSwxNXM2LjcxNiwxNSwxNSwxNWgzMjEuMzM0DQoJCQljMzQuODQ4LDAsNjMuMTk5LTI4LjM1Miw2My4xOTktNjMuMjAxYzAtMzQuODQ4LTI4LjM1MS02My4xOTktNjMuMTk5LTYzLjE5OWMtMTguMzA3LDAtMzMuMjAxLTE0Ljg5NC0zMy4yMDEtMzMuMTk5DQoJCQljMC0xOC4zMDgsMTQuODk0LTMzLjIwMSwzMy4yMDEtMzMuMjAxSDQ5N2M4LjI4NCwwLDE1LTYuNzE2LDE1LTE1UzUwNS4yODQsMjc0LjE3Miw0OTcsMjc0LjE3MnoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8L3N2Zz4NCg==" /><span class="menu-title" data-i18n="User">&nbsp &nbsp &nbsp</i><span class="menu-title" data-i18n="User">Upload Signatures</span></a>
                </li>
                @endif
                <li class=" nav-item {{ Request::is('stakeholderprofile*') ? 'active' : '' }}"><a href="{{ route('stakeholder.profile') }}"><i class="fa fa-user o" aria-hidden="true"></i><span class="menu-title" data-i18n="User">My Profile</span></a>
                </li>
            </ul>
        </div>
    </div>