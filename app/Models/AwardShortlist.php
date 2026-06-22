<?php

namespace App\Models;

use App\Models\Award;
use App\Models\AwardShortlistStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AwardShortlist extends Model
{
    protected $fillable = [
        'award_id',
        'award_shortlist_stage_id',
        'shortlisted_by',
        'remarks',
    ];

    public function award()
    {
        return $this->belongsTo(Award::class);
    }

    public function stage()
    {
        return $this->belongsTo(AwardShortlistStage::class, 'award_shortlist_stage_id');
    }

    public function shortlistedBy()
    {
        return $this->belongsTo(User::class, 'shortlisted_by');
    }
}
