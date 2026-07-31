<?php

namespace App\Models;

use App\Models\Food;
use App\Models\Post;
use App\Models\Hostel;
use App\Models\Payout;
use App\Models\Chapter;
use App\Models\Stakeholder;
use App\Models\Transaction;
use App\Models\Notification;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use App\Models\StakeholderDesignation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    private const IMPERSONATOR_ID_SESSION_KEY = 'impersonator_id';
    private const IMPERSONATOR_GUARD_SESSION_KEY = 'impersonator_guard';

	use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

	protected $guarded = [];

    protected $casts = [
        'permissions' => 'array'
    ];

	public function isSubAdmin() {
		$subAdmins = [3,4,5,6];
        if(in_array($this->role, $subAdmins) && $this->status == 0){
            return true;
        }else return false;
	}

	public function scopeIsAdmin(){
		$admins = [1];
		if(in_array($this->role, $admins)){
			return true;
		}else return false;
	}

	public function scopeIsMember(){
		if($this->status == '0'){
			return true;
		}else return false;
	}

    public function campus(){
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

	public function transactions(){
        return $this->hasMany(Transaction::class)->orderBy('created_at','DESC');
    }

    public function payouts(){
        return $this->hasMany(Payout::class);
    }

    public function setSwitchingUser($id)
    {
        Session::put(self::IMPERSONATOR_ID_SESSION_KEY, $id);
    }

    public function stopSwitchingUser()
    {
        // Session::forget('switchuser');
    }

    public function isSwitchingUser()
    {
        return (
                Session::get(self::IMPERSONATOR_GUARD_SESSION_KEY) === 'web'
                && Session::has(self::IMPERSONATOR_ID_SESSION_KEY)
            )
            || (Session::get('switchuser_guard') === 'web' && Session::has('switchuser'));
    }

	public function getRolenameAttribute(){
		$attributes = getCommunityPortfolios();
		return $attributes[$this->role] ?? ($this->role == 1 ? 'Admin' : 'Unknown Role');
	}

	public function scopeAlumni()
	{
		
	}

	public function stakeholder(){
		return $this->hasOne(Stakeholder::class, 'email', 'email');
	}

    public function designation(){
		return $this->belongsTo(StakeholderDesignation::class, 'designation_id');
	}
}
