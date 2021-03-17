<div class="container">
    <script>
        var alumnis_amount = {!! json_encode($alumnis_amount, JSON_HEX_TAG) !!};

    </script>
    <div class="row align-items-center">
        <div class="col-md-12">
            <hr>
            <div class="contact-form">
                <div id="success">
                    <h6 style="color:green">Kindly fill the form below and click proceed to payment</h6>
                </div>
                <form action="{{ route('pay') }}" method="POST">
                    @csrf
                    <div class="control-group">
                        <label>Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Enter your full names" required="required">
                    </div>

                    <div class="control-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                            required="required">
                    </div>
                    <div class="control-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            placeholder="Enter your phone number" required="required">
                    </div>

                    <div class="control-group">
                        <label>Old alumni/New alumni?</label>
                        <select name="alumni_type" class="form-control select2 @error('alumni_type')is-invalid @enderror"
                            onchange="document.querySelector('#alumni_amount').value = alumnis_amount[this.value]?alumnis_amount[this.value]*100:''"
                            required>
                            <option value="">Select alumni type</option>
                            <option value="new_alumni_registration_fee">New Alumni</option>
                            <option value="alumni_registration_fee">Old Alumni</option>
                        </select>
                        @error('alumni_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <br>
                    {{-- <input type="hidden" name="orderID" value="345"> --}}
                    <div class="control-group">
                        <input class="form-control" type="text" name="amount" id="alumni_amount"
                            value="{{ $setting->alumni_fee * 100 }}" readonly required> {{-- required in kobo --}}
                    </div>
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="currency" value="NGN">
                    <input type="hidden" name="metadata" value="{{ json_encode($array = ['type' => '3']) }}">
                    {{-- For other necessary things you want to add to your payload. it is optional though --}}
                    <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"> {{-- employ this in place of csrf_field only in laravel 5.0 --}}
                    <div class="control-group">
                        <button class="btn submitregistration" type="submit" style="width:100%">Proceed to
                            Payment</button>
                    </div>
                </form>
            </div>
            <hr>
        </div>
    </div>
</div>
