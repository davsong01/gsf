<?php $__env->startComponent('mail::message'); ?>

Dear <?php echo e($data['name']); ?>,

Your registration for GSF National conference is successful.

Below are the details of your registration

<strong>Name: </strong> <?php echo e($data['name']); ?> <br>
<strong>Email: </strong> <?php echo e($data['email']); ?> <br>
<strong>Phone: </strong> <?php echo e($data['phone']); ?> <br>
<strong>Conference ID: </strong><?php echo e($data['conference_number']); ?> <br>
<strong>Amount Paid: </strong> &#8358;<?php echo e($data['amount']); ?> <br>


To complete your registration and have access to hostel space, food stand, I.D. card and more, kindly login to your personalized portal and fill in your details.

Login Details are:

<strong>Conference I.D: </strong> <?php echo e($data['conference_number']); ?> <br>
<strong>Password: </strong> <?php echo e($data['phone']); ?><br>

You can login and change your password for confidential reasons

<?php $__env->startComponent('mail::button', ['url' => config('app.url') .'/myaccount']); ?>
Login to portal here<br><br>
<?php echo $__env->renderComponent(); ?>



Thanks,<br>
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\laragon\www\gsf\resources\views/emails/welcomeMail.blade.php ENDPATH**/ ?>