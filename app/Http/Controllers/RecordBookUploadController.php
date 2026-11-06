<?php

namespace App\Http\Controllers;

use App\Models\CampusRecordBookUpload;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Zone;
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
            'status' => ['nullable', 'in:pending,processing,extracted,rejected'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'search' => ['nullable', 'string', 'max:150'],
        ]);

        $query = CampusRecordBookUpload::with(['chapter.zone.field', 'stakeholder'])->latest();
        if (!empty($filters['chapter_id'])) $query->where('chapter_id', $filters['chapter_id']);
        if (!empty($filters['zone_id'])) $query->whereHas('chapter', fn ($q) => $q->where('zone_id', $filters['zone_id']));
        if (!empty($filters['field_id'])) $query->whereHas('chapter', fn ($q) => $q->where('field_id', $filters['field_id']));
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
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
}
