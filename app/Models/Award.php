<?php

namespace App\Models;

use App\Models\AwardEntries;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function entries(){
        return $this->hasMany(AwardEntries::class);
    }

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }

    public function field(){
        return $this->belongsTo(Field::class);
    }

    public function zone(){
        return $this->belongsTo(Zone::class);
    }

}
