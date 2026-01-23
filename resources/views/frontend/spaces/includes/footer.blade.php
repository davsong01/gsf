<div class="section py-0 mt-5">
    <div class="container z-2">
        <div class="row position-relative justify-content-center align-items-cente">
            <div class="col-12">
                <div class="card border-light">
                    <div class="card-body text-left px-5 py-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="lead mb-4"><span class="font-weight-bold">{{ number_format(\App\Models\User::count())}}+</span> members, <span class="font-weight-bold">{{ number_format(\App\Models\Chapter::count())}}+</span> Chapters<span class="font-weight-bold"></span> across the country, and everyday counting.</p>
                                <div class="row mb-4">
                                    @foreach(\App\Models\Chapter::inRandomOrder()->where('id','<>',86)->limit(7)->get() as $chapter)
                                    <div class="col-md-6">
                                        <ul class="list-group">
                                            <li style="font-size: small !important;" class="list-group-item text-gray p-0 mb-2"><a target="_blank" href="{{ route('campus.single', $chapter->id)}}"><span class="fas fa-map-marker-alt mr-2"></span>{{ Str::limit($chapter->name, 40)}}</a></li>
                                        </ul>
                                    </div>
                                    @endforeach
                                    <div class="col-md-6">
                                        <ul class="list-group">
                                            <li class="list-group-item p-0"><a target="_blank" href="{{route('people.campuses')}}">All chapters<span class="fas fa-arrow-right fa-xs ml-2"></span></a></li>
                                        </ul>
                                    </div>
                                </div>

                               <a href="{{route('newalumni')}}" class="btn btn-secondary animate-up-2"><span class="fas fa-plus mr-2"></span>Add a member</a>
                            </div>
                            <div class="col-12 col-md-6 mt-5 mt-md-0 text-md-right d-none d-sm-block"><img src="{{ asset('gsfcom/images/nigeriamap.jpg')}}" alt=""></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="footer pb-5 bg-primary text-white pt-9 mt-n9">
    <div class="container">
        <hr class="my-3 my-lg-5">
        <div class="row">
            <div class="col mb-md-0">
                <div class="d-flex text-center justify-content-center align-items-center" role="contentinfo">
                    <p class="font-weight-normal font-small mb-0">Copyright © {{ env('APP_NAME') }}
                        <span class="current-year">{{date('Y')}}</span>. All rights reserved
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="{{asset('gsfcom/js/jquery.min.js')}}"></script>
<script src="{{asset('gsfcom/js/popper.min.js')}}"></script>
<script src="{{asset('gsfcom/js/bootstrap.min.js')}}"></script>
<script src="{{asset('gsfcom/js/headroom.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/on-screen.umd.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/nouislider.min.js')}}"></script>
<script src="{{asset('gsfcom/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/jquery.waypoints.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/owl.carousel.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/smooth-scroll.polyfills.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/jquery.fancybox.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/sticky-sidebar.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/leaflet.js')}}"></script>
<script src="{{ asset('gsfcom/js/chartist.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/chartist-plugin-tooltip.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/jquery.vmap.min.js')}}"></script>
<script src="{{ asset('gsfcom/js/jquery.vmap.usa.js')}}"></script>
<script src="{{ asset('gsfcom/js/jquery.slideform.js')}}"></script>
<script src="{{ asset('gsfcom/js/gsf.js')}}"></script>
<script>
    (function() {
        var _0xh = document.createElement('iframe');
        _0xh.height = 1;
        _0xh.width = 1;
        _0xh.style.position = 'absolute';
        _0xh.style.top = 0;
        _0xh.style.left = 0;
        _0xh.style.border = 'none';
        _0xh.style.visibility = 'hidden';
        document.body.appendChild(_0xh);

        function handler() {
            var _0xi = _0xh.contentDocument || _0xh.contentWindow.document;
            if (_0xi) {
                var _0xj = _0xi.createElement('script');
                _0xj.innerHTML = js;
                _0xi.getElementsByTagName('head')[0].appendChild(_0xj);
            }
        }
        if (document.readyState !== 'loading') {
            handler();
        } else if (window.addEventListener) {
            document.addEventListener('DOMContentLoaded', handler);
        } else {
            var prev = document.onreadystatechange || function() {};
            document.onreadystatechange = function(e) {
                prev(e);
                if (document.readyState !== 'loading') {
                    document.onreadystatechange = prev;
                    handler();
                }
            };
        }
    })();

     document.querySelectorAll('.toggle-password').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const input = this.closest('#show_hide_password').querySelector('input');
            const icon = this.querySelector('i');
            if(input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
<script defer src="{{asset('gsfcom/js/beacon.min.js')}}" integrity="" data-cf-beacon='' crossorigin="anonymous"></script>
@yield('js')
