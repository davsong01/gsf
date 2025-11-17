<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceSchedule extends Model
{
    use HasFactory;
    protected $casts = ['sessions' => 'array'];

    protected $guarded = [];
}
