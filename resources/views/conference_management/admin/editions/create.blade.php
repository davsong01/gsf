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
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="">Select...</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                      
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="conference_theme">Conference theme</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="conference_theme" value="{{ old('conference_theme') }}" id="conference_theme">
                                        <div class="form-control-position">
                                           &#8962;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="slug">Conference slug(Will be used on ID cards)</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="slug" value="{{ old('slug') }}" id="conference_theme">
                                        <div class="form-control-position">
                                           &#8962;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="registration_fee">Registration Fee</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="number" class="form-control" name="registration_fee" value="{{ old('registration_fee') }}" id="registration_fee">
                                        <div class="form-control-position">
                                            &#8358;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="official_email">Official Email</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="official_email" value="{{ old('official_email') }}" id="official_email">
                                        <div class="form-control-position">
                                            &#128231;
                                        </div>

                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="new_alumni_registration_fee">New Alumni Fee</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="number" class="form-control" name="new_alumni_registration_fee" value="{{ old('new_alumni_registration_fee') }}" id="new_alumni_registration_fee" required>
                                        <div class="form-control-position">
                                            &#8358;
                                        </div>
                                    </fieldset>
                                </div>
                                
                                <div class="col-sm-6 col-md-6">
                                    <label for="alumni_registration_fee">Old Alumni Fee</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="number" class="form-control" name="alumni_registration_fee" value="{{ old('alumni_registration_fee') }}" id="alumni_registration_fee" required>
                                        <div class="form-control-position">
                                            &#8358;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="start_date">Conference Start Date</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}" id="start_date">
                                        <div class="form-control-position">
                                            &#128197;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="end_date">Conference End Date</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}" id="end_date">
                                       <div class="form-control-position">
                                            &#128197;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="close_registration">Registration Close date</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="date" class="form-control" name="close_registration" value="{{ old('close_registration') }}" id="close_registration">
                                       <div class="form-control-position">
                                            &#128197;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="random_hostel">Enable Random Hostel</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <select class="form-control" name="random_hostel" id="random_hostel" required>
                                            <option value="">Select...</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                      
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="random_hostel">Enable Random Foodstand</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <select class="form-control" name="random_foodstand" id="random_foodstand" required>
                                            <option value="">Select...</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                      
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="reg_prefix">Registration Prefix</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="reg_prefix" value="{{ old('reg_prefix') }}" id="reg_prefix">
                                        <div class="form-control-position">
                                            &#128231;
                                        </div>
                                    </fieldset>
                                </div>
                                
                                <div class="col-sm-12 col-md-12">
                                    <label for="conference_overview">Conference Overview</label><small> You can use html tags here</small>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <textarea class="form-control" id="conference_overview" rows="3" name="conference_overview" rows="10" cols="200"></textarea>
                                        <div class="form-control-position">
                                           &#9745;
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                   <h3> Payment Settings</h3>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="PAYSTACK_PUBLIC_KEY">Paystack Public Key</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="PAYSTACK_PUBLIC_KEY" value="{{ old('PAYSTACK_PUBLIC_KEY') }}" id="PAYSTACK_PUBLIC_KEY">
                                        
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="PAYSTACK_SECRET_KEY">Paystack Secret Key</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="PAYSTACK_SECRET_KEY" value="{{ old('PAYSTACK_SECRET_KEY') }}" id="PAYSTACK_SECRET_KEY">
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="MERCHANT_EMAIL">Merchant Email</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="MERCHANT_EMAIL" value="{{ old('MERCHANT_EMAIL') }}" id="MERCHANT_EMAIL">
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