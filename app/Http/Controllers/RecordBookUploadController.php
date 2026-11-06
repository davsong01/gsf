<?php

namespace App\Http\Controllers;

use App\Models\CampusRecordBookUpload;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Zone;
use App\Services\ImageExtractorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecordBookUploadController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(isAdmin()['status'], 403);

        $filters = $request->validate([
            'chapter_id' => ['nullable', 'integer', 'exists:chapters,id'],
            'zone_id' => ['nullable', 'integer', 'exists:zones,id'],
            'field_id' => ['nullable', 'integer', 'exists:fields,id'],
            'status' => ['nullable', 'in:pending,processing,extracted,rejected,duplicate'],
            'record_type' => ['nullable', 'in:member,alumni'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'search' => ['nullable', 'string', 'max:150'],
        ]);

        $query = CampusRecordBookUpload::with(['chapter.zone.field', 'stakeholder'])->latest();
        if (!empty($filters['chapter_id'])) $query->where('chapter_id', $filters['chapter_id']);
        if (!empty($filters['zone_id'])) $query->whereHas('chapter', fn ($q) => $q->where('zone_id', $filters['zone_id']));
        if (!empty($filters['field_id'])) $query->whereHas('chapter', fn ($q) => $q->where('field_id', $filters['field_id']));
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['record_type'])) $query->where('record_type', $filters['record_type']);
        if (!empty($filters['from'])) $query->whereDate('created_at', '>=', $filters['from']);
        if (!empty($filters['to'])) $query->whereDate('created_at', '<=', $filters['to']);
        if (!empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(function ($q) use ($term) {
                $q->where('original_name', 'like', "%{$term}%")
                    ->orWhereHas('chapter', fn ($campus) => $campus->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('stakeholder', fn ($person) => $person->where('name', 'like', "%{$term}%"));
            });
        }

        return view('admin.users.extraction', [
            'uploads' => $query->paginate(25)->withQueryString(),
            'chapters' => Chapter::actualCampus()->orderBy('name')->get(['id', 'name']),
            'zones' => Zone::orderBy('name')->get(['id', 'name']),
            'fields' => Field::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
            'totalUploads' => CampusRecordBookUpload::count(),
            'pendingCount' => CampusRecordBookUpload::where('status', 'pending')->count(),
            'extractedCount' => CampusRecordBookUpload::where('status', 'extracted')->count(),
            'duplicateCount' => CampusRecordBookUpload::where('status', 'duplicate')->count(),
        ]);
    }

    public function destroy(CampusRecordBookUpload $upload)
    {
        abort_unless(isAdmin()['status'], 403);

        $path = base64_decode($upload->image_path, true);
        if ($path && !str_contains($path, '..')) {
            Storage::disk('protected_uploads')->delete($path);
        }
        $upload->delete();

        return back()->with('message', 'Record book image deleted.');
    }

    public function bulkAction(Request $request)
    {
        abort_unless(isAdmin()['status'], 403);

        $validated = $request->validate([
            'action' => ['required', 'in:extract,reject,delete'],
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:campus_record_book_uploads,id'],
        ]);

        $uploads = CampusRecordBookUpload::whereIn('id', $validated['ids'])->get();

        if ($validated['action'] === 'delete') {
            foreach ($uploads as $upload) {
                $path = base64_decode($upload->image_path, true);
                if ($path && !str_contains($path, '..')) {
                    Storage::disk('protected_uploads')->delete($path);
                }
                $upload->delete();
            }

            return back()->with('message', count($uploads).' record book page(s) deleted.');
        }

        if ($validated['action'] === 'extract' && count($uploads) > 10) {
            return back()->withErrors(['ids' => 'Extract up to 10 images at a time.']);
        }

        if ($validated['action'] === 'extract') {
            $extracted = 0;
            $cached = 0;
            $failures = [];
            foreach ($uploads as $upload) {
                try {
                    $result = app(ImageExtractorService::class)->extract($upload);
                    $result['cached'] ? $cached++ : $extracted++;
                } catch (\Throwable $exception) {
                    $failures[] = $upload->original_name.': '.$exception->getMessage();
                }
            }

            $message = $extracted.' page(s) extracted; '.$cached.' reused cached results; '.count($failures).' failed.';
            if ($failures) {
                return back()->with('error', $message.' '.$failures[0]);
            }

            return back()->with('message', $message);
        }

        foreach ($uploads as $upload) {
            if ($validated['action'] === 'reject') {
                $upload->status = 'rejected';
                $upload->extraction_error = null;
                $upload->save();
            }
        }

        return back()->with('message', count($uploads).' record book page(s) rejected.');
    }

    public function extract(CampusRecordBookUpload $upload)
    {
        abort_unless(isAdmin()['status'], 403);

        try {
            $result = app(ImageExtractorService::class)->extract($upload);
            $message = $result['cached']
                ? 'Previously extracted records reused for this identical image.'
                : count($result['records']).' record(s) extracted successfully.';

            return back()->with('message', $message);
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function updateStatus(Request $request, CampusRecordBookUpload $upload)
    {
        abort_unless(isAdmin()['status'], 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,rejected,duplicate'],
        ]);

        $upload->status = $validated['status'];
        if ($validated['status'] !== 'extracted') {
            $upload->extraction_error = null;
        }
        $upload->save();

        return back()->with('message', 'Record book page status updated.');
    }
}
