<?php

namespace App\Models;

use App\Models\Award;
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

    public function awards()
    {
        return $this->hasMany(Award::class, 'current_shortlist_stage_id')
            ->where('is_archive', false);
    }
}
