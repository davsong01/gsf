<?php

namespace App\Models;

use App\Models\Field;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
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
        return $this->hasMany(Payment::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
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
