<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'otp',
        'type',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];


    public function userable()
    {
        return $this->morphTo();
    }
}
