@extends('frontend.site.layout')

@section('title', "Our Story — GOFAMINT Students' Fellowship")
@section('description', 'Discover the history, purpose and growth of GOFAMINT Students’ Fellowship, from its beginnings at the University of Ife in 1982 to its campus communities today.')
@section('mainClass', 'public-page gsf-story')
@section('activeSection', 'about')

@section('content')
<section class="inner-hero inner-hero--story">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ route('home.index') }}">Home</a><span>·</span><span>About GSF</span><span>·</span><span>Our Story</span>
            </nav>
            <div class="eyebrow eyebrow--light"><span></span> Our Story · Since 1982</div>
            <h1>A fellowship shaped by <em>faith, purpose and students.</em></h1>
            <p>From a campus gathering at the University of Ife to a growing fellowship across Nigeria, GSF has helped generations of students grow spiritually, pursue academic excellence and serve with godly purpose.</p>
            <a class="button button--gold story-hero__link" href="#our-beginnings">Explore our story <span aria-hidden="true">↓</span></a>
        </div>
    </div>
</section>

<section class="story-overview" aria-label="GSF at a glance">
    <div class="site-container story-overview__grid">
        <div class="story-overview__lead">
            <div class="eyebrow"><span></span> Who We Are</div>
            <h2>The student ministry of GOFAMINT.</h2>
        </div>
        <p>GOFAMINT Students’ Fellowship is the students’ ministry of The Gospel Faith Mission International. Established in 1982, GSF serves students in secondary and tertiary institutions, helping them follow Christ in the classroom, on campus and throughout life.</p>
        <div class="story-stat"><strong>1982</strong><span>Founded with a heart for students</span></div>
        <div class="story-stat"><strong>~100</strong><span>Campus fellowships across Nigeria</span></div>
        <div class="story-stat"><strong>One mission</strong><span>Faith, learning and godly service</span></div>
    </div>
</section>

<section class="story-beginnings" id="our-beginnings">
    <div class="site-container story-beginnings__grid">
        <div class="story-beginnings__copy">
            <div class="eyebrow"><span></span> The Beginning</div>
            <h2>A campus fellowship with a national calling.</h2>
            <p>GSF began at the then University of Ife, now Obafemi Awolowo University, in Ile-Ife, Osun State, Nigeria. Its founding vision was to care for the spiritual life of GOFAMINT students while they studied in demanding academic environments.</p>
            <p>What started as a response to the needs of students grew into a fellowship committed to spiritual, academic and social development. Over the years, GSF has become a source of hope and community for students across many institutions.</p>
        </div>
        <aside class="story-origin-card" aria-label="GSF origins">
            <span class="story-origin-card__year">1982</span>
            <span class="story-origin-card__rule" aria-hidden="true"></span>
            <strong>University of Ife</strong>
            <p>Now Obafemi Awolowo University, Ile-Ife, Osun State.</p>
            <span class="story-origin-card__caption">Where the fellowship began</span>
        </aside>
    </div>
</section>

<section class="story-purpose">
    <div class="site-container">
        <header class="story-section-heading">
            <div><div class="eyebrow"><span></span> Our Mission</div><h2>Forming the whole student.</h2></div>
            <p>From the beginning, the Fellowship set out to care for students’ spiritual lives, open paths to academic success and build relationships shaped by biblical values.</p>
        </header>
        <div class="story-purpose__grid">
            <article class="story-purpose-card story-purpose-card--blue"><span>01</span><div><small>Spiritual life</small><h3>Guide students in faith</h3></div><p>To guide the spiritual affairs of GOFAMINT students and help them remain rooted in Christ amid the pressures of academic life.</p></article>
            <article class="story-purpose-card"><span>02</span><div><small>Academic growth</small><h3>Advance learning</h3></div><p>To strengthen students’ academic development and encourage them to pursue excellence in secondary and tertiary institutions in Nigeria.</p></article>
            <article class="story-purpose-card story-purpose-card--gold"><span>03</span><div><small>Excellence</small><h3>Encourage achievement</h3></div><p>To facilitate academic excellence among Christian students through encouragement, support and a culture of diligence.</p></article>
            <article class="story-purpose-card"><span>04</span><div><small>Relationships</small><h3>Build godly community</h3></div><p>To foster social relationships guided by biblical principles and support healthy interpersonal, professional and marital relationships.</p></article>
        </div>
    </div>
</section>

