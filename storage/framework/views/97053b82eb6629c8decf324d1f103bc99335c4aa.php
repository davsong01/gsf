<?php if(session()->get('message')): ?>
<div class="alert alert-success" role="alert" style="width: 100%;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Success!</strong> <?php echo e(session()->get('message')); ?>

      </div>
<?php endif; ?>

<?php if(session()->get('error')): ?>
<div class="alert alert-danger" role="alert" style="width: 100%;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Whoops!</strong> <?php echo e(session()->get('error')); ?> </strong>
      </div>
<?php endif; ?>
<?php if(session()->get('id_not_found')): ?>
<div class="alert alert-danger" role="alert" style="width: 100%;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Whoops!</strong> <?php echo e(session()->get('id_not_found')); ?>Please click <a target="_blank" href="https://thewaacsp.com/english/get-started"> <b>HERE</b></a> to get a WAACSP membership ID! </strong>
</div>
<?php endif; ?>

<?php if(session()->get('warning')): ?>
<div class="alert alert-warning" role="alert" style="width: 100%;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong style="color:black">Hey!</strong> <span style="color:black"><?php echo e(session()->get('warning')); ?></span>
      </div>
<?php endif; ?>

<?php if(session()->get('response')): ?>
<?php $__currentLoopData = session()->get('response'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="user-profile-social-list">
            <table class="table small m-b-xs">
                <tbody>
                  <tr>
                    <td>
                        Status: <?php echo $in['status']; ?>

                    </td>
                  </tr>
                  <tr>
                    <td>
                        Rating: <strong><?php echo e($in['rating']); ?></strong>
                    </td>
                </tr>
                    <tr>
                        <td>
                            Score: <strong><?php echo e($in['score']); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                          Name: <strong><?php echo e($in['name']); ?></strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
    <div class="col-lg-6 col-md-6 col-sm-6">
      <div class="user-profile-social-list">
          <table class="table small m-b-xs">
              <tbody>
                <tr>
                  <td>
                    Email: <strong><?php echo e($in['email']); ?></strong>
                  </td>
              </tr>
                <tr>
                  <td>
                      Training: <strong><?php echo e($in['training']); ?></strong>
                  </td>
              </tr>
                  <tr>
                      <td>
                          Balance: <strong><?php echo e($in['balance']); ?></strong>
                      </td>
                  </tr>
                  <tr>
                    <td>Date Certified: <strong><?php echo e(\Carbon\Carbon::parse($in['registered'])->format('j F, Y')); ?></strong></td>
                        
                  </tr>
                 
              </tbody>
          </table>
      </div>
  </div> 
    
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\gsf\resources\views/includes/falerts.blade.php ENDPATH**/ ?>