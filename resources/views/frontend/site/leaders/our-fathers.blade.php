@extends('frontend.site.layout')

@section('title', "Our Leaders — GOFAMINT Students' Fellowship")
@section('mainClass', 'public-page leadership-page')
@section('activeSection', 'leaders')

@section('content')
<section class="inner-hero inner-hero--leaders">
    <div class="inner-hero__pattern" aria-hidden="true"></div>
    <div class="site-container inner-hero__grid">
        <div class="inner-hero__copy">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><span>Meet Our Leaders</span></nav>
            <div class="eyebrow eyebrow--light"><span></span> Our Leaders</div>
            <h1>Honouring the leaders who provide <em>spiritual covering and direction.</em></h1>
            <p>GSF is strengthened by faithful leaders serving across GOFAMINT, Youth and Campus Ministries, and the national fellowship.</p>
        </div>
    </div>
</section>

<section class="leadership-intro">
    <div class="site-container leadership-intro__grid">
        <div>
            <div class="eyebrow"><span></span> A Heritage of Faithful Leadership</div>
            <h2>Guided by conviction, experience and a commitment to the next generation.</h2>
        </div>
        <div class="leadership-intro__copy">
            <p>Our leaders represent a connected line of leadership—from the wider GOFAMINT family to the leaders directly responsible for youth, campus ministry and GSF nationally.</p>
            <p>Together, they provide spiritual oversight, institutional continuity and direction for the Fellowship’s mission among students.</p>
        </div>
    </div>
</section>

<section class="fathers-section">
    <div class="site-container fathers-grid">
        <article class="father-card father-card--featured">
            <div class="father-card__portrait">
                <img src="{{ asset('site/images/leaders/e-o-abina.jpeg') }}" alt="Pastor Dr E. O. Abina">
                <span>GOFAMINT</span>
            </div>
            <div class="father-card__copy"><span>01</span><h2>Pastor (Dr) E.O. Abina</h2><p>The General Overseer, GOFAMINT</p></div>
        </article>

        <article class="father-card">
            <div class="father-card__portrait">
                <img src="{{ asset('site/images/leaders/tunde-ojo.jpg') }}" alt="Pastor Engineer Tunde Ojo">
                <span>Youth &amp; Campus</span>
            </div>
            <div class="father-card__copy"><span>02</span><h2>Pastor (Engr) Tunde Ojo</h2><p>Head, Youth and Campus Ministries</p></div>
        </article>

        <article class="father-card">
            <div class="father-card__portrait">
                <img src="{{ asset('site/images/leaders/joshua-taiwo-adebayo.jpg') }}" alt="Pastor Joshua Taiwo Olushola Adebayo">
                <span>GSF National</span>
            </div>
            <div class="father-card__copy"><span>03</span><h2>Pastor Joshua Taiwo Olushola Adebayo</h2><p>National Campus Pastor, GSF</p></div>
        </article>

        <article class="father-card">
            <div class="father-card__portrait">
                <img src="{{ asset('site/images/leaders/olayemi-olanegan.jpg') }}" alt="Pastor Olayemi Olanegan">
                <span>GSF National</span>
            </div>
            <div class="father-card__copy"><span>04</span><h2>Pastor Olayemi Olanegan</h2><p>National President, GSF</p></div>
        </article>
    </div>
</section>

<section class="leadership-continuum">
    <div class="site-container leadership-continuum__grid">
        <div><div class="eyebrow eyebrow--light"><span></span> Leadership Continuum</div><h2>One mission, stewarded across every level.</h2></div>
        <div class="leadership-continuum__points">
            <p><span>01</span><strong>Spiritual oversight</strong><em>Rooted in GOFAMINT’s doctrine, values and ministry direction.</em></p>
            <p><span>02</span><strong>Generational formation</strong><em>Preparing young people and students for faithful Christian influence.</em></p>
            <p><span>03</span><strong>National stewardship</strong><em>Translating the mission into coordinated leadership across GSF.</em></p>
        </div>
    </div>
</section>

<section class="page-next">
    <div class="site-container page-next__card">
        <div><span>Current national leadership</span><h2>Meet the team coordinating the work of GSF across the nation.</h2></div>
        <a class="button button--blue" href="{{ route('people.nec') }}">National Executive Committee <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection
