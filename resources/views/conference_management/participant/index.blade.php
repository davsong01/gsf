@extends('layouts.participant_single_edition')
@section('title', 'My Conferences')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('conferencemanagement.index', ['edition'=>$edition->id]) }}">Conferences</a></li>
@endsection

@section('single')
<section id="dashboard-analytics">
        <div class="row">
            <!-- Website Analytics Starts-->
			
			@if(isset(auth()->user()->payments))
				@foreach(auth()->user()->payments->sortBy('created_at') as $payment)
				
				<div class="col-md-6 col-sm-12">
					<div class="card" style="background-color: {{ $payment->edition->id == App\ConferenceEdition::where('status', 'active')->first()->value('id') ? 'green':'#c1bfbf;'}}">
						<a href="">
						<div class="card-header d-flex justify-content-between align-items-center">
							<h4 class="card-title" style="color: white;">{{ $payment->edition->conference_theme }}{{ $payment->edition->id  }}</h4> <br>
							<small style="color:{{ $payment->edition->id == App\ConferenceEdition::where('status', 'active')->first()->value('id') ? 'white':'black'}}">{{ $payment->edition->start_date .' to '.$payment->edition->end_date }}</small>
						</div>
						<div class="card-content">
							<div class="card-body">
								<span style="color:yellow">{{ $payment->level }}</span>
							</div>
						</div>
					</a>
					</div>
				</div>
				@endforeach
			@endif
			
        </div>
    </section>
