@extends('layouts.stakeholderdashboard')

@section('title', 'Import Users')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('stakeholders.users.index') }}">Members</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Import Users</li>
@endsection
@section('content')
<div class="income-order-visit-user-area mg-t-40">
	<div class="container">

	</div>
</div>
<!-- Transitions Start-->
<div class="transition-world-area">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="transition-world-list shadow-reset">
					<div class="sparkline7-list mg-b-40">
						<div class="sparkline7-hd">
							<div class="main-spark7-hd">
								<h1>Import Users</h1>
								<a class="btn btn-primary" href="/frontend/exportsamples/userssample.xlsx"><i class="fa fa-download"></i> Download
									sample</a>
							</div>

						</div>
						<div class="sparkline7-graph">
							<div class="row">

								<div class="card-header">
									<p>Select an excel file to upload, please pay attention to the following: </p>
									<ul>
										<li>
										<b>Only Excel format is acceptable</b>
										</li>
										<li>
										<b>Please pay attention to the instructions in the sample file</b>
										</li>
										<li><b>There must be no spaces after the last line in the excel file to be imported</b></li>
										<li><b>Default Password for each user is the user's phone number</b></li>

										</li>
									</ul>
								</div>
							</div>
							<form onsubmit="return confirm('Are you sure?')" action="{{ route('stakeholders.users.import') }}" method="POST" name="importform" enctype="multipart/form-data"
									class="@if($errors->any()) has-error @endif">
									@csrf

									<fieldset class="form-group">
                                        <label for="type">Type</label>
                                        <select class="form-control" name="type" id="type" required>
                                            <option value="">--Select Type--</option>
                                            <option value="0">Undergraduate</option>
                                            <option value="1">Alumni</option>
										</select>
                                    </fieldset>

									<label for="file">Upload file</label>
									<input type="file" name="file" class="form-control" accept=".csv, .xlsv, .xls, .xlsx" required>
									<br>
									@error('file')
									<div class="alert alert-danger" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
										  {{ $message }}
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
<!-- Transitions End-->
@endsection
