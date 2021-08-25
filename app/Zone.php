<?php

namespace App;

use App\Field;
use App\Chapter;
use App\Stakeholder;
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

}
