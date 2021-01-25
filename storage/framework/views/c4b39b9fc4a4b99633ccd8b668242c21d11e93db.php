<?php $__env->startSection('title', 'New hostel'); ?>
<?php $__env->startSection('item'); ?>
<li class="breadcrumb-item"> <a href="<?php echo e(route('hostels.index')); ?>">Hostels</a></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('active'); ?>
<li class="breadcrumb-item">Create hostel</li>
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
                            <form action="<?php echo e(route('hostels.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo e(old('name')); ?>" placeholder="Enter name">
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="type">Type</label>
                                        <select class="form-control" name="type" id="type" required>
                                        <option value="">-- Select option --</option>
                                        <option value="Male" <?php echo e(old('type') == 'Male' ? 'selected' : ''); ?>>Male</option>
                                        <option value="Female" <?php echo e(old('type')  == 'Female' ? 'selected' : ''); ?>>Female</option>
                                        </select>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="level">Level</label>
                                        <select class="form-control" name="level" id="level" required>
                                            <option value="">-- Select option --</option>
                                            <option value="Alumni" <?php echo e(old('level') == 'Alumni' ? 'selected' : ''); ?>>Alumni</option>
                                            <option value="Participant" <?php echo e(old('level')  == 'Participant' ? 'selected' : ''); ?>>Participant</option>
                                            <option value="Nec" <?php echo e(old('level')  == 'Nec' ? 'selected' : ''); ?>>Nec</option>
                                        </select>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="capacity">Capacity</label>
                                        <input type="number" id="capacity" min="1" name="capacity" class="form-control" value="<?php echo e(old('capacity')); ?>" required>
                                    </fieldset>
                                
                                </div>

                            
                                
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <button class="btn btn-primary" style="width:100%" type="submit">Create</button>
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
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/admin/hostel/create.blade.php ENDPATH**/ ?>