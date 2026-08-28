<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StakeholderQuestionSubSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StakeholderQuestionSection extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'access_roles' => 'array',
    ];

    public function scopeForModule($query, string $moduleType = 'report')
    {
        return $query->where('module_type', $moduleType);
    }

    public function subsections()
    {
        return $this->hasMany(StakeholderQuestionSubSection::class, 'section_id');
    }

    public function scopeIsActive($query)
    {
        return $query->where('status', 1);
    }
}
