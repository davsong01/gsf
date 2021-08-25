@extends('layouts.dashboard')
@section('title', 'Update setting')
@section('active')
<li class="breadcrumb-item">Settings</li>
@endsection
@section('content')
@section('content')
<div class="content-body">

<section id="input-with-icons">
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ config('app.name') }} Settings</h4>
                    @include('includes.alerts')
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('settings.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-12">
                                    <p>You can set Application variables here</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-md-6">
                                    <label for="conference_theme">Conference theme</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="conference_theme" value="{{ old('conference_theme') ?? $setting->conference_theme }}" id="conference_theme">

                                        <div class="form-control-position">
                                           &#8962;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="registration_fee">Registration Fee</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="number" class="form-control" name="registration_fee" value="{{ old('registration_fee') ?? $setting->registration_fee }}" id="registration_fee">
                                        <div class="form-control-position">
                                            &#8358;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="official_email">Official Email</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="official_email" value="{{ old('official_email') ?? $setting->official_email }}" id="official_email">
                                        <div class="form-control-position">
                                            &#128231;
                                        </div>

                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="official_reports_email">Official Reports Email</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="official_reports_email" value="{{ old('official_email') ?? $setting->official_email }}" id="official_reports_email">
                                        <div class="form-control-position">
                                            &#128231;
                                        </div>

                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="new_alumni_registration_fee">New Alumni Fee</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="number" class="form-control" name="new_alumni_registration_fee" value="{{ old('new_alumni_registration_fee') ?? $setting->new_alumni_registration_fee }}" id="new_alumni_registration_fee" required>
                                        <div class="form-control-position">
                                            &#8358;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="start_date">Conference Start Date</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="date" class="form-control" name="start_date" value="{{ old('start_date') ?? $setting->start_date }}" id="start_date">

                                       <div class="form-control-position">
                                            &#128197;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="alumni_registration_fee">Old Alumni Fee</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="number" class="form-control" name="alumni_registration_fee" value="{{ old('alumni_registration_fee') ?? $setting->alumni_registration_fee }}" id="alumni_registration_fee" required>
                                        <div class="form-control-position">
                                            &#8358;
                                        </div>
                                    </fieldset>
                                </div>
                                
                               
                                 <div class="col-sm-6 col-md-6">
                                    <label for="end_date">Conference End Date</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="date" class="form-control" name="end_date" value="{{ old('end_date') ?? $setting->end_date }}" id="end_date">
                                       <div class="form-control-position">
                                            &#128197;
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-sm-6 col-md-6">
                                    <label for="close_registration">Registration Close date</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="date" class="form-control" name="close_registration" value="{{ old('close_registration') ?? $setting->close_registration }}" id="close_registration">
                                       <div class="form-control-position">
                                            &#128197;
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-sm-12 col-md-12">
                                    <label for="conference_overview">Conference Overview</label><small> You can use html tags here</small>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <textarea class="form-control" id="conference_overview" rows="3" name="conference_overview" rows="10" cols="200">{!! old('conference_overview') ??$setting->conference_overview !!}</textarea>
                                        <div class="form-control-position">
                                           &#9745;
                                        </div>
                                    </fieldset>
                                </div>


                            </div>
                            <button class="btn btn-primary" style="width:100%; margin-top:10px" type="submit">Save</button>
                        </form>
                        <br>
                        <div class="row" style="background-color:black !important; padding: 20px;">
                            <h2 style="color:red">Caution: use the BUTTON below only if you know what you are doing as this will clear all data from the database!</h2>
                                <a  onclick="return confirm('This application will reset and everything will be deleted?');" href="{{ route('database.clear') }}" class="btn btn-danger mt-1"><i class="fa fa-warning"></i> Reset application Database</a>
                         </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
     <script>
     CKEDITOR.replace( 'conference_overview' );</script>
    <script src="https://cdn.quilljs.com/1.0.5/quill.min.js" type="text/javascript"></script>
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
@endsection