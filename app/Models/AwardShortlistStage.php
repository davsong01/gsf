<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwardShortlistStage extends Model
{
    protected $casts = [
        'active' => 'boolean',
        'mark_as_final' => 'boolean',
        'system_conditions' => 'array',
    ];

    protected $fillable = [
        'title',
        'description',
        'slug',
        'award_type',
        'stage_engine',
        'system_conditions',
        'position',
        'active',
        'mark_as_final'
    ];
}
