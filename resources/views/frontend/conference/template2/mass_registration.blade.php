@extends('frontend.conference.template1.index')
@section('css')
    <style>
        .mpopup {
          display: none;
          position: fixed;
          z-index: 1;
          padding-top: 100px;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          overflow: auto;
          background-color: rgb(0,0,0);
          background-color: rgba(0,0,0,0.4);
      }
      .modal-content {
          position: relative;
          background-color: #fff;
          margin: auto;
          padding: 0;
          width: 450px;
          box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
          -webkit-animation-name: animatetop;
          -webkit-animation-duration: 0.4s;
          animation-name: animatetop;
          animation-duration: 0.4s;
          border-radius: 0.3rem;
      }
      .modal-header {
          padding: 2px 12px;
          background-color: #ffffff;
          color: #333;
          border-bottom: 1px solid #e9ecef;
          border-top-left-radius: 0.3rem;
          border-top-right-radius: 0.3rem;
      }
      .modal-header h2{
          font-size: 1.25rem;
          margin-top: 14px;
          margin-bottom: 14px;
      }
      .modal-body {
          padding: 2px 12px;
      }
      .modal-footer {
          padding: 1rem;
          background-color: #ffffff;
          color: #333;
          border-top: 1px solid #e9ecef;
          border-bottom-left-radius: 0.3rem;
          border-bottom-right-radius: 0.3rem;
          text-align: right;
      }

      .close {
          color: #888;
          float: right;
          font-size: 28px;
          font-weight: bold;
      }
      .close:hover, .close:focus {
          color: #000;
          text-decoration: none;
          cursor: pointer;
      }

      /* add animation effects */
      @-webkit-keyframes animatetop {
          from {top:-300px; opacity:0}
          to {top:0; opacity:1}
      }

      @keyframes animatetop {
          from {top:-300px; opacity:0}
          to {top:0; opacity:1}
      }
    </style>

@endsection
@section('sec-content')


<!-- Modal popup box -->
<div id="mpopupBox" class="mpopup">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">×</span>
            <h2>Simple Modal Popup</h2>
        </div>
        <div class="modal-body">
            <p>This is a modal popup created with JavaScript and CSS</p>
            <p>Insert content here...</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary">Save changes</button>
        </div>
    </div>
</div>
<script>
// Select modal
var mpopup = document.getElementById('mpopupBox');

// Select trigger link
var mpLink = document.getElementById("mpopupLink");

// Select close action element
var close = document.getElementsByClassName("close")[0];

// Open modal once the link is clicked
mpLink.onclick = function() {
    mpopup.style.display = "block";
};

// Close modal once close element is clicked
close.onclick = function() {
    mpopup.style.display = "none";
};

// Close modal when user clicks outside of the modal box
window.onclick = function(event) {
    if (event.target == mpopup) {
        mpopup.style.display = "none";
    }
};
</script>
<section class="pb-6">

  <div class="container">
    <!-- Link to trigger modal -->
<a href="javascript:void(0);" class="btn btn-primary" id="mpopupLink">Launch Modal Popup</a>

    <div class="row flex-center">
      <div class="col-lg-12 col-md-12 order-md-1">
        <div class="contact-form">
				<div id="success">
					<h6 style="color:green">Kindly fill the form below and click proceed to payment</h6>
				</div>
				<form action="{{ route('pay') }}" method="POST">
					@csrf
					<div class="control-group">
						<label>Name</label>
						<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
							placeholder="Enter your full name" required="required">
						@error('name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
						@enderror
					</div>
					<div class="control-group">
						<label>Email</label>
						<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
							placeholder="Enter your email" required="required">
						@error('email')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
						@enderror
					</div>
					<div class="control-group">
						<label>Phone</label>
						<input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
							placeholder="Enter your phone number" required="required">
						@error('phone')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
						@enderror
					</div>
					<div class="control-group">
						<label for="gender">Gender</label><br>
						<select name="gender" class="form-control" id="gender"
							class="chapter" required>
							<option value="">--Select</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
					</div>
					<div class="control-group">
                <label for="chapter">GSF Campus</label> <small style="color:blue">(Please Select "other" if you are coming from an assembly)</small><br>
                <select name="chapter" class="form-control select2 chapterind" id="chapterind" required>
                    <option value="">--Select Campus</option>
                    @foreach($chapters as $chapter)
                        <option value="{{ $chapter->id }}" {{ old('chapter') == $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>  
                    @endforeach
                </select>
            </div>
					</div>

					<br>
					{{-- <input type="hidden" name="orderID" value="345"> --}}
					<input type="hidden" name="amount" value="{{ $setting->registration_fee * 100 }}"> {{-- required in kobo --}}
					<input type="hidden" name="quantity" value="1">
					<input type="hidden" name="currency" value="NGN">
					<input type="hidden" name="metadata[]" id="metadata">
					<input type="hidden" name="metadata" value="{{ json_encode($array = ['type' => '1',]) }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					{{-- employ this in place of csrf_field only in laravel 5.0 --}}

					<div class="control-group">
						<button class="btn submitregistration" type="submit" style="width:100%">Proceed to Payment</button>
					</div>
				</form>
			</div>
      </div>
     
    </div>
  </div>
  <!-- end of .container-->
</section>
@endsection 