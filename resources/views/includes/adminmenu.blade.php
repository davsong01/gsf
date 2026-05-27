<li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="fa fa-bars"></i><span class="menu-title" data-i18n="Kanban">Dashboard</span></a></li>

@if(auth()->user()->conference_role == 'superadmin')
    <li class="nav-item {{(Route::is('edit.conference.edition') || Route::is('show.conference.edition') || Route::is('conferencemanagement*') || Route::is('conference.transactions') || Route('conference.participants') || Route::is('show.conference.edition') || Route::is('ministry.index*') ) ? 'open is_shown' : '' }}"><a href="#"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="Content">Conference Mgt</span></a>
        <ul class="menu-content">
            <li class="is_shown"><a href="{{ route('ministry.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Ministries</span></a>
            </li>
            <li class="is_shown"><a href="{{ route('paymentproviders.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-title">Payment Providers</span></a>
            </li>
            <li class="is_shown"><a href="{{ route('conference_speakers.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Conference Speakers</span></a>
            </li>
            <li class="is_shown"><a href="{{ route('conference_faqs.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Conference FAQs</span></a>
            </li>
            <li class="is_shown"><a href="{{ route('conferencemanagement.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Conferences</span></a>
            </li>
        </ul>
    </li>
@endif

@php
    $userPermissions = auth()->user()->permissions ?? [];
@endphp

