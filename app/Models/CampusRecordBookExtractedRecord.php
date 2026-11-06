<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampusRecordBookExtractedRecord extends Model
{
    protected $fillable = ['upload_id', 'user_id', 'data', 'status', 'approved_by', 'approved_by_guard', 'approved_at'];

    protected $casts = [
        'data' => 'array',
        'approved_at' => 'datetime',
    ];

    public function upload()
    {
        return $this->belongsTo(CampusRecordBookUpload::class, 'upload_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
