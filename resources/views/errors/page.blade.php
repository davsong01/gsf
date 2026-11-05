@push('head')
<style>
        .gsf-error-screen { color-scheme:light; --navy:#0b2452; --blue:#3159a5; --teal:#1177ad; --gold:#d9b963; --ink:#111d3e; --muted:#66728b; }
        .gsf-error-screen *, .gsf-error-screen *::before, .gsf-error-screen *::after { box-sizing:border-box; }
        .gsf-error-screen { display:grid; min-height:68vh; place-items:center; padding:48px 20px; overflow:hidden; color:var(--ink); background:radial-gradient(ellipse at 8% 4%,rgba(49,89,165,.1),transparent 34%),radial-gradient(ellipse at 96% 92%,rgba(17,119,173,.1),transparent 35%),linear-gradient(145deg,#f8faff,#edf3f8); font-family:"Avenir Next",Avenir,Inter,"Segoe UI",Roboto,Helvetica,Arial,sans-serif; -webkit-font-smoothing:antialiased; }
        .gsf-error-screen a { color:inherit; text-decoration:none; }
        .error-shell { width:min(100%,980px); }
        .error-card { position:relative; display:grid; min-height:485px; grid-template-columns:minmax(0,1fr) 290px; overflow:hidden; border:1px solid rgba(255,255,255,.9); border-radius:30px; background:rgba(255,255,255,.82); box-shadow:0 28px 85px rgba(19,43,78,.13),0 2px 8px rgba(19,43,78,.04); backdrop-filter:blur(18px); }
        .error-card__copy { position:relative; z-index:1; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; padding:58px clamp(28px,6vw,72px); }
        .error-label { display:inline-flex; align-items:center; gap:8px; padding:7px 11px; border:1px solid rgba(49,89,165,.14); border-radius:999px; color:#385991; background:#f1f5fb; font-size:10px; font-weight:850; letter-spacing:.12em; text-transform:uppercase; }
        .error-label__dot { width:7px; height:7px; border-radius:50%; background:var(--accent,#3159a5); box-shadow:0 0 0 4px color-mix(in srgb,var(--accent,#3159a5) 14%,transparent); }
        .error-code { margin:23px 0 0; color:var(--accent,#3159a5); font-size:clamp(76px,12vw,132px); font-weight:850; letter-spacing:-.09em; line-height:.9; }
        .error-card h1 { margin:15px 0 10px; color:var(--ink); font-size:clamp(25px,4vw,35px); font-weight:800; letter-spacing:-.045em; line-height:1.15; }
        .error-card__message { max-width:470px; margin:0; color:var(--muted); font-size:15px; line-height:1.75; }
        .error-actions { display:flex; flex-wrap:wrap; gap:10px; margin-top:27px; }
        .error-button { display:inline-flex; min-height:46px; align-items:center; justify-content:center; gap:9px; padding:0 18px; border:1px solid #dce4ee; border-radius:12px; color:#3d4e68; background:#fff; font-size:13px; font-weight:800; transition:transform .18s ease,box-shadow .18s ease,background .18s ease; }
        .error-button:hover { transform:translateY(-2px); box-shadow:0 9px 20px rgba(20,46,81,.11); }
        .error-button--primary { border-color:transparent; color:#fff; background:linear-gradient(120deg,var(--blue),var(--teal)); box-shadow:0 8px 18px rgba(49,89,165,.2); }
        .error-button--primary:hover { color:#fff; background:linear-gradient(120deg,#284e95,#0e6b9e); }
        .error-button__icon { font-size:16px; line-height:1; }
        .error-art { position:relative; display:grid; min-height:100%; place-items:center; overflow:hidden; padding:30px; color:#fff; background:radial-gradient(circle at 78% 20%,rgba(217,185,99,.24),transparent 24%),linear-gradient(150deg,#102f67,#0b2452 55%,#0a4f73); }
        .error-art::before,.error-art::after { position:absolute; width:290px; height:290px; border:1px solid rgba(255,255,255,.18); border-radius:50%; content:""; }
        .error-art::before { right:-112px; top:-110px; box-shadow:0 0 0 27px rgba(255,255,255,.035),0 0 0 57px rgba(255,255,255,.025); }
        .error-art::after { left:-206px; bottom:-202px; width:360px; height:360px; border-color:rgba(217,185,99,.3); }
        .error-art__content { position:relative; z-index:1; width:100%; text-align:center; }
        .error-art__mark { display:grid; width:78px; height:78px; place-items:center; margin:0 auto 23px; border:1px solid rgba(255,255,255,.25); border-radius:25px; color:#f2d98e; background:rgba(255,255,255,.09); box-shadow:0 12px 30px rgba(0,0,0,.12); font-size:35px; }
        .error-art__code { display:block; color:rgba(255,255,255,.96); font-size:50px; font-weight:850; letter-spacing:-.06em; }
        .error-art__rule { display:block; width:48px; height:3px; margin:14px auto; border-radius:4px; background:var(--gold); }
        .error-art__caption { display:block; color:rgba(255,255,255,.72); font-size:10px; font-weight:800; letter-spacing:.16em; text-transform:uppercase; }
        .error-footer { margin:15px 8px 0; color:#8a95a8; font-size:10px; }
        @media(max-width:700px) { .gsf-error-screen { min-height:60vh; padding:30px 14px; } .error-card { min-height:0; grid-template-columns:1fr; border-radius:23px; } .error-card__copy { padding:35px 25px 31px; } .error-art { min-height:160px; padding:20px; } .error-art__mark { width:54px; height:54px; margin-bottom:10px; border-radius:17px; font-size:25px; } .error-art__code { font-size:30px; } .error-art__rule { margin:8px auto; } .error-art__caption { font-size:8px; } .error-footer { margin-left:4px; } }
        @media(prefers-reduced-motion:reduce) { .gsf-error-screen *, .gsf-error-screen *::before, .gsf-error-screen *::after { scroll-behavior:auto!important; transition:none!important; } }
    </style>
@endpush

<div class="gsf-error-screen">
    @php
        $stakeholderRequest = request()->is('stakeholders*');
        $homeUrl = $stakeholderRequest && \Illuminate\Support\Facades\Route::has('stakeholders.loginpage')
            ? route('stakeholders.loginpage')
            : url('/');
        $backUrl = url()->previous();
        $resetUrl = \Illuminate\Support\Facades\Route::has('session.reset')
            ? route('session.reset', ['scope' => $stakeholderRequest ? 'stakeholder' : 'web'])
            : $homeUrl;
    @endphp
    <div class="error-shell" style="--accent:{{ $accent ?? '#3159a5' }}">
        <section class="error-card" aria-labelledby="error-title">
            <div class="error-card__copy">
                <span class="error-label"><i class="error-label__dot" aria-hidden="true"></i>{{ $eyebrow ?? 'A little detour' }}</span>
                <div class="error-code" aria-label="Error {{ $code }}">{{ $code }}</div>
                <h1 id="error-title">{{ $title }}</h1>
                <p class="error-card__message">{{ $message }}</p>
                <div class="error-actions">
                    @if($showReset ?? false)
                        <a class="error-button error-button--primary" href="{{ $resetUrl }}"><span class="error-button__icon" aria-hidden="true">↻</span>Start a fresh session</a>
                    @endif
                    <a class="error-button" href="{{ $homeUrl }}"><span class="error-button__icon" aria-hidden="true">⌂</span>{{ $homeLabel ?? 'Go to homepage' }}</a>
                    @if(!empty($showLogin) && \Illuminate\Support\Facades\Route::has('login'))
                        <a class="error-button" href="{{ route('login') }}">Sign in</a>
                    @else
                        <a class="error-button" href="{{ $backUrl }}">Go back</a>
                    @endif
                </div>
            </div>
            <aside class="error-art" aria-hidden="true">
                <div class="error-art__content">
                    <span class="error-art__mark">{{ $symbol ?? '↗' }}</span>
                    <span class="error-art__code">{{ $code }}</span>
                    <span class="error-art__rule"></span>
                    <span class="error-art__caption">{{ $artCaption ?? 'We’ll help you find your way' }}</span>
                </div>
            </aside>
        </section>
        <p class="error-footer">If you believe this is a mistake, please try again or contact the GSF Secretariat.</p>
    </div>
</div>
