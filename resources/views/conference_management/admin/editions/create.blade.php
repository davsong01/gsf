@extends('layouts.dashboard')
@section('title', 'Create Conference edition')
@section('active')
<li class="breadcrumb-item">Create Conference Edition</li>
@endsection
@section('content')
<div class="content-body">
<section id="input-with-icons">
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ config('app.name') }} Conference Editions</h4>
                     @include('includes.alerts')
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('conferenceeditions.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6 col-md-6">
                                    <label for="enable_conference">Status</label>
                                    <fieldset class="form-group  ">
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="">Select...</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="template_id">Template to Use</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <select class="form-control" name="template_id" id="template_id" required>
                                            <option value="">Select...</option>
                                            <option value="1" {{ old('emplate_id') == '1' ?'selected':'' }}>Template 1</option>
                                            <option value="2"  {{ old('emplate_id') == '2' ?'selected':'' }}>Template 2</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="conference_theme">Conference theme</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="conference_theme" value="{{ old('conference_theme') }}" id="conference_theme">
                                        
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="slug">Conference slug(Will be used on ID cards)</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="slug" value="{{ old('slug') }}" id="conference_theme">
                                       
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="registration_fee">Registration Fee</label>
                                    <fieldset class="form-group  ">
                                        <input type="number" class="form-control" name="registration_fee" value="{{ old('registration_fee') }}" id="registration_fee">
                                        
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="official_email">Official Email</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="official_email" value="{{ old('official_email') }}" id="official_email">
                                       
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="new_alumni_registration_fee">New Alumni Fee</label>
                                    <fieldset class="form-group  ">
                                        <input type="number" class="form-control" name="new_alumni_registration_fee" value="{{ old('new_alumni_registration_fee') }}" id="new_alumni_registration_fee" required>
                                        
                                    </fieldset>
                                </div>
                                
                                <div class="col-sm-6 col-md-6">
                                    <label for="alumni_registration_fee">Old Alumni Fee</label>
                                    <fieldset class="form-group  ">
                                        <input type="number" class="form-control" name="alumni_registration_fee" value="{{ old('alumni_registration_fee') }}" id="alumni_registration_fee" required>
                                        
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="start_date">Conference Start Date</label>
                                    <fieldset class="form-group  ">
                                        <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}" id="start_date">
                                       
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="end_date">Conference End Date</label>
                                    <fieldset class="form-group  ">
                                        <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}" id="end_date">
                                       
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="start_registration">Registration Start date</label>
                                    <fieldset class="form-group">
                                        <input type="date" class="form-control" name="start_registration" value="{{ old('start_registration') }}" id="start_registration">
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="close_registration">Registration Close date</label>
                                    <fieldset class="form-group">
                                        <input type="date" class="form-control" name="close_registration" value="{{ old('close_registration') }}" id="close_registration">
                            
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="hostel_assignment_type">Hostel Assignment Type</label>
                                    <fieldset class="form-group ">
                                        <select class="form-control" name="hostel_assignment_type" id="hostel_assignment_type" required>
                                            <option value="">Select...</option>
                                            @foreach (hostelAssignmentTypes() as $key => $value)
                                            <option value="{{ $key }}" {{ old('hostel_assignment_type') == $value ? 'selected':'' }}>{{ $value }}</option> 
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="service_point_assignment_type">Service Point Assignemt Type</label>
                                    <fieldset class="form-group ">
                                        <select class="form-control" name="service_point_assignment_type" id="service_point_assignment_type" required>
                                            <option value="">Select...</option>
                                            @foreach (servicePointAssignmentTypes() as $key => $value)
                                            <option value="{{ $key }}" {{ old('service_point_assignment_type') == $value ? 'selected':'' }}>{{ $value }}</option> 
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                {{-- <div class="col-sm-6 col-md-6">
                                    <label for="random_hostel">Enable mass registration</label>
                                    <fieldset class="form-group ">
                                        <select class="form-control" name="mass_registration" id="mass_registration" required>
                                            <option value="">Select...</option>
                                            <option value="yes" {{ old('mass_registration') == 'yes'?'selected':'' }}>Yes</option>
                                            <option value="no"  {{ old('mass_registration') == 'no'?'selected':'' }}>No</option>
                                        </select>
                                      
                                    </fieldset>
                                </div> --}}
                                <div class="col-sm-6 col-md-6">
                                    <label for="reg_prefix">Registration Prefix</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="reg_prefix" value="{{ old('reg_prefix') }}" id="reg_prefix">
                                        
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="ministry">Ministry</label>
                                    <fieldset class="form-group ">
                                        <select class="form-control" name="ministry" id="ministry" required>
                                            <option value="">Select...</option>
                                            <option value="gsf" {{ old('ministry') == 'gsf'?'selected':'' }}>GSF</option>
                                            <option value="gyf"  {{ old('ministry') == 'gyf'?'selected':'' }}>GYF</option>
                                        </select>
                                      
                                    </fieldset>
                                </div>
                                <div class="col-sm-12 col-md-12">
                                    <label for="conference_overview">Conference Overview</label><small> You can use html tags here</small>
                                    <fieldset class="form-group  ">
                                        <textarea class="form-control" id="conference_overview" rows="3" name="conference_overview" rows="10" cols="200"></textarea>
                                       
                                    </fieldset>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                   <h3> Payment Settings</h3>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="PAYSTACK_PUBLIC_KEY">Paystack Public Key</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="PAYSTACK_PUBLIC_KEY" value="{{ old('PAYSTACK_PUBLIC_KEY') }}" id="PAYSTACK_PUBLIC_KEY">
                                        
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="PAYSTACK_SECRET_KEY">Paystack Secret Key</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="PAYSTACK_SECRET_KEY" value="{{ old('PAYSTACK_SECRET_KEY') }}" id="PAYSTACK_SECRET_KEY">
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="MERCHANT_EMAIL">Merchant Email</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="MERCHANT_EMAIL" value="{{ old('MERCHANT_EMAIL') }}" id="MERCHANT_EMAIL">
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="enable_sub_account">Enable Sub Account</label>
                                    <fieldset class="form-group  ">
                                        <select class="form-control" name="enable_sub_account" id="enable_sub_account" required>
                                            <option value="">Select...</option>
                                            <option value="yes" {{ old('enable_sub_account') == 'yes'?'selected':'' }}>Yes</option>
                                            <option value="no" {{ old('enable_sub_account') == 'no'?'selected':'' }}>No</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="paystack_subaccount_id">Paystack Subaccount ID</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="paystack_subaccount_id" value="{{ old('paystack_subaccount_id') }}" id="paystack_subaccount_id">
                                    </fieldset>
                                </div>
                            </div>
                            <button class="btn btn-primary" style="width:100%; margin-top:10px" type="submit">Save</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        CKEDITOR.replace( 'conference_overview' );</script>
    <script>

        var currentEditor; // selected / focused editor
        var currentFormats; // save the current formattings

        createEditor("#editor1");
        createEditor("#editor2");

        function createEditor(selector)
        {
            let quill = new Quill(selector, { });

            quill.on("editor-change", (eventName, ...args) =>
            {
                currentEditor = quill;
                updateButtons();
            });
        }

        // get current formattings to style the toolbar buttons
        function updateButtons()
        {
            if(currentEditor.getSelection())
            {
                currentFormats = currentEditor.getFormat();

                if(currentFormats.bold)
                {
                    bold.classList.add("active");
                }
                else
                {
                    bold.classList.remove("active");
                }
            }
        }

        // if selected text is bold => unbold it - if it isn't => bold it
        function onBoldClick()
        {
            if(!currentFormats || !currentEditor)
            {
                return;
            }

            if(currentFormats.bold)
            {
                currentEditor.format("bold", false);
            }
            else
            {
                currentEditor.format("bold", true);
            }
        }

    </script>

</section>
    <!-- Basic Inputs end -->
</div>
@endsection