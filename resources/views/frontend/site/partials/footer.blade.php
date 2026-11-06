<footer class="site-footer">
    <div class="site-container site-footer__grid">
        <div class="site-footer__brand">
            <a class="brand brand--footer" href="{{ route('home.index') }}">
                <img src="{{ $siteLogoUrl }}" alt="">
                <span class="brand__copy"><strong>GOFAMINT Students’ Fellowship</strong><small>Raising Godly Gems.</small></span>
            </a>
            <p>The Student Ministry of The Gospel Faith Mission International (GOFAMINT) is dedicated to nurturing and empowering students nationwide—helping them deepen their faith, discover their purpose, build lifelong relationships, and equip themselves to impact the world.</p>
        </div>
        <div>
            <h2>Explore</h2>
            <ul>
                <li><a href="{{ route('about.our-story') }}">Our Story</a></li>
                <li><a href="{{ route('about.foundational-concept') }}">About GSF</a></li>
                <li><a href="{{ route('conferences.index') }}">GSF Conferences</a></li>
                <li><a href="{{ route('blog.index') }}">News</a></li>
                <li><a href="{{ route('events.public.index') }}">Programs &amp; Events</a></li>
                <li><a href="{{ route('about.contact') }}">Contact Us</a></li>
                <li><a href="{{ route('site.privacy') }}">Privacy</a></li>
                <li><a href="{{ route('site.terms') }}">Terms &amp; Conditions</a></li>
            </ul>
        </div>
        <div>
            <h2>Directory</h2>
            <ul>
                <li><a href="{{ route('people.campuses') }}">Campuses</a></li>
                <li><a href="{{ route('people.alumni') }}">Alumni</a></li>
                <li><a href="{{ route('people.students') }}">Members</a></li>
                <li><a href="{{ route('campus.tracker') }}">Campus Tracker</a></li>
                <li><a href="{{ route('stakeholders.loginpage') }}">Stakeholder Login</a></li>
            </ul>
        </div>
        <div class="site-footer__action">
            <h2>Already a member?</h2>
            <p>Access reports, appraisals and other member services from the portal.</p>
            <a class="text-link text-link--light" href="{{ route('login') }}">Open member portal <span aria-hidden="true">→</span></a>
        </div>
    </div>
    <div class="site-container site-footer__bottom">
        <span>© {{ date('Y') }} GOFAMINT Students’ Fellowship</span>
        <span>A ministry of GOFAMINT</span>
        <span class="site-footer__legal"><button type="button" data-privacy-open>Privacy notice</button></span>
        <span>Built by <a href="https://dembrix.com/" target="_blank" rel="noopener noreferrer">Dembrix</a></span>
    </div>
</footer>
