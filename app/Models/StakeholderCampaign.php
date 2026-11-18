<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StakeholderCampaign extends Model
{
    protected $guarded = [];

    protected $casts = [
        'open_from' => 'datetime',
        'open_until' => 'datetime',
        'event_at' => 'datetime',
    ];

    public function questions()
    {
        return $this->belongsToMany(
            StakeholderReportQuestion::class,
            'stakeholder_campaign_questions',
            'campaign_id',
            'question_id'
        )->withPivot('position')->withTimestamps()->orderBy('stakeholder_campaign_questions.position');
    }

    public function responses()
    {
        return $this->hasMany(StakeholderCampaignResponse::class, 'campaign_id');
    }
}
