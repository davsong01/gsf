<li class="nav-item {{ Request::is('conferencemanagement*') ? 'active' : '' }}"><a href="{{ route('conferencemanagement.index') }}"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="Dashboard">Conference</span></a>
{{-- <li class="nav-item {{ Request::is('conferencemanagement*') ? 'active' : '' }}"><a href="{{ route('conferencemanagement.index') }}"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="Dashboard">Conference</span></a> --}}
</li>
@if(auth()->user()->conference_role == 'superadmin')
<li class=" nav-item {{ Request::is('staff') ? 'active' : '' }}"><a href="{{ route('staff.index') }}"><i class="fa fa-group"></i><span class="menu-title">Staff</span></a>
</li>
<li class=" nav-item {{ Request::is('nec*') ? 'active' : '' }}"><a href="{{ route('nec.index') }}"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 1 224 0a128 128 0 1 1 0 256zM209.1 359.2l-18.6-31c-6.4-10.7 1.3-24.2 13.7-24.2H224h19.7c12.4 0 20.1 13.6 13.7 24.2l-18.6 31 33.4 123.9 36-146.9c2-8.1 9.8-13.4 17.9-11.3c70.1 17.6 121.9 81 121.9 156.4c0 17-13.8 30.7-30.7 30.7H285.5c-2.1 0-4-.4-5.8-1.1l.3 1.1H168l.3-1.1c-1.8 .7-3.8 1.1-5.8 1.1H30.7C13.8 512 0 498.2 0 481.3c0-75.5 51.9-138.9 121.9-156.4c8.1-2 15.9 3.3 17.9 11.3l36 146.9 33.4-123.9z"/></svg></i><span class="menu-title"> &nbsp; &nbsp; &nbsp; NEC Management</span></a></li>

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
@endif
