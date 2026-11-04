<?php

namespace Tests\Feature;

use App\Models\User;
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
            $path = parse_url($result['url'], PHP_URL_PATH);

            $this->assertSame(200, $response->getStatusCode());
            $this->assertSame($expectedSize, $result['size']);
            $this->assertSame($expectedSize, filesize(public_path($path)));
            $this->assertSame('jpg', pathinfo($result['name'], PATHINFO_EXTENSION));
        } finally {
            if ($path && is_file(public_path($path))) {
                unlink(public_path($path));
            }
        }
    }
}
