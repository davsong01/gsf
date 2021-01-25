<?php $__env->startSection('title', 'My Account'); ?>
<?php $__env->startSection('active'); ?>
<li class="breadcrumb-item">Dashboard</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <?php echo $__env->make('includes.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if(auth()->user()->wallet >= $setting->min_payout_amount): ?>
    <!-- Dashboard Ecommerce Starts -->
    <section id="dashboard-ecommerce">
        <div class="row">
            <!-- Greetings Content Starts -->
            <div class="col-md-12 col-12 dashboard-greetings">
                <div class="card">
                    <div class="card-header">
                        <h3 class="greeting-text">Welcome <?php echo e(auth()->user()->name); ?>!</h3>
                        <p class="mb-0">Here, you can fill in the form below, click save to complete your registration. When your registration is complete, the buttons to download your ID and meal ticket will be enabled.</p>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="dashboard-content-left">
                                    <h1 class="text-primary font-large-2 text-bold-500"></h1>
                                    
                                  <?php if(auth()->user()->registration_status == 'Pending'): ?>  
                                
                                     <a href="#" onclick="return false;" data-toggle="tooltip" data-placement="top" title="You must complete registration to use this button" class="btn btn-primary glow disabled"><i class="fa fa-download" aria-hidden="true"></i> Download Conference I.D. Card
                                       
                                    </a> 
                                     <a href="#" onclick="return false;" data-toggle="tooltip" data-placement="top" title="You must complete registration to use this button" class="btn btn-primary glow disabled"><i class="fa fa-download" aria-hidden="true"></i> Download Conference Meal ticket
                                        
                                    </a> 
                                    <?php endif; ?>

                                    <?php if(auth()->user()->registration_status == 'Complete'): ?>  
                                                                   
                                    <a href="<?php echo e(route('profile.edit', auth()->user()->id)); ?>" onclick="return false;"  class="btn btn-primary glow"> <i class="fa fa-download" aria-hidden="true" disabled></i> Download Conference I.D. card
                                    </a>
                                     <a href="<?php echo e(route('profile.edit', auth()->user()->id)); ?>"  >
                                        <button type="button" onclick="return confirm('You are about to download your conference meal ticket?');" class="btn btn-primary glow">Download Conference Meal ticket
                                        </button>
                                    </a> 
                                    <?php endif; ?>
                                   
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>               
        </div>
    </section>
    <?php endif; ?>
</div>
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="card">
            <div class="card-header">
                <h4 class="greeting-text">Your Registration Details</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <form action="<?php echo e(route('participants.update', auth()->user()->id)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <fieldset class="form-group">
                                <label for="conference_id">Conference ID</label>
                                <input type="text" class="form-control" name="conference_id" id="conference_id" value="<?php echo e(auth()->user()->conference_number); ?>" disabled required>
                            </fieldset>

                            <fieldset class="form-group">
                            <label for="registration_status">Registration Status</label>
                            <input type="text" class="form-control" name="registration_status" id="registration_status" value="<?php echo e(auth()->user()->registration_status); ?>" disabled required>
                            </fieldset>

                                <fieldset class="form-group">
                                <label for="uploaded_by">Registered by</label>
                                <input type="text" id="uploaded_by" name="uploaded_by" class="form-control" value="<?php echo e((auth()->user()->moderator === NULL) ? 'N/A' : auth()->user()->moderator->name); ?>" disabled required>
                            </fieldset>
                            
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <fieldset class="form-group">
                                <label for="hostel_id">Hostel</label>
                                <input type="text" id="hostel_id" name="hostel_id" class="form-control" value="<?php echo e((auth()->user()->hostel === NULL) ? 'N/A' : auth()->user()->hostel->name); ?>" disabled required>
                            </fieldset>

                            <fieldset class="form-group">
                                <label for="food_id">Food Stand</label>
                                <input type="text" id="food_id" name="food_id" class="form-control" value="<?php echo e((auth()->user()->food === NULL) ? 'N/A' : auth()->user()->food->name); ?>" disabled required>
                            </fieldset>
                            <fieldset class="form-group">
                            <label for="transid">Transaction ID</label>
                            <input type="text" id="transid" name="transid" class="form-control" value="<?php echo e(old('transid') ?? auth()->user()->transid); ?>" disabled required>
                        </fieldset>
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <fieldset class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo e(old('name') ?? auth()->user()->name); ?>" placeholder="Enter name">
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo e(old('email') ?? auth()->user()->email); ?>" required>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="phone">Phone</label>
                            <input type="phone" id="phone" name="phone" class="form-control" value="<?php echo e(old('phone') ?? auth()->user()->phone); ?>" required>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="sex">Sex</label>
                            <select class="form-control" name="sex" id="sex" required>
                                <option value="">--Select Option--</option>
                                <option value="Male" <?php echo e(auth()->user()->sex == 'Male' ? 'selected' : ''); ?>>Male</option>
                                <option value="Female" <?php echo e(auth()->user()->sex == 'Female' ? 'selected' : ''); ?>>Female</option>
                            </select>
                        </fieldset>
                         <fieldset class="form-group">
                            <label for="sex">Change Image</label>
                            <input type= "file" class="form-control" name="passport" id="passport" required>
                               
                        </fieldset>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <fieldset class="form-group">
                            <label for="chapter">Campus</label>
                            <select class="form-control" name="chapter" id="chapter" required>
                                
                                <option value="">--Select Campus--</option>
                                <?php $__currentLoopData = $chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($chapter); ?>" <?php echo e(auth()->user()->chapter == $chapter->id ? 'selected' : ''); ?>><?php echo e($chapter->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="amount">Amount Paid</label>
                            <input type="number" id="amount" name="amount_paid" class="form-control" value="<?php echo e(old('amount_paid') ?? auth()->user()->amount_paid); ?>" required>
                        </fieldset>
                        <fieldset class="form-group">
                            <label for="payment_type">Payment Type</label>
                            <input type="text" id="payment_type" name="payment_type" class="form-control" value="<?php echo e(old('payment_type') ?? auth()->user()->payment_type); ?>" required>
                        </fieldset>

                        
                        
                        <fieldset class="form-group">
                            <label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank except you want to change your password</i></small>
                            <input type="text" class="form-control" name="password" id="password" value="<?php echo e(old('password')); ?>" placeholder="Enter password">
                        </fieldset>

                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <button class="btn btn-primary" style="width:100%" type="submit">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->          
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/participant/index.blade.php ENDPATH**/ ?>