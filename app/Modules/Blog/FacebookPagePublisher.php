<?php

namespace App\Modules\Blog;

use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogSetting;
use Illuminate\Support\Facades\Http;
use Throwable;

class FacebookPagePublisher
{
    public function publish(BlogPost $post): bool
    {
        if (! $post->facebook_publish_requested || $post->facebook_status === 'published') {
            return $post->facebook_status === 'published';
        }

        $publishingEnabled = $post->type === 'event'
            ? BlogSetting::campusEventsCanPublishToFacebook()
            : BlogSetting::campusPostsCanPublishToFacebook();

        if (! $publishingEnabled) {
            return $this->fail($post, 'Facebook publishing is disabled in Blog Settings.');
        }

        $pageId = BlogSetting::facebookPageId();
        $accessToken = BlogSetting::facebookPageAccessToken();

        if (! filled($pageId) || ! filled($accessToken)) {
            return $this->fail($post, 'Facebook Page credentials are missing in Blog Settings.');
        }

        $version = trim((string) config('services.facebook.graph_version', 'v26.0'), '/');
        $baseUrl = 'https://graph.facebook.com/'.$version.'/'.rawurlencode($pageId);
        $event = $post->type === 'event';
        $url = $event
            ? route('events.public.show', $post->slug)
            : route('blog.show', $post->slug);
        $message = $this->message($post, $url);
        $imageUrl = BlogMedia::url($post->featured_image);

        try {
            $response = $imageUrl
                ? Http::asForm()->timeout(25)->post($baseUrl.'/photos', [
                    'url' => $imageUrl,
                    'caption' => $message,
                    'access_token' => $accessToken,
                ])
                : Http::asForm()->timeout(25)->post($baseUrl.'/feed', [
                    'message' => $message,
                    'link' => $url,
                    'access_token' => $accessToken,
                ]);

            if (! $response->successful()) {
                $error = $response->json('error.message') ?: 'Facebook returned HTTP '.$response->status().'.';

                return $this->fail($post, mb_substr((string) $error, 0, 1000));
            }

            $facebookPostId = $response->json('post_id') ?: $response->json('id');
            if (! filled($facebookPostId)) {
                return $this->fail($post, 'Facebook accepted the request but returned no post ID.');
            }

            $post->forceFill([
                'facebook_status' => 'published',
                'facebook_post_id' => (string) $facebookPostId,
                'facebook_published_at' => now(),
                'facebook_error' => null,
            ])->save();

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return $this->fail($post, 'Facebook could not be reached. Please retry the Facebook publication.');
        }
    }

    private function message(BlogPost $post, string $url): string
    {
        $summary = trim(html_entity_decode(strip_tags((string) ($post->excerpt ?: $post->body)), ENT_QUOTES | ENT_HTML5));
        $summary = preg_replace('/\s+/u', ' ', $summary) ?: '';
        $summary = mb_substr($summary, 0, 700);

        $lines = [$post->title];
        if ($post->type === 'event') {
            if ($post->event_starts_at) {
                $lines[] = $post->event_starts_at->format('l, j F Y · g:i A');
            }
            if (filled($post->event_venue)) {
                $lines[] = 'Venue: '.$post->event_venue;
            }
        }
        if ($summary !== '') {
            $lines[] = $summary;
        }
        $lines[] = $url;

        return implode("\n\n", $lines);
    }

    private function fail(BlogPost $post, string $message): bool
    {
        $post->forceFill([
            'facebook_status' => 'failed',
            'facebook_error' => $message,
        ])->save();

        return false;
    }
}
