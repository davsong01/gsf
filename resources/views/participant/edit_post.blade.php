@extends('layouts.dashboard')
@section('title', 'View Post')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('posts.index') }}">My Posts</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">View/Edit Post</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
<section id="basic-input">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                        <a class="nav-link active" href="#"><b>Status:</b> {{ $post->status == 0 ? 'Pending' : 'Approved' }}</b></a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="#"><b>Post Type: {{ $post->type }}</b></a>
                        </li>
                    </ul>

                    @include('includes.alerts')
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <form action="{{ route('posts.update', $post->id)}}" method="post">
                                        @csrf
                                        @method('PATCH')
                                    <label for="text" style="color:red">Content</label>
                                    <textarea {{ $post->status == 1 ? 'readonly' : ''}} id="textckeditor" required="required" type="text" class="form-control" name="text" value="{{ $post->content }}" rows="8" autofocus>{{ $post->content }}</textarea>
                                    @if($post->status == 0)
                                    <button class="btn btn-primary glow" style="color:white; margin-top:10px; width:100%"><small><label for="text" style="color:white;">Update this Content</small></button>
                                    @endif
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if(isset($post->images) && $post->images != 'null')
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="text" style="color:red">Post Images</label>
                                </div>
                            </div>
                            @foreach(json_decode($post->images) as $image)
                            <div class="col-md-4 col-sm-12">
                                <img src="{{route('get.file', $image) }}" alt="{!! $image !!}" style="width: 100%; height: 250px;">
                                @if($post->status == 0)
                                <div>    
                                    <form action="{{ route('file.replace')}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                       
                                    <input class="btn btn-primary glow" type="file" name="image" accept="image/*" required>
                                    <input type="hidden" value="{{ $image }}" name="old_image">
                                    
                                    <input type="hidden" value="{{ $post->id }}" name="pid">
                                    <button class="btn btn-primary glow"><small><label for="image" style="color:white">Replace</small></button>
                                    </form>
                                </div>
                                @endif
                            </div>                        
                            @endforeach                                  
                        </div>
                        @endif

                        @if(isset($post->videos))
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="text" style="color:red">Post Videos</label>
                                </div>
                            </div>
                            @foreach(json_decode($post->videos) as $video)
                            <div class="col-md-6 col-sm-12">
                                <video width="100%" height="500px" controls>
                                <source src="{{route('get.file', $video) }}">
                                Your browser does not support HTML video.
                                </video>
                            <img src="" alt="{!! $video !!}" style="width: 100%; height: 250px;">
                            @if($post->status == 0)
                            <div> 
                                <form action="{{ route('file.replace')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    
                                <input class="btn btn-primary glow" type="file" name="video" accept="video/*" required>
                                <input type="hidden" value="{{ $video }}" name="old_video">
                                
                                <input type="hidden" value="{{ $post->id }}" name="pid">
                                <button class="btn btn-primary glow"><small><label for="video" style="color:white">Replace</small></button>
                                </form>
                            </div>
                            @endif
                            </div>                        
                            @endforeach                                  
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Basic Inputs end -->
<scrip src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></scrip>
<script>
    CKEDITOR.replace('textckeditor');
</script>

@endsection
