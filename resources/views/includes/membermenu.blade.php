<li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="fa fa-bars"></i><span class="menu-title" data-i18n="Kanban">Dashboard</span></a>
</li>
@if(\App\Setting::first()->value('enable_conference') == 1)
<li class="nav-item {{ Request::is('conferencemanagement*') ? 'active' : '' }}"><a href="{{ route('conferencemanagement.index') }}"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="Dashboard">Conference</span></a>
</li>
@endif