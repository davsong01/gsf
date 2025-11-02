<?php

namespace App\Models;

use App\Models\Hostel;
use App\Models\ConferenceEdition;
use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    protected $fillable = ['name', 'code', 'description', 'status'];

    public function conferenceEditions()
    {
        return $this->hasMany(ConferenceEdition::class);
    }

    public function fields()
    {
        return $this->hasMany(MinistryField::class);
    }
}
