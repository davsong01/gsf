{{-- <li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="fa fa-bars"></i><span class="menu-title" data-i18n="Kanban">Dashboard</span></a>
</li> --}}

<li class="nav-item {{ Request::is('conferencemanagement*') ? 'active' : '' }}"><a href="{{ route('conferencemanagement.index') }}"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="Dashboard">Conference</span></a>
{{-- <li class="nav-item {{ Request::is('conferencemanagement*') ? 'active' : '' }}"><a href="{{ route('conferencemanagement.index') }}"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="Dashboard">Conference</span></a> --}}
</li>

<li class=" nav-item {{ Request::is('staff') ? 'active' : '' }}"><a href="{{ route('staff.index') }}"><i class="fa fa-group"></i><span class="menu-title">Staff</span></a>
</li>

<li class=" nav-item {{ Request::is('users*') ? 'active' : '' }}"><a href="{{ route('users.index') }}"><i class="fa fa-user"></i><span class="menu-title">Users</span></a></li>
<li class="nav-item {{ Request::is('trashedusers') ? 'active' : '' }}"><a href="{{ route('users.trashed') }}"><i class="fa fa-trash-o"></i><span class="menu-title">Trashed Users</span></a></li>
<li class="nav-item {{ Request::is('events*') ? 'active' : '' }}"><a href="{{ route('events.index') }}"><i class="fa fa-calendar" aria-hidden="true"></i><span class="menu-title">Events</span></a></li> 
<li class="nav-item {{ Request::is('fields*') ? 'active' : '' }}"><a href="{{ route('fields.index') }}"><i class="fa fa-globe" aria-hidden="true"></i><span class="menu-title">Fields</span></a>
</li>
<li class="nav-item {{ Request::is('zones*') ? 'active' : '' }}"><a href="{{ route('zones.index') }}"><i class="fa fa-flag" aria-hidden="true"></i><span class="menu-title">Zones</span></a>
</li>
<li class="nav-item {{ Request::is('chapters*') ? 'active' : '' }}"><a href="{{ route('chapters.index') }}"><i class="fa fa-thumb-tack" aria-hidden="true"></i><span class="menu-title">Chapters</span></a>
</li>

<li class=" nav-item {{ Request::is('useremails') ? 'active' : '' }}"><a href="{{ route('useremails.index') }}"><i class="fa fa-envelope"></i><span class="menu-title">Emails</span></a>
</li>
<li class="nav-item">
    <a class="nav-link {{ Request::is('criticalemails*') ? 'active' : '' }}" href="{{ route('criticalEmail.index') }}"><i class="fa fa-envelope"></i><span class="menu-title" >Logged Emails</a>
</li>
<li class="nav-item {{ Request::is('profile*') ? 'active' : '' }}">
<a href="{{ route('users.show', auth()->user()->id) }}"><i class="fa fa-user-circle-o"></i><span class="menu-title"
data-i18n="Kanban">My Profile</span></a>
</li>
<li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="fa fa-bars"></i><span class="menu-title" data-i18n="Kanban">General Settings</span></a></li>
