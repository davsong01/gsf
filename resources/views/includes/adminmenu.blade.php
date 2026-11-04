<li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="fa fa-bars"></i><span class="menu-title" data-i18n="Kanban">Dashboard</span></a></li>

@php
    $userPermissions = auth()->user()->permissions ?? [];
@endphp

{!! renderMenuTree(rootPermissions()->toArray(), $userPermissions) !!}

@if($isAdmin)
<li class="nav-item {{ Request::is('general-settings*') ? 'active' : '' }}">
    <a href="{{ route('general-settings.edit') }}">
        <i class="fa fa-cog"></i><span class="menu-title">General Settings</span>
    </a>
</li>
@endif

<li class="nav-item {{ Request::is('profile*') ? 'active' : '' }}">
    <a href="{{ route('users.show', auth()->user()->id) }}">
        <i class="fa fa-user-circle-o"></i><span class="menu-title" data-i18n="Kanban">My Profile</span>
    </a>
</li>
