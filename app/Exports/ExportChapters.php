<?php

namespace App\Exports;

use App\Chapter;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportChapters implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $chapters =  Chapter::with('zone', 'field')->get();

        foreach($chapters as $chapter){
            $chapter->stakeholder_id = $chapter->stakeholder->name ?? 'N/A';
            $chapter->zone_id = $chapter->zone->name ?? 'N/A';
            $chapter->field_id = $chapter->field->name ?? 'N/A';
            $chapter->stakeholder_id = $chapter->stakeholder->name ?? 'N/A';
            unset($chapter->facebook);
            unset($chapter->twitter);
            unset($chapter->created_at);
            unset($chapter->updated_at);
                 
        }

        return $chapters;
    }

    public function headings(): array {
        return ["S/N", "Campus", "Zone", "Field", "Campus Address", "Campus Email", "Campus Phone", "Token", "President"];
    }
}
