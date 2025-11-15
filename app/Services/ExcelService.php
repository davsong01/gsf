<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExcelService
{

    public static function download(array $data, array $headers, ?string $filename = null)
    {
        $filename = $filename ?? 'export_' . time() . '.xlsx';
        
        // Ensure $data is a collection of associative arrays matching $headers
        $collection = collect($data)->map(function ($row) use ($headers) {
            $formattedRow = [];
            foreach ($headers as $header) {
                $formattedRow[$header] = $row[$header] ?? null;
            }
            return $formattedRow;
        });

        return Excel::download(new class($collection) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $collection;

            public function __construct($collection)
            {
                $this->collection = $collection;
            }

            public function collection()
            {
                return $this->collection;
            }

            public function headings(): array
            {
                return $this->collection->isEmpty() ? [] : array_keys($this->collection->first());
            }
        }, $filename);
    }
}
