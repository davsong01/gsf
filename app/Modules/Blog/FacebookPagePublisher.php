<?php

namespace App\Modules\Blog;

use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogSetting;
use App\Models\Stakeholder;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Throwable;

class FacebookPagePublisher
{
    public function publish(BlogPost $post, bool $allowSettingsOverride = false): bool
    {
        $post->refresh();
        if (! $post->facebook_publish_requested || in_array($post->facebook_status, ['published', 'publishing'], true)) {
            return in_array($post->facebook_status, ['published', 'publishing'], true);
        }

        // Claim the pending request atomically so duplicate clicks or parallel
        // requests cannot both send the same post to Facebook.
        $claimed = BlogPost::query()
            ->whereKey($post->getKey())
            ->where('facebook_status', 'pending')
            ->update(['facebook_status' => 'publishing']);

        if (! $claimed) {
            $post->refresh();

            return in_array($post->facebook_status, ['published', 'publishing'], true);
        }
        $post->refresh();

        $publishingEnabled = $allowSettingsOverride || $post->facebook_settings_override || $post->author_type === User::class
            || ($post->type === 'event'
                ? BlogSetting::campusEventsCanPublishToFacebook()
                : BlogSetting::campusPostsCanPublishToFacebook());

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
        $imageFile = BlogMedia::localFilePath($post->featured_image);

        try {
            $attachedMedia = [];
            if ($imageFile) {
                $photoResponse = Http::attach('source', file_get_contents($imageFile), basename($imageFile))
                    ->timeout(25)
                    ->post($baseUrl.'/photos', [
                        'published' => 'false',
                        'access_token' => $accessToken,
                    ]);

                if (! $photoResponse->successful()) {
                    return $this->fail($post, $this->facebookError($photoResponse));
                }

                $photoId = $photoResponse->json('id');
                if (! filled($photoId)) {
                    return $this->fail($post, 'Facebook uploaded the image but returned no photo ID.');
                }

                $attachedMedia['attached_media[0]'] = json_encode(['media_fbid' => (string) $photoId]);
            }

            $response = Http::asForm()->timeout(25)->post($baseUrl.'/feed', array_merge([
                    'message' => $message,
                    'access_token' => $accessToken,
                ], $attachedMedia));

            if (! $response->successful()) {
                return $this->fail($post, $this->facebookError($response));
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

    private function facebookError($response): string
    {
        $error = $response->json('error.message') ?: 'Facebook returned HTTP '.$response->status().'.';
        $errorCode = (int) $response->json('error.code', 0);

        if (str_contains(strtolower((string) $error), 'publish_actions')) {
            return 'The saved Facebook Page token uses the retired publish_actions permission. Generate a new Page token with pages_manage_posts and pages_read_engagement, then replace the token in Blog Settings.';
        }

        if ($errorCode === 200 || str_contains(strtolower((string) $error), 'permissions error')) {
            return 'Facebook rejected the saved Page token. Replace it in Blog Settings with a Page access token that has pages_manage_posts and pages_read_engagement, and confirm the token owner has permission to create content on the GSF National Facebook Page.';
        }

        return mb_substr((string) $error, 0, 1000);
    }

    public function deletePost(BlogPost $post): array
    {
        $pageId = BlogSetting::facebookPageId();
        $accessToken = BlogSetting::facebookPageAccessToken();
        if (! filled($pageId) || ! filled($accessToken) || ! filled($post->facebook_post_id)) {
            return ['success' => false, 'message' => 'Facebook Page credentials or the Facebook post ID are missing.'];
        }

        $version = trim((string) config('services.facebook.graph_version', 'v26.0'), '/');
        $url = 'https://graph.facebook.com/'.$version.'/'.rawurlencode($post->facebook_post_id);

        try {
            $response = Http::asForm()->timeout(25)->delete($url, ['access_token' => $accessToken]);
            if (! $response->successful() || $response->json('success') === false) {
                return ['success' => false, 'message' => $this->facebookError($response)];
            }

            return ['success' => true, 'message' => 'Facebook post deleted.'];
        } catch (Throwable $exception) {
            report($exception);

            return ['success' => false, 'message' => 'Facebook could not be reached. The post was not removed from the website.'];
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
        } else {
            $post->loadMissing('author');
            if ($post->author instanceof Stakeholder) {
                $post->author->loadMissing('chapter');
                if (filled($post->author->chapter?->name)) {
                    $lines[] = 'By: '.$post->author->chapter->name;
                }
            }
        }
        if ($summary !== '') {
            $lines[] = $summary;
        }
        $lines[] = $post->type === 'event'
            ? $url
            : 'Continue reading at: '.$url;

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
