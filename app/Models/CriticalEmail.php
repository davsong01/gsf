<?php

namespace App\Models;

use App\Models\ConferenceEdition;
use Illuminate\Database\Eloquent\Model;

class CriticalEmail extends Model
{
    protected $guarded = [];
    protected $casts = ['attachments' => 'array'];

    public function settings(){
        return $this->belongsTo(ConferenceEdition::class, 'conference_edition_id');
    }
}
