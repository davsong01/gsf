@extends('layouts.dashboard')
@section('title', 'Edit Blog Comment')
@section('active')<li class="breadcrumb-item"><a href="{{ route('admin.blog.comments.index', ['post_id' => $comment->post_id]) }}">Blog Comments</a></li><li class="breadcrumb-item active">Edit Comment</li>@endsection
@section('content')
<div class="content-body" style="max-width:980px">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
            <div><h4 class="card-title mb-1">Edit comment #{{ $comment->id }}</h4><small class="text-muted">Update the comment and its moderation status.</small></div>
            <a class="btn btn-outline-secondary" href="{{ route('admin.blog.comments.index', ['post_id' => $comment->post_id]) }}">← Back to comments</a>
        </div>
        <div class="card-body">
            @include('includes.alerts')
            <div class="border rounded p-3 mb-4" style="background:#f8fafc">
                <strong>On:</strong> @if($comment->post)<a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank" rel="noopener noreferrer">{{ $comment->post->title }} ↗</a>@else Post removed @endif
                <div class="text-muted small mt-1">Submitted {{ $comment->created_at?->format('d M Y, g:i a') }} · IP: {{ $comment->ip_address ?: 'Not recorded' }}</div>
            </div>
            <form method="POST" action="{{ route('admin.blog.comments.update', $comment) }}">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 form-group"><label for="comment-editor-name">Name <span class="text-danger">*</span></label><input class="form-control" id="comment-editor-name" name="name" value="{{ old('name', $comment->name) }}" required maxlength="120">@error('name')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="col-md-6 form-group"><label for="comment-editor-email">Email <span class="text-muted">(optional)</span></label><input class="form-control" id="comment-editor-email" type="email" name="email" value="{{ old('email', $comment->email) }}">@error('email')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="col-md-8 form-group"><label for="comment-editor-campus">Campus <span class="text-muted">(optional)</span></label><select class="form-control" id="comment-editor-campus" name="chapter_id"><option value="">No campus selected</option>@foreach($campuses as $campus)<option value="{{ $campus->id }}" @selected(old('chapter_id', $comment->chapter_id) == $campus->id)>{{ $campus->name }}</option>@endforeach</select>@error('chapter_id')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="col-md-4 form-group"><label for="comment-editor-status">Status</label><select class="form-control" id="comment-editor-status" name="status" required>@foreach(['pending', 'approved', 'rejected'] as $status)<option value="{{ $status }}" @selected(old('status', $comment->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select>@error('status')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="col-12 form-group"><label for="comment-editor-body">Comment <span class="text-danger">*</span></label><textarea class="form-control" id="comment-editor-body" name="body" rows="7" maxlength="5000" required>{{ old('body', $comment->body) }}</textarea>@error('body')<small class="text-danger">{{ $message }}</small>@enderror</div>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap:10px"><button class="btn btn-primary" type="submit">Save comment</button><a class="btn btn-outline-secondary" href="{{ route('admin.blog.comments.index', ['post_id' => $comment->post_id]) }}">Cancel</a></div>
            </form>
        </div>
    </div>
</div>
@endsection
