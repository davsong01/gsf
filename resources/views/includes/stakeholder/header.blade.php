<!-- BEGIN: Header-->
    <div class="header-navbar-shadow"></div>
    <nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top ">
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                <div class="navbar-collapse" id="navbar-mobile">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        <ul class="nav navbar-nav">
                            <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon bx bx-menu"></i></a></li>
                        </ul>

                    </div>
                    <ul class="nav navbar-nav float-right">
            
                        <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" data-toggle="dropdown">  
                            <div class="user-nav d-sm-flex"><span class="user-name">Hi, {{ Auth::guard('stakeholder')->user()->name }}</span><span class="user-status text-muted"></span></div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right pb-0">
                                
                            <div class="dropdown-divider mb-0"></div>
                                <a class="dropdown-item" href="{{ route('stakeholder.logout') }}" onclick="return alert('You are about to log out')">
                                    <i class="bx bx-power-off mr-50"></i>Logout
                                </a> 
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- END: Header-->