<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterGalleryPhoto extends Model
{
    protected $fillable = ['chapter_id', 'image_path', 'caption'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
