<?php $__env->startComponent('mail::message'); ?>

Dear Admin,
<?php if($data['level'] == 'Moderator' || $data['level'] == 'Participant'): ?>
A participant has just registered for the GSF Conference, Please find details below:
<?php endif; ?>

<?php if($data['level'] == 'Alumni'): ?>
An Alumni has just registered for the GSF Conference, Please find details below:
<?php endif; ?>

<strong>Name: </strong> <?php echo e($data['name']); ?> <br>
<strong>Email: </strong> <?php echo e($data['email']); ?> <br>
<strong>Phone: </strong> <?php echo e($data['phone']); ?> <br>
<strong>Conference ID: </strong><?php echo e($data['conference_number']); ?> <br>
<strong>Amount Paid: </strong> &#8358;<?php echo e($data['amount']); ?> <br>
<strong>Chapter: </strong> <?php echo e($data['chapter']); ?> <br>


You can also login to the portal to view and manage registrations

<?php $__env->startComponent('mail::button', ['url' => config('app.url') .'/myaccount']); ?>
Login to portal here<br><br>
<?php echo $__env->renderComponent(); ?>



Thanks,<br>
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/emails/admin.blade.php ENDPATH**/ ?>