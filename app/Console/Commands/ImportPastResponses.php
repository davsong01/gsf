<?php

namespace App\Console\Commands;

use App\Models\Award;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// php artisan import:responses etf
class ImportPastResponses extends Command
{
    // The terminal command name you will type
    protected $signature = 'import:responses {type=etf}';
    protected $description = 'Imports old excel/csv responses into awards and award_entries';

    public function handle()
    {
        $type = $this->argument('type');
        $csvPath = storage_path('app/past_responses.csv');

        if (!file_exists($csvPath)) {
            $this->error("File not found at storage/app/past_responses.csv");
            return Command::FAILURE;
        }

        // Open the file for reading
        $file = fopen($csvPath, 'r');

        // 1. Grab the first row to act as our dictionary headers (e.g. "First Name", "Gender")
        $headers = fgetcsv($file);

        // Clean up headers to look like your programmatic keys
        $keys = array_map(function($header) {
            return Str::of($header)->lower()->replaceMatches('/[^a-z0-9\s]/', '')->slug('_')->__toString();
        }, $headers);

        $rowCount = 0;

        $this->info("Starting import for type: {$type}...");

        // 2. Loop through each row of student responses
        while (($row = fgetcsv($file)) !== false) {
            // Combine headers keys with row values
            $rowData = array_combine($keys, $row);

            DB::transaction(function () use ($rowData, $type) {
                // Create the parent Award record
                $award = Award::create([
                    'type'            => $type,
                    'reference'       => strtoupper($type . '-' . uniqid()),
                    'zone_status'     => 0,
                    'field_status'    => 0,
                    'national_status' => 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                // Loop through every single column field in this row
                foreach ($rowData as $key => $value) {
                    if ($value === null || $value === '') {
                        continue;
                    }

                    if(in_array($key, ['column_38'])){
                        continue;
                    }

                    // Reconstruct the human-readable label back from our slug key
                    $humanName = ucwords(str_replace('_', ' ', $key));

                    // Catch and handle the institutional ID mapping dynamically
                    if ($key === 'select_institution') {
                        // Look up the database to instantly grab the matching ID record
                        $chapter = DB::table('chapters')
                                        ->where('name', $value)
                                        ->first();

                        $value = $value;
                        $key = 'chapter_id';

                        if ($chapter) {
                            $award->update([
                                'chapter_id' => $chapter->id,
                                'zone_id' => $chapter->zone_id,
                                'field_id' => $chapter->field_id
                            ]);
                        }

                    }

                    if($key == 'timestamp'){
                        $award->update([
                            'created_at' => $value
                        ]);
                    }

                    // Insert right into your EAV entries mapping table
                    DB::table('award_entries')->insert([
                        'award_id'   => $award->id,
                        'key'        => $key,
                        'name'       => $humanName,
                        'value'      => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            $rowCount++;
        }

        fclose($file);
        $this->info("Successfully imported {$rowCount} student records seamlessly!");
        return Command::SUCCESS;
    }
}
