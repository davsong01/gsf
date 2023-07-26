<?php

namespace app\Models;

use App\Models\field;
use AppModels\Chapter;
use AppModels\Stakeholder;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $guarded = [];
    
    public function chapters(){
        return $this->hasMany(Chapter::class);
    }

    public function field(){
        return $this->belongsTo(Field::class);
    }

    public function stakeholder(){
        return $this->hasOne(Stakeholder::class);
    }

    public function zonalCord(){
        return $this->hasOne(Stakeholder::class, 'zone_id');
    }

}
