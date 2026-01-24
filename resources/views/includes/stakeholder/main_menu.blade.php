@php
    $currentRoute = Route::currentRouteName();
    $user = auth()->guard('stakeholder')->user();
@endphp

<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="/">
                    <div class="brand-logo"></div>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i>
                    <i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary" data-ticon="bx-disc"></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="shadow-bottom"></div>

    <div>
        <a class="navbar-brand" href="/">
            <img style="text-align: center; width:30%; display: block; margin-left: 80px; margin-right: auto;" class="logo" src="{{ asset('frontend/img/logo.png') }}">
        </a>
    </div>
    <br><br>

    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">

            <li class="nav-item {{ $currentRoute === 'stakeholders.dashboard' ? 'active' : '' }}">
                <a href="{{ route('stakeholders.dashboard') }}">
                    <i class="fa fa-desktop" aria-hidden="true"></i>
                    <span class="menu-title" data-i18n="User">Dashboard</span>
                </a>
            </li>

            @if($user->role_id == 6)
                <li class="nav-item {{ $currentRoute === 'stakeholders.financial.report' ? 'active' : '' }}">
                    <a href="{{ route('stakeholders.financial.report') }}">
                        <i class="fa fa-file" aria-hidden="true"></i>
                        <span class="menu-title" data-i18n="User">Financial Reports</span>
                    </a>
                </li>
            @else
                <li class="nav-item {{ Str::startsWith($currentRoute, 'stakeholders.reports') ? 'active' : '' }}">
                    <a href="{{ route('stakeholders.reports.index') }}">
                        <i class="fa fa-file" aria-hidden="true"></i>
                        <span class="menu-title" data-i18n="User">Monthly Reports</span>
                    </a>
                </li>

                <li class="nav-item {{ Str::startsWith($currentRoute, 'stakeholders.users') ? 'active' : '' }}">
                    <a href="{{ route('stakeholders.users.index') }}">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <span class="menu-title" data-i18n="User">Chapter Members</span>
                    </a>
                </li>
                <li class="nav-item {{ Str::startsWith($currentRoute, 'stakeholders.alumni') ? 'active' : '' }}">
                    <a href="{{ route('stakeholders.alumni.index') }}">
                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                        <span class="menu-title" data-i18n="User">Chapter Alumni</span>
                    </a>
                </li>
                <li class="nav-item {{ Str::startsWith($currentRoute, 'stakeholders.chapters') ? 'active' : '' }}">
                    <a href="{{ route('stakeholders.chapters.edit', $user->chapter_id) }}">
                        <i class="fa fa-thumb-tack" aria-hidden="true"></i>
                        <span class="menu-title" data-i18n="User">Chapter Details</span>
                    </a>
                </li>
            @endif

            <li class="nav-item {{ Str::startsWith($currentRoute, 'stakeholders.profile') ? 'active' : '' }}">
                <a href="{{ route('stakeholders.profile') }}">
                    <i class="fa fa-user-o" aria-hidden="true"></i>
                    <span class="menu-title" data-i18n="User">My Profile</span>
                </a>
            </li>
        </ul>
    </div>
</div>
