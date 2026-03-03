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
                                            <option value="1" {{ old('template_id') == '1' ?'selected':'' }}>Template 1</option>
                                            <option value="2"  {{ old('template_id') == '2' ?'selected':'' }}>Template 2</option>
                                            <option value="3"  {{ old('template_id') == '3' ?'selected':'' }}>Aivent</option>

                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="conference_theme">Conference theme</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="conference_theme" value="{{ old('conference_theme') }}" id="conference_theme" required>

                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="slug">Conference slug(Will be used on ID cards)</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="slug" value="{{ old('slug') }}" id="slug" required>

                                    </fieldset>
                                </div>

                                <div class="col-sm-6 col-md-6">
                                    <label for="official_email">Official Email</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="official_email" value="{{ old('official_email') }}" id="official_email" required>

                                    </fieldset>
                                </div>

                                <div class="col-sm-6 col-md-6">
                                    <label for="start_date">Conference Start Date</label>
                                    <fieldset class="form-group  ">
                                        <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}" id="start_date required">

                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="end_date">Conference End Date</label>
                                    <fieldset class="form-group  ">
                                        <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}" id="end_date" required>

                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="start_registration">Registration Start date</label>
                                    <fieldset class="form-group">
                                        <input type="date" class="form-control" name="start_registration" value="{{ old('start_registration') }}" id="start_registration" required>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="close_registration">Registration Close date</label>
                                    <fieldset class="form-group">
                                        <input type="date" class="form-control" name="close_registration" value="{{ old('close_registration') }}" id="close_registration" required>

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
                                        <select class="form-control" name="service_point_assignment_type" id="service_point_assignment_type">
                                            <option value="">Select...</option>
                                            @foreach (servicePointAssignmentTypes() as $key => $value)
                                            <option value="{{ $key }}" {{ old('service_point_assignment_type') == $value ? 'selected':'' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-sm-6 col-md-6">
                                    <label for="reg_prefix">Registration Prefix</label>
                                    <fieldset class="form-group  ">
                                        <input type="text" class="form-control" name="reg_prefix" value="{{ old('reg_prefix') }}" id="reg_prefix">

                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                        <label for="ministry_id">Ministry</label>
                                        <fieldset class="form-group">
                                            <select class="form-control" name="ministry_id" id="ministry_id" required>
                                                <option value="">Select Ministry...</option>
                                                @foreach ($ministries as $ministry)
                                                    <option value="{{ $ministry->id }}" {{old('ministry_id') == $ministry->id ? 'selected':'' }}>
                                                        {{ $ministry->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                <div class="col-sm-12 col-md-12">
                                    <label for="conference_overview">Conference Overview</label><small> You can use html tags here</small>
                                    <fieldset class="form-group  ">
                                        <textarea class="form-control" id="conference_overview" rows="3" name="conference_overview" rows="10" cols="200" required></textarea>

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
