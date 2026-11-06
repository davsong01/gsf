<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $casts = [
        'record_book_openai_api_key' => 'encrypted',
    ];

    protected $hidden = ['record_book_openai_api_key'];
}
