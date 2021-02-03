<div class="container">
	<div class="row align-items-center">
		<div class="col-md-12">
			<hr>
			<div class="contact-form">
				<div id="success">
					<h6 style="color:green">Kindly fill the form below and click proceed to payment</h6>
				</div>
				<form action="{{ route('pay') }}" method="POST">
					@csrf
					<div class="control-group">
						<label>Name</label>
						<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
							placeholder="Enter your full name" required="required">
						@error('name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
						@enderror
					</div>
					<div class="control-group">
						<label>Email</label>
						<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
							placeholder="Enter your email" required="required">
						@error('email')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
						@enderror
					</div>
					<div class="control-group">
						<label>Phone</label>
						<input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
							placeholder="Enter your phone number" required="required">
						@error('phone')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
						@enderror
					</div>
					<div class="control-group">
						<label for="chapter">GSF Campus</label><br>
						<select name="chapter" class="form-control select2 @error('chapter')is-invalid @enderror" id="chapterind"
							class="chapter" required>
							<option value="">--Select Campus</option>
							@foreach($chapters as $chapter)
							<option value="{{ $chapter->id }}" {{ old('chapter') == $chapter->id ? 'selected' : ''}}>
								{{ $chapter->name }}</option>
							@endforeach
							@error('chapter')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
							@enderror
						</select>
					</div>

					<br>
					{{-- <input type="hidden" name="orderID" value="345"> --}}
					<input type="hidden" name="amount" value="{{ $setting->registration_fee * 100 }}"> {{-- required in kobo --}}
					<input type="hidden" name="quantity" value="1">
					<input type="hidden" name="currency" value="NGN">
					<input type="hidden" name="metadata[]" id="metadata">
					<input type="hidden" name="metadata" value="{{ json_encode($array = ['type' => '1',]) }}">
					<input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					{{-- employ this in place of csrf_field only in laravel 5.0 --}}

					<div class="control-group">
						<button class="btn submitregistration" type="submit" style="width:100%">Proceed to Payment</button>
					</div>
				</form>
			</div>
			<hr>
		</div>
	</div>
</div>