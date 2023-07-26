@extends('layouts.conference')
@section('title', 'Participant Import')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('conference.participants',['type'=>'Participant', 'edition'=>$edition->id]) }}">Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Add Participant</li>
@endsection
@section('content2')
<!-- Transitions Start-->
<div class="content-body">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="transition-world-list shadow-reset">
					<div class="sparkline7-list mg-b-40">
						<div class="sparkline7-hd">
							<div class="main-spark7-hd">
								<h3>Import {{ $type ?? null }}</h3>
								@include('includes.alerts')
							</div>

						</div>
						<div class="sparkline7-graph">
							<div class="row">
								<a style="margin-left: 20px;margin-bottom: 20px;" class="btn btn-primary" href="{{ route('conference.usersexport.sample',['type'=>$type]) }}"><i class="fa fa-download"></i> Download
									sample</a>
								<div class="card-header">
									<p>Select an excel file to upload, please pay attention to the following: </p>
									<ul>
										<li>
											Only Excel format is acceptable
										</li>
										<li>
											Name and email must be present
										</li>
										<li>There must be no spaces after the last line in the excel file to be imported</li>
									</ul>

								</div>

								<form action="{{ route('conferenceuser.import',['type'=>$type, 'edition'=>$edition->id]) }}" method="POST" name="importform" enctype="multipart/form-data"
									class="@if($errors->any()) has-error @endif">
									@csrf
									<input type="file" name="file" class="form-control" accept=".csv, .xlsv, .xls, .xlsx" required>
									<input type="hidden" name="import_level" value="{{ $type }}">
									<br>
									@error('file')
									<div class="alert alert-danger" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
										<strong>Whoops!</strong> {{ $message }}
									</div>
									@enderror

									<button class="btn btn-success submit-button">Import File</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Transitions End-->
@endsection