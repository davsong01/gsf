<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StakeholderReportAnswer extends Model
{
    protected $fillable = ['report_id', 'question_id', 'answer_value'];

    public function report()
    {
        return $this->belongsTo(StakeholderReport::class, 'report_id');
    }

    public function question()
    {
        return $this->belongsTo(StakeholderReportQuestion::class, 'question_id');
    }
}