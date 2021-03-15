@extends('layouts.dashboard')
@section('active')
<li class="breadcrumb-item">Conference I.D. Card</li>
@endsection
@section('extra_styles')
<style>
	/* body {
		
	} */

	h2 {
		font-size: 15px;
		margin: 5px 0;
	}

	h3 {
		
	}

</style>
@endsection
@section('content')
<div class="content-body">
	@include('includes.alerts')
	<!-- Dashboard Ecommerce Starts -->
	<script>
		function PrintElem()
	{
			var mywindow = window.open('', 'PRINT', 'height=640,width=480');
	
			mywindow.document.write('<html><head><title>' + document.title  + '</title>');
			mywindow.document.write('</head><body>');
			mywindow.document.write('<h1>' + document.title  + '</h1>');
			mywindow.document.write(document.getElementById('id').innerHTML);
			mywindow.document.write('</body></html>');
	
			mywindow.document.close(); // necessary for IE >= 10
			mywindow.focus(); // necessary for IE >= 10*/
	
			mywindow.print();
			// mywindow.close();
	
			return true;
			
	}
	</script>
	<section id="dashboard-ecommerce">
		<div class="row">
			<!-- Greetings Content Starts -->
			<div class="col-md-12 col-12 dashboard-greetings">
				<div class="card-header">
					<a onclick="PrintElem()" class="btn btn-primary mt-1">Print I.D.</a>
					@include('includes.alerts')
				</div>

				<div class="card" id="id">

					<div class="card-content">
						<div class="card-body">
							<section id="dashboard-ecommerce">
								<div class="row">
									<div class="id-card" style="font-family: 'verdana';background: linear-gradient(to bottom, #33ccff 0%, #ff99cc 100%);padding: 10px; border-radius: 10px; text-align: center; height: 620px; box-shadow: 0 0 1.5px 0px #b9b9b9; width: 480px; height: 640px;margin: 0 auto;">
										<div class="header">
											<img style="width:15%; height:10%" src="{{ asset('frontend/img/logo.png') }}">
											<h2 class="name" style="margin-top: 10px; margin-bottom: 10px; font-size:22px">GOFAMINT STUDENTS' FELLOWSHIP
											</h2>
											<h2 class="alias" style="margin-top: 10px; margin-bottom: 10px; font-size:15px">19th Biennial National
												Conference</h2>
											<h3 style="font-size: 12px; margin: 2.5px 0;font-weight: 300;" class="date">{{ $setting->start_date .' to '. $setting->end_date }}</h3><br>
										</div>
										<div class="photo" >
											<h2 class="alias"  style="font-family:initial; margin-top: 5px; margin-bottom: 5px; font-size:15px">{{ $setting->conference_theme }}</h2>
											<h1>@if($user->level == 'Participant')PARTICIPANT
												@elseif($user->level == 'Alumni')ALUMNUS
												@elseif($user->level == 'Moderator')PARTICIPANT
												@elseif($user->level == 'Official' || $user->level == 'Nec' || $user->level == 'Official')OFFICIAL
												@elseif($user->level == 'Choir')CHOIR
												@elseif($user->level == 'Medical')MEDICAL PERSONNEL

												@endif

											</h1>
											<img style="width: 150px; height: 150px; margin-top: 10px; border-radius: 50%;" src="{{ asset($user->passport ? '/'.$user->passport : '/frontend/passports/avatar.jpg') }}">
										</div>
										<div>
											<br>
											<h3 style="font-size:25px"><strong>{{ $user->name }}</strong></h3>
											@if(isset($user->campus))
											<h3 style="font-weight:20px; font-size:10px">{{ $user->campus->name  }}</h3>
											
											@endif
											
											<hr style="margin-top: 1rem;margin-bottom: 1rem; border-top: 1px solid #DFE3E7;">
											
											<h3 style="font-size:15px; font-weight:lighter;"> <strong>Hostel:</strong>
												{{ isset($user->hostel->name) ? $user->hostel->name : 'Registration incomplete'  }}</h3>
											<h3 style="font-size:15px; font-weight:lighter"> <b>Food Stand:</b>
												{{ isset($user->food->name) ? $user->food->name : 'Registration incomplete'  }}</h3>
											
										</div>
										
									</div>
								</div>

							</section>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
@section('scripts')

@endsection