<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardSetting extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'allow_chapter_edit' => 'datetime',
        'allow_chapter_comment' => 'datetime',
        'allow_chapter_approval' => 'datetime',

        'allow_zone_edit' => 'datetime',
        'allow_zone_comment' => 'datetime',
        'allow_zone_approval' => 'datetime',

        'allow_field_edit' => 'datetime',
        'allow_field_comment' => 'datetime',
        'allow_field_approval' => 'datetime',

        'first_class_awards_deadline' => 'datetime',
        'etf_awards_deadline' => 'datetime',
    ];
}
