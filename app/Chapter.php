<?php

namespace App;

use App\User;
use App\Stakeholder;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $guarded = [];
    
    public function users(){
        return $this->hasMany(User::class, 'chapter');
    }

    public function stakeholder(){
        return $this->hasOne(Stakeholder::class);
    }

}
