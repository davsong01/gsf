<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\Donation;
use App\Models\Material;
use App\Models\Ministry;
use App\Models\TempUser;
use App\Models\Transaction;
use App\Models\ConferencePlan;
use App\Models\PaymentProvider;
use App\Models\ConferenceSchedule;
use Illuminate\Database\Eloquent\Model;

class ConferenceEdition extends Model
{
    protected $guarded = [];
    protected $casts = [
        'template_settings' => 'array',
        'conference_speakers' => 'array',
        'faq_ids' => 'array',
        'speaker_ids' => 'array'
    ];

    public function transactions(){
        return $this->hasMany(Transaction::Class, 'conference_edition_id', 'id')->where('purpose', 'conference');
    }

    public function donations()
    {
        return $this->hasMany(Transaction::Class, 'conference_edition_id', 'id')->where('purpose','donation');
    }

    public function material()
    {
        return $this->hasMany(Material::Class, 'conference_edition_id');
    }

    public function attemptedPayments()
    {
        return $this->hasMany(Transaction::class)->where('status', '!=', 'Complete');
    }

    public function alumniCount()
    {
        return $this->hasMany(Transaction::class, 'conference_edition_id')->where('level', 'Alumni')->where('status', '=', 'Complete');
    }

    public function participantCount()
    {
        return $this->hasMany(Transaction::class, 'conference_edition_id')
            ->where('status', '=', 'Complete')
            ->where(function ($query) {
                $query->where('level', 'Participant')
                    ->orWhere('level', 'Moderator');
            })
        ->count();
    }

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function conferenceplans()
    {
        return $this->hasMany(ConferencePlan::class);
    }

    public function schedules()
    {
        return $this->hasMany(ConferenceSchedule::class);
    }

    public function paymentprovider()
    {
        return $this->belongsTo(PaymentProvider::class, 'payment_provider_id');
    }
}
