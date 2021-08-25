<?php

namespace App\Exports;

use App\Chapter;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportChapters implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $chapters =  Chapter::all();

        foreach($chapters as $chapter){
            // $chapter->date = $chapter->created_at;
            // $chapter->name = User::whereId($chapter->user_id)->value('name');
            // $chapter->email = User::whereId($chapter->user_id)->value('email');
            // $chapter->phone = User::whereId($chapter->user_id)->value('t_phone');
            // $chapter->paid = $chapter->t_amount;
            $chapter->users_count = $chapter->users->count();
        
        }

        return $chapters;
    }
}
