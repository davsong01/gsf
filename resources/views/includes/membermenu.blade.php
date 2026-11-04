<li class="nav-item {{ Request::is('account') || Request::is('home') ? 'active' : '' }}">
    <a href="{{ route('account') }}"><i class="fa fa-home"></i><span class="menu-title">Dashboard</span></a>
</li>
<li class="nav-item {{ Request::is('conferencemanagement*') ? 'active' : '' }}">
    <a href="{{ route('conferencemanagement.index') }}"><i class="fa fa-calendar"></i><span class="menu-title">My Conferences</span></a>
</li>
<li class="nav-item {{ Request::is('profile') ? 'active' : '' }}">
    <a href="{{ route('member.profile') }}"><i class="fa fa-user-circle-o"></i><span class="menu-title">My Profile</span></a>
</li>
<li class="nav-item">
    <a href="{{ route('conferences.index') }}"><i class="fa fa-compass"></i><span class="menu-title">Explore Conferences</span></a>
</li>
