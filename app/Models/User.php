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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{

	use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

	protected $guarded = [];

	/**
	 * The attributes that should be hidden for arrays
	 * @var array
	 */
    protected $guaraded = [];

	public function isSubAdmin() {
		$subAdmins = [3,4,5,6];
        if(in_array($this->role, $subAdmins) && $this->status == 0){
            return true;
        }else return false;
	}

	public function isAdmin(){
		$admins = [1];
		if(in_array($this->role, $admins)){
			return true;
		}else return false;
	}
	
	public function isMember(){
		if($this->status == '0'){
			return true;
		}else return false;
	}

	public function isParticipant($edition)
	{
		$check = Transaction::where(['level'=>'Participant', 'conference_edition_id'=>$edition->id,'user_id'=>$this->id])->first();
		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isAlumni($edition)
	{
		$check = Transaction::where(['level' => 'Alumni', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isNec($edition)
	{
		$check = Transaction::where(['level' => 'Nec', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isModerator($edition)
	{
		$check = Transaction::where(['level' => 'Moderator', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();
		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isChoir($edition)
	{
		$check = Transaction::where(['level' => 'Choir', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isOfficial($edition)
	{
		$check = Transaction::where(['level' => 'Official', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}


	public function completeReg($edition)
	{
		$check = Transaction::where(['registration_status' => 'Complete', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();
		
		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
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
        Session::put('switchuser', $id);
    }

    public function stopSwitchingUser()
    {
        // Session::forget('switchuser');
    }

    public function isSwitchingUser()
    {
        return Session::has('switchuser');
    }  

	public function getRolenameAttribute(){
		$attributes = new Controller();
		$attributes = $attributes->getCommunityPortfolios();
		
		return $attributes[$this->role];
	}

	public function scopeAlumni()
	{
		// $check = Payment::where(['level' => 'Alumni', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		// if (isset($check) && !empty($check)) {
		// 	return true;
		// } else return false;
	}


	public function stakeholder(){
		return $this->hasOne(Stakeholder::class, 'email', 'email');
	}
}
