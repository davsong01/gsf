<a class="skip-link" href="#main-content">Skip to content</a>

<div class="affiliation-bar">
    <div class="site-container affiliation-bar__inner">
        <span>A corporate ministry of The Gospel Faith Mission International</span>
        <a href="https://www.gofamint.org/" target="_blank" rel="noopener">Visit GOFAMINT <span aria-hidden="true">↗</span></a>
    </div>
</div>

<header class="site-header" data-site-header>
    @php
        $stakeholderGuard = auth()->guard('stakeholder');
        $webGuard = auth()->guard('web');
        $portalIsAuthenticated = $stakeholderGuard->check() || $webGuard->check();
        $portalUrl = $stakeholderGuard->check()
            ? route('stakeholders.dashboard')
            : ($webGuard->check() ? route('account') : route('login'));
        $portalLabel = $portalIsAuthenticated ? 'My Account' : 'Member Portal';
    @endphp
    <div class="site-container site-header__inner">
        <a class="brand" href="{{ route('home.index') }}" aria-label="GSF homepage">
            <img src="{{ $siteLogoUrl }}" alt="GSF logo">
            <span class="brand__copy">
                <strong>GOFAMINT Students’ Fellowship</strong>
                <small>National Secretariat</small>
            </span>
        </a>

        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
            <span class="menu-toggle__label">Menu</span>
            <span class="menu-toggle__icon" aria-hidden="true"><i></i><i></i><i></i></span>
        </button>

        <nav class="primary-nav" id="primary-navigation" aria-label="Primary navigation" data-primary-nav>
            <ul>
                <li><a class="{{ ($activeSection ?? '') === 'home' ? 'is-active' : '' }}" href="{{ route('home.index') }}">Home</a></li>
                <li class="nav-dropdown">
                    <button class="{{ ($activeSection ?? '') === 'about' ? 'is-active' : '' }}" type="button" aria-expanded="false" data-dropdown-toggle>
                        About GSF <span aria-hidden="true">⌄</span>
                    </button>
                    <ul class="nav-dropdown__menu">
                        <li><a href="{{ route('about.mission-strategies') }}">GSF Mission Strategies</a></li>
                        <li><a href="{{ route('about.foundational-concept') }}">GSF Foundational Concept</a></li>
                        <li><a href="{{ route('about.contact') }}">Contact Us</a></li>
                    </ul>
                </li>
                <li class="nav-dropdown">
                    <button class="{{ ($activeSection ?? '') === 'leaders' ? 'is-active' : '' }}" type="button" aria-expanded="false" data-dropdown-toggle>
                        Our Leaders <span aria-hidden="true">⌄</span>
                    </button>
                    <ul class="nav-dropdown__menu">
                        <li><a href="{{ route('leaders.fathers') }}">Our Leaders</a></li>
                        <li><a href="{{ route('people.nec') }}">National Executive Committee</a></li>
                    </ul>
                </li>
                <li class="nav-dropdown">
                    <button class="{{ ($activeSection ?? '') === 'directory' ? 'is-active' : '' }}" type="button" aria-expanded="false" data-dropdown-toggle>
                        Directory <span aria-hidden="true">⌄</span>
                    </button>
                    <ul class="nav-dropdown__menu">
                        <li><a href="{{ route('directory.index') }}">Directory Home</a></li>
                        <li><a href="{{ route('people.campuses') }}">Campuses</a></li>
                        <li><a href="{{ route('people.alumni') }}">Alumni</a></li>
                        <li><a href="{{ route('people.students') }}">Members</a></li>
                        <li><a href="{{ route('campus.tracker') }}">Campus Tracker</a></li>
                    </ul>
                </li>
                <li class="nav-dropdown">
                    <button class="{{ ($activeSection ?? '') === 'awards' ? 'is-active' : '' }}" type="button" aria-expanded="false" data-dropdown-toggle>
                        Education Awards <span aria-hidden="true">⌄</span>
                    </button>
                    <ul class="nav-dropdown__menu">
                        <li><a href="{{ route('public.awards.etf') }}">Education Trust Fund (ETF)</a></li>
                        <li><a href="{{ route('public.awards.go') }}">GO’s Excellence Beneficiary Award</a></li>
                    </ul>
                </li>
                <li><a class="{{ ($activeSection ?? '') === 'conferences' ? 'is-active' : '' }}" href="{{ route('conferences.index') }}">Conferences</a></li>
                <li class="nav-dropdown">
                    <button class="{{ in_array($activeSection ?? '', ['news', 'events']) ? 'is-active' : '' }}" type="button" aria-expanded="false" data-dropdown-toggle>News &amp; Events <span aria-hidden="true">⌄</span></button>
                    <ul class="nav-dropdown__menu">
                        <li><a href="{{ route('blog.index') }}">News</a></li>
                        <li><a href="{{ route('events.public.index') }}">Programs and Events</a></li>
                    </ul>
                </li>
                <li class="primary-nav__theme">
                    <button class="theme-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false" data-theme-toggle>
                        <svg class="theme-toggle__moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.2 15.2A8.5 8.5 0 0 1 8.8 3.8 8.5 8.5 0 1 0 20.2 15.2Z"/></svg>
                        <svg class="theme-toggle__sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3.5"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                        <span class="theme-toggle__text" data-theme-label>Night mode</span>
                    </button>
                </li>
                <li class="primary-nav__portal"><a href="{{ $portalUrl }}">{{ $portalLabel }} @if(!$portalIsAuthenticated)<span aria-hidden="true">↗</span>@endif</a></li>
            </ul>
        </nav>
    </div>
</header>
