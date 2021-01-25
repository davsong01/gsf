<?php $__env->startSection('title', 'Alumni'); ?>
<?php $__env->startSection('active'); ?>
<li class="breadcrumb-item">Alumni</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Alumni</h4>
                        <a href="<?php echo e(route('alumni.create')); ?>" class="btn btn-primary mt-1">Add new Alumni</a>
                        <?php echo $__env->make('includes.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Conference ID</th>
                                            <th>Status</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Amount Paid</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $participant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($count); ?></td>
                                            <td><?php echo e($participant->conference_number); ?></td>
                                            <td><?php if($participant->status == 'Complete'): ?>)
                                                <i class="bx bxs-circle success font-small-1 mr-50"></i><small>Complete</small> <?php else: ?>
                                                <i class="bx bxs-circle danger font-small-1 mr-50"></i><small>Pending</small>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td><?php echo e($participant->name); ?></td>
                                            <td><?php echo e($participant->email); ?></td>
                                            <td><?php echo e($participant->phone); ?></td>
                                            <td>&#8358;<?php echo e($participant->amount_paid); ?></td>
                                            
                                            
                                                
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Edit User" href="<?php echo e(route('alumni.edit', $participant->id)); ?>"> <i class="bx bxs-edit actions"></i></
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" data-placement="top" title="Switch To"
                                                href="<?php echo e(route('switchuser', $participant->id)); ?>"><i
                                                    class="fa fa-unlock actions"></i>
                                            </a>
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete User" href="<?php echo e(route('alumni.delete', $participant->id)); ?>"> <i class="fa fa-trash"></i></
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
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/admin/alumni/index.blade.php ENDPATH**/ ?>