<section class="story-reach">
    <div class="site-container story-reach__grid">
        <div class="story-reach__copy">
            <div class="eyebrow eyebrow--light"><span></span> Growing Together</div>
            <h2>A fellowship reaching campuses across Nigeria.</h2>
            <p>GSF now serves almost 100 campuses in Nigerian tertiary institutions. Its community connects students across institutions and generations, and the Fellowship has, by God’s grace, produced thousands of graduates who contribute in many fields of endeavour.</p>
        </div>
        <div class="story-reach__address">
            <span class="story-reach__icon" aria-hidden="true">⌖</span>
            <small>National Secretariat</small>
            <strong>GOFAMINT International Office</strong>
            <p>KM 40, Lagos–Ibadan Expressway<br>Aseese, Ogun State, Nigeria</p>
        </div>
    </div>
</section>

<section class="story-presidents">
    <div class="site-container">
        <header class="story-section-heading story-section-heading--presidents">
            <div><div class="eyebrow"><span></span> A Legacy of Leadership</div><h2>National Presidents.</h2></div>
            <p>Across the Fellowship’s growth, these leaders have served as National President, helping shape GSF’s life and work.</p>
        </header>
        <ol class="story-presidents__list">
            <li><span>01</span><strong>Evang. Z.I. Ilori</strong></li>
            <li><span>02</span><strong>Pastor D.A. Adekunle</strong></li>
            <li><span>03</span><strong>Pastor (Engr.) Tunde Ojo</strong></li>
            <li><span>04</span><strong>Pastor Akinola Oladele</strong></li>
            <li><span>05</span><strong>Pastor Richard Ologbonyo</strong></li>
            <li><span>06</span><strong>Pastor Prof. Moses Aderibigbe</strong></li>
            <li><span>07</span><strong>Pastor (Dr.) Gbenga Ayannuga</strong></li>
            <li><span>08</span><strong>Pastor Daniel Oyela</strong></li>
            <li><span>09</span><strong>Pastor (Dr.) Seun Ajileye</strong></li>
            <li><span>10</span><strong>Pastor Tunde Sebanjo</strong></li>
            <li><span>11</span><strong>Pastor Taiwo Salau</strong></li>
            <li><span>12</span><strong>Pastor Kayode Adegbesan</strong></li>
            <li><span>12</span><strong>Pastor Dr. Olayemi Olanegan</strong></li>
        </ol>
    </div>
</section>

<section class="story-timeline">
    <div class="site-container">
        <header class="story-section-heading">
            <div><div class="eyebrow eyebrow--light"><span></span> A Developing Ministry</div><h2>Growing with the Fellowship.</h2></div>
            <p>As GSF expanded, GOFAMINT and Fellowship leaders adapted its coordination and structure to support a growing national ministry.</p>
        </header>
        <div class="story-timeline__list">
            <article><span class="story-timeline__year">1987</span><div><h3>A National Coordinating Minister appointed</h3><p>Evangelist R.O. Usenu was appointed National Coordinating Minister (NCM), coordinating the Fellowship’s activities for 17 years.</p></div></article>
            <article><span class="story-timeline__year">2004</span><div><h3>A new season of coordination</h3><p>Pastor Richard Ologbonyo succeeded Evangelist Usenu. After 13 years of service, he voluntarily stepped aside to pursue a new mandate assigned to him.</p></div></article>
            <article><span class="story-timeline__year">2017</span><div><h3>A renewed name and structure</h3><p>At GSF’s 17th Biennial Conference, Gospel Students’ Fellowship was rebranded as Gofamint Students’ Fellowship to reflect the Mission’s widely recognised name. The Fellowship also reorganised its administrative structure as it marked 35 years.</p><p>The office of Head of Youth and GSF was created, and Pastor (Eng.) Tunde Ojo—formerly a National President of GSF and a District and Regional Pastor in GOFAMINT—was appointed to coordinate both ministries. The National Coordinating Minister role became National Campus Pastor (NCP); Pastor Prof. Aderibigbe Moses was appointed to lead the Fellowship alongside Field Pastors and the National Executive Committee (NEC).</p></div></article>
        </div>
    </div>
</section>

<section class="story-future">
    <div class="site-container story-future__card">
        <span class="story-future__mark" aria-hidden="true">GSF</span>
        <div><div class="eyebrow eyebrow--light"><span></span> Still Moving Forward</div><h2>Faithful to our calling. Ready for what’s next.</h2><p>We have challenges, but we continue to make steady and determined progress. With almost 100 chapters across Nigerian institutions, we trust God for greater opportunities in Nigeria and beyond.</p></div>
        <div class="story-future__links"><a class="button button--gold" href="{{ route('about.foundational-concept') }}">Explore the Seven Rs <span aria-hidden="true">→</span></a><a class="button button--outline-light" href="{{ route('about.mission-strategies') }}">Our mission strategies <span aria-hidden="true">→</span></a></div>
    </div>
</section>
@endsection
