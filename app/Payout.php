<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $guarded = [];

    public function getStatusAttribute($value)
    {
        if($value == 0){
            $value = 'Pending';
        }

        if($value == 1){
            $value = 'Paid';
        }

        return $value;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
