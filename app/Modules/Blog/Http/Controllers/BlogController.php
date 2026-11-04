<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Models\Stakeholder;
use App\Models\User;
use App\Modules\Blog\BlogPermissions;
use App\Modules\Blog\BlogMedia;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->user();
        $this->authorize($user, BlogPermissions::VIEW);
        $isStakeholder = $user instanceof Stakeholder;

        $query = BlogPost::with(['categories', 'author'])->withCount('comments')->latest('updated_at');
        if ($isStakeholder) $query->where('author_type', $user::class)->where('author_id', $user->id);

        $filters = $request->validate([
            'search' => 'nullable|string|max:120',
            'status' => 'nullable|in:draft,review,scheduled,published,archived',
            'type' => 'nullable|in:article,video',
            'category' => 'nullable|integer|exists:blog_categories,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['type'])) $query->where('type', $filters['type']);
        if (!empty($filters['category'])) $query->whereHas('categories', fn ($categories) => $categories->where('blog_categories.id', $filters['category']));
        if (!empty($filters['date_from'])) $query->whereRaw('DATE(COALESCE(published_at, created_at)) >= ?', [$filters['date_from']]);
        if (!empty($filters['date_to'])) $query->whereRaw('DATE(COALESCE(published_at, created_at)) <= ?', [$filters['date_to']]);
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($posts) use ($search) {
                $posts->where('title', 'like', '%'.$search.'%')
                    ->orWhereHasMorph('author', [User::class, Stakeholder::class], function ($author) use ($search) {
                        $author->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        $summaryQuery = BlogPost::query();
        if ($isStakeholder) {
            $summaryQuery->where('author_type', $user::class)->where('author_id', $user->id);
        }
        $statusCounts = $summaryQuery->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        return view('modules.blog.admin.index', [
            'posts' => $query->paginate(20)->withQueryString(),
            'isStakeholder' => $isStakeholder,
            'viewer' => $user,
            'canCreate' => BlogPermissions::allows($user, BlogPermissions::CREATE),
            'canArchive' => BlogPermissions::allows($user, BlogPermissions::ARCHIVE),
            'statusSummary' => [
                'all' => $statusCounts->sum(),
                'draft' => (int) ($statusCounts['draft'] ?? 0),
                'review' => (int) ($statusCounts['review'] ?? 0),
                'scheduled' => (int) ($statusCounts['scheduled'] ?? 0),
                'published' => (int) ($statusCounts['published'] ?? 0),
                'archived' => (int) ($statusCounts['archived'] ?? 0),
            ],
            'categories' => BlogCategory::orderBy('name')->get(),
            'canManageComments' => BlogPermissions::allows($user, BlogPermissions::COMMENTS),
            'bulkActions' => $isStakeholder ? [] : collect([
                'delete' => 'Delete',
                'change-category' => 'Change category',
                'archive' => 'Archive',
                'review' => 'Move to review',
            ])->filter(function ($label, $action) use ($user) {
                return match ($action) {
                    'delete', 'change-category' => BlogPermissions::allows($user, BlogPermissions::MANAGE_OWN)
                        || BlogPermissions::allows($user, BlogPermissions::EDIT),
                    'archive' => BlogPermissions::allows($user, BlogPermissions::ARCHIVE),
                    'review' => BlogPermissions::allows($user, BlogPermissions::REVIEW),
                    default => false,
                };
            })->all(),
        ]);
    }

    public function bulkAction(Request $request)
    {
        $user = $this->user();
        $validated = $request->validate([
            'action' => 'required|in:delete,archive,review,change-category',
            'post_ids' => 'required|array|min:1|max:100',
            'post_ids.*' => 'required|integer|distinct',
            'category_ids' => ['required_if:action,change-category', 'array', 'min:1'],
            'category_ids.*' => [Rule::in(array_merge(['uncategorized'], BlogCategory::query()->pluck('id')->map(fn ($id) => (string) $id)->all()))],
        ]);

        $posts = BlogPost::whereIn('id', $validated['post_ids'])->get();
        $updated = 0;
        $skipped = count($validated['post_ids']) - $posts->count();

        foreach ($posts as $post) {
            $ownsPost = $post->author_type === $user::class && (int) $post->author_id === (int) $user->id;
            $allowed = match ($validated['action']) {
                'delete', 'change-category' => $ownsPost
                    ? BlogPermissions::allows($user, BlogPermissions::MANAGE_OWN)
                    : BlogPermissions::allows($user, BlogPermissions::EDIT),
                'archive' => BlogPermissions::allows($user, BlogPermissions::ARCHIVE),
                'review' => BlogPermissions::allows($user, BlogPermissions::REVIEW),
            };

            if (!$allowed) {
                $skipped++;
                continue;
            }

            match ($validated['action']) {
                'delete' => $post->delete(),
                'change-category' => $post->categories()->sync(
                    in_array('uncategorized', $validated['category_ids'], true)
                        ? []
                        : array_map('intval', $validated['category_ids'])
                ),
                'archive' => $post->update(['status' => 'archived']),
                'review' => $post->update(['status' => 'review']),
            };
            $updated++;
        }

        $actionLabel = $validated['action'] === 'change-category'
            ? 'Category change'
            : ucfirst($validated['action']);
        $message = "{$actionLabel} applied to {$updated} post(s).";
        if ($skipped > 0) {
            $message .= " {$skipped} post(s) were skipped because they were missing or you do not have permission.";
        }

        return redirect()->route($this->routePrefix($user).'.index')->with('message', $message);
    }

    public function create()
    {
        $user = $this->user();
        $this->authorize($user, BlogPermissions::CREATE);

        return view('modules.blog.admin.form', array_merge($this->formData($user), ['post' => null]));
    }

    public function store(Request $request)
    {
        $user = $this->user();
        $this->authorize($user, BlogPermissions::CREATE);
        $data = $this->validated($request, $user);
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);
        $data['author_type'] = $user::class;
        $data['author_id'] = $user->id;
        $data['status'] = $this->isStakeholder($user) ? ($request->boolean('submit_review') ? 'review' : 'draft') : ($data['status'] ?? 'draft');
        $data['published_at'] = $this->publicationDate($data);

        $post = BlogPost::create($data);
        $this->syncTaxonomy($post, $request, $categoryIds);

        return redirect()->route($this->routePrefix($user).'.edit', $post)->with('message', 'Post saved successfully.');
    }

    public function edit(BlogPost $post)
    {
        $user = $this->user();
        $this->authorizePost($user, $post, BlogPermissions::EDIT);

        return view('modules.blog.admin.form', array_merge($this->formData($user), [
            'post' => $post->load(['author', 'categories']),
            'authors' => $user instanceof User && BlogPermissions::allows($user, BlogPermissions::EDIT)
                ? $this->eligibleAuthors()
                : collect(),
            'canChangeAuthor' => $user instanceof User && BlogPermissions::allows($user, BlogPermissions::EDIT),
        ]));
    }

    public function preview(BlogPost $post)
    {
        $user = $this->user();
        $this->authorizePost($user, $post, BlogPermissions::EDIT);
        $post->load(['categories', 'tags', 'author', 'related'])
            ->loadCount(['comments as approved_comments_count' => fn ($query) => $query->where('status', 'approved')]);
        if ($post->author instanceof Stakeholder) {
            $post->author->loadMissing('chapter');
        }

        $relatedPosts = $post->suggestedRelated();

        return view('modules.blog.public.show', ['post' => $post, 'relatedPosts' => $relatedPosts, 'isPreview' => true]);
    }

    public function update(Request $request, BlogPost $post)
    {
        $user = $this->user();
        $this->authorizePost($user, $post, BlogPermissions::EDIT);
        $data = $this->validated($request, $user, $post);
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);
        if ($request->filled('author_key')) {
            abort_unless($user instanceof User && BlogPermissions::allows($user, BlogPermissions::EDIT), 403);
            $authors = $this->eligibleAuthors()->keyBy('key');
            $validatedAuthor = $request->validate([
                'author_key' => ['required', 'string', Rule::in($authors->keys()->all())],
            ])['author_key'];
            [$data['author_type'], $data['author_id']] = explode(':', $validatedAuthor, 2);
            $data['author_id'] = (int) $data['author_id'];
            if (\Illuminate\Support\Facades\Schema::hasColumn('blog_posts', 'legacy_author_name')) {
                $data['legacy_author_name'] = null;
            }
        }
        if ($this->isStakeholder($user)) $data['status'] = $request->boolean('submit_review') ? 'review' : ($post->status === 'published' ? 'published' : 'draft');
        $data['published_at'] = $this->publicationDate($data, $post);
        $post->update($data);
        $this->syncTaxonomy($post, $request, $categoryIds);

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
            $path = BlogMedia::upload($file, 'content');
            $url = BlogMedia::url($path);

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

    protected function formData($user): array
    {
        return [
            'canFeaturePosts' => BlogPermissions::canFeaturePosts($user),
            'categories' => BlogCategory::orderBy('name')->get(),
            'tags' => BlogTag::orderBy('name')->get(),
        ];
    }

    protected function eligibleAuthors()
    {
        $users = User::query()
            ->where(function ($query) {
                $query->where('role', 1)->orWhereJsonContains('permissions', BlogPermissions::CREATE);
            })
            ->get(['id', 'name', 'email'])
            ->map(fn (User $author) => [
                'key' => User::class.':'.$author->id,
                'name' => $author->name ?: $author->email,
                'email' => $author->email,
            ]);

        $stakeholders = Stakeholder::query()
            ->whereHas('role.permissions', fn ($query) => $query->where('slug', BlogPermissions::CREATE))
            ->with('role')
            ->get(['id', 'name', 'email'])
            ->map(fn (Stakeholder $author) => [
                'key' => Stakeholder::class.':'.$author->id,
                'name' => $author->name ?: $author->email,
                'email' => $author->email,
            ]);

        return $users->concat($stakeholders)->sortBy(fn ($author) => mb_strtolower($author['name'] ?? ''))->values();
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
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', 'exists:blog_categories,id'],
            'status' => ['nullable', 'in:draft,review,scheduled,published,archived'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'tag_names' => ['nullable', 'string', 'max:1000'],
            'allow_comments' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['scope_type'] = $post?->scope_type ?? $this->defaultScopeType($user);
        $data['scope_id'] = $post?->scope_id ?? $this->scopeId($user, $data['scope_type'], null);
        $data['allow_comments'] = $request->boolean('allow_comments');
        if (BlogPermissions::canFeaturePosts($user)) {
            $data['is_featured'] = $request->boolean('is_featured');
        } else {
            unset($data['is_featured']);
        }

        if ($request->hasFile('featured_image')) $data['featured_image'] = BlogMedia::upload($request->file('featured_image'), 'featured_image');

        // Tags are stored in blog_tags and synced after the post save.
        unset($data['tag_names']);

        return $data;
    }

    protected function syncTaxonomy(BlogPost $post, Request $request, array $categoryIds = []): void
    {
        $post->categories()->sync($categoryIds);
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

    protected function defaultScopeType($user): string
    {
        if (!$this->isStakeholder($user)) return 'national';
        if ($user->chapter_id) return 'chapter';
        if ($user->zone_id) return 'zone';
        if ($user->field_id) return 'field';
        return 'national';
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
        abort_unless(
            $permission === BlogPermissions::EDIT
                ? BlogPermissions::canEditPost($user, $post)
                : BlogPermissions::allows($user, $permission),
            403
        );
    }
}
