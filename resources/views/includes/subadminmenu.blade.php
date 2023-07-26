
<li class="nav-item {{ Request::is('profile*') ? 'active' : '' }}">
        <a href="{{ route('users.show', auth()->user()->id) }}"><i class="fa fa-user-circle-o"></i><span class="menu-title"
                data-i18n="Kanban">My Profile</span></a>
</li>
<li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="fa fa-bars"></i><span class="menu-title" data-i18n="Kanban">Dashboard</span></a>
</li>
<li class=" nav-item {{ Request::is('cahpters*') ? 'active' : '' }}"><a href="{{ route('chapters.index') }}"><i class="fa fa-info-circle"></i><span class="menu-title">Fellowship details</span></a></li>
<li class=" nav-item {{ Request::is('users*') ? 'active' : '' }}"><a href="{{ route('users.index') }}"><i class="fa fa-users"></i><span class="menu-title">My community</span></a></li>
<li class="nav-item {{ Request::is('trashedusers') ? 'active' : '' }}"><a href="{{ route('users.trashed') }}"><i class="fa fa-trash-o"></i><span class="menu-title">Trashed Members</span></a></li>
<li class="nav-item {{ Request::is('events*') ? 'active' : '' }}"><a href="{{ route('events.index') }}"><i class="fa fa-calendar" aria-hidden="true"></i><span class="menu-title">Events</span></a>
</li> 
