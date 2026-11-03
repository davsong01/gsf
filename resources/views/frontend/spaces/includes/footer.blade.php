@include('frontend.site.partials.directory-network')
@include('frontend.site.partials.footer')

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
<script src="{{ asset('site/js/gsf-site.js') }}" defer></script>
<script>
    document.querySelectorAll('.toggle-password').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            var input = this.closest('#show_hide_password').querySelector('input');
            var icon = this.querySelector('i');

            if (input.type === 'password') {
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
@yield('js')
