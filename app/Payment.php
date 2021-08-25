<?php

namespace App;

use App\Chapter;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }
}
