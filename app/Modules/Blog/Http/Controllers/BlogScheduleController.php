<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Modules\Blog\FacebookPagePublisher;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BlogScheduleController extends Controller
{
    public function processScheduledPosts(Request $request): JsonResponse
    {
        $expectedToken = (string) config('services.blog_schedule_token', '');
        $providedToken = (string) $request->query('token', '');

        abort_unless($expectedToken !== '', 503, 'Scheduled post cron token is not configured.');
        abort_unless($providedToken !== '' && hash_equals($expectedToken, $providedToken), 403);

        $now = now();
        $duePostIds = BlogPost::query()
            ->where('status', 'scheduled')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->orderBy('id')
            ->limit(100)
            ->pluck('id');

        $published = 0;
        $facebookPublished = 0;
        $facebookFailed = 0;

        foreach ($duePostIds as $postId) {
            $claimed = BlogPost::query()
                ->whereKey($postId)
                ->where('status', 'scheduled')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', $now)
                ->update(['status' => 'published']);

            if ($claimed !== 1) {
                continue;
            }

            $post = BlogPost::find($postId);
            if (! $post) {
                continue;
            }

            $published++;

            if (! $post->facebook_publish_requested) {
                continue;
            }

            if ($post->facebook_status === 'waiting_for_publication') {
                BlogPost::query()
                    ->whereKey($postId)
                    ->where('facebook_status', 'waiting_for_publication')
                    ->update(['facebook_status' => 'pending', 'facebook_error' => null]);
                $post->refresh();
            }

            // Requests awaiting Facebook review remain queued for an admin.
            if ($post->facebook_status !== 'pending') {
                continue;
            }

            $sent = app(FacebookPagePublisher::class)->publish($post, (bool) $post->facebook_settings_override);
            if ($sent) {
                $facebookPublished++;
            } elseif ($post->fresh()?->facebook_status === 'failed') {
                $facebookFailed++;
            }
        }

        $remainingDue = BlogPost::query()
            ->where('status', 'scheduled')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->count();

        return response()->json([
            'published_posts' => $published,
            'facebook_published' => $facebookPublished,
            'facebook_failed' => $facebookFailed,
            'remaining_due' => $remainingDue,
        ]);
    }
}
