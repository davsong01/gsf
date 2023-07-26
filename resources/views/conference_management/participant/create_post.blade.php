@extends('layouts.dashboard')
@section('title', 'Create Post')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('posts.index') }}">My Posts</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Create Post</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
<section id="basic-input">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add New Post</h4>
                    @include('includes.alerts')
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('posts.store') }}" method="POST" id="myform" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <fieldset class="form-group">
                                    <label for="type">Post Type</label>
                                    <select class="form-control" name="type" id="type" required>
                                        <option value="" selected>-- Select Type --</option>
                                        <option value="1">Text Only</option>
                                        <option value="2">Text + 3 or more images</option>
                                        <option value="3">Text + Video(s)</option>
                                    </select>
                                </fieldset>
                            </div>
                        </div>
                        
                        <div class="row">
                           
                            <div class="col-sm-12 textonlydiv">

                                <div class="form-group" id="textonly">
                                    <label for="text" style="color:red">Content</label>
                                    <textarea id="textckeditor" required="required" type="text" class="form-control" name="text" value="{{ old('text') }}" rows="8" autofocus>{{ old('text') }}</textarea>
                                </div>
                              
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-sm-12 textandimagesdiv">
                               <div class="form-group" id="textandimages">
                                    <label for="imagestext" style="color:red">Content</label>
                                    <textarea id="imagesckeditor" type="text" class="form-control" name="imagestext" value="{{ old('imagestext') }}" rows="8" autofocus>{{ old('imagestext') }}</textarea>
                                </div>
                                <fieldset class="form-group">
                                    <label for="name">Upload Images(At least 3 images are required)</label><br><small>Only jpg,jpeg,png or bmp allowed</small><br>
                                    <input type="file" name="images[]" id="imagefiles" accept="image/*" multiple>
                                </fieldset>
                            </div>                                                  
                        </div>

                        <div class="row">
                            <div class="col-sm-12 textandvideosdiv">
                               <div class="form-group" id="textandvideos">
                                    <label for="videos" style="color:red">Content</label>
                                    <textarea id="videosckeditor" type="text" class="form-control" name="videotext" value="{{ old('videotext') }}" rows="8" autofocus>{{ old('videotext') }}</textarea>
                                </div>
                                <fieldset class="form-group">
                                    <label for="name">Upload Videos (At least 1 video file is required, only mp4,3gp,avi,wmv,webm files allowed, file size should not be greater than 100mb)</label><br>
                                    <input type="file" name="videos[]" id="videofiles"  accept="video/*"  multiple>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="name">Upload Additional Images (Optional)</label><br>
                                    <input type="file" name="images[]" accept="image/*"  multiple>
                                </fieldset>
                            </div>                                                  
                        </div>

                        <button class="btn btn-primary" style="width:100%" type="submit">Submit</button>
                        </form>
                   
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Basic Inputs end -->
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('textckeditor');
    CKEDITOR.replace('imagesckeditor');
    CKEDITOR.replace('videosckeditor'); 
</script>
<script>
// Limit number of files
    var textckeditor = $('#textckeditor');
    var imagesckeditor = $('#imagesckeditor');
    var videosckeditor = $('#videosckeditor');
    var imagefiles = $('#imagefiles');
    var videofiles = $('#videofiles');

    $('#type').on('change', function () {
        console.log($('#type').val());

        if ($('#type').val() == '1') {

            $('.textonlydiv').css('display', 'block');
            // textckeditor.attr('required', true);
            $('.textandimagesdiv').css('display', 'none');
            $('.textandvideosdiv').css('display', 'none');
            imagesckeditor.attr('required', false);
            videosckeditor.attr('required', false);
            imagefiles.attr('required', false);
            videofiles.attr('required', false);
            
        } else if ($('#type').val() == '2') {
            $('.textandimagesdiv').css('display', 'block');
            // imagesckeditor.attr('required', true);
            imagefiles.attr('required', true);
            $('.textonlydiv').css('display', 'none');
            $('.textandvideosdiv').css('display', 'none');
            textckeditor.attr('required', false);
            videofiles.attr('required', false);

        } else if ($('#type').val() == '3') {
            $('.textandvideosdiv').css('display', 'block');
            $('.textonlydiv').css('display', 'none');
            $('.textandimagesdiv').css('display', 'none');
            videofiles.attr('required', true);
            imagefiles.attr('required', false);
        } 

    });
</script>


@endsection
