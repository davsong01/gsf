<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderQuestionSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StakeholderQuestionSubSection extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'access_roles' => 'array',
    ];

    public function questions()
    {
        return $this->hasMany(StakeholderReportQuestion::class, 'sub_section_id');
    }

    public function section()
    {
        return $this->belongsTo(StakeholderQuestionSection::class, 'section_id');
    }

    public function scopeIsActive($query)
    {
        return $query->where('status', 1);
    }
}
