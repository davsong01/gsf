<?php
//  php artisan import:responses etf

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Services\AwardService;

class ImportPastResponses extends Command
{
    protected $signature = 'import:responses {type=etf}';
    protected $description = 'Imports old excel/csv responses uniformly using the AwardService tier';

    public function handle(AwardService $awardService)
    {
        $type = $this->argument('type');

        if($type == 'etf'){
            $csvPath = storage_path('app/past_responses.csv');
        }else{
            $csvPath = storage_path('app/go_past_responses.csv');
        }

        if (!file_exists($csvPath)) {
            $this->error("File not found at storage/app/past_responses.csv");
            return Command::FAILURE;
        }

        $file = fopen($csvPath, 'r');
        $headers = fgetcsv($file);
        
        $keys = array_map(function($header) {
            return Str::of($header)->lower()->replaceMatches('/[^a-z0-9\s]/', '')->slug('_')->__toString();
        }, $headers);

        $rowCount = 0;
        $this->info("Starting uniform import loop for type: {$type}...");

        while (($row = fgetcsv($file)) !== false) {
            if (count($keys) !== count($row)) {
                continue;
            }

            $rawRowData = array_combine($keys, $row);
            $fieldsPayload = [];

            foreach ($rawRowData as $key => $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                $headerIndex = array_search($key, $keys);
                $originalHeaderName = $headers[$headerIndex] ?? ucwords(str_replace('_', ' ', $key));

                $fieldsPayload[] = [
                    'key'   => $key,
                    'name'  => $originalHeaderName,
                    'value' => $value
                ];
            }

            // Execute service logic natively using structured arrays
            $result = $awardService->storeFromGoogle([
                'type'   => $type,
                'fields' => $fieldsPayload
            ]);

            if ($result['code'] !== 201) {
                $this->error("Failed importing row " . ($rowCount + 1) . ": " . ($result['message'] ?? 'Unknown error'));
            } else {
                $rowCount++;
            }
        }

        fclose($file);
        $this->info("Successfully imported {$rowCount} student records using uniform service layout architecture!");
        return Command::SUCCESS;
    }
}
