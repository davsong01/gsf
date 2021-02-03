<!DOCTYPE html>
<html lang="en">
    @include('includes.fheader')
    @include('includes.navbar')
    <body>
      
        <!-- Carousel Start -->
    
          @yield('sliders')
        <!-- Carousel End -->
        @yield('body')            

        @include('includes.ffooter')

        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

				<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- JavaScript Libraries -->

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('frontend/lib/easing/easing.min.js') }}"></script>
        <script src="{{ asset('frontend/lib/owlcarousel/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('frontend/lib/waypoints/waypoints.min.js') }}"></script>
        <script src="{{ asset('frontend/lib/counterup/counterup.min.js') }}"></script>
        
        <!-- Contact Javascript File -->
        <script src="{{ asset('frontend/mail/jqBootstrapValidation.min.js') }}"></script>
        <script src="{{ asset('frontend/mail/contact.js') }}"></script>

        <!-- Template Javascript -->
        <script src="{{ asset('frontend/js/main.js') }}"></script>

         @yield('extra-scripts')
    
    </body>
</html>
