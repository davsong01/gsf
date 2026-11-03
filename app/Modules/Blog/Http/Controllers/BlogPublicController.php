<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BlogPublicController extends Controller
{
    public function index(Request $request)
    {
        $scope = $request->string('scope', 'national')->toString();
        $scopeId = $request->integer('scope_id') ?: null;
        $posts = BlogPost::published()->forNetwork($scope, $scopeId)->with(['category', 'author'])->latest('published_at')->paginate(12)->withQueryString();
        $categories = BlogCategory::withCount(['posts' => fn ($query) => $query->published()])->orderBy('name')->get();
        return view('modules.blog.public.index', compact('posts', 'categories', 'scope', 'scopeId'));
    }

    public function network(string $scopeType, int $scopeId, Request $request)
    {
        abort_unless(in_array($scopeType, ['field', 'zone', 'chapter'], true), 404);
        $request->merge(['scope' => $scopeType, 'scope_id' => $scopeId]);
        return $this->index($request);
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()->with(['category', 'tags', 'author', 'related'])->where('slug', $slug)->firstOrFail();
        BlogPost::whereKey($post->id)->increment('view_count');
        return view('modules.blog.public.show', compact('post'));
    }

    public function comment(Request $request, BlogPost $post)
    {
        abort_unless($post->status === 'published' && $post->allow_comments, 404);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:255'], 'body' => ['required', 'string', 'max:5000']]);
        $post->comments()->create($data + ['user_id' => auth()->id()]);
        return back()->with('message', 'Your comment has been submitted for review.');
    }

    public function rss()
    {
        $posts = BlogPost::published()->latest('published_at')->limit(30)->get();
        return response()->view('modules.blog.public.rss', compact('posts'))->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function sitemap()
    {
        $posts = BlogPost::published()->latest('updated_at')->get();
        return response()->view('modules.blog.public.sitemap', compact('posts'))->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
