<footer class="site-footer">
    <div class="site-container site-footer__grid">
        <div class="site-footer__brand">
            <a class="brand brand--footer" href="{{ route('home.index') }}">
                <img src="{{ asset('frontend/img/logo.png') }}" alt="">
                <span class="brand__copy"><strong>GOFAMINT Students’ Fellowship</strong><small>Growing faith. Forming leaders. Serving communities.</small></span>
            </a>
            <p>A connected fellowship community serving students and campuses within The Gospel Faith Mission International.</p>
        </div>
        <div>
            <h2>Explore</h2>
            <ul>
                <li><a href="{{ route('about.foundational-concept') }}">About GSF</a></li>
                <li><a href="{{ route('leaders.fathers') }}">Our Fathers</a></li>
                <li><a href="{{ route('people.nec') }}">National Executive Committee</a></li>
                <li><a href="{{ route('conferences.index') }}">GSF Conferences</a></li>
                <li><a href="{{ route('home.index') }}#news-events">News &amp; events</a></li>
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
        <span>Built by <a href="https://dembrix.com/" target="_blank" rel="noopener noreferrer">Dembrix</a></span>
    </div>
</footer>
