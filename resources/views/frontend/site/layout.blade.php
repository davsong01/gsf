<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b2452">
    <meta name="description" content="{{ trim($__env->yieldContent('description')) ?: "GOFAMINT Students' Fellowship — a connected community of students growing in faith, leadership and service." }}">
    <link rel="icon" type="image/png" href="{{ asset('frontend/img/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('site/css/gsf-site.css') }}">
    <title>@yield('title', "GOFAMINT Students' Fellowship")</title>
    @stack('head')
</head>
<body>
    @include('frontend.site.partials.header', ['activeSection' => trim($__env->yieldContent('activeSection')) ?: 'home'])

    <main id="main-content" class="@yield('mainClass')">
        @yield('content')
    </main>

    @include('frontend.site.partials.directory-network')
    @include('frontend.site.partials.footer')

    <script src="{{ asset('site/js/gsf-site.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
