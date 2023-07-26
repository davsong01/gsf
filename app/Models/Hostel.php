<?php

namespace app\Models;

use App\Models\User;
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
}
