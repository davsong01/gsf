@if($setting->close_registration >= date('Y-m-d'))
{{-- Include form --}}
<div class="about">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-md-12">
				<hr>
				<div class="contact-form">
					<div id="success">
						<h6 style="color:green">Kindly fill the form below and click process donation</h6>
					</div>
					<form action="{{ route('pay') }}" method="POST">
						@csrf
						<div class="control-group">
							<label>Full Name</label>
							<input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name"
								required="required">
						</div>

						<div class="control-group">
							<label>Email</label>
							<input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
								required="required">
						</div>
						<div class="control-group">
							<label>State</label>
							<input type="text" class="form-control" id="state" name="state" placeholder="Your Location"
								required="required">
						</div>
						<div class="control-group">
							<label>Phone</label>
							<input type="text" class="form-control" id="phone" name="phone" placeholder="Enter your phone number"
								required="required">
						</div>

						<div class="control-group">
							<label>Enter amount</label>
							<input type="number" class="form-control" id="donations" name="donations" value="{{ old('donations') }}"
								placeholder="Amount you want to donate" min="1000" required="required">
						</div>

						<br>
						{{-- <input type="hidden" name="orderID" value="345"> --}}
						<input type="hidden" name="amount" id="amount" value="">
						{{-- required in kobo --}}
						<input type="hidden" name="quantity" value="1">
						<input type="hidden" name="currency" value="NGN">
						<input type="hidden" name="metadata" value="{{ json_encode($array = ['type' => '5']) }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<script>
							document.addEventListener('DOMContentLoaded', function(){
								var donations = document.getElementById('donations');
       					var amount = donations.form.querySelector('#amount'); 
							 	donations.addEventListener('change', function () {
								 amount.value = Number(this.value) * 100; 
								});
							});			
						</script>

						<div class="control-group">
							<button class="btn submitregistration" type="submit" style="width:100%">Process donation </button>
						</div>
					</form>
				</div>
				<hr>
			</div>
		</div>
	</div>
</div>
@else
<div class="col-md-12 col-sm-12">
	<h2 style="text-align: center;">REGISTRATION HAS NOW CLOSED!!!</h2>
</div>
@endif