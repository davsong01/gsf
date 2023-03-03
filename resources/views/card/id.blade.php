@extends('layouts.dashboard')
@section('title', 'Print ID')
@section('item')
<li class="breadcrumb-item"><a href="{{ route('conferencemanagement.show', ['conferencemanagement'=>$payment->id, 'edition'=>$payment->conference_edition_id]) }}">{{ $setting->conference_theme }}</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Print ID Card</li>
@endsection
@section('extra_styles')
<style>
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
					<a style="color:white" onclick="PrintElem()" class="btn btn-primary mt-1">Print I.D. for {{ $edition->conference_theme }}</a>
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
											<h2 class="alias" style="margin-top: 10px; margin-bottom: 10px; font-size:15px">{{ $edition->slug }}</h2>
											<h2 class="alias"  style="font-family:initial; margin-top: 5px; margin-bottom: 20px; font-size:15px">{{ $setting->conference_theme }}</h2>

										</div>
										<div class="photo" >
											<h1>@if($payment->level == 'Participant')PARTICIPANT
												@elseif($payment->level == 'Alumni')ALUMNUS 
												@if ($payment->amount_paid == $edition->new_alumni_registration_fee) <h4><i class="fa fa-id-badge" aria-hidden="true"></i>
													
												</h4> 
												@elseif ($payment->amount_paid == $edition->alumni_registration_fee) <h4><i class="fa fa-id-badge" aria-hidden="true"></i>
													<i class="fa fa-id-badge" aria-hidden="true"></i></i>
												</h4> 
												@endif
												@elseif($payment->first()->level == 'Moderator')PARTICIPANT
												@elseif($payment->level == 'Official' || $payment->level == 'Nec' || $payment->level == 'Official')OFFICIAL
												@elseif($payment->level == 'Choir')CHOIR
												@elseif($payment->level == 'Medical')MEDICAL PERSONNEL
												@endif

											</h1>
											<h6>{{ $payment->transid }}</h6>
											<img style="width: 150px; height: 150px; margin-top: 10px; border-radius: 50%;" src="{{ asset($user->passport ? '/'.$user->passport : 'https://place-hold.it/300x150?text=No%20Image&fontsize=23') }}"> 
											<?php
												$details = 'Verified: '.$user->family_id.' | '.$payment->transid;
											?>
											@if($payment->registration_status == 'Complete')
											{!! QrCode::size(100)->generate('.Verified: '.$payment->transid); !!}
											@endif
										</div>
										<div>
											<br>
											<h3 style="font-size:25px"><strong>{{ $user->name }}</strong></h3>
											<strong style="font-weight: bold; font-size:10px">Family ID: </strong><span style="font-weight:20px; font-size:10px">{{ $user->family_id  }}</span> <br>


											@if(isset($user->campus))
											<strong style="font-weight: bold; font-size:10px">Campus: </strong><span style="font-weight:20px; font-size:10px">{{ $user->campus->name  }}</span>
											@endif
											<hr style="margin-top: 1rem;margin-bottom: 1rem; border-top: 1px solid #DFE3E7;">
											
											<h3 style="font-size:15px; font-weight:lighter;"> <strong>Hostel:</strong>
												{{ isset($payment->hostel->name) ? $payment->hostel->name : 'N/A'  }}</h3>
											<h3 style="font-size:15px; font-weight:lighter"> <b>Food Stand:</b>
												{{ isset($payment->food->name) ? $payment->food->name : 'N/A'  }}</h3>
											
										</div>
										<div class="card-footer">
											<small style="color:black">
												{{-- {{ date("F dS, Y", strtotime($setting->start_date)) .' to '. date("F dS, Y", strtotime($setting->end_date)) }}<br> --}}
											</small>
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