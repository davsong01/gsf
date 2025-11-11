<?php

namespace App\Models;

use App\Models\User;
use App\Models\Zone;
use App\Models\Event;
use App\Models\Field;
use App\Models\Payment;
use App\Models\Stakeholder;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $guarded = [];
    
    public function users(){
        return $this->hasMany(User::class, 'chapter_id');
    }

    public function members()
    {
        return User::where('chapter_id', $this->id)->where('status', 0)->where('role', '<>', 1)->orderBy('created_at', 'ASC')->get();
    }

    public function alumni()
    {
        return User::where('chapter_id', $this->id)->where('status', 1)->where('role', '<>', 1)->orderBy('created_at', 'ASC')->get();
    }

    public function stakeholder(){
        return $this->hasOne(Stakeholder::class);
    }

    public function stakeholders()
    {
        return $this->hasMany(Stakeholder::class);
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
