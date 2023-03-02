<?php

namespace App;

use App\User;
use App\Payment;
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
}
