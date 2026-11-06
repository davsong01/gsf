{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($pages as $url)
    <url><loc>{{ $url }}</loc></url>
@endforeach
@foreach($conferences as $conference)
    <url><loc>{{ route('conferences.show', $conference->slug) }}</loc>@if($conference->updated_at)<lastmod>{{ $conference->updated_at->toAtomString() }}</lastmod>@endif</url>
@endforeach
@foreach($posts as $post)
    <url><loc>{{ route($post->type === 'event' ? 'events.public.show' : 'blog.show', $post->slug) }}</loc>@if($post->updated_at)<lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>@endif</url>
@endforeach
</urlset>
