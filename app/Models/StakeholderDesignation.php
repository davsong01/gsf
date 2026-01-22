<?php

namespace App\Models;

use App\Models\Stakeholder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StakeholderDesignation extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'order',
        'type',
        'status',
        'zone_id',
        'field_id',
    ];

    public function stakeholder(){
        return $this->hasOne(Stakeholder::class, 'designation_id');
    }
}
