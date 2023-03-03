<?php

namespace App;

use App\Field;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $guarded = [];
    
    public function user(){
        return $this->hasMany(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
