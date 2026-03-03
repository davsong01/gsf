<?php

namespace App\Models;

use App\Models\User;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{

    protected $guarded = [];
    protected $casts = [
        'field_ids' => 'array',
        'chapter_ids' => 'array',
    ];

    public function user(){
        return $this->hasMany(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Transaction::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFieldIdsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getChapterIdsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getFieldsAttribute()
    {
        return Field::whereIn('id', $this->field_ids)->get();
    }

    public function getChaptersAttribute()
    {
        return Chapter::whereIn('id', $this->chapter_ids)->get();
    }
}
