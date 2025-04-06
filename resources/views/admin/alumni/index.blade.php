@extends('layouts.dashboard')
@section('title', 'Alumni')
@section('active')
<li class="breadcrumb-item">Alumni</li>
@endsection
@section('content')
<div class="content-body">
	<!-- Zero configuration table -->
	<section id="basic-datatable">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">All Alumni</h4>
						<div class="">
							<a href="{{ route('users.create') }}" class="btn btn-primary mt-1">Add new</a>
							<a href="{{ route('alumnis.import.index') }}" class="btn btn-primary mt-1">Import</a>
							<a href="{{ route('alumnis.export') }}" class="btn btn-primary mt-1">Export</a>
						</div>
						@include('includes.alerts')

					</div>
					<div class="card-content">
						<div class="card-body card-dashboard">
							<div class="table-responsive">
								<table class="table zero-configuration">
									<thead>
										<tr>
											<th>S/N</th>
											<th>Conference ID</th>
											<th>Status</th>
											<th>Name</th>
											<th>Email</th>
											<th>Phone</th>
											<th>Amount Paid</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										@foreach($participants as $participant)
										<tr>
											<td>{{ $count }}</td>
											<td>{{ $participant->conference_number }}</td>
											<td>@if($participant->registration_status == 'Complete')
												<i class="bx bxs-circle success font-small-1 mr-50"></i><small>Complete</small> 
												@else
												<i class="bx bxs-circle danger font-small-1 mr-50"></i><small>Pending</small>
												@endif
											</td>

											<td>{{ $participant->name }}</td>
											<td>{{ $participant->email }}</td>
											<td>{{ $participant->phone }}</td>
											<td>&#8358;{{ $participant->amount_paid }}</td>



											<td style="padding-left: 5px;padding-right: 5px;">
												<a class="actions" data-toggle="tooltip" title="View/Edit User"
													href="{{ route('alumni.edit', $participant->id) }}"> <i class="bx bxs-edit actions"></i></
														</a> <a class="actions" data-toggle="tooltip" data-placement="top" title="Switch To"
														href="{{ route('switchuser', $participant->id) }}"><i class="fa fa-unlock actions"></i>
												</a>
												
												<a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');"
													title="Delete User" href="{{ route('alumni.delete', $participant->id) }}"> <i
														class="fa fa-trash"></i></a>
											</td> 
										</tr> 
										@endforeach 
									</tbody>
										
								</table> 
								</div> 
							</div>				
						</div> 
					</div> 
				</div>
			</div>
		</div>
	</section> <!--/ Zero configuration table -->
@endsection