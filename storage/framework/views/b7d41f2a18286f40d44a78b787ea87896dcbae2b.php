<div class="container">
    <div class="row align-items-center">
        <div class="col-md-12">
        <hr>
            <div class="contact-form">
                <div id="success"><h6 style="color:green">Kindly fill the form below and click proceed to payment</h6></div>
                <form action="<?php echo e(route('pay')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="control-group">
                        <label>Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Enter your full names" required="required">
                    </div>

                    <div class="control-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Enter your email" required="required">
                    </div>
                    <div class="control-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            placeholder="Enter your phone number" required="required">
                    </div>
                   
                   
                    <br>
                    
                    <input type="hidden" name="amount" value="<?php echo e($setting->alumni_fee * 100); ?>"> 
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="currency" value="NGN">
                    <input type="hidden" name="metadata" value="<?php echo e(json_encode($array = ['type' => '3',])); ?>" > 
                    <input type="hidden" name="reference" value="<?php echo e(Paystack::genTranxRef()); ?>"> 
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"> 
                    <div class="control-group">
                        <button class="btn submitregistration" type="submit" style="width:100%">Proceed to Payment</button>
                    </div>
                </form>
            </div>
        <hr>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\gsf\resources\views/includes/alumniform.blade.php ENDPATH**/ ?>