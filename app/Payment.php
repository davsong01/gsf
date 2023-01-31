<?php

namespace App;

use App\User;
use App\Chapter;
use App\ConferenceEdition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    public $guarded = [];
    
    public function chapter(){
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function edition()
    {
        return $this->belongsTo(ConferenceEdition::class, 'conference_edition_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function moderator(){
        return $this->belongsTo(Payment::class, 'uploaded_by');
    }

    public function hostel(){
        return $this->belongsTo(Hostel::class);
    }

    public function food(){
        return $this->belongsTo(Food::class);
    }
    public function foodstand(){
        return $this->belongsTo(Food::class);
    }

}
