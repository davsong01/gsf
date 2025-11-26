<li class="nav-item {{ Request::is('account*') ? 'active' : '' }}"><a href="/account"><i class="fa fa-bars"></i><span class="menu-title" data-i18n="Kanban">Dashboard</span></a></li>
@if(auth()->user()->conference_role == 'superadmin')
    <li class="nav-item {{ Request::is('conferencemanagement.index*') ? 'open' : '' }}"><a href="#"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="Content">Conference Mgt</span></a>
        <ul class="menu-content">
            <li class="is_shown"><a href="{{ route('ministry.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Ministries</span></a>
            </li>
            <li class=" nav-item {{ Request::is('paymentproviders') ? 'active' : '' }}"><a href="{{ route('paymentproviders.index') }}"><i class="fa fa-money"></i><span class="menu-title">Payment Providers</span></a>
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


@if(env('MINISTRY') == 'gsf')
    @if(auth()->user()->conference_role == 'superadmin')
    <li class="nav-item {{ Request::is('stakeholder*') ? 'open' : '' }}"><a href="#"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="Content">Digital Reports</span></a>
        <ul class="menu-content">
            <li class="is_shown"><a href="{{ route('ministry.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Reports</span></a>
            </li>
            <li class="is_shown"><a href="{{ route('stakeholderpersonnel.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Stakeholders</span></a>
            </li>
            <li class="is_shown"><a href="{{ route('stakeholder.questions.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Report Sections</span></a>
            </li>
            <li class="is_shown"><a href="{{ route('stakeholder.questions.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Report Sub Sections</span></a>
            </li>
            <li class="is_shown"><a href="{{ route('stakeholder.questions.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Grid">Report Questions</span></a>
            </li>
        </ul>
    </li>

    <li class=" nav-item {{ Request::is('nec*') ? 'active' : '' }}"><a href="{{ route('nec.index') }}"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 1 224 0a128 128 0 1 1 0 256zM209.1 359.2l-18.6-31c-6.4-10.7 1.3-24.2 13.7-24.2H224h19.7c12.4 0 20.1 13.6 13.7 24.2l-18.6 31 33.4 123.9 36-146.9c2-8.1 9.8-13.4 17.9-11.3c70.1 17.6 121.9 81 121.9 156.4c0 17-13.8 30.7-30.7 30.7H285.5c-2.1 0-4-.4-5.8-1.1l.3 1.1H168l.3-1.1c-1.8 .7-3.8 1.1-5.8 1.1H30.7C13.8 512 0 498.2 0 481.3c0-75.5 51.9-138.9 121.9-156.4c8.1-2 15.9 3.3 17.9 11.3l36 146.9 33.4-123.9z"/></svg></i><span class="menu-title"> &nbsp; &nbsp; &nbsp; NEC Management</span></a></li>
    <li class=" nav-item {{ Request::is('archive-nec*') ? 'active' : '' }}"><a href="{{ route('archive.nec.index') }}"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 1 224 0a128 128 0 1 1 0 256zM209.1 359.2l-18.6-31c-6.4-10.7 1.3-24.2 13.7-24.2H224h19.7c12.4 0 20.1 13.6 13.7 24.2l-18.6 31 33.4 123.9 36-146.9c2-8.1 9.8-13.4 17.9-11.3c70.1 17.6 121.9 81 121.9 156.4c0 17-13.8 30.7-30.7 30.7H285.5c-2.1 0-4-.4-5.8-1.1l.3 1.1H168l.3-1.1c-1.8 .7-3.8 1.1-5.8 1.1H30.7C13.8 512 0 498.2 0 481.3c0-75.5 51.9-138.9 121.9-156.4c8.1-2 15.9 3.3 17.9 11.3l36 146.9 33.4-123.9z"/></svg></i><span class="menu-title"> &nbsp; &nbsp; &nbsp; Archive NEC Members</span></a></li>

    <li class=" nav-item {{ Request::is('users*') ? 'active' : '' }}"><a href="{{ route('users.index') }}"><i class="fa fa-user"></i><span class="menu-title">Users</span></a></li>
    <li class=" nav-item {{ Request::is('listing-pending*') ? 'active' : '' }}"><a href="{{ route('listing-pending') }}"><i class="fa fa-user"></i><span class="menu-title">Pending Listing</span></a></li>
    <li class="nav-item {{ Request::is('trashedusers') ? 'active' : '' }}"><a href="{{ route('users.trashed') }}"><i class="fa fa-trash-o"></i><span class="menu-title">Trashed Users</span></a></li>
    <li class="nav-item {{ Request::is('events*') ? 'active' : '' }}"><a href="{{ route('events.index') }}"><i class="fa fa-calendar" aria-hidden="true"></i><span class="menu-title">Events</span></a></li> 
    <li class="nav-item {{ Request::is('all-donations*') ? 'active' : '' }}"><a href="{{ route('donations.all') }}"><i class="fa fa-money" aria-hidden="true"></i><span class="menu-title">Donations</span></a></li> 
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
    @endif
@endif
