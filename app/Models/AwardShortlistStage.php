<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwardShortlistStage extends Model
{
    protected $casts = [
        'active' => 'boolean',
        'mark_as_final' => 'boolean',
    ];

    protected $fillable = [
        'title',
        'slug',
        'position',
        'active',
        'mark_as_final'
    ];
}
