<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwardShortlistStage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'position',
        'active',
    ];
}