@endsection
{{-- @extends('layouts.dashboard')
@section('title', 'My Account')
@section('active')
<li class="breadcrumb-item">Dashboard</li>
@endsection
@section('content')
<div class="content-body">
	@include('includes.alerts')
	
	<!-- Dashboard Ecommerce Starts -->
	<section id="dashboard-ecommerce">
		<div class="row">
			<!-- Greetings Content Starts -->
			<div class="col-md-12 col-12 dashboard-greetings">
				<div class="card">
					<div class="card-header">
						<h3 class="greeting-text">Welcome {{ auth()->user()->name }}!</h3>
						<p class="mb-0">Please fill in the form below, click save to complete your registration and get hostel and ID details.</p>
					</div>
					<div class="card-content">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-end">
								<div class="dashboard-content-left">
									<h1 class="text-primary font-large-2 text-bold-500"></h1>
									@if(auth()->user()->completeReg($edition))
									<a href="{{ route('participants.card', auth()->user()->id) }}" class="btn btn-primary glow"><i class="fa fa-print" aria-hidden="true"></i> View/Print Conference I.D. card
									</a>
									@else
									<a href="#" onclick="return false;" data-toggle="tooltip" data-placement="top" title="You must complete registration to use this button" class="btn btn-primary glow disabled"><i class="fa fa-print" aria-hidden="true"></i> View/PrintConference I.D. Card
									</a>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<div class="content-body">
	<!-- Basic Inputs start -->
	<section id="basic-input">
		<div class="card">
			<div class="card-header">
				<h4 class="greeting-text">Your Registration Details</h4>
			</div>
			<div class="card-content">
				<div class="card-body">
					<form action="{{ route('conferencemanagement.update', auth()->user()->payment->id) }}"
						onsubmit="return confirm('I am sure all my details are correct and current');" method="POST"
						enctype="multipart/form-data">
						@csrf
						@method('PATCH')
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<fieldset class="form-group">
									<label for="conference_id">Conference ID</label>
									<input type="text" class="form-control" name="conference_id" id="conference_id"
										value="{{ auth()->user()->family_id }}" disabled required>
								</fieldset>

								<fieldset class="form-group">
									<label for="registration_status">Registration Status</label>
									<input type="text" class="form-control" name="registration_status" id="registration_status"
										value="{{ auth()->user()->payment->registration_status }}" disabled required>
								</fieldset>

								<fieldset class="form-group">
									<label for="uploaded_by">Registered by</label>
									<input type="text" id="uploaded_by" name="uploaded_by" class="form-control"
										value="{{ (auth()->user()->payment->moderator === NULL) ? 'N/A' : auth()->user()->payment->moderator->name }}"
										disabled required>
								</fieldset>
								<fieldset class="form-group">
									<label for="amount">Amount Paid (&#8358;)</label>
									<input type="number" id="amount" class="form-control @error('amount_paid')is-invalid @enderror"
										value="{{ auth()->user()->payment->amount_paid }}" disabled required>

								</fieldset>
								<fieldset class="form-group">
									<label for="email">Email</label>
									<input type="email" id="email" class="form-control @error('email')is-invalid @enderror"
										value="{{ auth()->user()->email }}" required disabled>
								</fieldset>
							</div>
							<div class="col-md-6 col-sm-12">
								<fieldset class="form-group">
									<label for="hostel_id">Hostel</label>
									<input type="text" id="hostel_id" name="hostel_id" class="form-control"
										value="{{ (auth()->user()->payment->hostel === NULL) ? 'N/A' : auth()->user()->payment->hostel->name }}" disabled
										required>
								</fieldset>

								<fieldset class="form-group">
									<label for="food_id">Food Stand</label>
									<input type="text" id="food_id" name="food_id" class="form-control"
										value="{{ (auth()->user()->payment->food === NULL) ? 'N/A' : auth()->user()->payment->food->name }}" disabled
										required>
								</fieldset>
								<fieldset class="form-group">
									<label for="payment_type">Payment Type</label>
									<input type="text" id="payment_type" class="form-control @error('payment_type')is-invalid @enderror"
										value="{{ auth()->user()->payment->payment_type }}" disabled required>

								</fieldset>
								<fieldset class="form-group">
									<label for="transid">Transaction ID</label>
									<input type="text" id="transid" name="transid" class="form-control"
										value="{{ old('transid') ?? auth()->user()->payment->transid }}" disabled required>
								</fieldset>


							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<fieldset class="form-group">
									<label for="name">Name</label>
									<input type="text" class="@error('name')is-invalid @enderror form-control" id="name" name="name"
										value="{{ old('name') ?? auth()->user()->name }}" placeholder="Enter name">

								</fieldset>

								<fieldset class="form-group">
									<label for="phone">Phone</label>
									<input type="phone" id="phone" name="phone" class="form-control @error('phone')is-invalid @enderror"
										value="{{ old('phone') ?? auth()->user()->phone }}" required>

								</fieldset>
								
								<fieldset class="form-group">
									<label for="sex">Gender</label>
									<select class="form-control @error('sex')is-invalid @enderror" name="sex" id="sex" required>
										<option value="">--Select Option--</option>
										<option value="Male" {{ (auth()->user()->sex == 'Male' || old('sex') == 'Male') ? 'selected' : ''}}>
											Male</option>
										<option value="Female"
											{{ (auth()->user()->sex == 'Female' || old('sex') == 'Female') ? 'selected' : ''}}>Female</option>
									</select>

								</fieldset>

							</div>
							<div class="col-md-6 col-sm-12">
								<fieldset class="form-group">
									<label for="chapter">Campus</label>
									@if (isset(auth()->user()->chapter_id)) 
									@foreach($chapters as $chapter)
									@if (auth()->user()->chapter_id == $chapter->id )
									<input type="text" id="chapter" class="form-control"
										value="{{ $chapter->name }}" disabled required>
									@endif
									@endforeach
									@else
									<input type="text" value = "N/A" class="form-control"
										 disabled required>
									@endif
								</fieldset>

								<fieldset class="form-group @error('passport')is-invalid @enderror">
									<label for="passport">Change Passport <small>(Not more than 200kilobybte | Only jpeg,jpg,png format is accepted)</small></label>
									<input type="file" accept="image/*" class="form-control" name="passport" id="passport">
								</fieldset>

								<fieldset class="form-group">
									<label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank
											except you want to change your password</i></small>
									<input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}"
										placeholder="Enter password">
								</fieldset>

							</div>
							<input type="hidden" name="level" value="{{ auth()->user()->payment->level  }}">
						</div>
						<div class="row">
							<div class="col-md-12 col-sm-12">
								<button class="btn btn-primary" style="width:100%" type="submit">Update Profile</button>
					</form>

				</div>
			</div>
		</div>
</div>
</section>
<!-- Basic Inputs end -->
</div>
@endsection --}}