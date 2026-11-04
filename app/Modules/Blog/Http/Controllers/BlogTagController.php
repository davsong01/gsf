<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Modules\Blog\BlogPermissions;
use App\Modules\Blog\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class BlogTagController extends Controller
{
    public function index()
    {
        $this->authorizeTags();

        return view('modules.blog.admin.tags', [
            'tags' => BlogTag::withCount('posts')->orderBy('name')->paginate(30),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeTags();
        $request->merge(['slug' => Str::slug((string) $request->input('name'))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140', 'unique:blog_tags,slug'],
        ]);

        BlogTag::create(['name' => $data['name'], 'slug' => $slug]);

        return back()->with('message', 'Tag created.');
    }

    public function destroy(BlogTag $tag)
    {
        $this->authorizeTags();
        abort_if($tag->posts()->exists(), 422, 'Remove the tag from its posts first.');
        $tag->delete();

        return back()->with('message', 'Tag deleted.');
    }

    protected function authorizeTags(): void
    {
        abort_unless(BlogPermissions::allows(auth()->user(), BlogPermissions::TAGS), 403);
    }
}
