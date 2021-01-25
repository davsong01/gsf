<?php $__env->startSection('title', 'Food Stands'); ?>
<?php $__env->startSection('active'); ?>
<li class="breadcrumb-item">Food Stands</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Foodstands</h4>
                        <a href="<?php echo e(route('foods.create')); ?>" class="btn btn-primary mt-1">Add new Food Stand</a>
                        <?php echo $__env->make('includes.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Level</th>
                                            <th>Capacity</th>
                                            <th>Allocation</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $foods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($count++); ?></td>
                                            <td><?php echo e($food->name); ?></td>
                                            <td><?php echo e($food->level); ?></td>
                                            <td><?php echo e($food->capacity); ?></td>
                                            <td><?php echo e($food->allocation); ?></td>
                                            
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update food details" href="<?php echo e(route('foods.edit', $food->id)); ?>"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete food" href="<?php echo e(route('foods.delete', $food->id)); ?>"> <i class="fa fa-trash"></i></
                                            </a>
                                        </tr>
                                      
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Zero configuration table -->         
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/admin/food/index.blade.php ENDPATH**/ ?>