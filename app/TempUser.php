<?php

namespace App;

use App\Chapter;
use Illuminate\Database\Eloquent\Model;

class TempUser extends Model
{
    protected $guarded = [];

     public function campus(){
        return $this->belongsTo(Chapter::class, 'chapter');
    }
}


