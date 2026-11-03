<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Modules\Blog\BlogPermissions;
use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $this->guard();
        return view('modules.blog.admin.categories', ['categories' => BlogCategory::withCount('posts')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $this->guard();
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string']]);
        BlogCategory::create($data + ['slug' => Str::slug($data['name'])]);
        return back()->with('message', 'Category created.');
    }

    public function destroy(BlogCategory $category)
    {
        $this->guard();
        abort_if($category->posts()->exists(), 422, 'Move or delete the category posts first.');
        $category->delete();
        return back()->with('message', 'Category deleted.');
    }

    protected function guard(): void
    {
        $user = auth()->user();
        abort_unless($user && BlogPermissions::allows($user, BlogPermissions::CATEGORIES), 403);
    }
}
