<?php

namespace App\Services;

use App\Models\CampusRecordBookUpload;
use App\Models\CampusRecordBookExtractedRecord;
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
            $matchingUploads = CampusRecordBookUpload::query()
                ->where('sha256', $upload->sha256)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $locked = $matchingUploads->firstWhere('id', $upload->id);
            if (!$locked) throw new RuntimeException('The source upload could not be found.');
            if ($locked->status !== 'approved' && $locked->extracted_data === null) {
                throw new RuntimeException('Approve this image before extracting it.');
            }
            if ($locked->status === 'processing') throw new RuntimeException('This image is already being extracted.');
            $cachedUpload = $matchingUploads->first(fn ($item) => $item->id !== $locked->id && $item->extracted_data !== null);
            if (!$cachedUpload && $matchingUploads->contains(fn ($item) => $item->id !== $locked->id && $item->status === 'processing')) {
                throw new RuntimeException('An identical image is already being extracted. It will be available from the cache shortly.');
            }
            if ($locked->extracted_data === null) {
                $locked->status = 'processing';
                $locked->extraction_error = null;
                $locked->save();
            }
            return ['upload' => $locked, 'cached_data' => $cachedUpload?->extracted_data, 'cached_response' => $cachedUpload?->extraction_response];
        });
        $upload = $claim['upload'];

        $cached = $upload->extracted_data !== null;
        $rawResponse = $upload->extraction_response;

        try {
            if (!$cached) {
                if ($claim['cached_data'] !== null) {
                    $extraction = $claim['cached_data'];
                    $rawResponse = $claim['cached_response'];
                    $cached = true;
                } else {
                    $extraction = $this->requestExtraction($upload);
                    $rawResponse = $extraction['raw_response'] ?? null;
                    unset($extraction['raw_response']);
                }
            } else {
                $extraction = $upload->extracted_data;
            }

            $records = $extraction['records'] ?? [];
            $upload = DB::transaction(function () use ($upload, $extraction, $records, $rawResponse) {
                $upload->refresh();
                $upload->extracted_data = $extraction;
                if ($rawResponse !== null) $upload->extraction_response = $rawResponse;
                $upload->extracted_records_count = count($records);
                $upload->extracted_at = now();
                $upload->completed_at = null;
                $upload->extraction_error = null;
                $upload->status = 'extracted';
                $upload->save();

                if (!$upload->extractedRecords()->exists()) {
                    foreach ($records as $record) {
                        CampusRecordBookExtractedRecord::create([
                            'upload_id' => $upload->id,
                            'data' => $record,
                            'status' => 'pending',
                        ]);
                    }
                }

                return $upload;
            });

            return [
                'upload' => $upload->fresh(),
                'records' => $records,
                'page_notes' => $extraction['page_notes'] ?? null,
                'cached' => $cached,
                'canonical_upload_id' => $upload->id,
            ];
        } catch (Throwable $exception) {
            $upload->refresh();
            if ($upload->status === 'processing') {
                $upload->status = 'approved';
                $upload->extraction_error = Str::limit($exception->getMessage(), 2000, '');
                $upload->save();
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
            'If a column or label says sex, map it to gender. Normalize common values such as M to Male and F to Female while preserving other written values.',
            'Preserve names and written values as they appear. Do not merge separate people into one record.',
            'Tables may have no column headers. When headers are absent, infer likely columns from their position, repeated row pattern, and value format (for example, a names column beside phone-number-shaped values).',
            'Apply an inferred column meaning only when the table structure provides reasonable evidence. If a value or column meaning is ambiguous, use null for the uncertain field and explain the uncertainty in the record notes or page_notes.',
            'For handwritten or unclear text, transcribe your best reading and put uncertainty in the notes field.',
            'Return a JSON object with a records array and a page_notes string.',
            'Each record must contain: name, email, phone, gender, dob, matric_year, graduation_year, course, program, course_duration, and notes.',
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

        $upload->extraction_response = $response->body();
        $upload->save();

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
                return ['name' => null, 'email' => null, 'phone' => null, 'gender' => null, 'dob' => null, 'matric_year' => null, 'graduation_year' => null, 'course' => null, 'program' => null, 'course_duration' => null, 'notes' => 'Could not parse this row.'];
            }

            $normalized = [];
            foreach (['name', 'email', 'phone', 'gender', 'dob', 'matric_year', 'graduation_year', 'course', 'program', 'course_duration', 'notes'] as $field) {
                $value = $record[$field] ?? null;
                $normalized[$field] = is_scalar($value) && trim((string) $value) !== '' ? trim((string) $value) : null;
            }

            return $normalized;
        }, $decoded['records']));

        return [
            'records' => $records,
            'page_notes' => is_scalar($decoded['page_notes'] ?? null) ? trim((string) $decoded['page_notes']) : null,
            'raw_response' => $response->body(),
        ];
    }

}
