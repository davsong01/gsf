<?php

namespace App\Models;

use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderQuestionSubSection;
use Illuminate\Database\Eloquent\Model;

class StakeholderReportAnswer extends Model
{
    protected $fillable = ['report_id', 'question_id', 'answer_value','answer_quantity','question_label','question_sub_section_id','question_section_id'];

    public function report()
    {
        return $this->belongsTo(StakeholderReport::class, 'report_id');
    }

    public function question()
    {
        return $this->belongsTo(StakeholderReportQuestion::class, 'question_id');
    }

    public function section()
    {
        return $this->belongsTo(StakeholderQuestionSection::class, 'question_section_id');
    }

    public function subSection()
    {
        return $this->belongsTo(StakeholderQuestionSubSection::class, 'question_sub_section_id');
    }

}
