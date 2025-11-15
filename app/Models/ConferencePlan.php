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
        $fields = MinistryField::whereIn('id', $this->registration_fields)->where('status', 1)->get();
        return $fields;
    }

    public function registered()
    {
        return $this->hasMany(Transaction::class);
    }
}
