<?php

namespace App\Models;

use App\Models\AwardEntries;
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

    public function entries(){
        return $this->hasMany(AwardEntries::class);
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
            if (strtolower($this->type) === 'etf') {
                $firstName  = $this->entries?->firstWhere('key', 'first_name')?->value;
                $middleName = $this->entries?->firstWhere('key', 'middle_name')?->value;
                $lastName   = $this->entries?->firstWhere('key', 'last_name')?->value;

                // Filter out empty values and join them cleanly with a space
                return implode(' ', array_filter([$firstName, $middleName, $lastName])) ?: 'Unnamed Nominee';
            }

            return $this->entries?->firstWhere('key', 'name_surname_first')?->value ?? 'Unnamed Nominee';
        });
    }

    protected function email(): Attribute
    {
        return Attribute::get(function () {
            return $this->entries?->firstWhere('key', 'email')?->value
                ?? $this->entries?->firstWhere('key', 'email_address')?->value
                ?? 'Unnamed Email';
        });
    }

    protected function phone(): Attribute
    {
        return Attribute::get(function () {
            return $this->entries?->firstWhere('key', 'phone_number')?->value
                // ?? $this->entries?->firstWhere('key', 'email_address')?->value
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
