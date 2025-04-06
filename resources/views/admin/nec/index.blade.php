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
                            <button type="button" class="btn btn-info mt-1" data-toggle="modal" data-target="#myModal" title="Change reference ID"> <i class="fa fa-cycle"></i>Move NEC to archive</button>
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
												Office: {{ $n->office }} <br>
												B/Day: {{ $n->bday }} <br>
											</td>
											<td>{{ $n->order }}</td>
											<td style="padding-left: 5px;padding-right: 5px;">
												<a class="actions" data-toggle="tooltip" title="View/Edit Nec" href="{{ route('nec.edit', $n->id) }}"> <i class="bx bxs-edit actions"></i>
												</a>
												
												@if(auth()->user()->conference_role == 'superadmin')
												<a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');"
													title="Delete Nec" href="{{ route('nec.delete', $n->id) }}"> <i
													class="fa fa-trash"></i>
												</a> 
												@endif
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
	<div class="modal" id="myModal">
		<div class="modal-dialog">
			<div class="modal-content">

				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">Move NEC members to archive</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<!-- Modal body -->
				<div class="modal-body">
					<form action="{{ route('nec.archive')}}" method="GET">
						@csrf
						<?php 
							$from = range(date('1982'), date('Y'));
							$to = range(date('1982'), date('Y') + 2);
						?>
						<div class="row">
							<div class="col-md-12">
								<fieldset class="form-group">
									<label for="from">Nec Members in</label>
									<select name="from" class="form-control" id="from" required>
										@foreach($from as $f)
										<option value="{{$f}}">{{$f}}</option>
										@endforeach
									</select>
								</fieldset>
							</div>
							<div class="col-md-12">
								<fieldset class="form-group">
									<label for="to">To Archived Tenure of</label>
									<select name="to" class="form-control" id="to" required>
										@foreach($to as $f)
										{{-- @if(!in_array($f, range(date('Y'), date('Y')))) --}}
										<option value="{{$f}}">{{$f}}</option>
										{{-- @endif --}}
										@endforeach
									</select>
								</fieldset>
							</div>
							<div class="col-md-12 col-sm-12">
								<button class="btn btn-primary" style="width:100%" type="submit">Move</button>
								</form>
							</div>
						</div>
					</form>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	@endsection