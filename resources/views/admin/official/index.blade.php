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
											<th>Login ID</th>
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
											<td>{{ $loop->iteration }}</td>
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
											<td style="padding-left: 5px; padding-right: 5px;">
    <div class="btn-group" role="group" aria-label="User Actions">
        {{-- Edit Button --}}
        <a href="{{ route('officials.edit', $participant->id) }}" class="btn btn-sm btn-primary">
            Edit
        </a>

        {{-- Delete Button --}}
        <a href="{{ route('officials.delete', $participant->id) }}" class="btn btn-sm btn-danger"
           onclick="return confirm('Are you really sure?');">
            Delete
        </a>
    </div>
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
