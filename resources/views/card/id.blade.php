<?php 
	$setting = $payment->edition;
?>
@extends('layouts.dashboard')
@section('title', 'Conference Badge')
@section('item')
<li class="breadcrumb-item"><a href="{{ route('conferencemanagement.show', ['conferencemanagement'=>$payment->id, 'edition'=>$payment->conference_edition_id]) }}">{{ $setting->conference_theme }}</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Conference Badge</li>
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
	{{-- <script>
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
	</script> --}}
	<section id="dashboard-ecommerce">
		<div class="row">
			<!-- Greetings Content Starts -->
			<div class="col-md-12 col-12 dashboard-greetings">
				<div class="card-header">
					<a style="color:white" href="{{ url($payment->badge_location) }}" class="btn btn-primary mt-1" download>Download Badge</a>
				</div>

				<div class="card" style="max-width: 600px; margin: 0 auto; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); border-radius: 10px; padding: 20px;">
					<div class="card-content">
						<div class="card-body">
							<div class="row">
								<img src="{{ url($payment->badge_location) }}" alt="" style="width: 100%; height: auto;">
							</div>
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