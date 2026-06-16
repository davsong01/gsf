<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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

    public static function downloadMultipleSheets(array $sheetsData, array $headers, string $fileName)
    {
        $workbook = new class($sheetsData, $headers) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
            private $sheets;
            private $headers;

            public function __construct($sheetsData, $headers) {
                $this->sheets = $sheetsData;
                $this->headers = $headers;
            }

            public function sheets(): array {
                $sheetObjects = [];
                foreach ($this->sheets as $title => $data) {
                    $sheetObjects[] = new class($title, $data, $this->headers) implements FromCollection, WithHeadings, WithTitle, WithStyles {
                        private $title; private $data; private $headers;

                        public function __construct($title, $data, $headers) {
                            $this->title = $title; $this->data = $data; $this->headers = $headers;
                        }
                        public function collection() { return collect($this->data); }
                        public function headings(): array { return $this->headers; }
                        public function title(): string { return $this->title; }
                        public function styles(Worksheet $sheet) {
                            return [
                                1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A8A']]],
                            ];
                        }
                    };
                }
                return $sheetObjects;
            }
        };

        return Excel::download($workbook, $fileName);
    }


    public static function import($file, bool $ignoreHeaders = false): array
    {
        $rows = Excel::toCollection(null, $file);

        if ($rows->isEmpty()) {
            return [];
        }

        $sheet = $rows->first()->toArray();

        if ($ignoreHeaders) {
            // Return all rows as numeric arrays
            return array_map(fn($row) => array_values($row), $sheet);
        }

        // First row as headers
        $headers = array_map(fn($header) => (string) $header, array_shift($sheet));

        $data = [];
        foreach ($sheet as $row) {
            $data[] = array_combine($headers, array_values($row));
        }

        return $data;
    }
}
