@extends('frontend.site.layout')

@section('title', "GSF Foundational Concept — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page')
@section('activeSection', 'about')

@section('content')
<section class="inner-hero">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>About GSF</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> GSF Foundational Concept</div>
            <h1>The foundation behind <em>who we are.</em></h1>
            <p>GSF is committed to a clear seven-part mandate: reaching students, retaining them in truth, reviving spiritual life, strengthening academic purpose, redirecting zeal, preparing saints and raising a generation for ministry.</p>
        </div>
    </div>
</section>

<section class="content-section">
    <div class="site-container content-intro">
        <div><div class="eyebrow"><span></span> Our Shared Mandate</div><h2>Seven commitments shaping one generation.</h2></div>
        <div class="content-intro__body">
            <p>To fulfil her mandate, GOFAMINT Students’ Fellowship is committed to the spiritual, academic and leadership development of students in higher institutions and secondary schools.</p>
            <p>These foundational concepts—often described as the <strong>Seven Rs</strong>—express how GSF reaches students with the gospel, walks with them through campus life and prepares them for lifelong service.</p>
        </div>
    </div>
</section>

<section class="concept-section concept-section--seven-rs">
    <div class="site-container">
        <div class="seven-rs-heading">
            <span class="seven-rs-heading__mark">7R</span>
            <div><div class="eyebrow"><span></span> The Foundational Concepts</div><h2>The Seven Rs of GSF</h2><p>A complete ministry journey—from encountering the saving love of God to becoming equipped for the work of ministry and the Great Commission.</p></div>
        </div>

        <div class="seven-rs-grid">
            <article class="foundation-r foundation-r--primary">
                <div class="foundation-r__top"><span>01</span><strong>R</strong></div>
                <small>Evangelism</small>
                <h2>Reaching out</h2>
                <p>Taking the message of eternal salvation to students and staff who are yet to experience the saving grace and love of God in our higher institutions and secondary schools.</p>
            </article>

            <article class="foundation-r">
                <div class="foundation-r__top"><span>02</span><strong>R</strong></div>
                <small>Preservation</small>
                <h2>Retaining</h2>
                <p>Keeping GOFAMINT students—and other truth-loving, holiness-oriented students—firmly grounded in a reckless world, so they can navigate the ills and pressures of campus life without stumbling, straying, stinking or sinking.</p>
            </article>

            <article class="foundation-r">
                <div class="foundation-r__top"><span>03</span><strong>R</strong></div>
                <small>Restoration</small>
                <h2>Reviving</h2>
                <p>Awakening lukewarm Christian students, restoring those who are straying, calling the erring to order, and re-orienting students who have become spiritually or academically disoriented.</p>
            </article>

            <article class="foundation-r foundation-r--image" aria-label="Students growing through worship and fellowship">
                <img src="{{ asset('main/images/bg3.jpg') }}" alt="A gathering of people in worship">
                <div><span>Spiritually alive</span><strong>Grounded for campus. Prepared for life.</strong></div>
            </article>

            <article class="foundation-r foundation-r--gold">
                <div class="foundation-r__top"><span>04</span><strong>R</strong></div>
                <small>Academic recovery</small>
                <h2>Redeeming</h2>
                <p>Helping Christian students rise from academic “Marah” and the mire of failure—providing a bridge of hope, discipline and support toward the promised land of meaningful academic success.</p>
            </article>

            <article class="foundation-r">
                <div class="foundation-r__top"><span>05</span><strong>R</strong></div>
                <small>Purpose</small>
                <h2>Redirecting</h2>
                <p>Channelling the zeal, enthusiasm and efforts of students toward noble goals, the Great Commission and active participation in the life and mission of the church.</p>
            </article>

            <article class="foundation-r">
                <div class="foundation-r__top"><span>06</span><strong>R</strong></div>
                <small>Preparation</small>
                <h2>Rationally preparing</h2>
                <p>Intentionally developing campus saints and rightly integrating them into living local churches as spiritually mature, responsible and useful members.</p>
            </article>

            <article class="foundation-r foundation-r--wide">
                <div class="foundation-r__top"><span>07</span><strong>R</strong></div>
                <small>Ministry formation</small>
                <h2>Raising</h2>
                <p>Raising a generation of firebrand saints prepared for the work of ministry—people who promote heavenly values, resist the forces of darkness and serve faithfully as part of the Great Commission army.</p>
            </article>
        </div>
    </div>
</section>

<section class="foundation-practice">
    <div class="site-container foundation-practice__grid">
        <div class="foundation-practice__visual">
            <img src="{{ asset('frontend/img/sliders/slider2.jpg') }}" alt="A student taking notes during a learning session">
            <span>Faith that engages the whole student</span>
        </div>
        <div class="foundation-practice__copy">
            <div class="eyebrow"><span></span> The Whole Student</div>
            <h2>Spiritual depth, academic purpose and responsible service.</h2>
            <p>The foundational concepts recognise that campus life brings spiritual, moral, relational and academic pressures. GSF therefore seeks to form students comprehensively—not merely gathering them for meetings, but helping them mature in every area of life.</p>
            <div class="foundation-practice__points">
                <p><span>01</span><strong>Know Christ</strong>Experience salvation and grow in holiness.</p>
                <p><span>02</span><strong>Stand firm</strong>Navigate campus pressures without losing conviction.</p>
                <p><span>03</span><strong>Pursue excellence</strong>Approach academic life with hope, discipline and purpose.</p>
                <p><span>04</span><strong>Serve faithfully</strong>Become useful in the local church, ministry and society.</p>
            </div>
        </div>
    </div>
</section>

<section class="relationship-section">
    <div class="site-container relationship-grid">
        <div class="relationship-mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF logo"></div>
        <div><div class="eyebrow eyebrow--light"><span></span> Our Ministry Family</div><h2>Rooted within GOFAMINT. Focused on students.</h2><p>GSF carries the spiritual heritage and ministry accountability of The Gospel Faith Mission International while responding intentionally to the opportunities and realities of student life.</p><a class="text-link text-link--light" href="https://www.gofamint.org/" target="_blank" rel="noopener">Visit GOFAMINT <span aria-hidden="true">↗</span></a></div>
    </div>
</section>

<section class="page-next">
    <div class="site-container page-next__card">
        <div><span>Continue exploring</span><h2>See how the foundation becomes action.</h2></div>
        <a class="button button--blue" href="{{ route('about.mission-strategies') }}">Our mission strategies <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection
