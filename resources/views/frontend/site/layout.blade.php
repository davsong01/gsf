<!doctype html>
<html lang="en">
@php
    $siteLogoPath = \App\Models\GeneralSetting::query()->value('site_logo');
    $siteLogoUrl = !$siteLogoPath
        ? asset('frontend/img/logo.png')
        : (filter_var($siteLogoPath, FILTER_VALIDATE_URL)
            ? $siteLogoPath
            : \Illuminate\Support\Facades\Storage::disk('public')->url($siteLogoPath));
    $siteFaviconPath = \App\Models\GeneralSetting::query()->value('site_favicon');
    $siteFaviconUrl = !$siteFaviconPath
        ? $siteLogoUrl
        : (filter_var($siteFaviconPath, FILTER_VALIDATE_URL)
            ? $siteFaviconPath
            : (str_starts_with($siteFaviconPath, 'uploads/')
                ? asset($siteFaviconPath)
                : \Illuminate\Support\Facades\Storage::disk('public')->url($siteFaviconPath)));
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b2452">
    <meta name="description" content="{{ trim($__env->yieldContent('description')) ?: "GOFAMINT Students' Fellowship — a connected community of students growing in faith, leadership and service." }}">
    <link rel="icon" href="{{ $siteFaviconUrl }}">
    <link rel="stylesheet" href="{{ asset('site/css/gsf-site.css') }}">
    <title>@yield('title', "GOFAMINT Students' Fellowship")</title>
    @stack('head')
</head>
<body>
    @include('frontend.site.partials.header', ['activeSection' => trim($__env->yieldContent('activeSection')) ?: 'home'])

    <main id="main-content" class="@yield('mainClass')">
        @yield('content')
    </main>

    @unless(trim($__env->yieldContent('hideDirectoryNetwork')) === '1')
        @include('frontend.site.partials.directory-network')
    @endunless
    @include('frontend.site.partials.footer')
    @include('frontend.site.partials.privacy-notice')

    <script src="{{ asset('site/js/gsf-site.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
