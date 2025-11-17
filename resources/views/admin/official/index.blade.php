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
							<a href="{{ route('officials.create') }}" class="btn btn-primary mt-1">Add new official</a>
							
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
											<th>Type</th>
											<th>Name</th>
											<th>Email</th>
											<th>Phone</th>
											<th>Gender</th>
											<th>Added by</th>
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
											<td>
												@if($participant->level == 'Admin' && $participant->official == NULL)Admin  @endif
												@if($participant->level == 'Admin' && $participant->official == 'YES')Official @endif
											</td>
											
											<td>{{ $participant->name }}</td>
											<td>{{ $participant->email }}</td>
											<td>{{ $participant->phone }}</td>
											<td>{{ $participant->gender }}</td>
											<td style="color:green">{{ isset($participant->moderator) ?$participant->moderator->name : 'N/A'}}</td>
											<td style="padding-left: 5px;padding-right: 5px;">
												<a class="actions" data-toggle="tooltip" title="View/Edit Official"
													href="{{ route('officials.edit', $participant->id) }}"> <i class="bx bxs-edit actions"></i>
												</a>
												<a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');"
														title="Delete Official" href="{{ route('officials.delete', $participant->id) }}"> <i
														class="fa fa-trash"></i>
												</a> 
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
	</section> 
</div> 
@endsection