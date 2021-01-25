<?php $__env->startSection('title', 'Update hostel'); ?>
<?php $__env->startSection('item'); ?>
<li class="breadcrumb-item"> <a href="<?php echo e(route('hostels.index')); ?>">Hostels</a></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('active'); ?>
<li class="breadcrumb-item">Update hostel</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        
                        <?php echo $__env->make('includes.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="<?php echo e(route('hostels.update', $hostel->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                         
                       
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo e(old('name') ?? $hostel->name); ?>" placeholder="Enter name">
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="type">Type</label>
                                        <select class="form-control" name="type" id="type" required>
                                        
                                         <option value="Male" <?php echo e($hostel->level == 'Male' ? 'selected' : ''); ?>>Male</option>
                                        <option value="Female" <?php echo e($hostel->level == 'Female' ? 'selected' : ''); ?>>Female</option>
                                        </select>
                                    </fieldset>
                                    
                                    

                                    <fieldset class="form-group">
                                        <label for="level">Level</label>
                                        <select class="form-control" name="level" id="level" required>
                                            <option value="Alumni" <?php echo e($hostel->level == 'Admin' ? 'selected' : ''); ?>>Alumni</option>
                                            <option value="Participant" <?php echo e($hostel->level == 'Participant' ? 'selected' : ''); ?>>Participant</option>
                                            <option value="Nec" <?php echo e($hostel->level == 'Nec' ? 'selected' : ''); ?>>Nec</option>
                                        </select>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="capacity">Capacity</label>
                                        <input type="number" id="capacity" name="capacity" class="form-control" value="<?php echo e(old('capacity') ?? $hostel->capacity); ?>" required>
                                    </fieldset>

                                <fieldset class="form-group">
                                        <label for="allocation">Allocation</label>
                                        <input type="numer" id="allocation" name="allocation" class="form-control" disabled value="<?php echo e(old('allocation') ?? $hostel->allocation); ?>" required>
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
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->          
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/admin/hostel/edit.blade.php ENDPATH**/ ?>