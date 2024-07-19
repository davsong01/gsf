<?php

namespace App\Services;

use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Collection;

class ExcelService
{
    public static function download(array $data, array $headers)
    {
        $filename = rand().'.xlsx';
        $dataWithHeaders = array_merge([$headers], $data);

        $collection = new Collection($data);

        return (new FastExcel($collection))->download($filename);
    }
}
