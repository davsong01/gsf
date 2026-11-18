<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StakeholderCampaignResponse extends Model
{
    protected $guarded = [];

    protected $casts = [
        'answers' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(StakeholderCampaign::class, 'campaign_id');
    }
}
