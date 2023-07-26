<?php

namespace app\Models;

use App\Models\Zone;
use App\Models\Chapter;
use App\Models\Stakeholder;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $guarded = [];

    public function zones(){
        return $this->hasMany(Zone::class);
    }
    
    public function chapters(){
        return $this->hasManyThrough(Chapter::class, Zone::class);
    }

    public function stakeholder(){
        return $this->hasOne(Stakeholder::class);
    }

    public function fieldCord(){
        return $this->hasOne(Stakeholder::class, 'field_id');
    }
}
