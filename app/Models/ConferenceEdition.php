<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\Donation;
use App\Models\Material;
use App\Models\TempUser;
use Illuminate\Database\Eloquent\Model;

class ConferenceEdition extends Model
{
    protected $guarded = [];

    public function payments(){
        return $this->hasMany(Payment::Class, 'conference_edition_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::Class, 'conference_edition_id');
    }

    public function material()
    {
        return $this->hasMany(Material::Class, 'conference_edition_id');
    }

    public function attemptedPayments()
    {
        return $this->hasMany(TempUser::class);
    }

    public function alumniCount()
    {
        return $this->hasMany(Payment::Class, 'conference_edition_id')->where('level', 'Alumni')->count();
    }

    public function participantCount()
    {
        return $this->hasMany(Payment::Class, 'conference_edition_id')->where('level', 'Participant')->orWhere('level', 'Moderator')->count();        
    }

}
