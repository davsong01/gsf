<?php

namespace App\Models;

use App\Models\StakeholderRole;
use Illuminate\Database\Eloquent\Model;
use App\Models\StakeholderReportQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StakeholderPermission extends Model
{
    protected $table = 'stakeholder_permissions';

    protected $fillable = ['name', 'slug', 'group'];

    public function roles()
    {
        return $this->belongsToMany(
            StakeholderRole::class,
            'stakeholder_rps',
            'stakeholder_permission_id',
            'stakeholder_role_id'
        );
    }

    public function reportQuestions()
    {
        return $this->belongsToMany(
            StakeholderReportQuestion::class,
            'stakeholder_report_question_permissions',
            'stakeholder_permission_id',
            'stakeholder_report_question_id'
        );
    }
}