@if(env('MINISTRY') == 'gsf')
    @if(
        in_array('stakeholderreports.index', $userPermissions) ||
        in_array('stakeholderreportsection.index', $userPermissions) ||
        in_array('stakeholderreportsubsection.index', $userPermissions) ||
        in_array('stakeholder.questions.index', $userPermissions) ||
        in_array('stakeholderroles.index', $userPermissions) ||
        in_array('stakeholderpermissions.index', $userPermissions) ||
        in_array('designation.index', $userPermissions) ||
        in_array('stakeholderpersonnel.index', $userPermissions)  ||
        in_array('award.go', $userPermissions)  ||
        in_array('award.etf', $userPermissions) ||
        in_array('award.settings', $userPermissions)
        
    )
        <li class="nav-item has-sub {{ Request::is('stakeholder*') ? 'open is_shown' : '' }}">
            <a href="#"><i class="fa fa-calendar" aria-hidden="true"></i><span class="menu-title" data-i18n="Content">Digital Portal Mgt</span></a>
            <ul class="menu-content">
                {{-- REPORTS --}}
                @if(in_array('stakeholderreports.index', $userPermissions))
                <li class="has-sub {{ Request::is('stakeholderreports*') ? 'open is_shown' : '' }}">
                    <a href="#"><i class="bx bx-file"></i><span class="menu-item">Reports</span></a>
                    <ul class="menu-content">
                        <li>
                            <a href="{{ route('stakeholderreports.index') }}">
                                <i class="bx bx-right-arrow-alt"></i><span class="menu-item">Monthly Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.analytics') }}">
                                <i class="bx bx-right-arrow-alt"></i><span class="menu-item">Reports Analytics</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if(in_array('award.etf', $userPermissions) || in_array('award.go', $userPermissions) || in_array('award.settings', $userPermissions))
                    <li class="has-sub {{ Request::is('award.etf*') || Request::is('award.go*') ? 'open is_shown' : '' }}">
                        <a href="#">
                            <i class="bx bx-file"></i>
                            <span class="menu-item">Awards</span>
                        </a>

                        <ul class="menu-content">

                            @if(in_array('award.etf', $userPermissions))
                                <li>
                                    <a href="{{ route('award.etf') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item">Etf Awards</span>
                                    </a>
                                </li>
                            @endif

                            @if(in_array('award.go', $userPermissions))
                                <li>
                                    <a href="{{ route('award.go') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item">G.O. Awards</span>
                                    </a>
                                </li>
                            @endif

                            @if(in_array('award.settings', $userPermissions))
                                <li>
                                    <a href="{{ route('award.settings') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item">Award Settings</span>
                                    </a>
                                </li>
                            @endif

                            

                        </ul>
                    </li>
                @endif

                {{-- REPORT STRUCTURE --}}
                @if(
                    in_array('stakeholderreportsection.index', $userPermissions) ||
                    in_array('stakeholderreportsubsection.index', $userPermissions) ||
                    in_array('stakeholder.questions.index', $userPermissions)
                )
                <li class="has-sub {{ Request::is('stakeholderreportsection*') || Request::is('stakeholderreportsubsection*') || Request::is('stakeholder.questions*') ? 'open is_shown' : '' }}">
                    <a href="#"><i class="bx bx-layer"></i><span class="menu-item">Report Structure</span></a>
                    <ul class="menu-content">
                        @if(in_array('stakeholderreportsection.index', $userPermissions))
                        <li>
                            <a href="{{ route('stakeholderreportsection.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Sections</span></a>
                        </li>
                        @endif
                        @if(in_array('stakeholderreportsubsection.index', $userPermissions))
                        <li>
                            <a href="{{ route('stakeholderreportsubsection.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Sub Sections</span></a>
                        </li>
                        @endif
                        @if(in_array('stakeholder.questions.index', $userPermissions))
                        <li>
                            <a href="{{ route('stakeholder.questions.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Items</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                {{-- ACCESS CONTROL --}}
                @if(
                    in_array('stakeholderroles.index', $userPermissions) ||
                    in_array('stakeholderpermissions.index', $userPermissions) ||
                    in_array('designation.index', $userPermissions) ||
                    in_array('stakeholderpersonnel.index', $userPermissions) ||
                    in_array('stakeholdersetting.index', $userPermissions)
                )
                <li class="has-sub {{ Request::is('stakeholderroles*') || Request::is('stakeholderpermissions*') || Request::is('designation*') || Request::is('stakeholderpersonnel*') ? 'open is_shown' : '' }}">
                    <a href="#"><i class="bx bx-lock"></i><span class="menu-item">Access Control</span></a>
                    <ul class="menu-content">
                        @if(in_array('stakeholderroles.index', $userPermissions))
                        <li><a href="{{ route('stakeholderroles.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Roles</span></a></li>
                        @endif
                        @if(in_array('stakeholderpermissions.index', $userPermissions))
                        <li><a href="{{ route('stakeholderpermissions.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Permissions</span></a></li>
                        @endif
                        @if(in_array('designation.index', $userPermissions))
                        <li><a href="{{ route('designation.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Designations</span></a></li>
                        @endif
                        @if(in_array('stakeholderpersonnel.index', $userPermissions))
                        <li><a href="{{ route('stakeholderpersonnel.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Stakeholders</span></a></li>
                        @endif
                        @if(in_array('stakeholdersetting.index', $userPermissions))
                        <li><a href="{{ route('stakeholdersetting.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Settings</span></a></li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>
        </li>
    @endif

    {{-- NEC MANAGEMENT --}}
    @if(in_array('nec.index', $userPermissions))
    <li class="nav-item {{ Request::is('nec*') ? 'active' : '' }}">
        <a href="{{ route('nec.index') }}"><i class="fa fa-shield"></i><span class="menu-title"> NEC Management</span></a>
    </li>
    @endif

    @if(in_array('archive.nec.index', $userPermissions))
    <li class="nav-item {{ Request::is('archive-nec*') ? 'active' : '' }}">
        <a href="{{ route('archive.nec.index') }}"><i class="fa fa-archive"></i><span class="menu-title"> Archive NEC Members</span></a>
    </li>
    @endif


    {{-- USERS --}}
    @if(in_array('users.index', $userPermissions))
    <li class="nav-item {{ Request::is('users*') ? 'active' : '' }}">
        <a href="{{ route('users.index') }}"><i class="fa fa-user"></i><span class="menu-title">Users</span></a>
    </li>
    @endif

    {{-- OFFICIALS --}}
    @if(in_array('officials.index', $userPermissions))
    <li class="nav-item {{ Request::is('officials*') ? 'active' : '' }}">
        <a href="{{ route('officials.index') }}"><i class="fa fa-users"></i><span class="menu-title">Officials</span></a>
    </li>
    @endif

    {{-- PENDING LISTING --}}
    @if(in_array('listing-pending', $userPermissions))
    <li class="nav-item {{ Request::is('listing-pending*') ? 'active' : '' }}">
        <a href="{{ route('listing-pending') }}"><i class="fa fa-user"></i><span class="menu-title">Pending Listing</span></a>
    </li>
    @endif

    {{-- TRASHED USERS --}}
    @if(in_array('users.trashed', $userPermissions))
    <li class="nav-item {{ Request::is('trashedusers') ? 'active' : '' }}">
        <a href="{{ route('users.trashed') }}"><i class="fa fa-trash-o"></i><span class="menu-title">Trashed Users</span></a>
    </li>
    @endif

    {{-- EVENTS --}}
    @if(in_array('events.index', $userPermissions))
    <li class="nav-item {{ Request::is('events*') ? 'active' : '' }}">
        <a href="{{ route('events.index') }}"><i class="fa fa-calendar" aria-hidden="true"></i><span class="menu-title">Events</span></a>
    </li>
    @endif

    {{-- DONATIONS --}}
    @if(in_array('donations.all', $userPermissions))
    <li class="nav-item {{ Request::is('all-donations*') ? 'active' : '' }}">
        <a href="{{ route('donations.all') }}"><i class="fa fa-money" aria-hidden="true"></i><span class="menu-title">Donations</span></a>
    </li>
    @endif

    {{-- FIELDS --}}
    @if(in_array('fields.index', $userPermissions))
    <li class="nav-item {{ Request::is('fields*') ? 'active' : '' }}">
        <a href="{{ route('fields.index') }}"><i class="fa fa-globe" aria-hidden="true"></i><span class="menu-title">Fields</span></a>
    </li>
    @endif

    {{-- ZONES --}}
    @if(in_array('zones.index', $userPermissions))
    <li class="nav-item {{ Request::is('zones*') ? 'active' : '' }}">
        <a href="{{ route('zones.index') }}"><i class="fa fa-flag" aria-hidden="true"></i><span class="menu-title">Zones</span></a>
    </li>
    @endif

    {{-- CHAPTERS --}}
    @if(in_array('chapters.index', $userPermissions))
    <li class="nav-item {{ Request::is('chapters*') ? 'active' : '' }}">
        <a href="{{ route('chapters.index') }}"><i class="fa fa-thumb-tack" aria-hidden="true"></i><span class="menu-title">Chapters</span></a>
    </li>
    @endif

    {{-- EMAILS --}}
    @if(in_array('useremails.index', $userPermissions))
    <li class="nav-item {{ Request::is('useremails') ? 'active' : '' }}">
        <a href="{{ route('useremails.index') }}"><i class="fa fa-envelope"></i><span class="menu-title">Emails</span></a>
    </li>
    @endif

    {{-- LOGGED EMAILS --}}
    @if(in_array('criticalEmail.index', $userPermissions))
    <li class="nav-item">
        <a class="nav-link {{ Request::is('criticalemails*') ? 'active' : '' }}" href="{{ route('criticalEmail.index') }}">
            <i class="fa fa-envelope"></i><span class="menu-title">Logged Emails</span>
        </a>
    </li>
    @endif
@endif

<li class="nav-item {{ Request::is('profile*') ? 'active' : '' }}">
<a href="{{ route('users.show', auth()->user()->id) }}"><i class="fa fa-user-circle-o"></i><span class="menu-title"
data-i18n="Kanban">My Profile</span></a>
</li>

