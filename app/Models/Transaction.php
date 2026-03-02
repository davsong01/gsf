<?php

namespace App\Models;

use App\Models\Food;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Chapter;
use App\Models\ConferencePlan;
use App\Models\ConferenceEdition;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionAllocationField;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use SoftDeletes;

    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'api_response' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function allocationFields()
    {
        return $this->hasMany(TransactionAllocationField::class);
    }

    public function conferenceplan()
    {
        return $this->belongsTo(ConferencePlan::class, 'conference_plan_id');
    }

    public function conferenceEdition()
    {
        return $this->belongsTo(ConferenceEdition::class, 'conference_edition_id');
    }

    public function paymentprovider()
    {
        return $this->belongsTo(PaymentProvider::class, 'payment_provider_id');
    }

    public function getChapterAttribute()
    {
        $chapterId = $this->allocationFields
            ->where('key', 'chapter')
            ->first()
            ?->value;

        return $chapterId ? Chapter::firstWhere('id', $chapterId) : null;
    }

    public function edition()
    {
        return $this->belongsTo(ConferenceEdition::class, 'conference_edition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function servicePoint()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }

    public function foodstand()
    {
        return $this->belongsTo(Food::class);
    }
    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
