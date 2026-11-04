@extends('layouts.dashboard')
@section('title', 'Blog Tags')
@section('active')<li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Posts</a></li><li class="breadcrumb-item active">Tags</li>@endsection
@section('content')
<div class="content-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div><h2 class="mb-1">Tags</h2><p class="text-muted mb-0">Manage topics used to organize and connect related stories.</p></div>
        <a class="btn btn-outline-primary" href="{{ route('admin.blog.posts.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to posts</a>
    </div>
    @include('includes.alerts')
    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card"><div class="card-header"><h4 class="card-title">Add a tag</h4></div><div class="card-body">
                <form method="POST" action="{{ route('admin.blog.tags.store') }}">@csrf
                    <div class="form-group"><label for="blog-tag-name">Tag name</label><input id="blog-tag-name" class="form-control" name="name" maxlength="120" required value="{{ old('name') }}" placeholder="e.g. Leadership"><small class="text-muted">A web address slug will be generated automatically.</small>@error('name')<small class="d-block text-danger">{{ $message }}</small>@enderror @error('slug')<small class="d-block text-danger">A tag with this name already exists.</small>@enderror</div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-plus" aria-hidden="true"></i> Create tag</button>
                </form>
            </div></div>
        </div>
        <div class="col-lg-8 mb-3">
            <div class="card"><div class="card-header d-flex align-items-center justify-content-between"><h4 class="card-title mb-0">All tags</h4><span class="badge badge-light">{{ number_format($tags->total()) }} tags</span></div>
                <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Tag</th><th>Slug</th><th>Posts</th><th></th></tr></thead><tbody>
                    @forelse($tags as $tag)
                        <tr><td><strong>{{ $tag->name }}</strong></td><td><code>{{ $tag->slug }}</code></td><td>{{ number_format($tag->posts_count) }}</td><td class="text-right">@if($tag->posts_count === 0)<form method="POST" action="{{ route('admin.blog.tags.destroy', $tag) }}" onsubmit="return confirm('Delete this tag?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button></form>@else<span class="text-muted small">In use</span>@endif</td></tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No tags yet.</td></tr>
                    @endforelse
                </tbody></table></div>
                @if($tags->hasPages())<div class="card-footer">{{ $tags->links() }}</div>@endif
            </div>
        </div>
    </div>
</div>
@endsection
