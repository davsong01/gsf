@extends('layouts.participant_single_edition')
@section('title', 'My Conferences')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('conferencemanagement.index', ['edition'=>$edition->id]) }}">Conferences</a></li>
@endsection

@section('single')
<section id="dashboard-analytics">
	<div class="row">
		@if(isset(auth()->user()->transactions))
			@foreach(auth()->user()->transactions->sortByDesc('created_at') as $payment)
			
			<div class="col-md-6 col-sm-12">
				<div class="card" style="height: 250px; background-color: {{ $payment->edition->id == $edition->id ? 'green':'gray;'}}">
					<a href="{{ route('conferencemanagement.show', ['conferencemanagement'=>$payment->id, 'edition'=>$payment->conference_edition_id]) }}">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4 class="card-title" style="color: white;">{{ $payment->edition->conference_theme }} conference <br><span style="color:{{ $payment->edition->status == 'active' ? 'black' : '#b30e0e'}}"> {{ $payment->edition->status }}</span></h4> <br>
						<small style="color:{{ $payment->edition->id == $edition->id ? 'white':'black'}}">{{ formatDates($payment->edition->start_date, $payment->edition->end_date) }}<br> <span style="color:yellow">{{ $payment->level }}</span> </small>
					</div>
					<div class="card-content">
						<div class="card-body">
							<span style="color:yellow">Family ID: <strong style="color:white;font-weight: bold;">{{ $payment->user->family_id }}</strong></span> <br>
							<span style="color:yellow">Transaction ID: <strong style="color:white;font-weight: bold;">{{ $payment->transid }}</strong></span> <br>
							{{-- @if($payment->edition->id == App\ConferenceEdition::where('status', 'active')->first()->id) --}}
							<center style="padding-top: 10px;">
							<button class="btn btn-primary">Click to Access</button></center> 
							{{-- @endif --}}
						</div>
					</div>
				</a>
				</div>
			</div>
			@endforeach
		@endif
	</div>
</section>
@endsection