<?php

namespace App\Models;

use App\Models\User;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Model;

class TempUser extends Model
{
    protected $guarded = [];

    protected $table = 'temp_users';
    
    public function campus(){
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'email','email');
    }
    
}


