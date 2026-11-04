(function () {
    'use strict';

    var header = document.querySelector('[data-site-header]');
    var menuToggle = document.querySelector('[data-menu-toggle]');
    var primaryNav = document.querySelector('[data-primary-nav]');
    var dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
    var heroSlider = document.querySelector('[data-hero-slider]');

    function closeDropdowns(except) {
        dropdownToggles.forEach(function (toggle) {
            if (toggle !== except) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.parentElement.classList.remove('is-open');
            }
        });
    }

    function closeMenu() {
        if (!menuToggle || !primaryNav) return;
        menuToggle.setAttribute('aria-expanded', 'false');
        primaryNav.classList.remove('is-open');
        document.body.classList.remove('nav-open');
        closeDropdowns();
    }

    if (menuToggle && primaryNav) {
        menuToggle.addEventListener('click', function () {
            var isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', String(!isOpen));
            primaryNav.classList.toggle('is-open', !isOpen);
            document.body.classList.toggle('nav-open', !isOpen);
            if (isOpen) closeDropdowns();
        });

        primaryNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });
    }

    dropdownToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            var isOpen = toggle.getAttribute('aria-expanded') === 'true';
            closeDropdowns(toggle);
            toggle.setAttribute('aria-expanded', String(!isOpen));
            toggle.parentElement.classList.toggle('is-open', !isOpen);
        });
    });

    document.addEventListener('click', function (event) {
        closeDropdowns();

        if (
            primaryNav &&
            menuToggle &&
            primaryNav.classList.contains('is-open') &&
            !primaryNav.contains(event.target) &&
            !menuToggle.contains(event.target)
        ) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            var wasOpen = primaryNav && primaryNav.classList.contains('is-open');
            closeMenu();
            if (wasOpen && menuToggle) menuToggle.focus();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 1020) closeMenu();
    });

    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function () {
            closeMenu();
        });
    });

    function updateHeader() {
        if (header) header.classList.toggle('is-scrolled', window.scrollY > 12);
    }

    updateHeader();
    window.addEventListener('scroll', updateHeader, { passive: true });

    if (heroSlider) {
        var slides = Array.prototype.slice.call(heroSlider.querySelectorAll('[data-hero-slide]'));
        var dots = Array.prototype.slice.call(heroSlider.querySelectorAll('[data-hero-dot]'));
        var previousButton = heroSlider.querySelector('[data-hero-prev]');
        var nextButton = heroSlider.querySelector('[data-hero-next]');
        var pauseButton = heroSlider.querySelector('[data-hero-pause]');
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var currentSlide = 0;
        var timer = null;
        var manuallyPaused = reducedMotion;
        var interactionPaused = false;
        var delay = 7000;

        function isPaused() {
            return manuallyPaused || interactionPaused || document.hidden;
        }

        function syncPauseState() {
            var paused = isPaused();
            heroSlider.classList.toggle('is-paused', paused);
            if (pauseButton) {
                pauseButton.setAttribute('aria-label', manuallyPaused ? 'Resume automatic slides' : 'Pause automatic slides');
            }
        }

        function scheduleNext() {
            window.clearTimeout(timer);
            syncPauseState();
            if (!isPaused() && slides.length > 1) {
                timer = window.setTimeout(function () { showSlide(currentSlide + 1); }, delay);
            }
        }

        function showSlide(index) {
            if (!slides.length) return;
            currentSlide = (index + slides.length) % slides.length;

            slides.forEach(function (slide, slideIndex) {
                var active = slideIndex === currentSlide;
                slide.classList.toggle('is-active', active);
                slide.setAttribute('aria-hidden', String(!active));
            });

            dots.forEach(function (dot, dotIndex) {
                var active = dotIndex === currentSlide;
                dot.classList.toggle('is-active', active);
                dot.setAttribute('aria-selected', String(active));
            });

            scheduleNext();
        }

        dots.forEach(function (dot, index) {
            dot.addEventListener('click', function () { showSlide(index); });
        });

        if (previousButton) previousButton.addEventListener('click', function () { showSlide(currentSlide - 1); });
        if (nextButton) nextButton.addEventListener('click', function () { showSlide(currentSlide + 1); });

        if (pauseButton) {
            pauseButton.addEventListener('click', function () {
                manuallyPaused = !manuallyPaused;
                scheduleNext();
            });
        }

        heroSlider.addEventListener('mouseenter', function () { interactionPaused = true; scheduleNext(); });
        heroSlider.addEventListener('mouseleave', function () { interactionPaused = false; scheduleNext(); });
        heroSlider.addEventListener('focusin', function () { interactionPaused = true; scheduleNext(); });
        heroSlider.addEventListener('focusout', function (event) {
            if (!heroSlider.contains(event.relatedTarget)) {
                interactionPaused = false;
                scheduleNext();
            }
        });
        heroSlider.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowLeft') showSlide(currentSlide - 1);
            if (event.key === 'ArrowRight') showSlide(currentSlide + 1);
        });
        document.addEventListener('visibilitychange', scheduleNext);

        showSlide(0);
    }
})();
