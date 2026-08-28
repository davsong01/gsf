<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StakeholderAppraisal extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'self_published_at' => 'datetime',
        'evaluation_published_at' => 'datetime',
    ];

    public function appraisee()
    {
        return $this->belongsTo(Stakeholder::class, 'appraisee_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(Stakeholder::class, 'evaluator_id');
    }

    public function answers()
    {
        return $this->hasMany(StakeholderAppraisalAnswer::class, 'appraisal_id');
    }
}
