@extends('layouts.dashboard')
@section('title', 'ID Card')
@section('active')
<li class="breadcrumb-item">Conference I.D. Card</li>
@endsection
@section('extra_styles')
<style>
	body {
		background-color: #d7d6d3;
		font-family: 'verdana';
	}

	.id-card-holder {
		width: 480px;
		height: 640px;
		padding: 2px;
		margin: 0 auto;
		background-color: #1f1f1f;
		border-radius: 5px;
		position: relative;
	}

	.id-card {

		background: linear-gradient(to bottom, #33ccff 0%, #ff99cc 100%);
		padding: 10px;
		border-radius: 10px;
		text-align: center;
		height: 620px;
		box-shadow: 0 0 1.5px 0px #b9b9b9;
	}

	.id-card img {
		margin: 0 auto;
	}

	.header img {
		width: 100px;
		margin-top: 15px;
	}

	.photo img {
		width: 150px;
		height: 150px;
		margin-top: 15px;
		border-radius: 50%;
	}

	h2 {
		font-size: 15px;
		margin: 5px 0;
	}

	h3 {
		font-size: 12px;
		margin: 2.5px 0;
		font-weight: 300;
	}

	.qr-code img {
		width: 50px;
	}

	p {
		font-size: 5px;
		margin: 2px;
	}

	.id-card-hook {
		background-color: #000;
		width: 70px;
		margin: 0 auto;
		height: 15px;
		border-radius: 5px 5px 0 0;
	}

	.id-card-hook:after {
		content: '';
		background-color: #d7d6d3;
		width: 47px;
		height: 6px;
		display: block;
		margin: 0px auto;
		position: relative;
		top: 6px;
		border-radius: 4px;
	}

	.id-card-tag-strip {
		width: 45px;
		height: 40px;
		background-color: #0950ef;
		margin: 0 auto;
		border-radius: 5px;
		position: relative;
		top: 9px;
		z-index: 1;
		border: 1px solid #0041ad;
	}

	.id-card-tag-strip:after {
		content: '';
		display: block;
		width: 100%;
		height: 1px;
		background-color: #c1c1c1;
		position: relative;
		top: 10px;
	}

	.id-card-tag {
		width: 0;
		height: 0;
		border-left: 100px solid transparent;
		border-right: 100px solid transparent;
		border-top: 100px solid #0958db;
		margin: -10px auto -30px auto;
	}

	.id-card-tag:after {
		content: '';
		display: block;
		width: 0;
		height: 0;
		border-left: 50px solid transparent;
		border-right: 50px solid transparent;
		border-top: 100px solid #d7d6d3;
		margin: -10px auto -30px auto;
		position: relative;
		top: -130px;
		left: -50px;
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
			var mywindow = window.open('', 'PRINT', 'height=400,width=600');
	
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
									<div class="id-card-holder">
										<div class="id-card">
											<div class="header">
												<img style="width:20%; height:10%" src="{{ asset('frontend/img/logo.png') }}">
												<h2 class="name" style="margin-top: 10px; margin-bottom: 10px;">GOFAMINT STUDENTS' FELLOWSHIP
												</h2>
												<h2 class="alias" style="margin-top: 10px; margin-bottom: 10px;">19th Biennial National
													Conference</h2>
												<p class="date">2nd April - 4th April 2020</p><br>
											</div>
											<div class="photo">
												<h1>@if($user->level == 'Participant' || $user->level == 'Alumni')PARTICIPANT
													@elseif($user->level == 'Moderator')PARTICIPANT
													@elseif($user->level == 'Official' || $user->level == 'Nec')OFFICIAL
													@elseif($user->level == 'Choir')CHOIR
													@elseif($user->level == 'Medical')MEDICAL PERSONNEL

													@endif

												</h1>
												<img
													src="{{ asset($user->passport ? '/'.$user->passport : '/frontend/passports/avatar.jpg') }}">
											</div>
											<div>
												<h3 style="font-size:25px">Name: {{ $user->name }}</h3>
												@if(isset($user->campus))
												<h3 style="font-size:15px">Campus </h3>
												<span><b>{{ $user->campus->name  }}</b></span>
												@endif
												<h3 style="font-size:15px"> <strong>Hostel:</strong>
													{{ isset($user->hostel->name) ? $user->hostel->name : 'Registration incomplete'  }}</h3>
												<h3 style="font-size:15px"> <b>Food Stand:</b>
													{{ isset($user->food->name) ? $user->food->name : 'Registration incomplete'  }}</h3>
												<div class="qr-code">
													<img src="{{ asset('frontend/img/qr.png') }}">
												</div>
											</div>
											<hr>
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