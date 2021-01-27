<!DOCTYPE html>
<html lang="en">
    <?php echo $__env->make('includes.fheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <body>
      
        <!-- Carousel Start -->
    
          <?php echo $__env->yieldContent('sliders'); ?>
        <!-- Carousel End -->
        <?php echo $__env->yieldContent('body'); ?>            

        <?php echo $__env->make('includes.ffooter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
        <!-- JavaScript Libraries -->

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo e(asset('frontend/lib/easing/easing.min.js')); ?>"></script>
        <script src="<?php echo e(asset('frontend/lib/owlcarousel/owl.carousel.min.js')); ?>"></script>
        <script src="<?php echo e(asset('frontend/lib/waypoints/waypoints.min.js')); ?>"></script>
        <script src="<?php echo e(asset('frontend/lib/counterup/counterup.min.js')); ?>"></script>
        
        <!-- Contact Javascript File -->
        <script src="<?php echo e(asset('frontend/mail/jqBootstrapValidation.min.js')); ?>"></script>
        <script src="<?php echo e(asset('frontend/mail/contact.js')); ?>"></script>

        <!-- Template Javascript -->
        <script src="<?php echo e(asset('frontend/js/main.js')); ?>"></script>

         <?php echo $__env->yieldContent('extra-scripts'); ?>
    
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/layouts/index.blade.php ENDPATH**/ ?>