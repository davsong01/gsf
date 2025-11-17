<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StakeholderQuestionSubSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StakeholderQuestionSection extends Model
{
    use HasFactory;

    public function subsections()
    {
        return $this->hasMany(StakeholderQuestionSubSection::class, 'section_id');
    }

    public function scopeIsActive($query)
    {
        return $query->where('status', 1);
    }
}
