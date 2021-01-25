<?php

namespace App\Exports;

use App\Appraisal;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportAppraisals implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Appraisal::all();
    }
}
