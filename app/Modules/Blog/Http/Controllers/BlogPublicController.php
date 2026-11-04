<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogSetting;
use App\Modules\Blog\Models\BlogPost;
use App\Models\Chapter;
use App\Models\Stakeholder;
use App\Modules\Blog\BlogPermissions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class BlogPublicController extends Controller
{
    public function media(Request $request)
    {
        $relative = (string) $request->query('path', '');
        abort_unless(str_starts_with($relative, 'uploads/blog/') && !str_contains($relative, "\0"), 404);

        $root = realpath(public_path('uploads/blog'));
        $file = realpath(public_path($relative));
        abort_unless($root && $file && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file), 404);

        $mime = mime_content_type($file);
        abort_unless(in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'application/pdf', 'audio/mpeg'], true), 404);

        return response()->file($file, [
            'Content-Type' => $mime,
            'Content-Disposition' => str_starts_with($mime, 'image/') ? 'inline' : 'attachment',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function index(Request $request)
    {
        $scope = $request->string('scope', 'national')->toString();
        $scopeId = $request->integer('scope_id') ?: null;
        $categorySlug = $request->string('category')->toString();
        $searchTerm = trim($request->string('search')->toString());
        $query = BlogPost::published()->forNetwork($scope, $scopeId)->with(['categories', 'author'])->latest('published_at');
        if ($categorySlug !== '') {
            $query->whereHas('categories', fn ($builder) => $builder->where('slug', $categorySlug));
        }
        if ($searchTerm !== '') {
            $query->where(function ($builder) use ($searchTerm) {
                $builder->where('title', 'like', '%'.$searchTerm.'%')
                    ->orWhere('excerpt', 'like', '%'.$searchTerm.'%')
                    ->orWhere('body', 'like', '%'.$searchTerm.'%');
            });
        }

        $posts = $query->paginate(12)->withQueryString();
        $featuredPost = $posts->currentPage() === 1
            ? (clone $query)->where('is_featured', true)->first()
            : null;
        $listingPosts = $posts->getCollection()->values();
        if ($featuredPost) {
            $listingPosts = $listingPosts->reject(fn ($post) => $post->id === $featuredPost->id)->values();
        }
        $mostReadQuery = BlogPost::published()->forNetwork($scope, $scopeId)
            ->where('view_count', '>', 0)
            ->with('categories')
            ->orderByDesc('view_count')
            ->orderByDesc('published_at');
        if ($categorySlug !== '') {
            $mostReadQuery->whereHas('categories', fn ($builder) => $builder->where('slug', $categorySlug));
        }
        $mostRead = $mostReadQuery->limit(5)->get();
        $categories = BlogCategory::withCount(['posts' => fn ($query) => $query->published()])->orderBy('name')->get();
        return view('modules.blog.public.index', compact('posts', 'categories', 'scope', 'scopeId', 'categorySlug', 'searchTerm', 'featuredPost', 'listingPosts', 'mostRead'));
    }

    public function network(string $scopeType, int $scopeId, Request $request)
    {
        abort_unless(in_array($scopeType, ['field', 'zone', 'chapter'], true), 404);
        $request->merge(['scope' => $scopeType, 'scope_id' => $scopeId]);
        return $this->index($request);
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->with(['categories', 'tags', 'author', 'related'])
            ->withCount(['comments as approved_comments_count' => fn ($query) => $query->where('status', 'approved')])
            ->where('slug', $slug)
            ->firstOrFail();
        if ($post->author instanceof Stakeholder) {
            $post->author->loadMissing('chapter');
        }
        $relatedPosts = $post->suggestedRelated();
        $approvedComments = $post->comments()->where('status', 'approved')->with('chapter')->latest()->limit(50)->get();
        $adjacentPosts = fn () => BlogPost::published()->forNetwork($post->scope_type, $post->scope_id);
        $previousPost = $adjacentPosts()
            ->where(fn ($query) => $query->where('published_at', '<', $post->published_at)
                ->orWhere(fn ($tie) => $tie->where('published_at', $post->published_at)->where('id', '<', $post->id)))
            ->orderByDesc('published_at')->orderByDesc('id')->first();
        $nextPost = $adjacentPosts()
            ->where(fn ($query) => $query->where('published_at', '>', $post->published_at)
                ->orWhere(fn ($tie) => $tie->where('published_at', $post->published_at)->where('id', '>', $post->id)))
            ->orderBy('published_at')->orderBy('id')->first();
        $viewer = auth('stakeholder')->user() ?: auth()->user();
        $countsAsView = !BlogPermissions::isWorkspaceUser($viewer);
        if ($countsAsView) {
            BlogPost::whereKey($post->id)->increment('view_count');
        }
        $challenge = $this->newCommentChallenge($post->id);
        $mathToken = $challenge['token'];
        $mathQuestion = $challenge['question'];
        $campuses = $post->allow_comments ? Chapter::orderBy('name')->get(['id', 'name']) : collect();

        return view('modules.blog.public.show', compact('post', 'mathToken', 'mathQuestion', 'campuses', 'countsAsView', 'relatedPosts', 'approvedComments', 'previousPost', 'nextPost'));
    }

    public function comment(Request $request, BlogPost $post)
    {
        abort_unless($post->status === 'published' && $post->allow_comments, 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'chapter_id' => ['nullable', 'exists:chapters,id'],
            'body' => ['required', 'string', 'max:5000'],
            'math_token' => ['required', 'string', 'size:40'],
            'math_answer' => ['required', 'integer'],
        ]);

        $challenge = session()->pull('blog_comment_math_'.$post->id);
        if (!$challenge || !hash_equals((string) $challenge['token'], $data['math_token'])
            || !hash_equals((string) $challenge['answer'], (string) $data['math_answer'])) {
            if ($request->expectsJson()) {
                $nextChallenge = $this->newCommentChallenge($post->id);
                return response()->json([
                    'message' => 'Please solve the new math question correctly.',
                    'errors' => ['math_answer' => ['Please solve the math question correctly.']],
                    'challenge' => $nextChallenge,
                ], 422);
            }
            return back()->withErrors(['math_answer' => 'Please solve the math question correctly.'])->withInput();
        }

        $status = BlogSetting::commentsRequireApproval() ? 'pending' : 'approved';
        $comment = $post->comments()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'ip_address' => $request->ip(),
            'chapter_id' => $data['chapter_id'] ?? null,
            'body' => $data['body'],
            'user_id' => auth()->id(),
            'status' => $status,
        ]);
        if ($request->expectsJson()) {
            $comment->load('chapter');
            $nextChallenge = $this->newCommentChallenge($post->id);
            $approvedCount = $post->comments()->where('status', 'approved')->count();
            return response()->json([
                'status' => $status,
                'message' => $status === 'approved'
                    ? 'Your comment has been published.'
                    : 'Your comment has been submitted for review.',
                'comment' => [
                    'name' => $comment->name ?: 'GSF reader',
                    'campus' => $comment->chapter?->name,
                    'body' => $comment->body,
                    'date' => $comment->created_at?->format('d M Y'),
                ],
                'approved_comments_count' => $approvedCount,
                'challenge' => $nextChallenge,
            ], 201);
        }

        return back()->with('message', $status === 'approved' ? 'Your comment has been published.' : 'Your comment has been submitted for review.');
    }

    private function newCommentChallenge(int $postId): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $token = Str::random(40);
        session()->put('blog_comment_math_'.$postId, ['token' => $token, 'answer' => $a + $b]);
        return ['token' => $token, 'question' => "{$a} + {$b}"];
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
