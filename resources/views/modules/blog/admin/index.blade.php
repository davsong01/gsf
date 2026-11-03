@extends($isStakeholder ? 'layouts.stakeholderdashboard' : 'layouts.dashboard')
@section('title', 'Posts')
@section('active')<li class="breadcrumb-item">Posts</li>@endsection
@section('content')
<div class="content-body">
    <section>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div><h4 class="card-title mb-1">Posts</h4><small class="text-muted">Articles and video posts</small></div>
                <a class="btn btn-primary" href="{{ route(($isStakeholder ? 'stakeholders' : 'admin').'.blog.posts.create') }}">Create post</a>
            </div>
            <div class="card-body">
                @include('includes.alerts')
                <form class="form-row mb-3" method="GET">
                    <div class="col-md-5"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search posts"></div>
                    <div class="col-md-3"><select class="form-control" name="status"><option value="">All statuses</option>@foreach(['draft','review','scheduled','published','archived'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                    <div class="col-md-2"><button class="btn btn-outline-secondary">Filter</button></div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Author</th><th>Views</th><th>Updated</th><th></th></tr></thead>
                        <tbody>
                        @forelse($posts as $post)
                            @php $prefix = $isStakeholder ? 'stakeholders' : 'admin'; @endphp
                            <tr>
                                <td><strong>{{ $post->title }}</strong><br><small class="text-muted">{{ $post->category?->name ?: 'Uncategorised' }}</small></td>
                                <td>{{ ucfirst($post->type) }}</td>
                                <td><span class="badge badge-{{ $post->status === 'published' ? 'success' : ($post->status === 'review' ? 'warning' : 'secondary') }}">{{ ucfirst($post->status) }}</span></td>
                                <td>{{ $post->author?->name ?: $post->author?->email ?: '—' }}</td>
                                <td>{{ number_format($post->view_count) }}</td>
                                <td>{{ optional($post->updated_at)->format('d M Y') }}</td>
                                <td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route($prefix.'.blog.posts.edit', $post) }}">Edit</a>@if(!$isStakeholder && $post->status !== 'archived') <form class="d-inline" method="POST" action="{{ route('admin.blog.posts.archive', $post) }}">@csrf<button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Archive this post?')">Archive</button></form>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">No posts found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $posts->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
