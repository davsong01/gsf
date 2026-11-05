<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterGalleryPhoto;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChapterGalleryController extends Controller
{
    private const MAX_PHOTOS = 20;

    public function index(Request $request): View
    {
        $chapter = $this->representativeChapter($request);
        $photos = $chapter->galleryPhotos()->latest()->get();

        return view('stakeholder.gallery.index', compact('chapter', 'photos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $chapter = $this->representativeChapter($request);
        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'caption' => ['required', 'string', 'max:255'],
        ]);

        if ($chapter->galleryPhotos()->count() >= self::MAX_PHOTOS) {
            return back()->withErrors(['photo' => 'This campus gallery already has the maximum of 20 photos.']);
        }

        $filename = Str::uuid().'.'.$request->file('photo')->extension();
        $directory = 'site/images/campus-galleries/'.$chapter->id;
        app(FileUploadService::class)->publicUpload($request->file('photo'), $directory, '', $filename);

        $chapter->galleryPhotos()->create([
            'image_path' => 'uploads/'.$directory.'/'.$filename,
            'caption' => $validated['caption'],
        ]);

        return back()->with('message', 'Photo added to the campus gallery.');
    }

    public function update(Request $request, ChapterGalleryPhoto $photo): RedirectResponse
    {
        $chapter = $this->representativeChapter($request);
        abort_unless($photo->chapter_id === $chapter->id, 404);

        $validated = $request->validate([
            'caption' => ['required', 'string', 'max:255'],
        ]);
        $photo->update(['caption' => $validated['caption']]);

        return back()->with('message', 'Photo caption updated.');
    }

    public function destroy(Request $request, ChapterGalleryPhoto $photo): RedirectResponse
    {
        $chapter = $this->representativeChapter($request);
        abort_unless($photo->chapter_id === $chapter->id, 404);

        $path = $this->absolutePublicPath($photo->image_path);
        $photo->delete();
        if (is_file($path)) {
            unlink($path);
        }

        return back()->with('message', 'Photo removed from the campus gallery.');
    }

    private function representativeChapter(Request $request): Chapter
    {
        $stakeholder = auth('stakeholder')->user();
        abort_unless(
            $stakeholder
            && $stakeholder->chapter_id
            && $stakeholder->role?->slug === 'chapter-representative',
            403
        );

        return Chapter::actualCampus()->findOrFail($stakeholder->chapter_id);
    }

    private function absolutePublicPath(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/');
        abort_if($relativePath === '' || str_contains($relativePath, '..'), 400);

        return rtrim(config('filesystems.image_upload_path', public_path()), '/').'/'.$relativePath;
    }
}
