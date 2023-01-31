<?php

namespace App;

use App\User;
use App\Zone;
use App\Event;
use App\Field;
use App\Payment;
use App\Stakeholder;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $guarded = [];
    
    public function users(){
        return $this->hasMany(User::class, 'chapter_id');
    }

    public function stakeholder(){
        return $this->hasOne(Stakeholder::class);
    }

    public function zone(){
        return $this->belongsTo(Zone::class);
    }
    public function field(){
        return $this->belongsTo(Field::class);
    }

    public function events(){
        return $this->hasMany(Event::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function registerdParticipants()
    {
        return $this->hasManyThrough(Payment::class, User::class);
    }
    
}
