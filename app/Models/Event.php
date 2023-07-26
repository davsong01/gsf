<?php

namespace app\Models;

use App\Models\Chapter;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 
        'date',
        'venue',
        'time',
        'banners',
        'chapter_id',
        'slug'
    ];

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }
}
