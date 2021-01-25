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
                            placeholder="Enter your full name" required="required">
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
                    <div class="control-group">
                        <label for="chapter">GSF Campus</label><br>
                        <select name="chapter" class="form-control" id="chapterind" class="chapter" required>
                            <option value="">--Select Campus</option>
                            <?php $__currentLoopData = $chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($chapter->id); ?>" <?php echo e(old('chapter') == $chapter->id ? 'selected' : ''); ?>><?php echo e($chapter->name); ?></option>  
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="control-group">
                        <label>No of Participants you want to register</label>
                        <input type="number" class="form-control" id="participants" name="participants"
                            placeholder="Number of participants from your fellowship" required="required">
                    </div>
                   
                    <br>
                    
                    <input type="hidden" name="amount" id= "amount" value=""> 
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="currency" value="NGN">
                    <input type="hidden" name="metadata" value="<?php echo e(json_encode($array = ['type' => '2',])); ?>" > 
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"> 
                    <script>
                        var participants = document.getElementById('participants');
                        var amount = document.getElementById('amount');

                        participants.addEventListener('input', function() {
                        amount.value = this.value * <?php echo e($setting->registration_fee * 100); ?>;
                        });

                        amount.addEventListener('input', function() {
                        participants.value = this.value;
                        });
                    </script>
                   
                    <div class="control-group">
                        <button class="btn submitregistration"  type="submit" style="width:100%">Proceed to Payment </button>
                    </div>
                </form>
            </div>
        <hr>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/includes/fellowshipform.blade.php ENDPATH**/ ?>