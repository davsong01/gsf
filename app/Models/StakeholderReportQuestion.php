<?php

namespace App\Models;

use App\Models\StakeholderPermission;
use App\Models\StakeholderReportAnswer;
use Illuminate\Database\Eloquent\Model;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderQuestionSubSection;

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

    public function section()
    {
        return $this->belongsTo(StakeholderQuestionSection::class, 'section_id');
    }

    public function subsection()
    {
        return $this->belongsTo(StakeholderQuestionSubSection::class, 'sub_section_id');
    }

    public function scopeIsActive($query)
    {
        return $query->where('status', 1);
    }
    
    public function permissions()
    {
        return $this->belongsToMany(
            StakeholderPermission::class,
            'stakeholder_report_question_permissions',
            'stakeholder_report_question_id',
            'stakeholder_permission_id'
        );
    }
}
