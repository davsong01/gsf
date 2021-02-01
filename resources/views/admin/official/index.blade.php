@extends('layouts.dashboard')
@section('title', 'Official')
@section('active')
<li class="breadcrumb-item">All Officials</li>
@endsection
@section('content')
<div class="content-body">
	<!-- Zero configuration table -->
	<section id="basic-datatable">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">All Officials</h4>
						<div class="">
							<a href="{{ route('users.create') }}" class="btn btn-primary mt-1">Add new</a>
							<a href="{{ route('officials.import.index') }}" class="btn btn-primary mt-1">Import</a>
							<a href="{{ route('officials.export') }}" class="btn btn-primary mt-1">Export</a>
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
											<th>Date</th>
											<th>Avatar</th>
											<th>Conference ID</th>
											<th>Level</th>
											<th>Status</th>
											<th>Name</th>
											<th>Email</th>
											<th>Phone</th>
											<th>Amount Paid</th>
											<th>Uploaded by</th>

											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										@foreach($participants as $participant)
										<tr>
											<td>{{ $count++ }}</td>
											<td>{{ $participant->created_at->format('Y-m-d') }}</td>
											<td><img class="mr-1" style="border-radius:50%"
													src="{{ asset($participant->passport ? $participant->passport : 'frontend/passports/avatar.jpg') }}"
													alt="avatar" height="40" width="40"></td>
											<td>{{ $participant->conference_number }}</td>
											<td>{{ $participant->level }}</td>
											<td>@if($participant->registration_status == 'Complete')
												<i class="bx bxs-circle success font-small-1 mr-50"></i> @else
												<i class="bx bxs-circle danger font-small-1 mr-50"></i>
												@endif
											</td>

											<td>{{ $participant->name }}</td>
											<td>{{ $participant->email }}</td>
											<td>{{ $participant->phone }}</td>
											<td>&#8358;{{ $participant->amount_paid }}</td>
											<td>@if(isset($participant->moderator->name) && ($participant->level) ==
												'Participant'){{ $participant->moderator->name }}
												@else N/A @endif
											</td>

											<td style="padding-left: 5px;padding-right: 5px;">
												<a class="actions" data-toggle="tooltip" title="View/Edit Nec"
													href="{{ route('choir.edit', $participant->id) }}"> <i class="bx bxs-edit actions"></i></ </a>
														<a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');"
														title="Delete Nec" href="{{ route('users.delete', $participant->id) }}"> <i
														class="fa fa-recycle"></i></ </a> </td> </tr> @endforeach </tbody> </table> </div> </div>
														</div> </div> </div> </div> </section> </div> @endsection