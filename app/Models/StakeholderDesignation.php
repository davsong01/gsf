<?php

namespace App\Models;

use App\Models\Stakeholder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class StakeholderDesignation extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'order',
        'type',
        'status',
        'zone_id',
        'field_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $designation) {
            if (blank($designation->slug)) {
                $designation->slug = Str::slug($designation->name);
            }
        });
    }

    public function stakeholder(){
        return $this->hasOne(Stakeholder::class, 'designation_id');
    }
}
