<?php

namespace App\Services;

use App\Models\CampusRecordBookUpload;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ImageExtractorService
{
    /**
     * Extract person records from a record-book image, reusing a result for an
     * identical SHA-256 image whenever one has already been extracted.
     *
     * @return array{upload: CampusRecordBookUpload, records: array, page_notes: string|null, cached: bool, canonical_upload_id: int}
     */
    public function extract(CampusRecordBookUpload $upload): array
    {
        $claim = DB::transaction(function () use ($upload) {
            $canonical = CampusRecordBookUpload::query()
                ->where('sha256', $upload->sha256)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (!$canonical) {
                throw new RuntimeException('The source upload could not be found.');
            }

            if ($canonical->extracted_data !== null) {
                $canonical->status = $canonical->duplicate_of_upload_id ? 'duplicate' : 'extracted';
                $canonical->extracted_records_count = count($canonical->extracted_data['records'] ?? []);
                $canonical->completed_at = $canonical->completed_at ?? now();
                $canonical->extraction_error = null;
                $canonical->save();

                return ['upload' => $canonical, 'cached' => true];
            }

            $cachedUpload = CampusRecordBookUpload::query()
                ->where('sha256', $upload->sha256)
                ->whereNotNull('extracted_data')
                ->where('id', '<>', $canonical->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if ($cachedUpload) {
                $canonical->extracted_data = $cachedUpload->extracted_data;
                $canonical->extracted_records_count = $cachedUpload->extracted_records_count ?? count($cachedUpload->extracted_data['records'] ?? []);
                $canonical->completed_at = $cachedUpload->completed_at ?? now();
                $canonical->extraction_error = null;
                $canonical->status = $canonical->duplicate_of_upload_id ? 'duplicate' : 'extracted';
                $canonical->save();

                return ['upload' => $canonical, 'cached' => true];
            }

            if ($canonical->status === 'processing') {
                throw new RuntimeException('This image is already being extracted.');
            }

            $canonical->status = 'processing';
            $canonical->extraction_error = null;
            $canonical->save();

            return ['upload' => $canonical, 'cached' => false];
        });

        $canonical = $claim['upload'];

        if ($claim['cached']) {
            $this->copyResultToDuplicates($canonical);

            return [
                'upload' => $upload->fresh(),
                'records' => $canonical->extracted_data['records'] ?? [],
                'page_notes' => $canonical->extracted_data['page_notes'] ?? null,
                'cached' => true,
                'canonical_upload_id' => $canonical->id,
            ];
        }

        try {
            $extraction = $this->requestExtraction($canonical);
            $records = $extraction['records'];
            $completedAt = now();

            DB::transaction(function () use ($canonical, $extraction, $records, $completedAt) {
                $canonical->refresh();
                $canonical->extracted_data = $extraction;
                $canonical->extracted_records_count = count($records);
                $canonical->completed_at = $completedAt;
                $canonical->extraction_error = null;
                $canonical->status = 'extracted';
                $canonical->save();

                $this->copyResultToDuplicates($canonical);
            });

            return [
                'upload' => $upload->fresh(),
                'records' => $records,
                'page_notes' => $extraction['page_notes'],
                'cached' => false,
                'canonical_upload_id' => $canonical->id,
            ];
        } catch (Throwable $exception) {
            $canonical->refresh();
            if ($canonical->status === 'processing') {
                $canonical->status = 'pending';
                $canonical->extraction_error = Str::limit($exception->getMessage(), 2000, '');
                $canonical->save();
            }

            throw $exception;
        }
    }

    protected function requestExtraction(CampusRecordBookUpload $upload): array
    {
        $settings = GeneralSetting::query()->first();
        $apiKey = trim((string) ($settings?->record_book_openai_api_key ?? ''));
        if ($apiKey === '') {
            throw new RuntimeException('OpenAI API key is not configured. Add it under General Settings → Campus Record Book Upload.');
        }

        $model = trim((string) ($settings?->record_book_openai_model ?? '')) ?: 'gpt-4.1-mini';
        $path = base64_decode($upload->image_path, true);
        if ($path === false || $path === '' || str_contains($path, '..')) {
            throw new RuntimeException('The stored image path is invalid.');
        }

        $disk = Storage::disk('protected_uploads');
        if (!$disk->exists($path)) {
            throw new RuntimeException('The uploaded image file could not be found.');
        }

        $mimeType = $upload->mime_type ?: $disk->mimeType($path) ?: 'image/jpeg';
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new RuntimeException('This image type is not supported for extraction.');
        }

        $image = base64_encode($disk->get($path));
        $recordType = $upload->record_type === 'alumni' ? 'alumni' : 'member';
        $instructions = implode("\n", [
            'Read this campus fellowship record-book page and extract every person record that is visibly present.',
            'This page is classified as: '.$recordType.'.',
            'Do not invent or infer missing information. Use null when a value is absent or unreadable.',
            'Preserve names and written values as they appear. Do not merge separate people into one record.',
            'Tables may have no column headers. When headers are absent, infer likely columns from their position, repeated row pattern, and value format (for example, a names column beside phone-number-shaped values).',
            'Apply an inferred column meaning only when the table structure provides reasonable evidence. If a value or column meaning is ambiguous, use null for the uncertain field and explain the uncertainty in the record notes or page_notes.',
            'For handwritten or unclear text, transcribe your best reading and put uncertainty in the notes field.',
            'Return a JSON object with a records array and a page_notes string.',
            'Each record must contain: name, email, phone, gender, matric_year, graduation_year, course, program, course_duration, and notes.',
            'Use null for fields that cannot be read. If no person records are present, return an empty records array.',
        ]);

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(120)
            ->post('https://api.openai.com/v1/responses', [
                'model' => $model,
                'store' => false,
                'max_output_tokens' => 8000,
                'text' => ['format' => ['type' => 'json_object']],
                'input' => [[
                    'role' => 'user',
                    'content' => [
                        ['type' => 'input_text', 'text' => $instructions],
                        [
                            'type' => 'input_image',
                            'image_url' => 'data:'.$mimeType.';base64,'.$image,
                            'detail' => 'high',
                        ],
                    ],
                ]],
            ]);

        if (!$response->successful()) {
            $message = (string) $response->json('error.message', 'The OpenAI Vision request failed.');
            throw new RuntimeException('OpenAI Vision error (HTTP '.$response->status().'): '.Str::limit($message, 500, ''));
        }

        $text = $response->json('output_text');
        if (!is_string($text) || trim($text) === '') {
            foreach ($response->json('output', []) as $output) {
                foreach ($output['content'] ?? [] as $content) {
                    if (($content['type'] ?? null) === 'output_text' && is_string($content['text'] ?? null)) {
                        $text = $content['text'];
                        break 2;
                    }
                }
            }
        }

        if (!is_string($text) || trim($text) === '') {
            throw new RuntimeException('OpenAI Vision returned no extracted text.');
        }

        try {
            $decoded = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new RuntimeException('OpenAI Vision returned invalid JSON.', previous: $exception);
        }

        if (!is_array($decoded) || !isset($decoded['records']) || !is_array($decoded['records'])) {
            throw new RuntimeException('OpenAI Vision did not return the expected records list.');
        }

        $records = array_values(array_map(function ($record) {
            if (!is_array($record)) {
                return ['name' => null, 'email' => null, 'phone' => null, 'gender' => null, 'matric_year' => null, 'graduation_year' => null, 'course' => null, 'program' => null, 'course_duration' => null, 'notes' => 'Could not parse this row.'];
            }

            $normalized = [];
            foreach (['name', 'email', 'phone', 'gender', 'matric_year', 'graduation_year', 'course', 'program', 'course_duration', 'notes'] as $field) {
                $value = $record[$field] ?? null;
                $normalized[$field] = is_scalar($value) && trim((string) $value) !== '' ? trim((string) $value) : null;
            }

            return $normalized;
        }, $decoded['records']));

        return [
            'records' => $records,
            'page_notes' => is_scalar($decoded['page_notes'] ?? null) ? trim((string) $decoded['page_notes']) : null,
        ];
    }

    protected function copyResultToDuplicates(CampusRecordBookUpload $canonical): void
    {
        CampusRecordBookUpload::query()
            ->where('sha256', $canonical->sha256)
            ->where('id', '<>', $canonical->id)
            ->where('status', 'duplicate')
            ->update([
                'extracted_data' => json_encode($canonical->extracted_data, JSON_UNESCAPED_UNICODE),
                'extracted_records_count' => $canonical->extracted_records_count,
                'completed_at' => $canonical->completed_at,
                'extraction_error' => null,
                'updated_at' => now(),
            ]);
    }
}
