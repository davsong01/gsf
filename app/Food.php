<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $guarded = [];
    
    public function user(){
        return $this->hasMany(User::class);
    }
}
