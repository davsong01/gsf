<div class="container">
	<div class="row align-items-center">
		<div class="col-md-12">
			<hr>
			<div class="contact-form">
				<div id="success">
					<h6 style="color:green">Kindly fill the form below and click proceed to payment</h6>
				</div>
				<form action="<?php echo e(route('pay')); ?>" method="POST">
					<?php echo csrf_field(); ?>
					<div class="control-group">
						<label>Name</label>
						<input type="text" class="form-control <?php if ($errors->has('name')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('name'); ?> is-invalid <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>" id="name" name="name"
							placeholder="Enter your full name" required="required">
						<?php if ($errors->has('name')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('name'); ?>
						<span class="invalid-feedback" role="alert">
							<strong><?php echo e($message); ?></strong>
						</span>
						<?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>
					</div>
					<div class="control-group">
						<label>Email</label>
						<input type="email" class="form-control <?php if ($errors->has('email')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('email'); ?> is-invalid <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>" id="email" name="email"
							placeholder="Enter your email" required="required">
						<?php if ($errors->has('email')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('email'); ?>
						<span class="invalid-feedback" role="alert">
							<strong><?php echo e($message); ?></strong>
						</span>
						<?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>
					</div>
					<div class="control-group">
						<label>Phone</label>
						<input type="text" class="form-control <?php if ($errors->has('phone')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('phone'); ?> is-invalid <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>" id="phone" name="phone"
							placeholder="Enter your phone number" required="required">
						<?php if ($errors->has('phone')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('phone'); ?>
						<span class="invalid-feedback" role="alert">
							<strong><?php echo e($message); ?></strong>
						</span>
						<?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>
					</div>
					<div class="control-group">
						<label for="chapter">GSF Campus</label><br>
						<select name="chapter" class="form-control <?php if ($errors->has('chapter')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('chapter'); ?>is-invalid <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>" id="chapterind"
							class="chapter" required>
							<option value="">--Select Campus</option>
							<?php $__currentLoopData = $chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<option value="<?php echo e($chapter->id); ?>" <?php echo e(old('chapter') == $chapter->id ? 'selected' : ''); ?>>
								<?php echo e($chapter->name); ?></option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php if ($errors->has('chapter')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('chapter'); ?>
							<span class="invalid-feedback" role="alert">
								<strong><?php echo e($message); ?></strong>
							</span>
							<?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>
						</select>
					</div>

					<br>
					
					<input type="hidden" name="amount" value="<?php echo e($setting->registration_fee * 100); ?>"> 
					<input type="hidden" name="quantity" value="1">
					<input type="hidden" name="currency" value="NGN">
					<input type="hidden" name="metadata[]" id="metadata">
					<input type="hidden" name="metadata" value="<?php echo e(json_encode($array = ['type' => '1',])); ?>">
					<input type="hidden" name="reference" value="<?php echo e(Paystack::genTranxRef()); ?>"> 
					<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
					

					<div class="control-group">
						<button class="btn submitregistration" type="submit" style="width:100%">Proceed to Payment</button>
					</div>
				</form>
			</div>
			<hr>
		</div>
	</div>
</div><?php /**PATH C:\laragon\www\gsf\resources\views/includes/individualform.blade.php ENDPATH**/ ?>