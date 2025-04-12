@extends('layouts.conference')
@section('title', 'Create Conference edition')
@section('active')
<li class="breadcrumb-item">Create Conference Edition</li>
@endsection
@section('content2')
<div class="content-body">
    <section id="input-with-icons">
        <script src="https://cdn.ckeditor.com/4.24.0/standard/ckeditor.js"></script>
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $edition->conference_theme }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('conferenceeditions.update', $edition->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')
                                <div class="row">
                                    <div class="col-sm-6 col-md-6">
                                        <label for="enable_conference">Status</label>
                                        <fieldset class="form-group ">
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="">Select...</option>
                                                <option value="active" {{ $edition->status == 'active'?'selected':'' }}>Active</option>
                                                <option value="inactive"  {{ $edition->status == 'inactive'?'selected':'' }}>Inactive</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="template_id">Template to Use</label>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <select class="form-control" name="template_id" id="template_id" required>
                                                <option value="">Select...</option>
                                                <option value="1" {{ $edition->template_id == '1' ?'selected':'' }}>Template 1</option>
                                                <option value="2"  {{ $edition->template_id == '2' ?'selected':'' }}>Template 2</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="conference_theme">Conference theme</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="conference_theme" value="{{ old('conference_theme') ?? $edition->conference_theme }}" id="conference_theme">
                                        
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="slug">Conference slug(Will be used on ID cards)</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="slug" value="{{ old('slug') ?? $edition->slug}}" id="conference_theme">
                                        
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="registration_fee">Registration Fee</label>
                                        <fieldset class="form-group ">
                                            <input type="number" class="form-control" name="registration_fee" value="{{ old('registration_fee') ?? $edition->registration_fee }}" id="registration_fee">
                                        
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="official_email">Official Email</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="official_email" value="{{ old('official_email') ?? $edition->official_email }}" id="official_email">
                                            

                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="new_alumni_registration_fee">New Alumni Fee</label>
                                        <fieldset class="form-group ">
                                            <input type="number" class="form-control" name="new_alumni_registration_fee" value="{{ old('new_alumni_registration_fee') ?? $edition->new_alumni_registration_fee }}" id="new_alumni_registration_fee" required>
                                            
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-sm-6 col-md-6">
                                        <label for="alumni_registration_fee">Old Alumni Fee</label>
                                        <fieldset class="form-group ">
                                            <input type="number" class="form-control" name="alumni_registration_fee" value="{{ old('alumni_registration_fee') ?? $edition->alumni_registration_fee }}" id="alumni_registration_fee" required>
                                            
                                        </fieldset>
                                    </div>
                                    {{-- {{dd($edition)}} --}}
                                    <div class="col-sm-6 col-md-6">
                                        <label for="start_date">Conference Start Date</label>
                                    
                                        <fieldset class="form-group ">
                                            <input type="date" class="form-control" name="start_date" value="{{ old('start_date') ?? date('Y-m-d', strtotime($edition->start_date)) }}" id="start_date">
                                            
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="end_date">Conference End Date</label>
                                        <fieldset class="form-group ">
                                            <input type="date" class="form-control" name="end_date" value="{{ old('end_date') ?? date('Y-m-d', strtotime($edition->end_date)) }}" id="end_date">
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="close_registration">Registration Close date</label>
                                        <fieldset class="form-group">
                                            <input type="date" class="form-control" name="close_registration" value="{{ old('close_registration') ?? $edition->close_registration }}" id="close_registration">
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-sm-6 col-md-6">
                                        <label for="hostel_assignment_type">Hostel Assignment Type</label>
                                        <fieldset class="form-group ">
                                            <select class="form-control" name="hostel_assignment_type" id="hostel_assignment_type" required>
                                                <option value="">Select...</option>
                                                @foreach (hostelAssignmentTypes() as $key => $value)
                                                <option value="{{ $key }}" {{ $edition->hostel_assignment_type == $key ? 'selected':'' }}>{{ $value }}</option> 
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
                                                <option value="{{ $key }}" {{ $edition->service_point_assignment_type == $key ? 'selected':'' }}>{{ $value }}</option> 
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-sm-6 col-md-6">
                                        <label for="random_hostel">Enable mass registration</label>
                                        <fieldset class="form-group ">
                                            <select class="form-control" name="mass_registration" id="mass_registration" required>
                                                <option value="">Select...</option>
                                                <option value="yes" {{ $edition->mass_registration == 'yes'?'selected':'' }}>Yes</option>
                                                <option value="no"  {{ $edition->mass_registration == 'no'?'selected':'' }}>No</option>
                                            </select>
                                        
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="reg_prefix">Registration Prefix</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="reg_prefix" value="{{ old('reg_prefix') ?? $edition->reg_prefix }}" id="reg_prefix">
                                            <div class="form-control-position">
                                                &#128231;
                                            </div>
                                        </fieldset>
                                    </div>
                                    @if( empty($edition->conference_logo))
                                    <div class="col-sm-6 col-md-6">
                                        <label for="conference_logo">Upload Logo</label>
                                        <fieldset class="form-group ">
                                            <input type="file" class="form-control" name="logo" value="{{ old('logo') }}" id="logo">
                                        </fieldset>
                                    </div>
                                    @else
                                    <div class="col-sm-6 col-md-6">
                                        <label for="banner">Replace logo</label>
                                        <fieldset class="form-group ">
                                            <input type="file" class="form-control" name="logo" value="{{ old('logo') }}" id="logo">
                                        </fieldset>
                                    </div>
                                    @endif
                                    @if( empty($edition->conference_favicon))
                                    <div class="col-sm-6 col-md-6">
                                        <label for="favicon">Upload Favicon</label>
                                        <fieldset class="form-group ">
                                            <input type="file" class="form-control" name="favicon" value="{{ old('favicon') }}" id="favicon">
                                        </fieldset>
                                    </div>
                                    @else
                                    <div class="col-sm-6 col-md-6">
                                        <label for="favicon">Replace Favicon</label>
                                        <fieldset class="form-group ">
                                            <input type="file" class="form-control" name="favicon" value="{{ old('favicon') }}" id="favicon">
                                        </fieldset>
                                    </div>
                                    @endif
                                    @if( empty($edition->banner))
                                    <div class="col-sm-6 col-md-6">
                                        <label for="banner">Upload Banner</label>
                                        <fieldset class="form-group ">
                                            <input type="file" class="form-control" name="ban" value="{{ old('ban') }}" id="banner">
                                        </fieldset>
                                    </div>
                                    @else
                                    <div class="col-sm-6 col-md-6">
                                        <label for="banner">Replace banner</label>
                                        <fieldset class="form-group ">
                                            <input type="file" class="form-control" name="ban" value="{{ old('ban') }}" id="banner">
                                        </fieldset>
                                    </div>
                                    @endif
                                    
                                    <div class="col-sm-12 col-md-12">
                                        <label for="conference_overview">Conference Overview</label><small> You can use html tags here</small>
                                        <fieldset class="form-group ">
                                            <textarea class="form-control" id="conference_overview" rows="3" name="conference_overview" rows="10" cols="200">
                                                {!! $edition->conference_overview !!}
                                            </textarea>
                                            <div class="form-control-position">
                                            &#9745;
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                    <h3>Social Media Settings</h3>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="facebook_page">Facebook Page</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="facebook_page" value="{{ old('facebook_page') ?? $edition->facebook_page}}" id="facebook_page">
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="telegram">Telegram Page</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="telegram" value="{{ old('telegram') ?? $edition->telegram}}" id="telegram">
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="telegram">Instagram Page</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="instagram" value="{{ old('instagram') ?? $edition->instagram}}" id="instagram">
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="telegram">Facebook Event Page</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="facebook_event_page" value="{{ old('facebook_event_page') ?? $edition->facebook_event_page}}" id="facebook_event_page">
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                    <h3> Payment Settings</h3>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="PAYSTACK_PUBLIC_KEY">Paystack Public Key</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="PAYSTACK_PUBLIC_KEY" value="{{ old('PAYSTACK_PUBLIC_KEY') ?? $edition->PAYSTACK_PUBLIC_KEY}}" id="PAYSTACK_PUBLIC_KEY">
                                            
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="PAYSTACK_SECRET_KEY">Paystack Secret Key</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="PAYSTACK_SECRET_KEY" value="{{ old('PAYSTACK_SECRET_KEY') ?? $edition->PAYSTACK_SECRET_KEY }}" id="PAYSTACK_SECRET_KEY">
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="MERCHANT_EMAIL">Merchant Email</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="MERCHANT_EMAIL" value="{{ old('MERCHANT_EMAIL') ?? $edition->MERCHANT_EMAIL}}" id="MERCHANT_EMAIL">
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="enable_sub_account">Enable Sub Account</label>
                                        <fieldset class="form-group ">
                                            <select class="form-control" name="enable_sub_account" id="enable_sub_account" required>
                                                <option value="">Select...</option>
                                                <option value="yes" {{ $edition->enable_sub_account == 'yes'?'selected':'' }}>Yes</option>
                                                <option value="no"  {{ $edition->enable_sub_account == 'no'?'selected':'' }}>No</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <label for="paystack_subaccount_id">Paystack Subaccount ID</label>
                                        <fieldset class="form-group ">
                                            <input type="text" class="form-control" name="paystack_subaccount_id" value="{{ old('paystack_subaccount_id') ?? $edition->paystack_subaccount_id}}" id="paystack_subaccount_id">
                                        </fieldset>
                                    </div>
                                    <input type="hidden" name="id" value="{{ $edition->id }}">
                                </div>
                                {{-- certificate --}}
                                <?php 
                                    $service = new App\Services\DynamicImageGeneratorService;
                                    $template_settings = $edition->template_settings ?? [];
                                ?>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <h3>Badge Settings</h3>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <label for="template">
                                            @if(isset($template_settings['template']))
                                                Replace Template
                                            @else
                                                Upload Template
                                            @endif
                                        </label>
                                        <fieldset class="form-group">
                                            <input type="file" name="template" class="form-control" id="template">
                                        </fieldset>
                                    </div>

                                    @if(!empty($template_settings['settings']))
                                        @php $template_counter = 1; @endphp
                                        <div id="template-holder" class="col-md-12">
                                            @foreach($template_settings['settings'] as $setting)
                                                @php $counter = $template_counter++; @endphp
                                                <div class="row" id="oldtemplate-{{ $counter }}" style="border-top: 1px solid #000; margin-top: 15px; padding-top: 15px;">
                                                    <div class="col-md-4">
                                                        <label>Text Type</label>
                                                        <fieldset class="form-group">
                                                            {{-- {{dd($setting)}} --}}
                                                            <select name="template_text_type[]" class="form-control" required> 
                                                                @foreach ($service::textType() as $key=>$option)
                                                                    <option value="{{ $key }}" {{ $setting['template_text_type'] && $setting['template_text_type'] == $key ? 'selected' : ''}}>{{ $option }}</option>
                                                                @endforeach
                                                            </select>
                                                        </fieldset>
                                                    </div>
                                            
                                                    <div class="col-md-4">
                                                        <label>Font Type Face</label>
                                                        <fieldset class="form-group">
                                                            <select name="template_text_type_face[]" class="form-control">
                                                                @foreach($service::templateFontType() as $key => $value)
                                                                    <option value="{{ $key }}" {{ $setting['template_text_type_face'] == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </fieldset>
                                                    </div>
                                            
                                                    <div class="col-md-4">
                                                        <label>Font Size</label>
                                                        <fieldset class="form-group">
                                                            <input type="number" min="0" class="form-control" name="template_font_size[]" value="{{ $setting['template_font_size'] }}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label>Top Offset</label>
                                                        <fieldset class="form-group">
                                                            <input type="number" min="0" class="form-control" name="template_top_offset[]" value="{{ $setting['template_top_offset'] }}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label>Left Offset</label>
                                                        <fieldset class="form-group">
                                                            <input type="number" min="0" class="form-control" name="template_left_offset[]" value="{{ $setting['template_left_offset'] }}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-sm-6 col-md-2">
                                                        <label>Color</label>
                                                        <fieldset class="form-group">
                                                            <input type="color" class="form-control" name="template_color[]" value="{{ $setting['template_color'] ?? '#000000' }}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2" style="">
                                                        <label for="" style="color:transparent">Hello</label> <br>
                                                        <button class="btn btn-danger remove-old-template" id="oldtemplate-{{ $counter }}" type="button">
                                                            <i class="fa fa-minus"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Dynamic Row Container --}}
                                    <div id="templateRows" class="col-md-12"></div>

                                    <div class="col-sm-12 col-md-12">
                                        <button type="button" class="btn btn-success btn-sm" id="addRowButton">
                                            <i class="fa fa-plus"></i> Add New Row
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" id="previewButton">
                                            <i class="fa fa-eye"></i> Preview
                                        </button>
                                        <span id="loadingSpinner" style="display: none; margin-left: 5px;">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </span>
                                    </div>
                                </div>

                                <button class="btn btn-primary" style="width:100%; margin-top:10px" type="submit">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div> 
<div class="modal" id="previewModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <img style="width:474px;height:auto;" id="templatePreviewImage" src="" alt="Template Preview">
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#addRowButton').on('click', function () {
            let lastChild = $("#templateRows").children().last();
            let lastId = $(lastChild).attr('id');
            let id = lastId ? parseInt(lastId.split('-')[1]) + 1 : 1;

            let newRow = `
                <div class="row added-row" style="border-top: black solid 1px;margin-bottom: 6px;padding-top: 15px;" id="template-${id}">
                    <div class="col-md-4">
                        <label>Text Type</label>
                        <select name="template_text_type[]" class="form-control" required>
                            <option value="">Select...</option>
                            @foreach ($service::textType() as $key=>$option)
                                <option value="{{ $key }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Font Type Face</label>
                        <select name="template_text_type_face[]" class="form-control">
                            @foreach($service::templateFontType() as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Font Size</label>
                        <input type="number" min="0" class="form-control" name="template_font_size[]">
                    </div>
                    
                    <div class="col-md-4">
                        <label>Top Offset</label>
                        <input type="number" min="0" class="form-control" name="template_top_offset[]">
                    </div>
                    <div class="col-md-4">
                        <label>Left Offset</label>
                        <input type="number" min="0" class="form-control" name="template_left_offset[]">
                    </div>
                    <div class="col-md-2">
                        <label>Color</label>
                        <input type="color" class="form-control" name="template_color[]">
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label style="color: transparent">Color</label> <br>

                        <button type="button" class="btn btn-danger btn-sm removeRowButton" style="width: 100%;">
                            <i class="fa fa-minus"></i> Remove
                        </button>
                    </div>
                </div>`;
            $('#templateRows').append(newRow);
        });

        // Remove row
        $(document).on('click', '.removeRowButton', function () {
            $(this).closest('.added-row').remove();
        });

        // Preview handling
        $('#previewButton').on('click', function (e) {
            e.preventDefault();
            $('#loadingSpinner').show();
            let formData = new FormData();

            $('select[name="template_text_type[]"]').each(function () {
                formData.append('template_text_type[]', $(this).val());
            });

            $('select[name="template_text_type_face[]"]').each(function () {
                formData.append('template_text_type_face[]', $(this).val());
            });

            $('input[name="template_font_size[]"]').each(function () {
                formData.append('template_font_size[]', $(this).val());
            });

            $('input[name="template_top_offset[]"]').each(function () {
                formData.append('template_top_offset[]', $(this).val());
            });

            $('input[name="template_left_offset[]"]').each(function () {
                formData.append('template_left_offset[]', $(this).val());
            });

            $('input[name="template_color[]"]').each(function () {
                formData.append('template_color[]', $(this).val());
            });

            const fileInput = $('#template')[0].files[0];
            if (fileInput) {
                formData.append('template', fileInput);
            }

            $.ajax({
                url: "{{ route('template.preview', $edition->id) }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    
                    $('#loadingSpinner').hide();
                    if (response.preview_image_path) {
                        $('#templatePreviewImage').attr('src', response.preview_image_path);
                        $('#previewModal').modal('show');

                    } else {
                        alert('Failed to generate preview: ' + response.error);
                    }
                },
                error: function (xhr, status, error) {
                    $('#loadingSpinner').hide();
                    alert('An error occurred: ' + error);
                }
            });
        });

        $("#template-holder").on('click', '.remove-old-template', function() {
            // Get the ID of the clicked element
            var removeId = $(this).attr('id');  
        
            // Check if the ID is not empty
            if (removeId) {
                // Remove the element with the corresponding ID
                $("#" + removeId).remove();
            } else {
                console.warn("No ID found to remove.");
            }
        });
    });

    CKEDITOR.replace( 'conference_overview' );

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
@endsection