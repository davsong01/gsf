<?php

namespace App\Http\Controllers;

use App\Models\CampusRecordBookUpload;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Zone;
use App\Services\ImageExtractorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
            'status' => ['nullable', 'in:pending,approved,processing,extracted,completed,rejected,duplicate'],
            'record_type' => ['nullable', 'in:member,alumni'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'search' => ['nullable', 'string', 'max:150'],
        ]);

        $query = CampusRecordBookUpload::with(['chapter.zone.field', 'stakeholder'])
            ->withCount(['extractedRecords as linked_user_records_count' => fn ($q) => $q->whereNotNull('user_id')])
            ->latest();
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
                    ->orWhereHas('extractedRecords', fn ($records) => $records->where('data->name', 'like', "%{$term}%"))
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
            'approvedCount' => CampusRecordBookUpload::where('status', 'approved')->count(),
            'extractedCount' => CampusRecordBookUpload::where('status', 'extracted')->count(),
            'completedCount' => CampusRecordBookUpload::where('status', 'completed')->count(),
            'duplicateCount' => CampusRecordBookUpload::where('status', 'duplicate')->count(),
        ]);
    }

    public function destroy(CampusRecordBookUpload $upload)
    {
        abort_unless(isAdmin()['status'], 403);
        abort_if($upload->status === 'processing', 409, 'Images currently processing cannot be deleted.');
        abort_if($upload->extractedRecords()->whereNotNull('user_id')->exists(), 409, 'This page has approved users linked to it and cannot be deleted.');

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
            'action' => ['required', 'in:approve,approve_extract,reject,delete'],
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:campus_record_book_uploads,id'],
        ]);

        $uploads = CampusRecordBookUpload::whereIn('id', $validated['ids'])->get();

        if ($validated['action'] === 'approve_extract' && $uploads->count() > 10) {
            return back()->withErrors(['ids' => 'Approve and extract up to 10 images at a time.']);
        }

        if ($validated['action'] === 'delete') {
            $deleted = 0;
            foreach ($uploads as $upload) {
                if ($upload->status === 'processing') continue;
                if ($upload->extractedRecords()->whereNotNull('user_id')->exists()) continue;
                $path = base64_decode($upload->image_path, true);
                if ($path && !str_contains($path, '..')) {
                    Storage::disk('protected_uploads')->delete($path);
                }
                $upload->delete();
                $deleted++;
            }

            return back()->with('message', $deleted.' record book page(s) deleted; approved batches with linked users were kept.');
        }

        if (in_array($validated['action'], ['approve', 'approve_extract'], true)) {
            $extracted = 0;
            $approved = 0;
            $failures = [];
            foreach ($uploads as $upload) {
                if (in_array($upload->status, ['completed', 'extracted', 'processing'], true)) continue;
                $upload->status = 'approved';
                $upload->approved_at = $upload->approved_at ?? now();
                $upload->extraction_error = null;
                $upload->save();
                $approved++;
                if ($validated['action'] === 'approve') continue;

                try {
                    $result = app(ImageExtractorService::class)->extract($upload);
                    $extracted += count($result['records']);
                } catch (\Throwable $exception) {
                    $failures[] = $upload->original_name.': '.$exception->getMessage();
                }
            }

            $message = $validated['action'] === 'approve'
                ? $approved.' image(s) approved and queued for extraction.'
                : $approved.' image(s) approved for immediate extraction; '.$extracted.' record(s) extracted; '.count($failures).' failed.';
            if ($failures) {
                return back()->with('error', $message.' '.$failures[0]);
            }

            return back()->with('message', $message);
        }

        $rejected = 0;
        foreach ($uploads as $upload) {
            if ($validated['action'] === 'reject' && in_array($upload->status, ['pending', 'approved', 'duplicate', 'rejected'], true)) {
                $upload->status = 'rejected';
                $upload->extraction_error = null;
                $upload->save();
                $rejected++;
            }
        }

        return back()->with('message', $rejected.' record book page(s) rejected. Extracted and completed pages were kept unchanged.');
    }

    public function extract(CampusRecordBookUpload $upload)
    {
        abort_unless(isAdmin()['status'], 403);

        try {
            abort_unless(!in_array($upload->status, ['processing', 'extracted', 'completed'], true), 409, 'This image is already processing or has already been extracted.');
            if (!in_array($upload->status, ['extracted', 'completed'], true)) {
                $upload->status = 'approved';
                $upload->approved_at = $upload->approved_at ?? now();
                $upload->save();
            }
            $result = app(ImageExtractorService::class)->extract($upload);
            $message = $result['cached']
                ? 'Previously extracted records reused for this identical image.'
                : count($result['records']).' record(s) extracted successfully.';

            return back()->with('message', $message);
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function approve(CampusRecordBookUpload $upload)
    {
        abort_unless(isAdmin()['status'], 403);
        abort_unless(!in_array($upload->status, ['extracted', 'completed', 'processing'], true), 409);
        $upload->status = 'approved';
        $upload->approved_at = $upload->approved_at ?? now();
        $upload->extraction_error = null;
        $upload->save();

        return back()->with('message', 'Image approved and added to the extraction queue.');
    }

    public function processApproved()
    {
        abort_unless(isAdmin()['status'], 403);
        $exitCode = Artisan::call('record-books:process-approved', ['--limit' => 10]);
        $output = trim(Artisan::output());

        return back()->with($exitCode === 0 ? 'message' : 'error', $output ?: 'Approved image processing queue started.');
    }

}
