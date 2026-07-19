<?php

namespace App\Models;

use App\Models\AwardEntry;
use App\Models\AwardShortlist;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Award extends Model
{
    use HasFactory, SoftDeletes; // 2. Use the trait inside the class

    // Optional: If you want to automatically cast it or treat it explicitly
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    protected $casts = [
        'is_archive' => 'boolean',
    ];

    public function entry(){
        return $this->hasOne(AwardEntry::class);
    }

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }

    public function field(){
        return $this->belongsTo(Field::class);
    }

    public function zone(){
        return $this->belongsTo(Zone::class);
    }

    public function approvedBy(){
        return $this->belongsTo(User::class, 'national_approved_by');
    }

    public function rejectedBy(){
        return $this->belongsTo(User::class, 'national_rejected_by');
    }


    protected function name(): Attribute
    {
        return Attribute::get(function () {

            $firstName  = $this->entry?->first_name;
            $middleName = $this->entry?->middle_name;
            $lastName   = $this->entry?->last_name;

            return implode(
                ' ',
                array_filter([
                    $firstName,
                    $middleName,
                    $lastName,
                ])
            ) ?: 'Unnamed Nominee';
        });
    }

    protected function email(): Attribute
    {
        return Attribute::get(function () {

            return $this->entry?->email_address
                ?? $this->entry?->email
                ?? 'No Email';
        });
    }

    protected function phone(): Attribute
    {
        return Attribute::get(function () {

            return $this->entry?->phone_number
                ?? '-';
        });
    }

    public function shortlists()
    {
        return $this->hasMany(AwardShortlist::class);
    }

    public function currentShortlistStage()
    {
        return $this->hasOne(AwardShortlist::class)
            ->latestOfMany();
    }

}
