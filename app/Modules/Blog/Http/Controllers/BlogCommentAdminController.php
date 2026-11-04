<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Models\Chapter;
use App\Modules\Blog\BlogPermissions;
use App\Modules\Blog\Models\BlogComment;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class BlogCommentAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeComments();
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,rejected,approved'],
            'post_id' => ['nullable', 'integer', 'exists:blog_posts,id'],
        ]);
        $activePost = !empty($filters['post_id']) ? BlogPost::findOrFail($filters['post_id']) : null;
        $comments = BlogComment::with(['post:id,title,slug', 'chapter:id,name'])
            ->when($activePost, fn ($query) => $query->where('post_id', $activePost->id))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()->paginate(25)->withQueryString();

        $summaryQuery = BlogComment::query()->when($activePost, fn ($query) => $query->where('post_id', $activePost->id));

        return view('modules.blog.admin.comments', [
            'comments' => $comments,
            'activeStatus' => $filters['status'] ?? '',
            'activePost' => $activePost,
            'canManageSettings' => BlogPermissions::allows(auth()->user(), BlogPermissions::SETTINGS),
            'canClearAll' => BlogPermissions::allows(auth()->user(), BlogPermissions::CLEAR_COMMENTS),
            'allCommentsCount' => BlogComment::count(),
            'summary' => [
                'all' => (clone $summaryQuery)->count(),
                'pending' => (clone $summaryQuery)->where('status', 'pending')->count(),
                'approved' => (clone $summaryQuery)->where('status', 'approved')->count(),
                'rejected' => (clone $summaryQuery)->where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function moderate(Request $request, BlogComment $comment)
    {
        $this->authorizeComments();
        $data = $request->validate(['status' => ['required', 'in:approved,rejected']]);
        $comment->update(['status' => $data['status']]);

        return back()->with('message', 'Comment '.($data['status'] === 'approved' ? 'approved' : 'rejected').'.');
    }

    public function edit(BlogComment $comment)
    {
        $this->authorizeComments();

        return view('modules.blog.admin.edit-comment', [
            'comment' => $comment->load(['post:id,title,slug', 'chapter:id,name']),
            'campuses' => Chapter::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, BlogComment $comment)
    {
        $this->authorizeComments();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'chapter_id' => ['nullable', 'integer', 'exists:chapters,id'],
            'body' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);
        $comment->update($data);

        return redirect()->route('admin.blog.comments.index', ['post_id' => $comment->post_id])
            ->with('message', 'Comment updated.');
    }

    public function destroy(BlogComment $comment)
    {
        $this->authorizeComments();
        $comment->delete();

        return back()->with('message', 'Comment deleted.');
    }

    public function bulkAction(Request $request)
    {
        $this->authorizeComments();
        $data = $request->validate([
            'action' => ['required', 'in:approve,reject,delete'],
            'comment_ids' => ['required', 'array', 'min:1', 'max:100'],
            'comment_ids.*' => ['required', 'integer', 'distinct', 'exists:blog_comments,id'],
        ]);
        $comments = BlogComment::whereIn('id', $data['comment_ids']);
        $changed = match ($data['action']) {
            'approve' => $comments->update(['status' => 'approved']),
            'reject' => $comments->update(['status' => 'rejected']),
            'delete' => $comments->delete(),
        };

        return back()->with('message', $changed.' comment'.($changed === 1 ? '' : 's').' '.match ($data['action']) {
            'approve' => 'approved.',
            'reject' => 'rejected.',
            'delete' => 'deleted.',
        });
    }

    public function clearAll(Request $request)
    {
        $this->authorizeComments();
        abort_unless(BlogPermissions::allows(auth()->user(), BlogPermissions::CLEAR_COMMENTS), 403);
        $request->merge(['confirmation' => trim((string) $request->input('confirmation'))]);
        $request->validate(['confirmation' => ['required', Rule::in(['DELETE ALL COMMENTS'])]]);
        $deleted = BlogComment::query()->delete();

        return redirect()->route('admin.blog.comments.index')->with('message', $deleted.' comments permanently deleted.');
    }

    private function authorizeComments(): void
    {
        abort_unless(BlogPermissions::allows(auth()->user(), BlogPermissions::COMMENTS), 403);
    }
}
