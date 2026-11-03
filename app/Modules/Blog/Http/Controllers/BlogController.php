<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Models\Chapter;
use App\Models\Field;
use App\Models\Stakeholder;
use App\Models\User;
use App\Models\Zone;
use App\Modules\Blog\BlogPermissions;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->user();
        $this->authorize($user, BlogPermissions::VIEW);
        $isStakeholder = $user instanceof Stakeholder;

        $query = BlogPost::with(['category', 'author'])->latest('updated_at');
        if ($isStakeholder) $query->where('author_type', $user::class)->where('author_id', $user->id);
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('type')) $query->where('type', $request->string('type'));
        if ($request->filled('search')) $query->where('title', 'like', '%'.$request->string('search').'%');

        return view('modules.blog.admin.index', [
            'posts' => $query->paginate(20)->withQueryString(),
            'isStakeholder' => $isStakeholder,
        ]);
    }

    public function create()
    {
        $user = $this->user();
        $this->authorize($user, BlogPermissions::CREATE);

        return view('modules.blog.admin.form', array_merge($this->formData(), ['post' => null]));
    }

    public function store(Request $request)
    {
        $user = $this->user();
        $this->authorize($user, BlogPermissions::CREATE);
        $data = $this->validated($request, $user);
        $data['author_type'] = $user::class;
        $data['author_id'] = $user->id;
        $data['status'] = $this->isStakeholder($user) ? ($request->boolean('submit_review') ? 'review' : 'draft') : ($data['status'] ?? 'draft');
        $data['published_at'] = $this->publicationDate($data);

        $post = BlogPost::create($data);
        $this->syncTaxonomy($post, $request);

        return redirect()->route($this->routePrefix($user).'.edit', $post)->with('message', 'Post saved successfully.');
    }

    public function edit(BlogPost $post)
    {
        $user = $this->user();
        $this->authorizePost($user, $post, BlogPermissions::EDIT);

        return view('modules.blog.admin.form', array_merge($this->formData(), compact('post')));
    }

    public function update(Request $request, BlogPost $post)
    {
        $user = $this->user();
        $this->authorizePost($user, $post, BlogPermissions::EDIT);
        $data = $this->validated($request, $user, $post);
        if ($this->isStakeholder($user)) $data['status'] = $request->boolean('submit_review') ? 'review' : ($post->status === 'published' ? 'published' : 'draft');
        $data['published_at'] = $this->publicationDate($data, $post);
        $post->update($data);
        $this->syncTaxonomy($post, $request);

        return back()->with('message', 'Post updated successfully.');
    }

    public function review(BlogPost $post)
    {
        $this->authorize($this->user(), BlogPermissions::REVIEW);
        $post->update(['status' => 'review']);
        return back()->with('message', 'Post moved to review.');
    }

    public function publish(Request $request, BlogPost $post)
    {
        $this->authorize($this->user(), BlogPermissions::PUBLISH);
        $publishedAt = $request->date('published_at') ?: now();
        $post->update(['status' => $publishedAt->isFuture() ? 'scheduled' : 'published', 'published_at' => $publishedAt]);
        return back()->with('message', 'Post publication status updated.');
    }

    public function archive(BlogPost $post)
    {
        $this->authorize($this->user(), BlogPermissions::ARCHIVE);
        $post->update(['status' => 'archived']);
        return back()->with('message', 'Post archived.');
    }

    public function destroy(BlogPost $post)
    {
        $user = $this->user();
        $this->authorizePost($user, $post, BlogPermissions::EDIT);
        $post->delete();
        return redirect()->route($this->routePrefix($user).'.index')->with('message', 'Post deleted.');
    }

    public function uploadMedia(Request $request)
    {
        $user = $this->user();
        abort_unless(
            BlogPermissions::allows($user, BlogPermissions::CREATE)
                || BlogPermissions::allows($user, BlogPermissions::EDIT)
                || BlogPermissions::allows($user, BlogPermissions::MANAGE_OWN),
            403
        );

        $files = collect($request->allFiles())
            ->flatten()
            ->filter(fn ($file) => $file instanceof \Illuminate\Http\UploadedFile)
            ->values();
        abort_unless($files->isNotEmpty(), 422, 'No image file was uploaded.');

        $results = $files->map(function ($file) {
            Validator::make(['upload' => $file], [
                'upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            ])->validate();
            $path = $file->store('blog/content', 'public');
            $url = Storage::disk('public')->url($path);

            return [
                'url' => $url,
                'name' => basename($path),
                'size' => $file->getSize(),
            ];
        })->all();

        return response()->json([
            'result' => $results,
        ]);
    }

    protected function formData(): array
    {
        return [
            'categories' => BlogCategory::orderBy('name')->get(),
            'tags' => BlogTag::orderBy('name')->get(),
            'chapters' => Chapter::orderBy('name')->get(),
            'fields' => Field::orderBy('name')->get(),
            'zones' => Zone::orderBy('name')->get(),
            'relatedPosts' => BlogPost::orderByDesc('created_at')->limit(100)->get(['id', 'title']),
        ];
    }

    protected function validated(Request $request, $user, ?BlogPost $post = null): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:article,video'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug,'.($post?->id ?? 'NULL')],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'category_id' => ['nullable', 'exists:blog_categories,id'],
            'status' => ['nullable', 'in:draft,review,scheduled,published,archived'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'tag_names' => ['nullable', 'string', 'max:1000'],
            'scope_type' => ['nullable', 'in:national,field,zone,chapter'],
            'scope_id' => ['nullable', 'integer'],
            'allow_comments' => ['nullable', 'boolean'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'social_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['scope_type'] = $data['scope_type'] ?? 'national';
        $data['scope_id'] = $this->scopeId($user, $data['scope_type'], $data['scope_id'] ?? null);
        $data['allow_comments'] = $request->boolean('allow_comments');

        foreach (['featured_image', 'social_image'] as $field) {
            if ($request->hasFile($field)) $data[$field] = $request->file($field)->store('blog', 'public');
        }

        return $data;
    }

    protected function syncTaxonomy(BlogPost $post, Request $request): void
    {
        $tagIds = collect($request->input('tag_ids', []))->filter()->map('intval');
        $tagNames = collect(explode(',', (string) $request->input('tag_names')))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique(fn ($name) => Str::lower($name));
        foreach ($tagNames as $tagName) {
            $tagIds->push(BlogTag::firstOrCreate(['slug' => Str::slug($tagName)], ['name' => $tagName])->id);
        }
        $tagIds = $tagIds->unique()->map('intval')->all();
        $post->tags()->sync($tagIds);
        $post->related()->sync(collect($request->input('related_ids', []))->filter()->reject(fn ($id) => (int) $id === $post->id)->map('intval')->all());
    }

    protected function publicationDate(array $data, ?BlogPost $post = null)
    {
        if (($data['status'] ?? $post?->status) === 'published' && empty($data['published_at'])) return $post?->published_at ?: now();
        return $data['published_at'] ?? $post?->published_at;
    }

    protected function scopeId($user, string $scopeType, ?int $scopeId): ?int
    {
        if (!$this->isStakeholder($user)) return $scopeType === 'national' ? null : $scopeId;
        return match ($scopeType) {
            'chapter' => $user->chapter_id,
            'field' => $user->field_id,
            'zone' => $user->zone_id,
            default => null,
        };
    }

    protected function user() { return auth('stakeholder')->user() ?: auth()->user(); }
    protected function isStakeholder($user): bool { return $user instanceof Stakeholder; }
    protected function routePrefix($user): string { return $this->isStakeholder($user) ? 'stakeholders.blog' : 'admin.blog'; }

    protected function authorize($user, string $permission): void
    {
        abort_unless(BlogPermissions::allows($user, $permission), 403);
    }

    protected function authorizePost($user, BlogPost $post, string $permission): void
    {
        $owns = $post->author_type === $user::class && (int) $post->author_id === (int) $user->id;
        $this->authorize($user, $owns ? BlogPermissions::MANAGE_OWN : $permission);
        abort_unless($owns || BlogPermissions::allows($user, $permission), 403);
    }
}
