<?php if(session()->get('message')): ?>
<div class="alert alert-success" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Great!</strong> <?php echo e(session()->get('message')); ?>

</div>
<?php endif; ?>

<?php if(session()->get('error')): ?>
<div class="alert alert-danger" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Whoops!</strong> <?php echo e(session()->get('error')); ?>

</div>
<?php endif; ?>

<?php if(session()->get('any')): ?>
<div class="alert alert-warning" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Whoops!</strong> <?php echo e(session()->get('any')); ?>

</div>
<?php endif; ?>

<?php if(session()->get('warning')): ?>
<div class="alert alert-warning" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Hey!</strong> <?php echo e(session()->get('warning')); ?>

      </div>
<?php endif; ?>

<?php if(session()->get('welcomeback')): ?>
<div class="alert alert-success" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Great!</strong> <?php echo e(session()->get('welcomeback')); ?> &#128515
</div>
<?php endif; ?>

<?php if($errors->any()): ?>
 <div class="alert alert-warning" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e($error); ?><br>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php if(session('resent')): ?>
<div class="alert alert-success" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>A New verification link has been sent to your email address. </strong>
</div>
<?php endif; ?>
<?php if(session('verified')): ?>
<div class="alert alert-success" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Email succesfully verified, Welcome! </strong>
</div>
<?php endif; ?>

<?php if(session('status')): ?>
<div class="alert alert-success" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong><?php echo e(session('status')); ?></strong>
</div>
<?php endif; ?>

<?php if(isset(auth()->user()->permission)): ?>
<?php if(auth()->user()->permission == 1 && auth()->user()->bank == NULL || auth()->user()->account_name == NULL || auth()->user()->account_number == NULL): ?>
<p style="display:none"><?php echo e($bank_details=0); ?></p>
<div class="alert alert-danger" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      Hi, you have not filled your bank details, <strong>click <a href="<?php echo e(route('profile.edit', auth()->user()->id)); ?>">HERE</a> to fill it now</strong></strong>
</div>
<?php endif; ?>
<?php endif; ?><?php /**PATH C:\laragon\www\gsf\resources\views/includes/alerts.blade.php ENDPATH**/ ?>