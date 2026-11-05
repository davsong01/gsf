<!DOCTYPE html>
<html lang="en">
@php
    $siteLogoPath = \App\Models\GeneralSetting::query()->value('site_logo');
    $siteLogoUrl = !$siteLogoPath
        ? asset('frontend/img/logo.png')
        : (filter_var($siteLogoPath, FILTER_VALIDATE_URL)
            ? $siteLogoPath
            : \Illuminate\Support\Facades\Storage::disk('public')->url($siteLogoPath));
@endphp
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
@include('frontend.spaces.includes.head')
<body class="directory-site-shell">
    @include('frontend.site.partials.header', ['activeSection' => 'directory'])
     <main id="main-content">
        <div class="preloader bg-dark flex-column justify-content-center align-items-center">
            <div class="position-relative">
                <img src="{{asset('frontend/img/logo.png')}}" class="rotate-letter" alt="Letter loader">
            </div>
        </div>
        @yield('content')
    </main>
    @include('frontend.spaces.includes.footer')
    @include('frontend.site.partials.privacy-notice')
</body>

</html>
