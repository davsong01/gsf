<?php

namespace App\Console\Commands;

use App\Models\CampusRecordBookUpload;
use App\Services\ImageExtractorService;
use Illuminate\Console\Command;

class ProcessApprovedRecordBookUploads extends Command
{
    protected $signature = 'record-books:process-approved {--limit=0 : Maximum images to process; zero means all approved images}';
    protected $description = 'Extract approved campus record-book images into reviewable temporary records';

    public function handle(ImageExtractorService $extractor): int
    {
        $limit = max(0, (int) $this->option('limit'));
        CampusRecordBookUpload::query()
            ->where('status', 'processing')
            ->where('updated_at', '<', now()->subMinutes(20))
            ->update(['status' => 'approved', 'updated_at' => now()]);
        $query = CampusRecordBookUpload::query()
            ->where('status', 'approved')
            ->whereNull('extracted_data')
            ->whereNull('extraction_error')
            ->orderBy('id');

        if ($limit > 0) $query->limit($limit);
        $uploads = $query->get();
        $success = 0;
        $failed = 0;

        foreach ($uploads as $upload) {
            try {
                $result = $extractor->extract($upload);
                $this->line('Extracted upload #'.$upload->id.' ('.count($result['records']).' rows).');
                $success++;
            } catch (\Throwable $exception) {
                $this->error('Upload #'.$upload->id.' failed: '.$exception->getMessage());
                $failed++;
            }
        }

        $this->info("Processed {$success} approved image(s); {$failed} failed.");
        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
