<?php

namespace App;

use App\Food;
use App\Post;
use App\Hostel;
use App\Payout;
use App\Chapter;
use App\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;

    protected $guarded = [];

			/**
	 * The attributes that should be hidden for arrays
	 * @var array
	 */
    protected $hidden = [
        'password', 'remember_token', 'id', 'created_at', 'updated_at'
    ];

    public function hostel(){
        return $this->belongsTo(Hostel::class);
    }

    public function food(){
        return $this->belongsTo(Food::class);
    }
    public function foodstand(){
        return $this->belongsTo(Food::class);
    }

    public function moderator(){
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function campus(){
        return $this->belongsTo(Chapter::class, 'chapter');
    }

    public function payouts(){
        return $this->hasMany(Payout::class);
    }

    public function setSwitchingUser($id)
    {
        Session::put('switchuser', $id);
    }

    public function stopSwitchingUser()
    {
        Session::forget('switchuser');
    }

    public function isSwitchingUser()
    {
        return Session::has('switchuser');
    }  
}
