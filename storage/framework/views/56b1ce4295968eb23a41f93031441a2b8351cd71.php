<?php $__env->startSection('title', 'Moderators'); ?>
<?php $__env->startSection('active'); ?>
<li class="breadcrumb-item">Moderators</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Moderators</h4>
                        <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary mt-1">Add new Moderator</a>
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
                                            <th>Email</th>
                                            <th>Chapter</th>
                                            <th>Phone</th>
                                            <th>Amount Paid</th>
                                            <th>Slots Remaining</th>
                                            <th>Slots Filled</th>
                                            
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $participant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($count); ?></td>
                                            <td><?php echo e($participant->name); ?></td>
                                            <td><?php echo e($participant->email); ?></td>
                                            <td><?php echo e($participant->chapter); ?></td>
                                            <td><?php echo e($participant->phone); ?></td>
                                            <td>&#8358;<?php echo e($participant->amount_paid); ?></td>
                                            <td><?php echo e($participant->slots_filled); ?></td>
                                            <td><?php echo e($participant->slot - $participant->slots_filled); ?></td>
                                            
                                                
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update Moderator details" href="<?php echo e(route('moderators.edit', $participant->id)); ?>"> <i class="bx bxs-edit actions"></i></
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" data-placement="top" title="Switch To"
                                                href="<?php echo e(route('switchuser', $participant->id)); ?>"><i
                                                    class="fa fa-unlock actions"></i>
                                            </a>
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Moderator" href="<?php echo e(route('moderators.delete', $participant->id)); ?>"> <i class="fa fa-trash"></i></
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
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/admin/moderator/index.blade.php ENDPATH**/ ?>