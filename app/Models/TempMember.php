<?php

namespace App\Models;

use App\Models\Chapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TempMember extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function campus()
    {
        return $this->belongsTo(Chapter::class, 'chapter');
    }

}
