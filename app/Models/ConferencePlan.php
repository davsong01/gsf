<?php

namespace App\Models;

use App\Models\Transaction;
use App\Models\MinistryField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConferencePlan extends Model
{
    use HasFactory;
    protected $guarded;
    
    protected $casts = ['items' => 'array', 'registration_fields' => 'array'];

    public function scopeFields(){
        $ministryId = $this->conferenceEdition?->ministry_id;

        if (!$ministryId || empty($this->registration_fields)) {
            return collect();
        }

        return MinistryField::where('ministry_id', $ministryId)
            ->whereIn('id', $this->registration_fields)
            ->where('status', 1)
            ->orderBy('display_order')
            ->get();
    }

    public function conferenceEdition()
    {
        return $this->belongsTo(ConferenceEdition::class, 'conference_edition_id');
    }

    public function registered()
    {
        return $this->hasMany(Transaction::class);
    }
}
