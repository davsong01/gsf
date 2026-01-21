<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
