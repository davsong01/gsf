<?php

namespace App\Http\Controllers;

use App\Models\CampusRecordBookExtractedRecord;
use App\Models\CampusRecordBookUpload;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class RecordBookExtractedRecordController extends Controller
{
    public function index(Request $request, CampusRecordBookUpload $upload)
    {
        $isAdmin = $this->authorizeUpload($upload);
        abort_unless(in_array($upload->status, ['extracted', 'completed'], true), 404);

        if (!$upload->extractedRecords()->exists() && !empty($upload->extracted_data['records'])) {
            foreach ($upload->extracted_data['records'] as $data) {
                $upload->extractedRecords()->create(['data' => $data, 'status' => 'pending']);
            }
            if ($upload->status === 'completed') $upload->update(['status' => 'extracted', 'completed_at' => null]);
        }

        $records = $upload->extractedRecords()->with('user')->orderBy('id')->paginate(30)->withQueryString();
        $recordType = $upload->record_type;
        $memberIndexUrl = $recordType === 'alumni' ? route('stakeholders.alumni.index') : route('stakeholders.users.index');
        $routeNames = $this->routeNames($isAdmin);

        return view($isAdmin ? 'admin.users.extracted-records' : 'stakeholder.users.extracted-records', compact(
            'upload', 'records', 'recordType', 'memberIndexUrl', 'routeNames', 'isAdmin'
        ));
    }

    public function store(Request $request, CampusRecordBookUpload $upload)
    {
        $this->authorizeUpload($upload);
        abort_unless(in_array($upload->status, ['extracted', 'completed'], true), 404);
        $data = $this->validatedRecord($request);
        $upload->extractedRecords()->create(['data' => $data, 'status' => 'pending']);
        $upload->update(['status' => 'extracted', 'completed_at' => null, 'extracted_records_count' => $upload->extractedRecords()->count()]);

        return back()->with('message', 'Record added to this batch for review.');
    }

    public function update(Request $request, CampusRecordBookUpload $upload, CampusRecordBookExtractedRecord $record)
    {
        $this->authorizeRecord($upload, $record);
        abort_unless($record->status === 'pending', 403, 'Approved records cannot be edited here.');
        $record->update(['data' => $this->validatedRecord($request)]);

        return back()->with('message', 'Extracted record updated.');
    }

    public function destroy(CampusRecordBookUpload $upload, CampusRecordBookExtractedRecord $record)
    {
        $this->authorizeRecord($upload, $record);
        abort_unless($record->status === 'pending', 403, 'Approved records cannot be deleted here.');
        $record->delete();
        $upload->update(['extracted_records_count' => $upload->extractedRecords()->count()]);
        $this->completeUploadIfResolved($upload);

        return back()->with('message', 'Extracted record deleted.');
    }

    public function approve(CampusRecordBookUpload $upload, CampusRecordBookExtractedRecord $record)
    {
        $this->authorizeRecord($upload, $record);
        try {
            $this->approveRecord($upload, $record);
            return back()->with('message', 'Record approved and added to campus users.');
        } catch (Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function bulk(Request $request, CampusRecordBookUpload $upload)
    {
        $isAdmin = $this->authorizeUpload($upload);
        $validated = $request->validate([
            'action' => ['required', 'in:edit,delete,approve'],
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['required', 'integer', 'distinct', Rule::exists('campus_record_book_extracted_records', 'id')->where('upload_id', $upload->id)],
        ]);

        $records = $upload->extractedRecords()->whereIn('id', $validated['ids'])->where('status', 'pending')->get();
        if ($validated['action'] === 'edit') {
            return view('record-book.bulk-edit', [
                'upload' => $upload,
                'records' => $records,
                'isAdmin' => $isAdmin,
                'routeNames' => $this->routeNames($isAdmin),
            ]);
        }

        if ($validated['action'] === 'delete') {
            $upload->extractedRecords()->whereIn('id', $records->pluck('id'))->where('status', 'pending')->delete();
            $upload->update(['extracted_records_count' => $upload->extractedRecords()->count()]);
            $this->completeUploadIfResolved($upload);
            return back()->with('message', $records->count().' record(s) deleted.');
        }

        $approved = 0;
        $errors = [];
        foreach ($records as $record) {
            try {
                $this->approveRecord($upload, $record);
                $approved++;
            } catch (Throwable $exception) {
                $errors[] = ($record->data['name'] ?? ('Record #'.$record->id)).': '.$exception->getMessage();
            }
        }

        $message = $approved.' record(s) approved and added to users.';
        return back()->with($errors ? 'error' : 'message', $errors ? $message.' First issue: '.$errors[0] : $message);
    }

    public function bulkUpdate(Request $request, CampusRecordBookUpload $upload)
    {
        $isAdmin = $this->authorizeUpload($upload);
        $validated = $request->validate([
            'records' => ['required', 'array', 'min:1', 'max:100'],
            'records.*.name' => ['required', 'string', 'max:255'],
            'records.*.email' => ['nullable', 'email', 'max:255'],
            'records.*.phone' => ['nullable', 'string', 'max:50'],
            'records.*.gender' => ['nullable', 'string', 'max:30'],
            'records.*.dob' => ['nullable', 'string', 'max:50'],
            'records.*.matric_year' => ['nullable', 'string', 'max:30'],
            'records.*.graduation_year' => ['nullable', 'string', 'max:30'],
            'records.*.program' => ['nullable', 'string', 'max:255'],
            'records.*.course' => ['nullable', 'string', 'max:255'],
            'records.*.course_duration' => ['nullable', 'string', 'max:100'],
            'records.*.notes' => ['nullable', 'string', 'max:2000'],
        ]);

        foreach ($validated['records'] as $id => $data) {
            $record = $upload->extractedRecords()->whereKey($id)->where('status', 'pending')->firstOrFail();
            $record->update(['data' => $data]);
        }

        return redirect()->route($this->routeNames($isAdmin)['index'], $upload)
            ->with('message', 'Selected extracted records updated.');
    }

    private function approveRecord(CampusRecordBookUpload $upload, CampusRecordBookExtractedRecord $record): void
    {
        DB::transaction(function () use ($upload, $record) {
            $record = CampusRecordBookExtractedRecord::query()->lockForUpdate()->findOrFail($record->id);
            if ($record->status === 'approved' && $record->user_id) return;
            if ($record->status !== 'pending') throw new RuntimeException('This record is no longer awaiting approval.');

            $data = $record->data ?? [];
            $name = trim((string) ($data['name'] ?? ''));
            if ($name === '') throw new RuntimeException('Add a name before approving this record.');
            $email = trim((string) ($data['email'] ?? ''));
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('A valid email address is required before adding a user. Add it by editing this record.');
            }
            foreach (['email' => 'email', 'phone' => 'phone'] as $field => $label) {
                $value = trim((string) ($data[$field] ?? ''));
                if ($value !== '' && User::where($field, $value)->exists()) {
                    throw new RuntimeException('This '.$label.' already belongs to a user. Edit the record before approving it.');
                }
            }

            $admin = request()->routeIs('users.extraction.records.*');
            $stakeholder = auth()->guard('stakeholder')->user();
            $user = app(UserService::class)->createUser([
                'name' => $name,
                'email' => $email,
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'dob' => $this->normalizeDate($data['dob'] ?? null),
                'matric_year' => $data['matric_year'] ?? null,
                'graduation_year' => $data['graduation_year'] ?? null,
                'program' => $data['program'] ?? null,
                'course' => $data['course'] ?? null,
                'course_duration' => preg_match('/\d+/', (string) ($data['course_duration'] ?? ''), $durationMatch) ? (int) $durationMatch[0] : null,
                'chapter_id' => $upload->chapter_id,
                'is_graduated' => $upload->record_type === 'alumni' ? 1 : 0,
                'role' => 2,
                'status' => 'active',
                'source' => 'record_book_upload',
                'password' => Hash::make($data['phone'] ?: Str::random(32)),
                'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            ]);

            $record->update([
                'user_id' => $user->id,
                'status' => 'approved',
                'approved_by' => $admin ? auth()->id() : $stakeholder?->id,
                'approved_by_guard' => $admin ? 'web' : 'stakeholder',
                'approved_at' => now(),
            ]);
            $this->completeUploadIfResolved($upload);
        });
    }

    private function completeUploadIfResolved(CampusRecordBookUpload $upload): void
    {
        $hasPending = $upload->extractedRecords()->where('status', 'pending')->exists();
        if (!$hasPending && in_array($upload->status, ['extracted', 'completed'], true)) {
            $upload->update(['status' => 'completed', 'completed_at' => $upload->completed_at ?? now()]);
        }
    }

    private function authorizeUpload(CampusRecordBookUpload $upload): bool
    {
        if (request()->routeIs('users.extraction.records.*')) {
            abort_unless(isAdmin()['status'] ?? false, 403);
            return true;
        }
        $stakeholder = auth()->guard('stakeholder')->user();
        abort_unless($stakeholder && (int) $stakeholder->chapter_id === (int) $upload->chapter_id, 403);
        return false;
    }

    private function authorizeRecord(CampusRecordBookUpload $upload, CampusRecordBookExtractedRecord $record): bool
    {
        abort_unless((int) $record->upload_id === (int) $upload->id, 404);
        return $this->authorizeUpload($upload);
    }

    private function validatedRecord(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'string', 'max:30'],
            'dob' => ['nullable', 'string', 'max:50'],
            'matric_year' => ['nullable', 'string', 'max:30'],
            'graduation_year' => ['nullable', 'string', 'max:30'],
            'program' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'course_duration' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function routeNames(bool $isAdmin): array
    {
        $prefix = $isAdmin ? 'users.extraction.records.' : 'stakeholders.users.record-book.records.';
        return [
            'index' => $prefix.'index',
            'store' => $prefix.'store',
            'bulk' => $prefix.'bulk',
            'bulk_update' => $prefix.'bulk-update',
            'update' => $prefix.'update',
            'approve' => $prefix.'approve',
            'destroy' => $prefix.'destroy',
        ];
    }

    private function normalizeDate(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'j/n/Y'] as $format) {
            $date = \DateTime::createFromFormat('!'.$format, $value);
            if ($date && $date->format($format) === $value) return $date->format('Y-m-d');
        }
        return null;
    }
}
