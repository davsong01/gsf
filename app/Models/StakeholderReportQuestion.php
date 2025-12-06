<?php

namespace App\Models;

use App\Models\StakeholderReportAnswer;
use Illuminate\Database\Eloquent\Model;

class StakeholderReportQuestion extends Model
{
    protected $guarded = [];

    protected $casts = [
        'options' => 'array',
        'access_roles' => 'array',
    ];

    public function answers()
    {
        return $this->hasMany(StakeholderReportAnswer::class, 'question_id');
    }
}
