<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampusRecordBookUpload extends Model
{
    protected $fillable = [
        'chapter_id', 'stakeholder_id', 'image_path', 'original_name', 'mime_type', 'file_size', 'sha256',
        'status', 'extracted_records_count', 'completed_at',
        'record_type',
    ];

    protected $casts = ['completed_at' => 'datetime'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function stakeholder()
    {
        return $this->belongsTo(Stakeholder::class);
    }
}
