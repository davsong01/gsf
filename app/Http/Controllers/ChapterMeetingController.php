<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterMeeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ChapterMeetingController extends Controller
{
    public function store(Request $request, Chapter $chapter): JsonResponse
    {
        $this->authorizeChapter($request, $chapter);
        $meeting = $chapter->meetings()->create($this->details($request));

        return response()->json(['meeting' => $meeting], 201);
    }

    public function update(Request $request, Chapter $chapter, ChapterMeeting $meeting): JsonResponse
    {
        $this->authorizeChapter($request, $chapter);
        abort_unless($meeting->chapter_id === $chapter->id, 404);
        $meeting->update($this->details($request));

        return response()->json(['meeting' => $meeting]);
    }

    public function destroy(Request $request, Chapter $chapter, ChapterMeeting $meeting): JsonResponse
    {
        $this->authorizeChapter($request, $chapter);
        abort_unless($meeting->chapter_id === $chapter->id, 404);
        $meeting->delete();

        return response()->json(['deleted' => true]);
    }

    private function details(Request $request): array
    {
        return Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'day_of_week' => ['required', Rule::in(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])],
            'time' => ['required', 'date_format:H:i'],
            'venue' => ['required', 'string', 'max:255'],
        ])->validate();
    }

    private function authorizeChapter(Request $request, Chapter $chapter): void
    {
        if ($request->routeIs('stakeholders.chapters.meetings.*')) {
            abort_unless((int) auth('stakeholder')->user()?->chapter_id === $chapter->id, 403);
            return;
        }

        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
