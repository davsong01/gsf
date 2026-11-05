<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Models\Chapter;
use App\Models\Field;
use App\Models\Stakeholder;
use App\Models\Zone;
use App\Models\User;
use App\Modules\Blog\BlogPermissions;
use App\Modules\Blog\BlogMedia;
use App\Modules\Blog\FacebookPagePublisher;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogSetting;
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
        $eventsWorkspace = $this->eventWorkspace();
        $user = $this->user();
        $this->authorize($user, BlogPermissions::VIEW);
        $isStakeholder = $user instanceof Stakeholder;
        $stakeholderRoleLabel = '';
        if ($isStakeholder) {
            $user->loadMissing(['role:id,name,slug', 'designation:id,name']);
            $stakeholderRoleLabel = strtolower(implode(' ', array_filter([
                $user->role?->name,
                str_replace('-', ' ', $user->role?->slug ?? ''),
                $user->designation?->name,
            ])));
        }
        $isFieldPastor = $isStakeholder && str_contains($stakeholderRoleLabel, 'field pastor');
        $isZonalPastor = $isStakeholder && str_contains($stakeholderRoleLabel, 'zonal pastor');
        $canFilterFields = !$isStakeholder;
        $canFilterZones = !$isStakeholder || $isFieldPastor;
        $canFilterChapters = !$isStakeholder || $isFieldPastor || $isZonalPastor;

        $query = BlogPost::with(['categories', 'author'])->withCount('comments')
            ->where('type', $eventsWorkspace ? '=' : '!=', 'event');
        if ($eventsWorkspace) {
            $query->orderByRaw('CASE WHEN event_starts_at >= ? THEN 0 ELSE 1 END', [now()])
                ->orderByRaw('CASE WHEN event_starts_at >= ? THEN event_starts_at END ASC', [now()])
                ->orderByRaw('CASE WHEN event_starts_at < ? THEN event_starts_at END DESC', [now()]);
        } else {
            $query->latest('updated_at');
        }
        if ($isStakeholder) $query->where('author_type', $user::class)->where('author_id', $user->id);

        $filters = $request->validate([
            'search' => 'nullable|string|max:120',
            'status' => 'nullable|in:draft,review,scheduled,published,archived',
            'facebook_status' => 'nullable|in:not_requested,pending_review,waiting_for_publication,pending,publishing,published,failed,rejected',
            'mine' => 'nullable|boolean',
            'field_id' => 'nullable|integer|exists:fields,id',
            'zone_id' => 'nullable|integer|exists:zones,id',
            'chapter_id' => 'nullable|integer|exists:chapters,id',
            'type' => 'nullable|in:article,video',
            'category' => 'nullable|integer|exists:blog_categories,id',
            'tag' => 'nullable|integer|exists:blog_tags,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if (!empty($filters['mine']) && !$isStakeholder) {
            $query->where('author_type', $user::class)->where('author_id', $user->id);
        }
        if ($canFilterFields && !empty($filters['field_id'])) {
            $fieldId = (int) $filters['field_id'];
            $zoneIds = Zone::where('field_id', $fieldId)->select('id');
            $chapterIds = Chapter::actualCampus()
                ->where(function ($chapters) use ($fieldId, $zoneIds) {
                    $chapters->where('field_id', $fieldId)
                        ->orWhereIn('zone_id', $zoneIds);
                })
                ->select('id');
            $query->where(function ($scopes) use ($fieldId, $chapterIds) {
                $scopes->where(fn ($scope) => $scope->where('scope_type', 'field')->where('scope_id', $fieldId))
                    ->orWhere(fn ($scope) => $scope->where('scope_type', 'zone')->whereIn('scope_id', Zone::where('field_id', $fieldId)->select('id')))
                    ->orWhere(fn ($scope) => $scope->where('scope_type', 'chapter')->whereIn('scope_id', $chapterIds))
                    ->orWhere(fn ($scope) => $scope->whereHasMorph('author', [Stakeholder::class], fn ($author) => $author->whereIn('chapter_id', $chapterIds)));
            });
        }
        if ($canFilterZones && !empty($filters['zone_id'])) {
            $zoneId = (int) $filters['zone_id'];
            $chapterIds = Chapter::actualCampus()->where('zone_id', $zoneId)->select('id');
            $query->where(function ($scopes) use ($zoneId, $chapterIds) {
                $scopes->where(fn ($scope) => $scope->where('scope_type', 'zone')->where('scope_id', $zoneId))
                    ->orWhere(fn ($scope) => $scope->where('scope_type', 'chapter')->whereIn('scope_id', $chapterIds))
                    ->orWhere(fn ($scope) => $scope->whereHasMorph('author', [Stakeholder::class], fn ($author) => $author->whereIn('chapter_id', $chapterIds)));
            });
        }
        if ($canFilterChapters && !empty($filters['chapter_id'])) {
            $chapterId = (int) $filters['chapter_id'];
            $query->where(function ($scopes) use ($chapterId) {
                $scopes->where(fn ($scope) => $scope->where('scope_type', 'chapter')->where('scope_id', $chapterId))
                    ->orWhere(fn ($scope) => $scope->whereHasMorph('author', [Stakeholder::class], fn ($author) => $author->where('chapter_id', $chapterId)));
            });
        }
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['facebook_status'])) $query->where('facebook_status', $filters['facebook_status']);
        if (!empty($filters['type'])) $query->where('type', $filters['type']);
        if (!empty($filters['category'])) $query->whereHas('categories', fn ($categories) => $categories->where('blog_categories.id', $filters['category']));
        if (!empty($filters['tag'])) $query->whereHas('tags', fn ($tags) => $tags->where('blog_tags.id', $filters['tag']));
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

        $summaryQuery = BlogPost::query()->where('type', $eventsWorkspace ? '=' : '!=', 'event');
        if ($isStakeholder) {
            $summaryQuery->where('author_type', $user::class)->where('author_id', $user->id);
        }
        $statusCounts = $summaryQuery->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $fields = $canFilterFields ? Field::orderBy('name')->get() : collect();
        $zonesQuery = Zone::with('field')->orderBy('name');
        $chaptersQuery = Chapter::actualCampus()->with(['field', 'zone'])->orderBy('name');
        if ($isStakeholder && $isFieldPastor) {
            $user->field_id ? $zonesQuery->where('field_id', $user->field_id) : $zonesQuery->whereRaw('1 = 0');
            $user->field_id ? $chaptersQuery->where('field_id', $user->field_id) : $chaptersQuery->whereRaw('1 = 0');
        } elseif ($isStakeholder && $isZonalPastor) {
            $user->zone_id ? $chaptersQuery->where('zone_id', $user->zone_id) : $chaptersQuery->whereRaw('1 = 0');
        }

        return view($eventsWorkspace ? 'modules.blog.admin.events' : 'modules.blog.admin.index', [
            'posts' => $query->paginate(20)->withQueryString(),
            'eventsWorkspace' => $eventsWorkspace,
            'isStakeholder' => $isStakeholder,
            'canFilterFields' => $canFilterFields,
            'canFilterZones' => $canFilterZones,
            'canFilterChapters' => $canFilterChapters,
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
            'tags' => BlogTag::orderBy('name')->get(),
            'fields' => $fields,
            'zones' => $canFilterZones ? $zonesQuery->get() : collect(),
            'chapters' => $canFilterChapters ? $chaptersQuery->get() : collect(),
            'canManageComments' => BlogPermissions::allows($user, BlogPermissions::COMMENTS),
            'bulkActions' => $isStakeholder ? [] : collect($eventsWorkspace ? [
                'delete' => 'Delete',
                'delete-facebook' => 'Delete from Facebook',
                'archive' => 'Archive',
                'review' => 'Move to review',
            ] : [
                'delete' => 'Delete',
                'delete-facebook' => 'Delete from Facebook',
                'change-category' => 'Change category',
                'archive' => 'Archive',
                'review' => 'Move to review',
            ])->filter(function ($label, $action) use ($user) {
                return match ($action) {
                    'delete', 'change-category' => BlogPermissions::allows($user, BlogPermissions::MANAGE_OWN)
                        || BlogPermissions::allows($user, BlogPermissions::EDIT),
                    'archive' => BlogPermissions::allows($user, BlogPermissions::ARCHIVE),
                    'review' => BlogPermissions::allows($user, BlogPermissions::REVIEW),
                    'delete-facebook' => BlogPermissions::allows($user, BlogPermissions::PUBLISH),
                    default => false,
                };
            })->all(),
        ]);
    }

    public function bulkAction(Request $request)
    {
        $user = $this->user();
        $eventsWorkspace = $this->eventWorkspace();
        $validated = $request->validate([
            'action' => ['required', Rule::in($eventsWorkspace ? ['delete', 'delete-facebook', 'archive', 'review'] : ['delete', 'delete-facebook', 'archive', 'review', 'change-category'])],
            'post_ids' => 'required|array|min:1|max:100',
            'post_ids.*' => 'required|integer|distinct',
            'category_ids' => ['exclude_unless:action,change-category', 'required_if:action,change-category', 'array', 'min:1'],
            'category_ids.*' => [Rule::in(array_merge(['uncategorized'], BlogCategory::query()->pluck('id')->map(fn ($id) => (string) $id)->all()))],
        ]);

        $posts = BlogPost::whereIn('id', $validated['post_ids'])
            ->where('type', $eventsWorkspace ? 'event' : '!=', 'event')
            ->get();
        $updated = 0;
        $skipped = count($validated['post_ids']) - $posts->count();
        $deletablePostIds = [];

        foreach ($posts as $post) {
            $ownsPost = $post->author_type === $user::class && (int) $post->author_id === (int) $user->id;
            $allowed = match ($validated['action']) {
                'delete', 'change-category' => $ownsPost
                    ? BlogPermissions::allows($user, BlogPermissions::MANAGE_OWN)
                    : BlogPermissions::allows($user, BlogPermissions::EDIT),
                'archive' => BlogPermissions::allows($user, BlogPermissions::ARCHIVE),
                'review' => BlogPermissions::allows($user, BlogPermissions::REVIEW),
                'delete-facebook' => BlogPermissions::allows($user, BlogPermissions::PUBLISH),
            };

            if (!$allowed) {
                $skipped++;
                continue;
            }

            if ($validated['action'] === 'delete') {
                $deletablePostIds[] = $post->id;
                $updated++;
                continue;
            }

            if ($validated['action'] === 'delete-facebook') {
                if ($post->facebook_status !== 'published' || ! filled($post->facebook_post_id)) {
                    $skipped++;
                    continue;
                }

                $result = app(FacebookPagePublisher::class)->deletePost($post);
                if (! $result['success']) {
                    $skipped++;
                    continue;
                }

                $post->forceFill([
                    'facebook_publish_requested' => false,
                    'facebook_status' => 'not_requested',
                    'facebook_post_id' => null,
                    'facebook_published_at' => null,
                    'facebook_error' => null,
                ])->save();
                $updated++;
                continue;
            }

            match ($validated['action']) {
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

        if ($deletablePostIds) {
            BlogPost::whereIn('id', $deletablePostIds)->forceDelete();
        }

        $actionLabel = match ($validated['action']) {
            'change-category' => 'Category change',
            'delete-facebook' => 'Facebook deletion',
            default => ucfirst($validated['action']),
        };
        $message = "{$actionLabel} applied to {$updated} post(s).";
        if ($skipped > 0) {
            $message .= " {$skipped} post(s) were skipped because they were missing or you do not have permission.";
        }

        return redirect()->route($this->routePrefix($user).'.posts.index')->with('message', $message);
    }

    public function create()
    {
        $user = $this->user();
        $this->authorize($user, BlogPermissions::CREATE);

        return view('modules.blog.admin.form', array_merge($this->formData($user), ['post' => null, 'eventsWorkspace' => $this->eventWorkspace()]));
    }

    public function store(Request $request)
    {
        if ($this->eventWorkspace()) $request->merge(['type' => 'event']);
        $user = $this->user();
        $this->authorize($user, BlogPermissions::CREATE);
        $data = $this->validated($request, $user);
        $categoryIds = $data['category_ids'] ?? [];
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['category_ids'], $data['tag_ids']);
        $data['author_type'] = $user::class;
        $data['author_id'] = $user->id;
        if ($this->isCampusStakeholder($user)) {
            $data['status'] = BlogSetting::campusPostsRequireReview()
                ? ($request->boolean('submit_review') ? 'review' : 'draft')
                : $this->campusPublicationStatus($data['published_at'] ?? null);
        } else {
            $data['status'] = $this->isStakeholder($user) ? ($request->boolean('submit_review') ? 'review' : 'draft') : ($data['status'] ?? 'draft');
        }
        $data['published_at'] = $this->publicationDate($data);
        $data = array_merge($data, $this->facebookPublishingFields($request, $user, $data));

        $post = BlogPost::create($data);
        if (!$this->eventWorkspace()) $this->syncTaxonomy($post, $categoryIds, $tagIds);
        $this->publishToFacebookWhenReady($post, ! $this->isStakeholder($user));

        return redirect()->route($this->routePrefix($user).'.posts.edit', $post)->with('message', 'Post saved successfully.');
    }

    public function edit(BlogPost $post)
    {
        abort_unless(($post->type === 'event') === $this->eventWorkspace(), 404);
        $user = $this->user();
        $this->authorizePost($user, $post, BlogPermissions::EDIT);

        return view('modules.blog.admin.form', array_merge($this->formData($user), [
            'post' => $post->load(['author', 'categories']),
            'eventsWorkspace' => $this->eventWorkspace(),
            'authors' => $user instanceof User && BlogPermissions::allows($user, BlogPermissions::EDIT)
                ? $this->eligibleAuthors()
                : collect(),
            'canChangeAuthor' => $user instanceof User && BlogPermissions::allows($user, BlogPermissions::EDIT),
        ]));
    }

    public function preview(BlogPost $post)
    {
        abort_unless(($post->type === 'event') === $this->eventWorkspace(), 404);
        $user = $this->user();
        $this->authorizePost($user, $post, BlogPermissions::EDIT);
        $post->load(['categories', 'tags', 'author', 'related'])
            ->loadCount(['comments as approved_comments_count' => fn ($query) => $query->where('status', 'approved')]);
        if ($post->author instanceof Stakeholder) {
            $post->author->loadMissing('chapter');
        }

        $relatedPosts = $post->suggestedRelated();

        return view($post->type === 'event' ? 'modules.blog.public.event-show' : 'modules.blog.public.show', ['post' => $post, 'relatedPosts' => $relatedPosts, 'isPreview' => true]);
    }

    public function update(Request $request, BlogPost $post)
    {
        abort_unless(($post->type === 'event') === $this->eventWorkspace(), 404);
        if ($this->eventWorkspace()) $request->merge(['type' => 'event']);
        $user = $this->user();
        $this->authorizePost($user, $post, BlogPermissions::EDIT);
        $data = $this->validated($request, $user, $post);
        $categoryIds = $data['category_ids'] ?? [];
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['category_ids'], $data['tag_ids']);
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
        if ($this->isCampusStakeholder($user)) {
            $data['status'] = BlogSetting::campusPostsRequireReview()
                ? ($request->boolean('submit_review') ? 'review' : ($post->status === 'published' ? 'published' : 'draft'))
                : $this->campusPublicationStatus($data['published_at'] ?? null);
        } elseif ($this->isStakeholder($user)) {
            $data['status'] = $request->boolean('submit_review') ? 'review' : ($post->status === 'published' ? 'published' : 'draft');
        }
        $data['published_at'] = $this->publicationDate($data, $post);
        $data = array_merge($data, $this->facebookPublishingFields($request, $user, $data, $post));
        $post->update($data);
        if (!$this->eventWorkspace()) $this->syncTaxonomy($post, $categoryIds, $tagIds);
        $this->publishToFacebookWhenReady($post, ! $this->isStakeholder($user));

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
        if ($post->facebook_status === 'waiting_for_publication' && $post->status === 'published') {
            $post->update(['facebook_status' => 'pending']);
            $this->publishToFacebookWhenReady($post, ! $this->isStakeholder($this->user()));
        }
        return back()->with('message', 'Post publication status updated.');
    }

    public function facebookReview(Request $request, BlogPost $post)
    {
        $user = $this->user();
        $this->authorize($user, BlogPermissions::PUBLISH);
        $decision = $request->validate(['decision' => ['required', Rule::in(['approve', 'reject', 'retry'])]])['decision'];

        if ($decision === 'reject') {
            abort_unless($post->facebook_status === 'pending_review', 422);
            $post->update(['facebook_status' => 'rejected', 'facebook_error' => null]);

            return back()->with('message', 'Facebook publication request rejected. Website publication was not changed.');
        }

        $expectedStatus = $decision === 'approve' ? 'pending_review' : 'failed';
        $claimed = BlogPost::query()
            ->whereKey($post->getKey())
            ->where('facebook_status', $expectedStatus)
            ->update(['facebook_status' => 'pending', 'facebook_error' => null]);
        abort_unless($claimed === 1, 409, 'This Facebook request has already been handled.');
        $post->refresh();
        // Both approval and retry are initiated by the current reviewer. Keep
        // their permission override tied to that reviewer, not the post author.
        $published = app(FacebookPagePublisher::class)->publish($post, ! $this->isStakeholder($user));

        return back()->with(
            $published ? 'message' : 'error',
            $published ? 'Published to the GSF National Facebook Page.' : 'Facebook publication failed. Check the Facebook status and credentials in Blog Settings.'
        );
    }

    public function facebookDelete(BlogPost $post)
    {
        $this->authorize($this->user(), BlogPermissions::PUBLISH);
        abort_unless($post->facebook_status === 'published' && filled($post->facebook_post_id), 422);

        $result = app(FacebookPagePublisher::class)->deletePost($post);
        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        $post->forceFill([
            'facebook_publish_requested' => false,
            'facebook_status' => 'not_requested',
            'facebook_post_id' => null,
            'facebook_published_at' => null,
            'facebook_error' => null,
        ])->save();

        return back()->with('message', 'Facebook post deleted. The website post remains published.');
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
        $post->forceDelete();
        return redirect()->route($this->routePrefix($user).'.posts.index')->with('message', 'Post permanently deleted.');
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
        abort_unless($files->isNotEmpty(), 422, 'No media file was uploaded.');

        $results = $files->map(function ($file) {
            $isAudio = strtolower($file->getClientOriginalExtension()) === 'mp3';
            Validator::make(['upload' => $file], [
                'upload' => $isAudio
                    ? ['required', 'file', 'mimes:mp3', 'mimetypes:audio/mpeg', 'max:204800']
                    : ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            ])->validate();
            $size = $file->getSize();
            $path = BlogMedia::upload($file, 'content', null);
            $url = BlogMedia::url($path);

            return [
                'url' => $url,
                'name' => basename($path),
                'size' => $size,
                'type' => $isAudio ? 'audio' : 'image',
            ];
        })->all();

        return response()->json([
            'result' => $results,
        ]);
    }

    protected function formData($user): array
    {
        $isCampusStakeholder = $this->isCampusStakeholder($user);
        if ($isCampusStakeholder) {
            $user->loadMissing('chapter');
        }
        $userCampusAddress = $isCampusStakeholder
            ? (filled($user->chapter?->address) ? trim($user->chapter->address) : null)
            : null;

        return [
            'isStakeholder' => $this->isStakeholder($user),
            'canFeaturePosts' => BlogPermissions::canFeaturePosts($user)
                || ($isCampusStakeholder && BlogSetting::campusPostsCanFeature()),
            'isCampusStakeholder' => $isCampusStakeholder,
            'userCampusAddress' => $userCampusAddress,
            'campusPostsRequireReview' => BlogSetting::campusPostsRequireReview(),
            'campusPostsCanAllowComments' => BlogSetting::campusPostsCanAllowComments(),
            'campusFacebookPublishingEnabled' => $this->eventWorkspace()
                ? BlogSetting::campusEventsCanPublishToFacebook()
                : BlogSetting::campusPostsCanPublishToFacebook(),
            'campusFacebookReviewRequired' => $this->eventWorkspace()
                ? BlogSetting::campusEventsFacebookRequireReview()
                : BlogSetting::campusPostsFacebookRequireReview(),
            'categories' => BlogCategory::orderBy('name')->get(),
            'fields' => Field::orderBy('name')->get(),
            'zones' => Zone::with('field')->orderBy('name')->get(),
            'chapters' => Chapter::actualCampus()->with(['field', 'zone'])->orderBy('name')->get(),
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
            'type' => ['required', Rule::in($this->eventWorkspace() ? ['event'] : ['article', 'video'])],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug,'.($post?->id ?? 'NULL')],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', 'exists:blog_categories,id'],
            'status' => ['nullable', 'in:draft,review,scheduled,published,archived'],
            'published_at' => ['nullable', 'date'],
            'event_starts_at' => ['required_if:type,event', 'nullable', 'date'],
            'event_venue' => ['required_if:type,event', 'nullable', 'string', 'max:255'],
            'event_speakers' => ['exclude_unless:type,event', 'nullable', 'array', 'max:30'],
            'event_speakers.*.kind' => ['exclude_unless:type,event', 'required', 'in:speaker,guest_minister'],
            'event_speakers.*.name' => ['exclude_unless:type,event', 'required', 'string', 'max:255'],
            'event_speakers.*.ministry' => ['exclude_unless:type,event', 'nullable', 'string', 'max:255'],
            'event_speakers.*.image' => ['exclude_unless:type,event', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'event_speakers.*.existing_image' => ['exclude_unless:type,event', 'nullable', 'string', 'max:500'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'distinct', 'exists:blog_tags,id'],
            'allow_comments' => ['nullable', 'boolean'],
            'publish_on_facebook' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);
        // This is a form control only; Facebook state is persisted through
        // facebookPublishingFields() using the model's facebook_* columns.
        unset($data['publish_on_facebook']);

        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['scope_type'] = $post?->scope_type ?? $this->defaultScopeType($user);
        $data['scope_id'] = $post?->scope_id ?? $this->scopeId($user, $data['scope_type'], null);
        if ($data['type'] !== 'event') {
            $data['event_starts_at'] = null;
            $data['event_venue'] = null;
            $data['event_speakers'] = null;
        } else {
            $previousSpeakerImages = collect($post?->event_speakers ?? [])->pluck('image')->filter()->all();
            $data['event_speakers'] = collect($data['event_speakers'] ?? [])->values()->map(function (array $speaker, int $index) use ($request, $previousSpeakerImages) {
                $existingImage = $speaker['existing_image'] ?? null;
                unset($speaker['existing_image']);
                $imageKey = "event_speakers.{$index}.image";
                $speaker['image'] = $request->hasFile($imageKey)
                    ? BlogMedia::upload($request->file($imageKey), 'event_speakers')
                    : (in_array($existingImage, $previousSpeakerImages, true) ? $existingImage : null);

                return $speaker;
            })->all();
        }
        $data['allow_comments'] = $this->isCampusStakeholder($user) && ! BlogSetting::campusPostsCanAllowComments()
            ? false
            : $request->boolean('allow_comments');
        if (BlogPermissions::canFeaturePosts($user)
            || ($this->isCampusStakeholder($user) && BlogSetting::campusPostsCanFeature())) {
            $data['is_featured'] = $request->boolean('is_featured');
        } else {
            unset($data['is_featured']);
        }

        if ($request->hasFile('featured_image')) $data['featured_image'] = BlogMedia::upload($request->file('featured_image'), 'featured_image');

        return $data;
    }

    protected function syncTaxonomy(BlogPost $post, array $categoryIds = [], array $tagIds = []): void
    {
        $post->categories()->sync($categoryIds);
        $post->tags()->sync(array_map('intval', $tagIds));
    }

    protected function publicationDate(array $data, ?BlogPost $post = null)
    {
        if (($data['status'] ?? $post?->status) === 'published' && empty($data['published_at'])) return $post?->published_at ?: now();
        return $data['published_at'] ?? $post?->published_at;
    }

    protected function facebookPublishingFields(Request $request, $user, array $data, ?BlogPost $post = null): array
    {
        $isEvent = ($data['type'] ?? $post?->type) === 'event';
        $enabled = $isEvent
            ? BlogSetting::campusEventsCanPublishToFacebook()
            : BlogSetting::campusPostsCanPublishToFacebook();
        $campusStakeholder = $this->isCampusStakeholder($user);
        $admin = ! $this->isStakeholder($user);
        // Campus users follow the Facebook controls configured by an admin.
        // National admins choose per post and can do so regardless of those
        // campus settings.
        $requested = $campusStakeholder
            ? ($enabled && $request->boolean('publish_on_facebook'))
            : ($admin && $request->boolean('publish_on_facebook'));

        if ($post?->facebook_status === 'published') {
            return [
                'facebook_publish_requested' => true,
                'facebook_status' => 'published',
            ];
        }

        if ($post && in_array($post->facebook_status, ['pending', 'publishing'], true)) {
            return [
                'facebook_publish_requested' => true,
                'facebook_status' => $post->facebook_status,
            ];
        }

        if (! $requested) {
            return [
                'facebook_publish_requested' => false,
                'facebook_status' => 'not_requested',
                'facebook_error' => null,
            ];
        }

        if ($campusStakeholder) {
            $requiresReview = $isEvent
                ? BlogSetting::campusEventsFacebookRequireReview()
                : BlogSetting::campusPostsFacebookRequireReview();
            $status = $requiresReview ? 'pending_review' : 'pending';
        } else {
            $status = ($data['status'] ?? $post?->status) === 'published'
                ? 'pending'
                : 'waiting_for_publication';
        }

        return [
            'facebook_publish_requested' => true,
            'facebook_status' => $status,
            'facebook_error' => null,
        ];
    }

    protected function publishToFacebookWhenReady(BlogPost $post, bool $allowSettingsOverride = false): void
    {
        if ($post->facebook_publish_requested && $post->facebook_status === 'pending') {
            app(FacebookPagePublisher::class)->publish($post, $allowSettingsOverride);
        }
    }

    protected function campusPublicationStatus($publishedAt): string
    {
        return filled($publishedAt) && \Illuminate\Support\Carbon::parse($publishedAt)->isFuture()
            ? 'scheduled'
            : 'published';
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

    protected function user()
    {
        if (request()->routeIs('stakeholders.blog.*', 'stakeholders.events.*')) {
            return auth('stakeholder')->user() ?: auth()->user();
        }

        return auth()->user() ?: auth('stakeholder')->user();
    }
    protected function isStakeholder($user): bool { return $user instanceof Stakeholder; }
    protected function isCampusStakeholder($user): bool { return $user instanceof Stakeholder && filled($user->chapter_id); }
    protected function routePrefix($user): string { return ($this->isStakeholder($user) ? 'stakeholders' : 'admin').($this->eventWorkspace() ? '.events' : '.blog'); }
    protected function eventWorkspace(): bool { return request()->routeIs('admin.events.*', 'stakeholders.events.*'); }

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
