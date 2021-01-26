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
        <div><img style="text-align: center; width:50%; display: block; margin-left: 30px;margin-right: auto;" class="logo" src="<?php echo e(asset('frontend/img/logo.png')); ?>"></div>
        <br><br>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">              
                 <?php if(Auth::user()->isSwitchingUser() ): ?>
                <li class="sidebar-item hide-menu"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    style="color:yellow !important; font-weight:bolder" href="<?php echo e(route('stop.switchuser')); ?>" aria-expanded="false"><i class="fa fa-arrow-left"></i><span
                        class="hide-menu">BACK TO ADMIN</span></a></li>
                <?php endif; ?>
                <li class="nav-item <?php echo e(Request::is('account*') ? 'active' : ''); ?>"><a href="/account"><i class="menu-livicon" data-icon="grid"></i><span class="menu-title" data-i18n="Kanban">Dashboard</span></a>
                </li>
                <?php if(auth::user()->level == 'Moderator'): ?>
                <li class=" nav-item <?php echo e(Request::is('users*') ? 'active' : ''); ?>"><a href="<?php echo e(route('users.index')); ?>"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Participants</span></a>
                </li>
                <?php endif; ?>
                <?php if(auth::user()->level == 'Admin'): ?>
                <li class=" nav-item <?php echo e(Request::is('users*') ? 'active' : ''); ?>"><a href="<?php echo e(route('users.index')); ?>"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Participants</span></a>
                </li>
                <li class=" nav-item <?php echo e(Request::is('moderators*') ? 'active' : ''); ?>"><a href="<?php echo e(route('moderators.index')); ?>"><i class="menu-livicon livicon-evo-holder" data-icon="user"></i><span class="menu-title" data-i18n="User">All Moderators</span></a>
                </li>
                <li class=" nav-item <?php echo e(Request::is('alumni*') ? 'active' : ''); ?>"><a href="<?php echo e(route('alumni.index')); ?>"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">All Alumni Participants</span></a>
                </li>
                <li class=" nav-item <?php echo e(Request::is('hostels*') ? 'active' : ''); ?>"><a href="<?php echo e(route('hostels.index')); ?>"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User">Hostel Management</span></a>
                </li>
                <li class=" nav-item <?php echo e(Request::is('foods*') ? 'active' : ''); ?>"><a href="<?php echo e(route('foods.index')); ?>"><i class="menu-livicon livicon-evo-holder" data-icon="users"></i><span class="menu-title" data-i18n="User"> Food stand Management</span></a>
                </li>
                
                <?php endif; ?>
             
                <li class=" nav-item <?php echo e(Request::is('payouts') ? 'active' : ''); ?>"><a href="<?php echo e(route('payouts.index')); ?>"><i class="menu-livicon" data-icon="notebook"></i><span class="menu-title" data-i18n="Invoice">Payment History</span></a>
                </li>
                
                <?php if(auth::user()->level == 'Admin'): ?>
                <li class=" nav-item <?php echo e(Request::is('settings') ? 'active' : ''); ?>"><a href="<?php echo e(route('settings.index')); ?>"><i class="menu-livicon" data-icon="wrench"></i><span class="menu-title" data-i18n="Account Settings">Settings</span></a>
                </li>
                <?php endif; ?>
        </div>
    </div><?php /**PATH C:\laragon\www\gsf\resources\views/includes/main_menu.blade.php ENDPATH**/ ?>