<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Blog\BlogMedia;
use App\Modules\Blog\Http\Controllers\BlogController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BlogMediaUploadTest extends TestCase
{
    public function test_image_upload_returns_a_usable_url_and_size_after_moving_the_file(): void
    {
        $user = new User();
        $user->role = 1;
        auth()->setUser($user);

        $image = UploadedFile::fake()->image('article.jpg', 500, 500);
        $expectedSize = $image->getSize();
        $request = Request::create('/admin/blog/media/upload', 'POST');
        $request->files->set('file-0', $image);

        $path = null;

        try {
            $response = app(BlogController::class)->uploadMedia($request);
            $result = $response->getData(true)['result'][0];
            parse_str((string) parse_url($result['url'], PHP_URL_QUERY), $query);
            $path = $query['path'] ?? null;

            $this->assertSame(200, $response->getStatusCode());
            $this->assertSame($expectedSize, $result['size']);
            $this->assertSame($expectedSize, filesize(public_path($path)));
            $this->assertSame('jpg', pathinfo($result['name'], PATHINFO_EXTENSION));
            $this->get($result['url'])->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        } finally {
            if ($path && is_file(public_path($path))) {
                unlink(public_path($path));
            }
        }
    }

    public function test_featured_image_uses_public_upload_service_and_is_readable(): void
    {
        $path = null;

        try {
            $path = BlogMedia::upload(UploadedFile::fake()->image('featured.png'), 'featured_image');
            $url = BlogMedia::url($path);

            $this->assertStringStartsWith('uploads/blog/featured_image/', $path);
            $this->assertFileExists(public_path($path));
            $this->assertNotNull($url);
            $this->get($url)->assertOk()->assertHeader('Content-Type', 'image/png');
            $importedHtml = '<p><img src="https://old.gsfnational.org/'.$path.'" alt="Story image"></p>';
            $this->assertStringContainsString(e($url), BlogMedia::htmlWithAvailableImages($importedHtml));
            $this->assertStringContainsString(e($url), BlogMedia::htmlWithAvailableImages('<p><img src="'.$url.'"></p>'));
        } finally {
            if ($path && is_file(public_path($path))) {
                unlink(public_path($path));
            }
        }
    }
}
