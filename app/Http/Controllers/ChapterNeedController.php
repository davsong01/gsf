<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterNeed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChapterNeedController extends Controller
{
    public function index(Request $request): View
    {
        $chapter = $this->representativeChapter();
        $needs = $chapter->needs()->get();

        return view('stakeholder.needs.index', compact('chapter', 'needs'));
    }

    public function store(Request $request): JsonResponse
    {
        $chapter = $this->representativeChapter();
        $need = $chapter->needs()->create($this->validated($request) + ['is_active' => true]);

        return response()->json(['message' => 'Campus need added.', 'need' => $need], 201);
    }

    public function update(Request $request, ChapterNeed $need): JsonResponse
    {
        $chapter = $this->representativeChapter();
        abort_unless($need->chapter_id === $chapter->id, 404);
        $need->update($this->validated($request) + ['is_active' => $request->boolean('is_active')]);

        return response()->json(['message' => 'Campus need updated.', 'need' => $need->fresh()]);
    }

    public function destroy(ChapterNeed $need): JsonResponse
    {
        $chapter = $this->representativeChapter();
        abort_unless($need->chapter_id === $chapter->id, 404);
        $need->delete();

        return response()->json(['message' => 'Campus need removed.']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:3000'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    private function representativeChapter(): Chapter
    {
        $stakeholder = auth('stakeholder')->user();
        abort_unless($stakeholder && $stakeholder->chapter_id && $stakeholder->role?->slug === 'chapter-representative', 403);

        return Chapter::actualCampus()->findOrFail($stakeholder->chapter_id);
    }
}
