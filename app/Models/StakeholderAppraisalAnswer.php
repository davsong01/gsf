<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StakeholderAppraisalAnswer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function appraisal()
    {
        return $this->belongsTo(StakeholderAppraisal::class, 'appraisal_id');
    }
}
