<?php

namespace App\Models;

use App\Models\AwardEntries;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;
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
                ?? 'Unnamed Nominee';
        });
    }

}
