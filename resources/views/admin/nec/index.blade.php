@extends('layouts.dashboard')
@section('title', 'Necs')
@section('active')
<li class="breadcrumb-item">Nec Members</li>
@endsection
@section('content')
<div class="content-body">
	<!-- Zero configuration table -->
	<section id="basic-datatable">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">All Nec Members</h4>
						<div class="">
							<a href="{{ route('nec.create') }}" class="btn btn-primary mt-1">Add new</a>
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
											<th>Order</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										@foreach($nec as $n)
										<tr>
											<td>{{ $count++ }}</td>
											<td><img class="mr-1" style="border-radius:50%"
													src="{{ asset($n->passport ? $n->passport : 'frontend/passports/avatar.jpg') }}"
													alt="avatar" height="40" width="40"></td>
											
											<td>
												Name: {{ $n->name }} <br>
												Email: {{ $n->email }} <br>
												Phone: {{ $n->phone }} <br>
												Office: {{ $n->office }}
											</td>
											<td>{{ $n->order }}</td>
											<td style="padding-left: 5px;padding-right: 5px;">
												<a class="actions" data-toggle="tooltip" title="View/Edit Nec" href="{{ route('nec.edit', $n->id) }}"> <i class="bx bxs-edit actions"></i>
												</a>
												<a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');"
													title="Delete Nec" href="{{ route('nec.delete', $n->id) }}"> <i
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