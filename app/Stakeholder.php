<?php

namespace App;

use App\Zone;
use App\Chapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Stakeholder extends Authenticatable
{
    use Notifiable;

    protected $guard = 'stakeholder';

    protected $fillable = [
        'signature',
        'name',
        'phone',
        'email',
        'field_id',
        'zone_id',
        'chapter_id',
        'role',
        'password',
        'day',
        'month',
        'year',
        'portfolio'
    ];
 
     protected $hidden = [
         'password',
     ];

    public function zone(){
        return $this->belongsTo(Zone::class);
    }

    public function field(){
        return $this->belongsTo(Field::class);
    }

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }
}
