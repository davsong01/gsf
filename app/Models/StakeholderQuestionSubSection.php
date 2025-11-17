<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StakeholderReportQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StakeholderQuestionSubSection extends Model
{
    use HasFactory;

    public function questions()
    {
        return $this->hasMany(StakeholderReportQuestion::class, 'sub_section_id');
    }
}
