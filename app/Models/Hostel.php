<?php

namespace App\Models;

use App\Models\User;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{

    protected $guarded = [];
    
    public function user(){
        return $this->hasMany(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getFieldsAttribute()
    {
        return Field::whereIn('id', $this->field_ids)->get();
    }

    public function getChaptersAttribute()
    {
        return Chapter::whereIn('id', $this->chapters_ids)->get();
    }
}
