<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterNeed extends Model
{
    protected $fillable = ['chapter_id', 'title', 'description', 'amount', 'is_active'];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
