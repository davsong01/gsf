
<li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="fa fa-bars"></i><span class="menu-title" data-i18n="Kanban">Profile</span></a>
</li>
<li class=" nav-item {{ Request::is('participants*') ? 'active' : '' }}"><a href="{{ route('conferencemanagement.index',['edition'=>$edition->id]) }}"><i class="fa fa-user"></i><span class="menu-title">Conferences</span></a></li>
