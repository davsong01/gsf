@extends('layouts.dashboard')
@section('title', 'Official')
@section('active')
<li class="breadcrumb-item">All Officials</li>
@endsection
@section('content')
<style>
    .details{
        font-weight: normal
    }
</style>
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

					</div>
					<div class="card-content">
						<div class="card-body card-dashboard">
							<div class="table-responsive">
								<table class="table zero-configuration">
									<thead>
										<tr>
											<th>S/N</th>
											<th>Avatar</th>
											<th>Details</th>
											<th>Last Login</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										@foreach($participants as $participant)
										<tr>
											<td>{{ $loop->iteration }}</td>
                                            <td>{!! renderAvatar($participant, 40, 'mr-1') !!}</td>
											<td>
                                                LoginID: <span class="details">{{ $participant->family_id }}</span> <br>
                                                Name: <span class="details">{{ $participant->name }}</span> <br>
                                                Email: <span class="details">{{ $participant->email }}</span> <br>
                                                Phone: <span class="details">{{ $participant->phone }}</span> <br>
                                                Gender: <span class="details">{{ $participant->gender }}</span>
                                            </td>
                                            <td>
                                                {{$participant->last_login}}
                                            </td>
											<td style="padding-left: 5px; padding-right: 5px;">
                                                <div class="btn-group" role="group" aria-label="User Actions">
                                                    {{-- Edit Button --}}
                                                    <a href="{{ route('officials.edit', $participant->id) }}" class="btn btn-sm btn-primary">
                                                        Edit
                                                    </a>

                                                    <a href="{{ route('switchuser', $participant->id) }}"
                                                       class="btn btn-sm btn-warning"
                                                       onclick="return confirm('Login as this official?');">
                                                        Login as
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
